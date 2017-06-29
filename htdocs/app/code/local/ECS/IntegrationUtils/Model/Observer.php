<?php

class ECS_IntegrationUtils_Model_Observer
{
    public function showProductWarning($observer) {
        $product = $observer->getEvent()->getProduct();
        if(!$product->getEnriched()) {
            $warningText = $this->_getWarningText();
            Mage::getSingleton('adminhtml/session')->addWarning($warningText);
        }
    }

    protected function _getWarningText() {
        $warningText = Mage::getStoreConfig('integrationutils/settings/product_warning_text');
        $warningText = $warningText ? $warningText : 'This product`s information has not been enriched yet, please ensure all data is cleansed before enabling. Set "Enriched" to yes to dismiss this message';
        return $warningText;
    }
}