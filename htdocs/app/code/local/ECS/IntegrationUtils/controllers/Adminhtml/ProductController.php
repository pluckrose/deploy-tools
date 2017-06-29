<?php

class ECS_IntegrationUtils_Adminhtml_ProductController
    extends Mage_Adminhtml_Controller_Action
{
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function createConfigurableAction() {
        $params = $this->getRequest()->getParams();
        if(count($params['product']) > 0) {
            $products = array();
            $usedAttributes = array();
            foreach($params['product'] as $id) {
                $_product = Mage::getModel('catalog/product')->load($id);
                $products[] = $_product;
                if(!array_key_exists($_product->getAttributeForConfigCreation(), $usedAttributes)) {
                    $usedAttributes[$_product->getAttributeForConfigCreation()] = array();
                }
                end($products);
                $key = key($products);
                $usedAttributes[$_product->getAttributeForConfigCreation()][] = $key;
            }

            $configurable = Mage::getModel('catalog/product');
            try
            {
                /** @var Mage_Catalog_Model_Product $exampleSimple */
                $exampleSimple = $products[0];
                $configurable->setWebsiteIds(array(1));
                $configurable
                    ->setAttributeSetId($exampleSimple->getAttributeSetId())
                    ->setTypeId('configurable')
                    ->setCreatedAt(strtotime('now'))
                    ->setSku('configurable-'.$exampleSimple->getSku())
                    ->setName($exampleSimple->getSku().'-'.$exampleSimple->getId().' - Update before enabling')
                    ->setStatus(2) // Disabled
                    ->setTaxClassId(2) // Tax class (0 - none, 1 - default, 2 - taxable, 4 - shipping)
                    ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
                    ->setManufacturer($exampleSimple->getManufacturerId())
                    ->setPrice($exampleSimple->getPrice())
                    ->setStockData(
                        array(
                            'use_config_manage_stock' => 0, //'Use config settings' checkbox
                            'manage_stock' => 1, // Manage stock
                            'is_in_stock' => 1, //Stock Availability
                        )
                    );

                /**
                 *  Adding simples to configurable
                 */

                $usedAttrIds = array();
                $configurableProductsData = array();
                foreach ($usedAttributes as $id => $associatedProducts) {
                    $usedAttrIds[] = $id;

                    foreach ($associatedProducts as $index) {
                        $attr = Mage::getModel('catalog/resource_eav_attribute')->load($id);
                        $configurableProductsData[$products[$index]->getId()] = array(
                            '0' => array(
                                'label' => $attr->getSource()->getOptionText($products[$index]->getAttributeForConfigValue()), //attribute label
                                'attribute_id' => $id, //attribute ID of attribute 'color' in my store
                                'value_index' => $products[$index]->getAttributeForConfigValue(), //value of 'Green' index of the attribute 'color'
                                'is_percent' => '0', //fixed/percent price for this option
                                'pricing_value' => '' //value for the pricing
                            )
                        );
                    }
                }

                $configurable->getTypeInstance()->setUsedProductAttributeIds($usedAttrIds);
                $configurableAttributesData = $configurable->getTypeInstance()->getConfigurableAttributesAsArray();

                $configurable->setCanSaveConfigurableAttributes(true);
                $configurable->setConfigurableAttributesData($configurableAttributesData);

                $configurable->setConfigurableProductsData($configurableProductsData);
                $configurable->save();

                Mage::getSingleton('core/session')->addSuccess('Configurable Product added successfully!');
                $this->_redirect('adminhtml/catalog_product/edit', array('id'=>$configurable->getId()));
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('core/session')->addError('There was a problem creating the configurable');
                $this->_redirect('*/adminhtml_recent');
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError('There was a problem creating the configurable');
                $this->_redirect('*/adminhtml_recent');
            }
        } else {
            Mage::getSingleton('core/session')->addError('No product IDs were selected');
            $this->_redirect('*/adminhtml_recent');
        }
    }
}