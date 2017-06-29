<?php

$installer = $this;
$installer->startSetup();

$installer->run("ALTER TABLE  `{$this->getTable('newsletter_subscriber')}` 
ADD  `source_code` VARCHAR( 120 ) NOT NULL ,
ADD  `first_name` VARCHAR( 80 ) NOT NULL ,
ADD  `last_name` VARCHAR( 80 ) NOT NULL ,
ADD  `additional_data` TEXT NOT NULL,
ADD  `timestamp` DATETIME NOT NULL;");

$installer->endSetup();
