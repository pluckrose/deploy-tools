<?php

class Juno_Banners_Model_Mysql4_Image extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{
		$this->_init('banners/image', 'image_id');
	}
}
