<?php
/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$attributeSetId = $installer->getAttributeSetId('catalog_product','Default');
$attributeGroupId = $installer->getAttributeGroup('catalog_product',$attributeSetId,'General');

$installer->addAttribute('catalog_product', 'rms_id', array(
    'backend'       => '',
    'frontend'      => '',
    'class'         => '',
    'default'       => '',
    'label'         => 'RMS ID',
    'input'         => 'text',
    'type'          => 'int',
    'source'        => '',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       => 0,
    'required'      => 0,
    'searchable'    => 0,
    'filterable'    => 0,
    'unique'        => 0,
    'comparable'    => 0,
    'visible_on_front' => 0,
    'is_html_allowed_on_front' => 0
));
$installer->addAttributeToSet('catalog_product', $attributeSetId, $attributeGroupId, 'rms_id', 10000);

$installer->addAttribute('catalog_product', 'rms_item_lookup_code', array(
    'backend'       => '',
    'frontend'      => '',
    'class'         => '',
    'default'       => '',
    'label'         => 'RMS Item Lookup Code',
    'input'         => 'text',
    'type'          => 'text',
    'source'        => '',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       => 0,
    'required'      => 0,
    'searchable'    => 0,
    'filterable'    => 0,
    'unique'        => 0,
    'comparable'    => 0,
    'visible_on_front' => 0,
    'is_html_allowed_on_front' => 0
));
$installer->addAttributeToSet('catalog_product', $attributeSetId, $attributeGroupId, 'rms_item_lookup_code', 10010);

$installer->addAttribute('catalog_product', 'rms_description', array(
    'backend'       => '',
    'frontend'      => '',
    'class'         => '',
    'default'       => '',
    'label'         => 'RMS Description',
    'input'         => 'text',
    'type'          => 'text',
    'source'        => '',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       => 0,
    'required'      => 0,
    'searchable'    => 0,
    'filterable'    => 0,
    'unique'        => 0,
    'comparable'    => 0,
    'visible_on_front' => 0,
    'is_html_allowed_on_front' => 0
));
$installer->addAttributeToSet('catalog_product', $attributeSetId, $attributeGroupId, 'rms_description', 10020);

$installer->endSetup();