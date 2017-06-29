<?php
/**
 * @copyright   Copyright (c) 2009-2014 Amasty (http://www.amasty.com)
 */
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE `{$this->getTable('amtable/rate')}` ADD `num_zip_from` INT UNSIGNED NOT NULL , ADD `num_zip_to` INT UNSIGNED NOT NULL;

UPDATE `{$this->getTable('amtable/rate')}` SET num_zip_from=zip_from, num_zip_to=zip_to;

");
