<?php

class ECS_IntegrationUtils_Model_Import_Product extends ECS_IntegrationUtils_Model_Import_Abstract
{
    protected $_productModel;
    protected $_eventPrefix = 'product';

    protected function _construct()
    {
        parent::_construct();
        $this->_init('integrationutils/product', 'Product import');
        $this->_successMessage = '%d product(s) imported';
        $this->_failureMessage = '%d product(s) failed to import';
        $this->_productModel = Mage::getModel('catalog/product');
    }

    protected function _getSuperAttributeId($productId, $attributeId)
    {
        $select = $this->_getRead()
            ->select()
            ->from($this->_getTableName('catalog/product_super_attribute'))
            ->where('product_id = ?', $productId)
            ->where('attribute_id = ?', $attributeId);

        return $this->_getRead()->fetchOne($select);
    }

    protected function _setSuperAttributeData($productId, array $attributes)
    {
        $position = 0;

        foreach ($attributes as $code) {
            /** @var $attribute Mage_Eav_Model_Entity_Attribute */
            $attribute = Mage::getModel('eav/entity_attribute');
            $attribute->loadByCode(Mage_Catalog_Model_Product::ENTITY, $code);

            /** @var $configurableAttribute Mage_Catalog_Model_Product_Type_Configurable_Attribute */
            $configurableAttribute = Mage::getModel('catalog/product_type_configurable_attribute');

            if ($id = $this->_getSuperAttributeId($productId, $attribute->getId())) {
                $configurableAttribute->load($id);
            }

            $configurableAttribute
                ->setProductId($productId)
                ->setAttributeId($attribute->getAttributeId())
                ->setPosition($position++)
                ->setStoreId(0)
                ->setUseDefault(null)
                ->setLabel($attribute->getFrontendLabel())
                ->setAttributeCode($attribute->getAttributeCode())
                ->setFrontendLabel($attribute->getFrontendLabel())
                ->setStoreLabel($attribute->getFrontendLabel())
                ->save();
        }
    }

    protected function _getSuperLinkId($productId, $parentId)
    {
        $select = $this->_getRead()
            ->select()
            ->from($this->_getTableName('catalog/product_super_link'))
            ->where('product_id = ?', $productId)
            ->where('parent_id = ?', $parentId);

        return $this->_getRead()->fetchOne($select);
    }

    protected function _linkProducts($simpleId, $configurableSku)
    {
        $configurableId = Mage::getSingleton('catalog/product')->getIdBySku($configurableSku);

        if (!$this->_getSuperLinkId($simpleId, $configurableId)) {
            $this->_getWrite()->delete($this->_getTableName('catalog/product_super_link'), array(
                'product_id = ?' => $simpleId
            ));

            $this->_getWrite()->delete($this->_getTableName('catalog/product_relation'), array(
                'child_id = ?' => $simpleId
            ));

            $this->_getWrite()->insert($this->_getTableName('catalog/product_super_link'), array(
                'product_id' => $simpleId,
                'parent_id' => $configurableId,
            ));

            $this->_getWrite()->insert($this->_getTableName('catalog/product_relation'), array(
                'parent_id' => $configurableId,
                'child_id' => $simpleId,
            ));
        }
    }

    protected function _importRecord($item)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->_productModel->reset();
        $productId = $product->getIdBySku($item->getSku());

        if ($productId) {
            $this->_log($item->getSku() . ' (update)');
            $product->load($productId);
        } else {
            $this->_log($item->getSku() . ' (create)');
            $product->setSku($item->getSku());
            $product->setAttributeSetId($item->getAttributeSetId());
            $product->setTypeId($item->getTypeId());
            $product->setStockData(array());
        }

        $product->addData($item->getProductData());

        // Shop specific special price will not be used if there is no value stored for default store
        if (!$product->hasSpecialPrice()) {
            $product->setSpecialPrice(null);
        }

        // Fire an event that would allow different customizations based on the product data
        Mage::dispatchEvent('integrationutils_import_product', array(
            'product' => $product,
            'raw' => $item
        ));

        $product->save();

        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            $this->_setSuperAttributeData($product->getId(), $item->getConfigurableAttributes());
        }

        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE && $item->getParentSku()) {
            $this->_linkProducts($product->getId(), $item->getParentSku());
        }

        return true;
    }

    protected function _deleteRecord($item)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product');
        $productId = $product->getIdBySku($item->getSku());

        if ($productId) {
            $this->_log($item->getSku() . ' (delete)');
            $product->load($productId);
            $product->delete();
            $product->save();
        }

        return true;
    }

}