<?php

	$this->startSetup();
	$this->run("

DROP TABLE IF EXISTS {$this->getTable('jbanners_image')};
CREATE TABLE IF NOT EXISTS {$this->getTable('jbanners_image')} (
	`image_id` int(11) unsigned NOT NULL auto_increment,
	`code` varchar(64) NOT NULL default '',
	`image_filename` varchar(255) NOT NULL default '',
	`title` varchar(255) NOT NULL default '',
	`description` TEXT NOT NULL default '',
	`url` varchar(255) NOT NULL default '',
	`sort_order` int(3) unsigned NOT NULL default 1,
	`status` int(1) unsigned NOT NULL default 1,
	`image_map` TEXT NOT NULL default '',
	PRIMARY KEY (image_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");
	
	$this->endSetup();
	
	
	
