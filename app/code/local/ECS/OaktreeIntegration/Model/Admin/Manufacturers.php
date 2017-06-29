<?php

class ECS_OaktreeIntegration_Model_Admin_Manufacturers
{
    public function toOptionArray() {
        $array = array();
        $attribute = Mage::getModel('eav/entity_attribute')
            ->loadByCode('catalog_product', 'manufacturer');

        $valuesCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter($attribute->getData('attribute_id'))
            ->setStoreFilter(0, false);

        foreach($valuesCollection as $value) {
            $array[] = array(
                'label' => $value->getValue(),
                'value' => $value->getOptionId()
            );
        }

        return $array;
    }
}