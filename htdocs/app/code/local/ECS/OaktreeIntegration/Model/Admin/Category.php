<?php

class ECS_OaktreeIntegration_Model_Admin_Category {
    public function toOptionArray()
    {
        $values = Array();

        $categories = Mage::getModel('catalog/category')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addRootLevelFilter()
            ->addOrderField('name');

        foreach($categories as $_category) {
            $values[] = Array (
                'value' => $_category->getId(),
                'label' => $_category->getName()
            );
        }
        return $values;
    }
}