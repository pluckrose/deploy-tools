<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `{$installer->getTable('shipperhq_synchronize')}`;
CREATE TABLE IF NOT EXISTS `{$installer->getTable('shipperhq_synchronize')}` (
  `synch_id` int(10) unsigned NOT NULL auto_increment COMMENT 'WebShopApps ShipperHQ',
  `notification_id`  int(10) unsigned NOT NULL default '0' COMMENT 'WebShopApps ShipperHQ',
  `attribute_code` varchar(255) NOT NULL COMMENT 'WebShopApps ShipperHQ',
  `attribute_type` varchar(255) NOT NULL COMMENT 'WebShopApps ShipperHQ',
  `value` varchar(255) NOT NULL COMMENT 'WebShopApps ShipperHQ',
  `status` varchar(255) NOT NULL COMMENT 'WebShopApps ShipperHQ',
  `date_added` datetime NOT NULL COMMENT 'WebShopApps ShipperHQ',
  PRIMARY KEY (`synch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


select @entity_type_id:=entity_type_id from {$this->getTable('eav_entity_type')} where entity_type_code='catalog_product';

select @attribute_group_id:=attribute_group_id from {$this->getTable('eav_attribute_group')} where attribute_group_name='Shipping' and attribute_set_id = @attribute_set_id;


/* Free Shipping Flag  */
insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
	attribute_code 	= 'shipperhq_free_shipping',
	backend_type	= 'int',
	frontend_input	= 'boolean',
	is_required	= 0,
	is_user_defined	= 1,
	frontend_label	= 'Free Shipping';

select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='shipperhq_free_shipping';

insert ignore into {$this->getTable('catalog_eav_attribute')}
    set attribute_id 	= @attribute_id,
    	is_visible 	= 1,
    	used_in_product_listing	= 0,
    	is_filterable_in_search	= 0;


/* Shipping Group attribute for modules like i.e ProductMatrix  */
insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
	attribute_code 	= 'shipperhq_shipping_group',
	backend_type	= 'varchar',
	frontend_input	= 'multiselect',
	backend_model = 'eav/entity_attribute_backend_array',
	is_required	= 0,
	is_user_defined	= 0,
	frontend_label	= 'Shipping Group';

select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='shipperhq_shipping_group';

insert ignore into {$this->getTable('catalog_eav_attribute')}
    set attribute_id 	= @attribute_id,
    	is_visible 	= 1,
    	used_in_product_listing	= 0,
    	is_filterable_in_search	= 0;

/* Warehouse attribute for modules like i.e Dropship  */
insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
	attribute_code 	= 'shipperhq_warehouse',
	backend_type	= 'text',
	frontend_input	= 'multiselect',
    backend_model = 'eav/entity_attribute_backend_array',
	is_required	= 0,
	is_user_defined	= 0,
	frontend_label	= 'Origin';

select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='shipperhq_warehouse';

insert ignore into {$this->getTable('catalog_eav_attribute')}
    set attribute_id 	= @attribute_id,
    	is_visible 	= 1,
    	used_in_product_listing	= 0,
    	is_filterable_in_search	= 0;

/* Shipping Qty */
insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
	attribute_code 	= 'shipperhq_shipping_qty',
	backend_type	= 'int',
	frontend_input	= 'text',
	is_required	= 0,
	is_user_defined	= 1,
	frontend_label	= 'Shipping Qty';

select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='shipperhq_shipping_qty';

insert ignore into {$this->getTable('catalog_eav_attribute')}
    set attribute_id 	= @attribute_id,
    	is_visible 	= 1,
    	used_in_product_listing	= 0,
    	is_filterable_in_search	= 0;

/* Shipping Price  */
insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
	attribute_code 	= 'shipperhq_shipping_fee',
	backend_type	= 'decimal',
	frontend_input	= 'price',
	is_required	= 0,
	is_user_defined	= 1,
	frontend_label	= 'Shipping Fee';

select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='shipperhq_shipping_fee';

insert ignore into {$this->getTable('catalog_eav_attribute')}
    set attribute_id 	= @attribute_id,
    	is_visible 	= 1,
    	used_in_product_listing	= 0,
    	is_filterable_in_search	= 0;

/* Additional Price  */
insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
	attribute_code 	= 'shipperhq_additional_price',
	backend_type	= 'decimal',
	frontend_input	= 'price',
	is_required	= 0,
	is_user_defined	= 1,
	frontend_label	= 'Additional Price';

select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='shipperhq_additional_price';

insert ignore into {$this->getTable('catalog_eav_attribute')}
    set attribute_id 	= @attribute_id,
    	is_visible 	= 1,
    	used_in_product_listing	= 0,
    	is_filterable_in_search	= 0;


/* freight class */
insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
	attribute_code 	= 'shipperhq_freight_class',
	backend_type	= 'int',
	frontend_input	= 'text',
	is_required	= 0,
	is_user_defined	= 1,
	frontend_label	= 'Freight Class';

select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='shipperhq_freight_class';

insert ignore into {$this->getTable('catalog_eav_attribute')}
    set attribute_id 	= @attribute_id,
    	is_visible 	= 1,
    	used_in_product_listing	= 0,
    	is_filterable_in_search	= 0;

/* nmfc */
insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
	attribute_code 	= 'shipperhq_nmfc_class',
	backend_type	= 'text',
	frontend_input	= 'text',
	is_required	= 0,
	is_user_defined	= 1,
	frontend_label	= 'NMFC Class';

select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='shipperhq_nmfc_class';

insert ignore into {$this->getTable('catalog_eav_attribute')}
    set attribute_id 	= @attribute_id,
    	is_visible 	= 1,
    	used_in_product_listing	= 0,
    	is_filterable_in_search	= 0;

/* Handling Price */
insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
	attribute_code 	= 'shipperhq_handling_fee',
	backend_type	= 'decimal',
	frontend_input	= 'price',
	is_required	= 0,
	is_user_defined	= 1,
	frontend_label	= 'Handling Fee';

select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='shipperhq_handling_fee';

insert ignore into {$this->getTable('catalog_eav_attribute')}
    set attribute_id 	= @attribute_id,
    	is_visible 	= 1,
    	used_in_product_listing	= 0,
    	is_filterable_in_search	= 0;


/* Carrier Code */
insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
	attribute_code 	= 'shipperhq_carrier_code',
	backend_type	= 'text',
	frontend_input	= 'text',
	is_required	= 0,
	is_user_defined	= 1,
	frontend_label	= 'Carrier Code';

select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='shipperhq_carrier_code';

insert ignore into {$this->getTable('catalog_eav_attribute')}
    set attribute_id 	= @attribute_id,
    	is_visible 	= 1,
    	used_in_product_listing	= 0,
    	is_filterable_in_search	= 0;


/* Volume Weight */
insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
	attribute_code 	= 'shipperhq_volume_weight',
	backend_type	= 'decimal',
	frontend_input	= 'text',
	is_required	= 0,
	is_user_defined	= 1,
	frontend_label	= 'Volume Weight';

select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='shipperhq_volume_weight';

insert ignore into {$this->getTable('catalog_eav_attribute')}
    set attribute_id 	= @attribute_id,
    	is_visible 	= 1,
    	used_in_product_listing	= 0,
    	is_filterable_in_search	= 0;

    	/* Declared Value */
insert ignore into {$this->getTable('eav_attribute')}
    set entity_type_id 	= @entity_type_id,
	attribute_code 	= 'shipperhq_declared_value',
	backend_type	= 'decimal',
	frontend_input	= 'price',
	is_required	= 0,
	is_user_defined	= 1,
	frontend_label	= 'Declared Value',
	note        = 'The deemed cost of this product for customs & insurance purposes';

select @attribute_id:=attribute_id from {$this->getTable('eav_attribute')} where attribute_code='shipperhq_declared_value';

insert ignore into {$this->getTable('catalog_eav_attribute')}
    set attribute_id 	= @attribute_id,
    	is_visible 	= 1,
    	used_in_product_listing	= 0,
    	is_filterable_in_search	= 0;
");

$entityTypeId = $installer->getEntityTypeId('catalog_product');

$attributeSetArr = $installer->getConnection()->fetchAll("SELECT attribute_set_id FROM {$this->getTable('eav_attribute_set')} WHERE entity_type_id={$entityTypeId}");

$addedAttributeCodes = array('shipperhq_free_shipping' => '3',
    'shipperhq_shipping_group' => '1',
    'shipperhq_warehouse' => '2',
    'shipperhq_shipping_fee' => '10',
    'shipperhq_freight_class' => '30',
    'shipperhq_handling_fee' =>'14');

foreach ($attributeSetArr as $attr) {
    $attributeSetId = $attr['attribute_set_id'];

    $installer->addAttributeGroup($entityTypeId, $attributeSetId, 'Shipping', '99');

    $attributeGroupId = $installer->getAttributeGroupId($entityTypeId, $attributeSetId, 'Shipping');

    foreach($addedAttributeCodes as $code => $sort) {
        $attributeId = $installer->getAttributeId($entityTypeId, $code);
        $installer->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, $attributeId, $sort);
    }
};

if (Mage::helper('wsalogger')->getNewVersion() > 10) {


    $isCheckout = array(
        'type'  => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'comment' => 'Shipperhq Shipper',
        'nullable' => 'false',
        'default'  => '0'
    );

    $carrierType = array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'comment' => 'Shipperhq Carrier Type',
        'nullable' => 'true',
    );

    $dispatchDate =  array(
        'type'    	=> Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'	=> 20,
        'comment' 	=> 'Dispatch Date',
        'nullable' 	=> 'true');

    $deliveryDate =  array(
        'type'    	=>  Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'	=> 20,
        'comment' 	=> 'Expected Delivery',
        'nullable' 	=> 'true');

    $installer->getConnection()->addColumn($installer->getTable('sales/quote_address'), 'is_checkout', $isCheckout);
    $installer->getConnection()->addColumn($installer->getTable('sales/quote_address'), 'carrier_type', $carrierType);
    $installer->getConnection()->addColumn($installer->getTable('sales/quote_address'),'dispatch_date', $dispatchDate );
    $installer->getConnection()->addColumn($installer->getTable('sales/quote_address'),'delivery_date', $deliveryDate );
    $installer->getConnection()->addColumn($installer->getTable('sales/quote_address_shipping_rate'), 'carrier_type', $carrierType);
    $installer->getConnection()->addColumn($installer->getTable('sales/quote_address_shipping_rate'),'dispatch_date', $dispatchDate );
    $installer->getConnection()->addColumn($installer->getTable('sales/quote_address_shipping_rate'),'delivery_date', $deliveryDate );
    $installer->getConnection()->addColumn($installer->getTable('sales/order'),'carrier_type', $carrierType );
    $installer->getConnection()->addColumn($installer->getTable('sales/order'),'dispatch_date', $dispatchDate );
    $installer->getConnection()->addColumn($installer->getTable('sales/order'),'delivery_date', $deliveryDate );


} else  {

    $quoteAddressTable = $installer->getTable('sales/quote_address');
    $quoteRateTable = $installer->getTable('sales/quote_address_shipping_rate');
    $orderTable = $installer->getTable('sales/order');

    if(!$installer->getConnection()->tableColumnExists($quoteAddressTable, 'is_checkout')){
        $installer->run("
            ALTER IGNORE TABLE {$quoteAddressTable} ADD is_checkout  smallint(1) unsigned NOT NULL default '0' COMMENT 'Shipperhq Shipper';
        ");
    }
    if(!$installer->getConnection()->tableColumnExists($quoteAddressTable, 'carrier_type')){
        $installer->run("
            ALTER IGNORE TABLE {$quoteAddressTable} ADD carrier_type  text COMMENT 'Shipperhq Carrier Type';
        ");
    }
    if(!$installer->getConnection()->tableColumnExists($quoteAddressTable, 'dispatch_date')){
        $installer->run("
            ALTER IGNORE TABLE {$quoteAddressTable} ADD dispatch_date  text COMMENT 'Shipperhq Dispatch Date';
        ");
    }
    if(!$installer->getConnection()->tableColumnExists($quoteAddressTable, 'delivery_date')){
        $installer->run("
            ALTER IGNORE TABLE {$quoteAddressTable} ADD delivery_date  text COMMENT 'Shipperhq Expected Delivery Date';
        ");
    }



    if(!$installer->getConnection()->tableColumnExists($quoteRateTable,  'carrier_type')){
        $installer->run("
            ALTER IGNORE TABLE {$quoteRateTable} ADD carrier_type text COMMENT 'Shipperhq Carrier Type';
        ");
    }
    if(!$installer->getConnection()->tableColumnExists($quoteRateTable,  'dispatch_date')){
        $installer->run("
            ALTER IGNORE TABLE {$quoteRateTable} ADD dispatch_date text COMMENT 'Shipperhq Dispatch Date';
        ");
    }
    if(!$installer->getConnection()->tableColumnExists($quoteRateTable,  'delivery_date')){
        $installer->run("
            ALTER IGNORE TABLE {$quoteRateTable} ADD delivery_date text COMMENT 'Shipperhq Delivery Date';
        ");
    }



    if(!$installer->getConnection()->tableColumnExists($orderTable,  'carrier_type')){
        $installer->run("
            ALTER IGNORE TABLE {$orderTable} ADD carrier_type text COMMENT 'Shipperhq Carrier Type';
        ");
    }
    if(!$installer->getConnection()->tableColumnExists($orderTable,  'dispatch_date')){
        $installer->run("
            ALTER IGNORE TABLE {$orderTable} ADD dispatch_date text COMMENT 'Shipperhq Dispatch Date';
        ");
    }
    if(!$installer->getConnection()->tableColumnExists($orderTable,  'delivery_date')){
        $installer->run("
            ALTER IGNORE TABLE {$orderTable} ADD delivery_date text COMMENT 'Shipperhq Delivery Date';
        ");
    }

}

$installer->endSetup();
