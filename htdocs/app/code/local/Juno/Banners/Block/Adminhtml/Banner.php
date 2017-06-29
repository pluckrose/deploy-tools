<?php

class Juno_Banners_Block_Adminhtml_Banner extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_banner';
		$this->_blockGroup = 'banners';
		$this->_headerText = $this->__('Banner Manager');
		$this->_addButtonLabel = $this->__('Create a new banner');

		parent::__construct();
	}
}
