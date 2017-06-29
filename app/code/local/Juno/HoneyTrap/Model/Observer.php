<?php

class Juno_HoneyTrap_Model_Observer extends Mage_Core_Model_Abstract {

    public function checkTrap(Varien_Event_Observer $observer) {

        // get event and then post data
        $event = $observer->getEvent();
        $post = $event->getControllerAction()->getRequest()->getPost();

        // If Honeytrap is not empty...
        if(Zend_Validate::is( trim($post['htname']) , 'NotEmpty')) {

            // Set error
            $helper = Mage::helper('core');
            $error = $helper->__('A problem has occured with your form submission, please try again.');
            Mage::getModel('core/session')->addError($error);

            // Redirect
            Mage::app()->getResponse()
                ->setRedirect(Mage::getUrl('*/*/*', array('_secure' => true, '_use_rewrite' => true)))
                ->sendResponse();
            exit;
        }
    }

}