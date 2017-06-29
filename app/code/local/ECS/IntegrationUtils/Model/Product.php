<?php

class ECS_IntegrationUtils_Model_Product extends ECS_IntegrationUtils_Model_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('integrationutils/product');
    }

    public function setConfigurableAttributes(array $configurableAttributes)
    {
        return $this->setData('configurable_attributes', implode(',', $configurableAttributes));
    }

    public function getConfigurableAttributes()
    {
        return $this->getData('configurable_attributes') ? explode(',', $this->getData('configurable_attributes')) : array();
    }

    public function setProductData(array $data)
    {
        return $this->_setComplexData('product_data', $data);
    }

    public function addProductData(array $data)
    {
        return $this->_addComplexData('product_data', $data);
    }

    public function getProductData($index = null)
    {
        return $this->_getComplexData('product_data', $index);
    }
}