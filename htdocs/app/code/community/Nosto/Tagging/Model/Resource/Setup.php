<?php
/**
 * Magento
 *
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Nosto
 * @package     Nosto_Tagging
 * @copyright   Copyright (c) 2013 Nosto Solutions Ltd (http://www.nosto.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Nosto Tagging Resource Setup Model.
 *
 * @category    Nosto
 * @package     Nosto_Tagging
 * @author      Nosto Solutions Ltd
 */
class Nosto_Tagging_Model_Resource_Setup extends Mage_Eav_Model_Entity_Setup
{
    /**
     * Get all root categories used by all stores.
     * Note that root categories defined but not used, are not included.
     *
     * @return Mage_Catalog_Model_Category[]
     */
    public function getAllRootCategories()
    {
        $categories = array();

        /** @var $stores Mage_Core_Model_Store[] */
        $stores = Mage::app()->getStores();

        foreach ($stores as $store) {
            $id = $store->getRootCategoryId();
            if (!isset($categories[$id])) {
                $categories[$id] = Mage::getModel('catalog/category')->load($id);
            }
        }

        return $categories;
    }
}
