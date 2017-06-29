<?php

class ECS_IntegrationUtils_Model_Stock extends ECS_IntegrationUtils_Model_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('integrationutils/stock');
    }

    public function setStockData(array $data)
    {
        return $this->setData('stock_data', serialize($data));
    }

    public function getStockData($valueKey = null)
    {
        return $this->_getComplexData('stock_data', $valueKey);
    }
}