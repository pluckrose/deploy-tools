<?php


	$this->startSetup();
	$this->run(
		"ALTER TABLE {$this->getTable('banners/image')} ADD COLUMN `short_description` varchar(255) NOT NULL DEFAULT '' after `title` "
	);
	
	
