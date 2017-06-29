<?php

abstract class ECS_IntegrationUtils_Model_Abstract extends Mage_Core_Model_Abstract
{
    protected function _beforeSave()
    {
        //TODO everything!
    }

    /**
     * Store array of data into a single entity attribute in serialized form
     *
     * @param string|array $key
     * @param array $data
     * @return Varien_Object
     */
    protected function _setComplexData($key, array $data)
    {
        return $this->setData($key, serialize($data));
    }

    /**
     * Add array of data into a single entity attribute in serialized form
     *
     * @param string|array $key
     * @param array $data
     * @return Varien_Object
     */
    protected function _addComplexData($key, array $data)
    {
        return $this->_setComplexData($key, array_merge($this->_getComplexData($key), $data));
    }

    protected function _getComplexData($key, $index = null)
    {
        $data = $this->getData($key) ? unserialize($this->getData($key)) : array();

        if ($index) {
            return isset($data[$index]) ? $data[$index] : '';
        } else {
            return $data;
        }
    }
}