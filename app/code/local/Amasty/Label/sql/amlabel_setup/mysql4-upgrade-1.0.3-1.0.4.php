<?php
/**
* @copyright Amasty.
*/
$this->startSetup();

$this->run("
    ALTER TABLE `{$this->getTable('amlabel/label')}` CHANGE `include_sku` `include_sku` TEXT;
");

$this->endSetup();