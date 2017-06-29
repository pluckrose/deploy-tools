<?php

class Juno_Banners_Block_Gallery extends Juno_Banners_Block_Abstract
{
	
	/**
	 * Retrieves a collection of the set banners
	 * If image_code has not been set, banner collection returns false
	 *
	 * @return false|Juno_Banners_Model_Mysql4_Image_Collection
	 */
	public function getBanners()
	{
		if (!$this->hasData('banners')) {
			if ($this->getImageCode()) {
				$banners = Mage::getResourceModel('banners/image_collection')
					->addImageCodeFilter($this->getImageCode())
					->setOrderByPosition();
					
				$this->setData('banners', $banners);
			}
			else {
				$this->setData('banners', false);
			}
		}
		
		return $this->getData('banners');
	}

}
