<?php

class Juno_Banners_Adminhtml_BannerController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

	public function newAction()
	{
		$this->_forward('edit');
	}
	
	public function editAction()
	{
		$this->_initImageModel();
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function saveAction()
	{
		if ($post = $this->getRequest()->getPost()) {
			
			$banner	= Mage::getModel('banners/image');
			$banner->setData($post);
			
			if ($this->getRequest()->getParam('id')) {
				$banner->setId($this->getRequest()->getParam('id'));
			}

			if ($imageFilename = $this->_uploadBannerImage('new_image_filename')) {
				$banner->setImageFilename($imageFilename);
			}
			
			if ($coords = $this->_getSerializedCoordData()) {
				$banner->setImageMap($coords);
			}
			
			try {
				$banner->save();
				$this->getSession()->addSuccess($this->__('The banner was saved successfully.'));
			}
			catch (Exception $e) {
				$this->getSession()->addError($e->getMessage());
			}
		
			$this->_redirect('*/*/edit', array('id' => $banner->getId()));
			return;
		}
		else {
			$this->getSession()->addError($this->__('There was no data to save'));
		}
	
		$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
	}
	
	protected function _getSerializedCoordData()
	{
		if ($post = $this->getRequest()->getPost('coord')) {
			
			$coords = array();
			
			foreach($post as $id => $coord) {
				if (isset($coord['url']) && trim($coord['url'])) {
					$coords[] = array(
						'url' => trim($coord['url']),
						'title' => htmlspecialchars(trim($coord['title'])),
						'left' => number_format($coord['left'], 0),
						'top' => number_format($coord['top'], 0),
						'width' => number_format($coord['width'], 0),
						'height' => number_format($coord['height'], 0),
					);
				}
			}
			
			return serialize($coords);
		}
	
		return false;	
	}
	
	public function deleteAction()
	{
		if ($id = $this->getRequest()->getParam('id')) {
			try {
				Mage::getModel('banners/image')->setId($id)->delete();
				$this->getSession()->addSuccess($this->__('The banner was deleted successfully.'));
			}
			catch (Exception $e) {
				$this->getSession()->addError($e->getMessage());
			}		
		}
		else {
			$this->getSession()->addError($this->__('There was no banner to delete'));
		}
	
		$this->_redirect('*/*/');
	}
	
	/**
	 * Banner/Image related functions
	 *
	 */
	 
	 /**
	  * Uploads a banner image using a key for the uploaded files global array
	  *
	  * @param string $uploadKey
	  * @return string|false
	  */
	protected function _uploadBannerImage($uploadKey)
	{
		if(isset($_FILES[$uploadKey]['name']) && (file_exists($_FILES[$uploadKey]['tmp_name']))) {
			try {
				$uploader = new Varien_File_Uploader($uploadKey);
				$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
				$uploader->setAllowRenameFiles(true);
				$uploader->setFilesDispersion(false);
				
				$path = Mage::helper('banners')->getUploadPath();

				return ($result = $uploader->save($path, $_FILES[$uploadKey]['name'])) ? $result['file'] : $_FILES[$uploadKey]['name'];
			}
			catch(Exception $e) {
				throw new Exception($e->getMessage());
			}
		}
	
	}
	
	protected function _initImageModel()
	{
		if ($bannerId = $this->getRequest()->getParam('id')) {
			$banner = Mage::getModel('banners/image')->load($bannerId);
		
			if ($banner->getId()) {
				Mage::register('jbanners_banner', $banner);
				return $banner;
			}
		}
		
		return false;
	}
	
	/**
	 * Other useful functions
	 *
	 */
	 
	 /**
	  * Retrieves the Admin session model
	  *
	  * @return Mage_Adminhtml_Model_Session
	  */
	public function getSession()
	{
		return Mage::getSingleton('adminhtml/session');
	}
}
