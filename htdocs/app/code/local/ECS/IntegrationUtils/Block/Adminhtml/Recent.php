<?php

class ECS_IntegrationUtils_Block_Adminhtml_Recent
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct() {
        $this->_controller = 'adminhtml_recent';
        $this->_blockGroup = 'integrationutils';
        $this->_headerText = Mage::helper('integrationutils')->__('Recently Imported Products');
        parent::__construct();

        $this->removeButton('add');
    }
}