<?php

class Juno_Banners_Block_Adminhtml_Banner_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();
		
		$this->_controller = 'adminhtml_banner';
		$this->_blockGroup = 'banners';
		$this->_headerText = $this->_getHeaderText();
		
		if ($extra = Mage::registry('splash_option_extra')) {
			$this->addButton('splash_view', array(
				'label' => $this->__('View'),
				'onclick' => "popWin('".$extra->getUrl()."', '_blank')",
			));
		}
	}

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }
    
	protected function _getHeaderText()
	{
		if ($banner = Mage::registry('jbanners_banner')) {
			if ($title = $banner->getTitle()) {
				return $title;
			}
		}
	
		return $this->__('Create a new banner');
	}
}
