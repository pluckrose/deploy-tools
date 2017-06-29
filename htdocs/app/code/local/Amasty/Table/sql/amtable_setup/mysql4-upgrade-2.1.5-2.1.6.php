<?php
/**
 * @copyright   Copyright (c) 2009-2014 Amasty (http://www.amasty.com)
 */
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE `{$this->getTable('amtable/rate')}` CHANGE `time_delivery` `time_delivery` VARCHAR(255) NULL DEFAULT NULL;
");