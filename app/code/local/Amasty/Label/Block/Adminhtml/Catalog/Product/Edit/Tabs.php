<?php
/**
 * @copyright   Copyright (c) 2010 Amasty (http://www.amasty.com)
 */ 
class Amasty_Label_Block_Adminhtml_Catalog_Product_Edit_Tabs extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
    protected function _beforeToHtml()
    {
        $name = Mage::helper('amlabel')->__('Product Labels');
        $this->addTab('general', array(
            'label'     => $name,
            'content'   => $this->getLayout()->createBlock('amlabel/adminhtml_catalog_product_edit_labels')
                ->setTitle($name)->toHtml(),
        ));  
        
        return parent::_beforeToHtml();
    }
}