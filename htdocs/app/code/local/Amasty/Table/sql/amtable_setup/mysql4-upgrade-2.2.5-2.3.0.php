<?php
/**
 * @copyright   Copyright (c) 2009-2014 Amasty (http://www.amasty.com)
 */
$installer = $this;

$installer->startSetup();

$installer->run("
  ALTER TABLE  `{$this->getTable('amtable/method')}`  ADD  `free_types`  varchar(255) NOT NULL default '' AFTER  `cust_groups`;
");

$installer->endSetup();