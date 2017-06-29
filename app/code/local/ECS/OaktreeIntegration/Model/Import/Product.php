<?php

class ECS_OaktreeIntegration_Model_Import_Product extends Mage_Core_Model_Abstract
{
    const XML_NAMESPACE = "http://www.RMSynergy.co.uk/StoreWebservices"; // TODO Get Namespace of Oaktree web services

    /** @var Mage_Catalog_Model_Product $_productModel  */
    protected $_productModel = null;
    protected $_attributeSetId = null;
    protected $_websiteIds = null;
    protected $_soapConnection = null;

    //protected $_productCount = 0;
    protected $_fetchCount = 0;
    protected $_statusCount = 0;
    protected $_priceCount = 0;
    protected $_stockCount = 0;
    protected $_successCount = 0;
    protected $_missingCount = 0;

    protected $_matixratesByCode = Array();
    protected $_allowedManufacturerIds = null;

    protected function _construct() {
        $this->_productModel = Mage::getModel('catalog/product');

    }

    protected function _getSoapConnection()
    {
        if (!$this->_soapConnection) {
            $this->_soapConnection = Mage::helper("oaktreeintegration")->getSoapConnection(
                Mage::getStoreConfig("oaktreeintegration/items/serviceurl"), self::XML_NAMESPACE
            );
        }
        return $this->_soapConnection;
    }

    protected function _log($message, $error = false) {
        $logFile = 'rms-product-import.log';
        if($error) {
            $logFile = 'rms-product-error.log';
        }
        Mage::log($message, null, $logFile);
    }

    public function getSuccessCount()
    {
        return $this->_successCount;
    }

    public function getMissingCount()
    {
        return $this->_missingCount;
    }

    protected function _getAttributeSetId()
    {
        if (!$this->_attributeSetId) {
            $this->_attributeSetId = Mage::getModel('eav/entity_type')
                ->loadByCode(Mage_Catalog_Model_Product::ENTITY)
                ->getDefaultAttributeSetId();
        }
        return $this->_attributeSetId;
    }

    protected function _getWebsiteIds()
    {
        if (!$this->_websiteIds) {
            /** @var $website Mage_Core_Model_Website */
            foreach (Mage::app()->getWebsites() as $website) {
                $this->_websiteIds[] = $website->getId();
            }
        }

        return $this->_websiteIds;
    }

    public function import() {
        $this->_log('Starting Import');
        $maxTimeModel = Mage::getModel('core/variable')->loadByCode("oaktreeintegration/items/maxtimestamp");
        $maxTimeStamp = $maxTimeModel->getValue(Mage_Core_Model_Variable::TYPE_TEXT);

        $maxTimeStamp = 0;

        $limit = Mage::getStoreConfig("oaktreeintegration/items/fetchlimit");

        $hasMoreResults = true;

        while($hasMoreResults) {
            try {
                $return = $this->_getSoapConnection()->__soapCall("GetNewOrChangedItems",
                    array(
                        new SoapVar($maxTimeStamp,XSD_LONG,null,null,'LastTimeStamp',self::XML_NAMESPACE),
                        new SoapVar($limit,XSD_INT,null,null,'BatchSize',self::XML_NAMESPACE)
                    ),
                    array('soapaction' => self::XML_NAMESPACE.'/GetNewOrChangedItems')
                );
            }
            catch (Exception $e) {
                $this->_log($this->_getSoapConnection()->__getLastResponse(), true);
                throw $e;
            }
            if ($return->Result) {
                $results = Array();
                if (is_array($return->DataSet->diffgram->ItemDataSet->Item)) {
                    $results = $return->DataSet->diffgram->ItemDataSet->Item;
                }
                else if (isset($return->DataSet->diffgram->ItemDataSet->Item)) {
                    $results[] = $return->DataSet->diffgram->ItemDataSet->Item;
                }
                else {
                    break;
                }

                foreach ($results as $item) {
                    $this->_fetchCount++;
                    $this->_importRow($item);
                }
//                $hasMoreResults = $maxTimeStamp != $return->DataSet->diffgram->ItemDataSet->Status->MaxBatchTimeStamp;
                $hasMoreResults = $return->DataSet->diffgram->ItemDataSet->Status->MoreRecords=='true';
                $maxTimeStamp = $return->DataSet->diffgram->ItemDataSet->Status->MaxBatchTimeStamp;
            }
            else {
                $hasMoreResults = false;
            }

            //if ($this->_fetchCount > 0){
            //    $maxTimeModel->setData('plain_value',$maxTimeStamp);
            //    $maxTimeModel->save();
            //}
        }
        $this->_log('Finished Import');
    }

    protected function _importRow($row)
    {
	Mage::log($row, null, 'product-objects.log');
        $allowAll = Mage::getStoreConfig('oaktreeintegration/items/allow_all_manufacturers');
        if(!$allowAll && !in_array($this->_getManufacturerId($row->SupplierName, false), $this->_allowedManufacturerIds())) {
            return false;
        }
        $mapFieldName = Mage::getStoreConfig('oaktreeintegration/items/skufield');
        $this->_log($mapFieldName.' '.$row->{$mapFieldName});
        if(!$mapFieldName) {
            // SKU field not set in System Configuration
            $this->_log('SKU field not set in System Configuration', true);
            Mage::throwException('SKU field not set in System Configuration');
        }

        $entityId = $this->_productModel->getIdBySku($row->{$mapFieldName});
        $productExists = $entityId ? true : false;
        if (!$productExists) {
            if ($row->WebItem=='true') {
                $this->_missingCount++;
                $this->_log('Creating Product..');
                //TODO:: Add try/catch
                $this->_createProduct($row, $mapFieldName);
            }
            return;
        }

        try{
            $this->_log('Price Import..');
            //Import price change
            /** @var ECS_IntegrationUtils_Model_Price $price */
            $price = Mage::getModel('integrationutils/price');
            $price->loadBySkuAndStore($row->{$mapFieldName}, 0);
            $price->setOrigData();
            $price->setStoreId(0);
            $price->setSku($row->{$mapFieldName});
            $price->setPrice($row->Price);
            if ($row->PriceA > $row->Price) {
                $price->setPrice($row->PriceA);
                $price->setSpecialPrice($row->Price);
            }
            else {
                $price->setSpecialPrice(null);
            }

            if ($price->dataHasChangedFor("price") || $price->dataHasChangedFor("special_price")) {
                $price->setCreatedAt(now());
                $price->setRowStatus(ECS_IntegrationUtils_Helper_Data::IMPORT_STATUS_TODO);
                $price->save();
                $this->_priceCount++;
                $this->_log('Success!');
            } else {
                $this->_log('No change.');
            }
        } catch(Exception $e) {
            $this->_log($e->getMessage(), true);
        }


        try {
            $this->_log('Stock Import..');
            //Import stock
            /** @var ECS_IntegrationUtils_Model_Stock $stock */
            $stock = Mage::getModel('integrationutils/stock');
            $stock->load($row->{$mapFieldName}, 'sku');
            $stock->setOrigData();
            $stock->setSku($row->{$mapFieldName});

            $stock->setQty($row->QuantityAvailable);

            if ($stock->dataHasChangedFor('qty')) {

                $stock->setStockStatus($row->QuantityAvailable > 0 ? Mage_CatalogInventory_Model_Stock::STOCK_IN_STOCK : Mage_CatalogInventory_Model_Stock::STOCK_OUT_OF_STOCK);

                $stock->setRowStatus(ECS_IntegrationUtils_Helper_Data::IMPORT_STATUS_TODO);
                $stock->save();
                $this->_stockCount++;

                $this->_successCount++;

                $this->_log('Success!');
            }
        } catch(Exception $e) {
            $this->_log($e->getMessage(), true);
        }

    }

    protected function _createProduct($row, $skuFieldName) {
        $product = Mage::getModel('catalog/product');

        // Attribute set fall back rules
        // RMS > Sysconfig > Magento
        $defaultAttrSet = Mage::getStoreConfig('oaktreeintegration/items/default_attribute_set');
        if(isset($row->Attribute->Code) && !empty($row->Attribute->Code)) {
            $attributeSetId = $row->Attribute->Code;
        } else if (!empty($defaultAttrSet)) {
            $attributeSetId = $defaultAttrSet;
        } else {
            $attributeSetId = $product->getDefaultAttributeSetId();
        }

        // Get import status (disabled/enabled), default to disabled
        $status = Mage::getStoreConfig('oaktreeintegration/items/create_as_enabled') ? 1 : 2;

        // Is MSRP enabled
        $msrpEnabled = $row->PriceB > 0 ? 1 : 0;

        // Is the item stocked, in stock, manage stock etc
        $isStockItem = $row->IsStock ? 1 : 0;
        $manageStock = $row->IsStock ? 0 : 1;
        $isInStock = $row->QuantityAvailable > 0 ? 1 : 0;
        $visibility = $row->RepresentAttribute ? Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE : Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH;

        try{

            $product
                ->setWebsiteIds(array(1))
                ->setAttributeSetId($attributeSetId)
                ->setTypeId('simple') // All Products created as simples. Configs/Bundles etc are manual
                ->setCreatedAt(strtotime('now'))
                ->setSku($row->{$skuFieldName})
                ->setName($row->Description)
                ->setStatus($status)
                ->setTaxClassId(1) // (0 - none, 1 - default, 2 - taxable, 4 - shipping)
                ->setVisibility($visibility)
                ->setPrice($row->Price)
                ->setCost($row->Cost)
                ->setSpecialPrice($row->PriceA)
                ->setMsrpEnabled($msrpEnabled)
                ->setMsrpDisplayActualPriceType(4) // (1 - on gesture, 2 - in cart, 3 - before order confirmation, 4 - use config)
                ->setMsrp($row->PriceB)
                ->setDescription($row->Description)
                ->setManufacturer($this->_getManufacturerId($row->SupplierName))
                ->setEnriched(0) // Shows the product needs to have content updated
                ->setStockData(array(
                    'use_config_manage_stock' => $isStockItem,
                    'manage_stock'            => $manageStock,
                    'use_config_min_sale_qty' => 1,
                    'use_config_max_sale_qty' => 1,
                    'is_in_stock'             => $isInStock,
                    'qty'                     => $row->QuantityAvailable
                ))
                ->setRmsId($row->ID)
                ->setRmsItemLookupCode($row->ItemLookupCode)
                ->setRmsDescription($row->Description)
                ->setRmsSku($row->ItemLookupCode)
                ->setUpc($row->Alias->Alias);

            $product->save();

            $this->_log('Success!');

        } catch (Mage_Core_Exception $e) {
            $this->_log($e->getMessage(), true);
        } catch (Exception $e) {
            $this->_log($e->getMessage(), true);
        }
    }

    /**
     * @param string $name
     * @return int
     */
    protected function _getManufacturerId($name, $create = true) {
	$manufacturerAttr = Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'manufacturer');
        $manufacturers = $manufacturerAttr->getSource()->getAllOptions(false);
        $id = null;
        foreach($manufacturers as $manufacturer) {
            if ($manufacturer['label'] === $name) {
                $id = $manufacturer['value'];
            }
        }
        if(is_null($id) && $create) {
            Mage::log('creating '.$name, null, 'manufacturer-duplication.log');
            Mage::log($manufacturers, null, 'manufacturer-duplication.log');
            $installer = new Mage_Eav_Model_Entity_Setup('core_setup');
            $installer->startSetup();

            $option = array();
            $option['attribute_id'] = $manufacturerAttr->getId();
            $option['value']['option'][0] = $name;
            $installer->addAttributeOption($option);

            $installer->endSetup();
        }

        return $manufacturerAttr->getSource()->getOptionId($name);
    }

    /**
     * @return array
     */
    protected function _allowedManufacturerIds() {
        if(!$this->_allowedManufacturerIds) {
            $ids = Mage::getStoreConfig('oaktreeintegration/items/allowed_manufacturers');
            $this->_allowedManufacturerIds = explode(',', $ids);
        }
        return $this->_allowedManufacturerIds;
    }
}
