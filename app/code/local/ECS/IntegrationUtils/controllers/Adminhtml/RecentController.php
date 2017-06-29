<?php

class ECS_IntegrationUtils_Adminhtml_RecentController
    extends Mage_Adminhtml_Controller_Action
{
    public function indexAction() {
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('integrationutils/adminhtml_recent'));
        $this->renderLayout();
    }
}