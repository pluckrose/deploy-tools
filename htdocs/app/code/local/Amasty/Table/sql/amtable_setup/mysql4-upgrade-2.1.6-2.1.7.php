<?php
/**
 * @copyright   Copyright (c) 2009-2014 Amasty (http://www.amasty.com)
 */
$installer = $this;

$installer->startSetup();

$installer->run("
  ALTER TABLE  `{$this->getTable('amtable/method')}`  ADD  `select_rate`  tinyint(2) unsigned NOT NULL default '0' AFTER  `cust_groups`;
  ALTER TABLE `{$this->getTable('amtable/method')}` ADD `comment` TEXT NULL DEFAULT NULL AFTER `name`;
");