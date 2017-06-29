<?php

class ECS_IntegrationUtils_Model_Resource_Abstract extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        parent::_construct();
    }

    public function loadByKeys($object, $kvpArray)
    {
        $read = $this->_getReadAdapter();

        $select = $read->select();
        $select->from($this->getMainTable());

        foreach ($kvpArray as $key => $value) {
            if ($value === NULL) {
                $select->where($key . ' IS NULL');
            } else {
                $select->where($key . ' = ?', (string)$value);
            }
        }

        if ($result = $read->fetchRow($select)) {
            $object->setData($result);
        }
    }
}