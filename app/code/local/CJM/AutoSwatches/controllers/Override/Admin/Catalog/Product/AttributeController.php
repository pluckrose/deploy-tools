<?php

include_once('Mage/Adminhtml/controllers/Catalog/Product/AttributeController.php');
class CJM_AutoSwatches_Override_Admin_Catalog_Product_AttributeController extends Mage_Adminhtml_Catalog_Product_AttributeController
{
    public function saveAction()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
            
            $thePath = Mage::getBaseDir('media') . DS . 'autoswatches' . DS . 'swatches' . DS;
            $deleteMe = Mage::app()->getRequest()->getPost('autoswatches_swatch_delete');
        	$d = 0;
        	$a = 0;
			
			if ($deleteMe) {
      			foreach ($deleteMe as $optionId => $delete) {
           			if ($delete) {
                    	$d++;
                    	@unlink($thePath . $optionId . '.jpg'); }
            	}
        	}
        	
        	if($d > 0){ Mage::getSingleton('adminhtml/session')->addSuccess($d.' Swatches Deleted!'); }

  			if(isset($_FILES['autoswatches_swatch']) && isset($_FILES['autoswatches_swatch']['error'])) {
				foreach ($_FILES['autoswatches_swatch']['error'] as $optionId => $error) {
  					try {
    					
    					$uploader = new Varien_File_Uploader('autoswatches_swatch['.$optionId.']');
						$uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
						$uploader->setAllowRenameFiles(false);
						$uploader->setFilesDispersion(false);
						$uploader->save($thePath, $optionId . '.jpg');
						$a++;
  					}catch(Exception $e) {
 						Mage::getSingleton('adminhtml/session')->addError($this->__($e->getMessage()));
  					}
  				}
			}
			
			if($a > 0) { Mage::getSingleton('adminhtml/session')->addSuccess($a.' Swatches Updated!'); }
			
        }
        
        return parent::saveAction();
    }
}
