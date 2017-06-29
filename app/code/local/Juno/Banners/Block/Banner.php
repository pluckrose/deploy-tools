<?php

class Juno_Banners_Block_Banner extends Juno_Banners_Block_Abstract
{
	protected function _beforeToHtml()
	{
		if (!$this->getTemplate()) {
			$this->setTemplate('jbanners/banner/default.phtml');
		}
		
		return $this;
	}

	public function getBanner()
	{
		if (!$this->hasDat('banner')) {
			if ($this->hasData('image_id')) {
				$this->setBanner(Mage::getModel('banners/image')->load($this->getImageId()));
			}
			else if ($this->hasData('image_code')) {
				$this->setBanner(Mage::getModel('banners/image')->loadByCode($this->getImageCode()));			
			}
			else {
				$this->setBanner(false);
			}
		}
		
		return $this->getData('banner');
	}
}
