<?php

class CJM_AutoSwatches_Block_Rewrite_Adminhtml_Catalog_Product_Attribute_Edit_Tabs 
                        extends Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tabs
{
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
		$swatch_attributes = Mage::helper('autoswatches')->getSwatchAttributes();
        if(in_array(Mage::registry('entity_attribute')->getData('attribute_code'), $swatch_attributes))
        {
            $this->addTab('swatches', array(
                'label'     => Mage::helper('autoswatches')->__('Manage Swatches'),
                'title'     => Mage::helper('autoswatches')->__('Manage Swatches'),
                'content'   => $this->getLayout()->createBlock('autoswatches/swatches')->toHtml(),
            ));
            
            return Mage_Adminhtml_Block_Widget_Tabs::_beforeToHtml();
        }
        
        return $this;
    }
}
