<?php
/**
 * @copyright   Copyright (c) 2009-2014 Amasty (http://www.amasty.com)
 */
$installer = $this;

$installer->startSetup();

$installer->run("
  ALTER TABLE  `{$this->getTable('amtable/method')}`  ADD  `max_rate`  decimal(12,2) unsigned NOT NULL default '0' AFTER  `select_rate`;
  ALTER TABLE `{$this->getTable('amtable/method')}` ADD `min_rate` decimal(12,2) unsigned NOT NULL default '0' AFTER `select_rate`;
");