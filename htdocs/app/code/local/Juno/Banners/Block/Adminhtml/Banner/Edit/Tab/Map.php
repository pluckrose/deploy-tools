<?php

class Juno_Banners_Block_Adminhtml_Banner_Edit_Tab_Map extends Mage_Adminhtml_Block_Template
{
	public function __construct()
	{
		parent::__construct();
		
		$this->setTemplate('jbanners/map.phtml');
	}
	
	public function getBanner()
	{
		return Mage::registry('jbanners_banner');
	}



	public function getCoords()
	{
		return ($coordData = $this->getBanner()->getImageMap()) ? unserialize($coordData) : false;
	}

}
