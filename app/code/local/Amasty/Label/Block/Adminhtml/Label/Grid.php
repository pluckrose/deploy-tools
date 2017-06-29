<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2011 Amasty (http://www.amasty.com)
*/ 
class Amasty_Label_Block_Adminhtml_Label_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('labelGrid');
        $this->setDefaultSort('label_id');
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('amlabel/label')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $hlp =  Mage::helper('amlabel'); 
        $this->addColumn('label_id', array(
          'header'    => $hlp->__('ID'),
          'align'     => 'right',
          'width'     => '50px',
          'index'     => 'label_id',
        ));
        
        $this->addColumn('name', array(
            'header'    => $hlp->__('Name'),
            'index'     => 'name',
        ));
        
        $url = Mage::getBaseUrl('media') . 'amlabel/';
        $this->addColumn('prod_img', array(
            'header'    => $hlp->__('Product Page Label'),
            'format'    => '<img src="' . $url .'/$prod_img" />',
            'index'     => 'prod_img',
        ));
        $this->addColumn('prod_txt', array(
            'header'    => $hlp->__('Product Page Text'),
            'index'     => 'prod_txt',
        ));        

        $this->addColumn('cat_img', array(
            'header'    => $hlp->__('Category Page'),
            'format'    => '<img src="' . $url .'/$cat_img" />',
            'index'     => 'cat_img',
        )); 
        $this->addColumn('cat_txt', array(
            'header'    => $hlp->__('Category Page Text'),
            'index'     => 'cat_txt',
        ));                      
    
        return parent::_prepareColumns();
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('label_id');
        $this->getMassactionBlock()->setFormFieldName('labels');
        
        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('amlabel')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('amlabel')->__('Are you sure?')
        ));
        
        return $this; 
    }
}