<?php

class ECS_IntegrationUtils_Model_Import_Price extends ECS_IntegrationUtils_Model_Import_Abstract
{
    protected $_productModel;
    protected $_eventPrefix = 'price';

    protected function _construct()
    {
        parent::_construct();
        $this->_init('integrationutils/price', 'Price import');
        $this->_successMessage = '%d price(s) imported';
        $this->_failureMessage = '%d price(s) failed to import';
        $this->_productModel = Mage::getModel('catalog/product');
    }

    protected function _importRecord($item)
    {
        $productId = $this->_productModel->getIdBySku($item->getSku());

        if (!$productId) {
            Mage::throwException('Product not found ' . $item->getSku());
        }

        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->_productModel->reset();
        $product->load($productId)->setOrigData(null, null);

        if (!$product->getId()) {
            Mage::throwException('Product could not be loaded ' . $item->getSku());
        }

        if ($storeId = $item->getStoreId()) {
            $product->setStoreId($storeId);
            if ($storeId != 0){
                $product->setOrigData("price", "FORCEUPDATE");
                $product->setOrigData("special_price", "FORCEUPDATE");
                $product->setOrigData("special_price_from_date", "FORCEUPDATE");
                $product->setOrigData("special_price_to_date", "FORCEUPDATE");
                if ($item->getTaxClassId() !== null) {
                    $product->setOrigData("tax_class_id", "FORCEUPDATE");
                }
            }
        }

        $this->_log($item->getSku() . ' - ' . $item->getPrice());

        $product->setPrice((string)$item->getPrice());
        // Note that setting this to NULL will actually end up leaving the special price active
        $product->setSpecialPrice((string)$item->getSpecialPrice()); // TODO: maybe should check it, and not override if empty
        $product->setSpecialFromDate((string)$item->getSpecialFromDate());
        $product->setSpecialToDate((string)$item->getSpecialToDate());

        if ($item->getTaxClassId() !== null) {
            $product->setTaxClassId($item->getTaxClassId());
        }

        $product->addData($item->getProductData());
        $product->save();

        return true;
    }

    protected function _deleteRecord($item)
    {
        Mage::throwException('Price import does not support delete');
    }

}