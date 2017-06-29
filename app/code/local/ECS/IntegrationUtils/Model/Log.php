<?php

class ECS_IntegrationUtils_Model_Log extends ECS_IntegrationUtils_Model_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('integrationutils/log');
    }
}