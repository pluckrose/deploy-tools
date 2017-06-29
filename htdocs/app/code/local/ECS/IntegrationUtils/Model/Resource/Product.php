<?php

class ECS_IntegrationUtils_Model_Resource_Product extends ECS_IntegrationUtils_Model_Resource_Abstract
{
    protected function _construct()
    {
        $this->_init('integrationutils/product', 'id');
    }
}