<?php

class Juno_Banners_Model_Image extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('banners/image');
	}
	
	public function loadByCode($code)
	{
		return $this->load($code, 'code');
	}
	
	public function getHtml($style = null)
	{
		if (is_null($style)) {
			if ($this->getUrl()) {
				return "<a href=\"{$this->getUrl()}\" title=\"{$this->getTitle()}\"><img src=\"{$this->getImageUrl()}\" alt=\"{$this->getTitle()}\"/></a>";
			}

			return "<img src=\"{$this->getImageUrl()}\" alt=\"{$this->getTitle()}\"/>";			
		}
	}
	
	public function getImageUrl()
	{
		return Mage::helper('banners')->getUploadUrl() . $this->getImageFilename();
	}
	
	public function hasMap()
	{
		return ($this->getCoords()) ?  true : false;
	}
	
	public function getCoords()
	{
		if (!$this->hasData('coords')) {
			if ($coords = $this->getImageMap()) {
				$coords = unserialize($coords);
				
				if (count($coords) > 0) {
					$this->setData('coords', $coords);
				}
				else {
					$this->setData('coords', false);
				}
			}
		}
		
		return 	$this->getData('coords');
	}
}
