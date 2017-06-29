<?php

/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;

/** Begin Install */
$installer->startSetup();


/**
 * Import Tables
 */

// Product import table
$table = $installer->getConnection()
    ->newTable($installer->getTable('integrationutils/product'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true,
    ), 'Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable' => false,
    ), 'Created At')
    ->addColumn('raw_data', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(), 'Raw import data')
    ->addColumn('sku', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(), 'SKU')
    ->addColumn('attribute_set_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned' => true,
        'nullable' => false,
        'default'  => '0',
    ), 'Attribute Set ID')
    ->addColumn('type_id', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable' => false,
        'default'  => Mage_Catalog_Model_Product_Type::DEFAULT_TYPE,
    ), 'Type ID')
    ->addColumn('parent_sku', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(), 'Parent SKU')
    ->addColumn('configurable_attributes', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Configurable Attributes')
    ->addColumn('product_data', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(), 'Product Data')
    ->addColumn('imported_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable' => true,
    ), 'Created At')
    ->addColumn('import_state', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
        'unsigned' => true,
        'nullable' => false,
        'default'  => 0,
    ), 'Import State')
    ->addIndex($installer->getIdxName('integrationutils/product', array('import_state')), array('import_state'))
    ->addIndex($installer->getIdxName('integrationutils/product', array('sku')), array('sku'))
    ->setComment('Integration Product Import Table');

$installer->getConnection()->createTable($table);


// Stock levels import table
$table = $installer->getConnection()
    ->newTable($installer->getTable('integrationutils/stock'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true,
    ), 'Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable' => false,
    ), 'Created At')
    ->addColumn('raw_data', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(), 'Raw import data')
    ->addColumn('stock_data', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(), 'Stock data')
    ->addColumn('sku', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(), 'SKU')
    ->addColumn('qty', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(), 'Qty')
    ->addColumn('stock_status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Stock Status')
    ->addColumn('imported_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable' => true,
    ), 'Created At')
    ->addColumn('import_state', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
        'unsigned' => true,
        'nullable' => false,
        'default'  => 0,
    ), 'Import State')
    ->addIndex($installer->getIdxName('integrationutils/stock', array('import_state')), array('import_state'))
    ->addIndex($installer->getIdxName('integrationutils/stock', array('sku')), array('sku'))
    ->setComment('Integration Utils Stock');

$installer->getConnection()->createTable($table);

// Price update table
$table = $installer->getConnection()
    ->newTable($installer->getTable('integrationutils/price'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true,
    ), 'Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable' => false
    ), 'Created At')
    ->addColumn('raw_data', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(), 'Raw import data')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned' => true,
        'nullable' => false,
        'default'  => 0,
    ), 'Store Id')
    ->addColumn('sku', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(), 'SKU')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(), 'Price')
    ->addColumn('special_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(), 'Special Price')
    ->addColumn('special_from_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(), 'Special Price From Date')
    ->addColumn('special_to_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(), 'Special Price To Date')
    ->addColumn('tax_class_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'default'   => null,
    ), 'Tax Class ID')
    ->addColumn('product_data', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(), 'Product Data')
    ->addColumn('imported_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable' => true,
    ), 'Created At')
    ->addColumn('import_state', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
        'unsigned' => true,
        'nullable' => false,
        'default'  => 0,
    ), 'Import State')
    ->addIndex($installer->getIdxName('integrationutils/price', array('import_state')), array('import_state'))
    ->addIndex($installer->getIdxName('integrationutils/price', array('sku')), array('sku'))
    ->addIndex($installer->getIdxName('integrationutils/price', array('store_id')), array('store_id'))
    ->setComment('Integration Utils Price');

$installer->getConnection()->createTable($table);

/**
 * Logging Tables
 */

// History
$table = $installer->getConnection()
    ->newTable($installer->getTable('integrationutils/log'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true,
    ), 'Id')
    ->addColumn('started_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable' => false,
    ), 'Created At')
    ->addColumn('finished_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable' => true,
    ), 'Created At')
    ->addColumn('message', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(), 'Message')
    ->addColumn('job_status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable' => false,
        'unsigned' => true
    ), 'Job Status')
    ->addIndex($installer->getIdxName('integrationutils/log', array('job_status')), array('job_status'))
    ->setComment('Integration Utils History Log');

$installer->getConnection()->createTable($table);

$installer->endSetup();