<?php


class Juno_Banners_Block_Adminhtml_Banner_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('banner_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle($this->__('Banner Information'));
	}
	
	protected function _beforeToHtml()
	{
		$this->addTab('general',
			array(
				'label' => $this->__('Banner Information'),
				'title' => $this->__('Banner Information'),
				'content' => $this->getLayout()->createBlock('banners/adminhtml_banner_edit_tab_general')->toHtml(),
			)
		);
		
		if (Mage::registry('jbanners_banner')) {
			$this->addTab('image_map',
				array(
					'label' => $this->__('Image Map'),
					'title' => $this->__('Image Map'),
					'content' => $this->getLayout()->createBlock('banners/adminhtml_banner_edit_tab_map')->toHtml(),
				)
			);
		
			$this->addTab('related',
				array(
					'label' => $this->__('Sibling Banners'),
					'title' => $this->__('Sibling Banners'),
					'content' => $this->getLayout()->createBlock('banners/adminhtml_banner_edit_tab_siblings')->toHtml(),
				)
			);
		}


		return parent::_beforeToHtml();
	}
}
