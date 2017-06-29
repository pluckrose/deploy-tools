<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @copyright   Copyright (c) 2014 Juno Media Ltd (http://www.junowebdesign.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */  
class Juno_CustonBaseFeed_Model_GoogleBaseFeedGenerator_Generator extends RocketWeb_GoogleBaseFeedGenerator_Model_Generator {

    /**
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    protected function _getCollection()
    {
        $_collection = parent::_getCollection();
        $_collection->addAttributeToFilter('in_store_only', array('neq' => 1));
        return $_collection;
    }
}
