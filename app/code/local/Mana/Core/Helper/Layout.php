<?php
/**
 * @category    Mana
 * @package     Mana_Core
 * @copyright   Copyright (c) http://www.manadev.com
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * @author Mana Team
 *
 */
class Mana_Core_Helper_Layout extends Mage_Core_Helper_Abstract {
    protected $_delayPrepareLayoutBlocks = array();
    /**
     * @param Mage_Core_Block_Abstract $block
     */
    public function delayPrepareLayout($block) {
        if (Mage::registry('m_page_is_being_rendered')) {
            $block->delayedPrepareLayout();
        }
        else {
            $this->_delayPrepareLayoutBlocks[$block->getNameInLayout()] = $block;
        }
    }
    public function prepareDelayedLayoutBlocks() {
        foreach ($this->_delayPrepareLayoutBlocks as $block) {
            $block->delayedPrepareLayout();
        }
    }
}