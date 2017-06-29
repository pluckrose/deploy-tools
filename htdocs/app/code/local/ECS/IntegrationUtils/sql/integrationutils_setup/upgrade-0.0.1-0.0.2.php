<?php

$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');

// Get Defaults
$defaultAttributeSet = $installer->getDefaultAttributeSetId('catalog_product');
$defaultAttributeGroup = $installer->getAttributeGroupId('catalog_product', $defaultAttributeSet, 'General');

// To show whether imported product content needs updating
$installer->addAttribute('catalog_product', 'enriched', array(
    'group'                     => 'General',
    'label'                     => 'Enriched',
    'input'                     => 'select',
    'type'                      => 'int',
    'source'                    => 'eav/entity_attribute_source_boolean',
    'global'                    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'                   => 1,
    'default'                   => 1,
    'required'                  => 0,
    'visible_on_front'          => 0,
    'is_html_allowed_on_front'  => 0,
    'is_configurable'           => 0,
    'searchable'                => 0,
    'filterable'                => 0,
    'comparable'                => 0,
    'unique'                    => false,
    'used_in_product_listing'   => false
));

$attribute = $installer->getAttribute('catalog_product', 'enriched');
$installer->addAttributeToSet('catalog_product', $defaultAttributeSet, $defaultAttributeGroup, $attribute['attribute_id']);


// Attribute for configurable
$installer->addAttribute('catalog_product', 'attribute_for_config_creation', array(
    'group'                     => 'General',
    'label'                     => 'Represent in Configurable',
    'input'                     => 'text',
    'type'                      => 'int',
    'global'                    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'                   => 1,
    'default'                   => '',
    'required'                  => 0,
    'visible_on_front'          => 0,
    'is_html_allowed_on_front'  => 0,
    'is_configurable'           => 0,
    'searchable'                => 0,
    'filterable'                => 0,
    'comparable'                => 0,
    'unique'                    => false,
    'used_in_product_listing'   => false
));

$attribute = $installer->getAttribute('catalog_product', 'attribute_for_config_creation');
$installer->addAttributeToSet('catalog_product', $defaultAttributeSet, $defaultAttributeGroup, $attribute['attribute_id']);


// Attribute value for configurable
$installer->addAttribute('catalog_product', 'attribute_for_config_value', array(
    'group'                     => 'General',
    'label'                     => 'Represent Value in Configurable',
    'input'                     => 'text',
    'type'                      => 'int',
    'global'                    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'                   => 1,
    'default'                   => '',
    'required'                  => 0,
    'visible_on_front'          => 0,
    'is_html_allowed_on_front'  => 0,
    'is_configurable'           => 0,
    'searchable'                => 0,
    'filterable'                => 0,
    'comparable'                => 0,
    'unique'                    => false,
    'used_in_product_listing'   => false
));

$attribute = $installer->getAttribute('catalog_product', 'attribute_for_config_value');
$installer->addAttributeToSet('catalog_product', $defaultAttributeSet, $defaultAttributeGroup, $attribute['attribute_id']);
