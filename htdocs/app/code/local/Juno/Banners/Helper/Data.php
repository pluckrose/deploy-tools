<?php

class Juno_Banners_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * Folder to store uploaded banners/images in
	 * This folder should be relative to the media folder
	 *
	 * @param string
	 */
	protected $_uploadFolder = 'jbanners';
	
	/**
	 * Retrieve the image upload folder
	 *
	 * @return string
	 */
	public function getUploadFolder()
	{
		return $this->_uploadFolder;
	}
	
	
	/**
	 * Retrieve the image upload path
	 *
	 * @return string
	 */
	public function getUploadPath()
	{
		return Mage::getBaseDir('media') . DS . $this->_uploadFolder . DS;
	}
	
	/**
	 * Retrieve the image upload URL
	 *
	 * @return string
	 */
	public function getUploadUrl()
	{
		return Mage::getBaseUrl('media') . $this->_uploadFolder . DS;
	}

	public function getBannerByCode($bannerCode)
	{
		return Mage::getModel('banners/image')->loadByCode($bannerCode);
	}
	
	public function getBannerList($code)
	{
		if($banners = Mage::getResourceModel('banners/image_collection')->addImageCodeFilter($code)->setOrderByPosition()) {
			return $banners;
		} else {
			return false;
		}
	}
}
