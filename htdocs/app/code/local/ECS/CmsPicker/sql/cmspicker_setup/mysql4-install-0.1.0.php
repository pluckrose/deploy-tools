<?php

//throw new Exception();

/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer = $this;

$installer->startSetup();
$installer->removeAttribute('catalog_product', 'product_banner');
$installer->addAttribute('catalog_product', 'product_banner', array(
        'type'              => 'text',
        'input'             => 'select',
        'label'             => 'Product Banner',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'           => true,
        'required'          => false,
        'visible_on_front'  => true,
        'source'            => 'ecs_cmspicker/source_cms'
));
$installer->endSetup();
