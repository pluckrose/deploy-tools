<?php
/**
 * @copyright   Copyright (c) 2009-2016 Amasty (http://www.amasty.com)
 */
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE `{$this->getTable('amtable/rate')}` CHANGE `cost_base` `cost_base` DECIMAL(12,2) NOT NULL DEFAULT '0.00';
");