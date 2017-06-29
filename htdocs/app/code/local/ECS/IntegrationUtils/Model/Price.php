<?php

class ECS_IntegrationUtils_Model_Price extends ECS_IntegrationUtils_Model_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('integrationutils/price');
    }

    public function loadBySkuAndStore($sku, $storeId = null)
    {
        if (is_object($storeId)) {
            $storeId = $storeId->getId();
        }

        $this->_getResource()->loadByKeys($this, array(
            'sku' => $sku,
            'store_id' => $storeId
        ));

        return $this;
    }

    public function setProductData(array $data)
    {
        return $this->_setComplexData('product_data', $data);
    }

    public function getProductData($valueKey = null)
    {
        return $this->_getComplexData('product_data', $valueKey);
    }
}