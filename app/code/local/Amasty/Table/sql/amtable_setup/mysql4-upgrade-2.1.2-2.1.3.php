<?php
/**
 * @copyright   Copyright (c) 2009-2014 Amasty (http://www.amasty.com)
 */
$installer = $this;

$installer->startSetup();

$installer->run("
  ALTER TABLE  `{$this->getTable('amtable/rate')}`  ADD  `time_delivery` INT( 10 ) NOT NULL DEFAULT '0' AFTER  `qty_to`;
");