<?php
/**
 * @category    Mana
 * @package     ManaPro_FilterExpandCollapse
 * @copyright   Copyright (c) http://www.manadev.com
 * @license     http://www.manadev.com/license  Proprietary License
 */
/**
 * Generic helper functions for ManaPro_FilterExpandCollapse module. This class is a must for any module even if empty.
 * @author Mana Team
 */
class ManaPro_FilterExpandCollapse_Helper_Data extends Mage_Core_Helper_Abstract {
    public function getCss($filterBlock) {
        if ($options = $filterBlock->getFilterOptions()) {
            /* @var $options Mana_Filters_Model_Filter2_Store */
            switch ($options->getCollapseable()) {
                case 'expand':
                case 'collapse':
                    return ' m-collapseable';
                    break;
                default:
                    return '';
            }
        }
        else {
            return '';
        }
    }
}