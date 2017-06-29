<?php

$customer_group = Mage::getModel('customer/group');
$customer_group->setCode('Facebook New');
$customer_group->setTaxClassId(3);
$customer_group->save();

$customer_group = Mage::getModel('customer/group');
$customer_group->setCode('Facebook Existing');
$customer_group->setTaxClassId(3);
$customer_group->save();


$installer = $this;
$installer->startSetup();

$installer->run("INSERT INTO `{$this->getTable('cms_block')}` (`title`, `identifier`, `content`, `creation_time`, `update_time`, `is_active`) VALUES
('facebook_form', 'facebook_form', '<form action=\"/facebook/frame/save/\" method=\"post\" name=\"regform\" id=\"regform\">\r\n<div id=\"formcontainer\">\r\n<div id=\"graphic\"><img src=\"{{media url=\"wysiwyg/graphic2.jpg\"}}\" alt=\"\" /></div>\r\n<div id=\"form\"><span>First Name</span><br />\r\n<input type=\"text\" name=\"firstname\" value=\"\"  class=\"input-text  required-entry\" /><br /> <span>Last Name</span><br /> <input type=\"text\" name=\"lastname\" value=\"\" class=\"input-text  required-entry\" /><br /> <span>Email</span><br /> <input type=\"text\" name=\"email\" value=\"\" class=\"input-text  required-entry validate-email\" /><br /> <input class=\"submitbutton\" type=\"submit\" value=\"Register\" /></div>\r\n</div>\r\n</form>', '2013-03-04 22:43:03', '2013-03-04 23:09:57', 1),
('facebook_likeus', 'facebook_likeus', '<p><img src=\"{{media url=\"wysiwyg/graphic1.jpg\"}}\" alt=\"\" /></p>', '2013-03-04 22:43:46', '2013-03-04 22:51:44', 1),
('facebook_thankyou', 'facebook_thankyou', '<p><img src=\"{{media url=\"wysiwyg/graphic3.jpg\"}}\" alt=\"\" /></p>', '2013-03-04 22:44:11', '2013-03-04 22:51:19', 1);");
 
$read = Mage::getModel('core/resource')->getConnection('core_read');

$facebook_thankyou = $read->fetchOne($read->select()->from($this->getTable('cms_block'), 'block_id')->where('identifier = ?', 'facebook_thankyou')->limit(1));
$facebook_likeus = $read->fetchOne($read->select()->from($this->getTable('cms_block'), 'block_id')->where('identifier = ?', 'facebook_likeus')->limit(1));
$facebook_form = $read->fetchOne($read->select()->from($this->getTable('cms_block'), 'block_id')->where('identifier = ?', 'facebook_form')->limit(1));

$installer->run("INSERT INTO `{$this->getTable('cms_block_store')}` (`block_id`, `store_id`) VALUES (".$facebook_form.", 0),(".$facebook_likeus.", 0),(".$facebook_thankyou.", 0);");

$installer->endSetup();
