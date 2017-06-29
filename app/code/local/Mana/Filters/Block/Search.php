<?php
/**
 * @category    Mana
 * @package     Mana_Filters
 * @copyright   Copyright (c) http://www.manadev.com
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Block type for showing filters in search pages.
 * @author Mana Team
 * Injected into layout instead of standard catalogsearch/layer in layout XML file.
 */
class Mana_Filters_Block_Search extends Mage_CatalogSearch_Block_Layer {
	protected $_mode = 'search';
	
    /**
     * This method is called during page rendering to generate additional child blocks for this block.
     * @return Mana_Filters_Block_View_Category
     * This method is overridden by copying (method body was pasted from parent class and modified as needed). All
     * changes are marked with comments.
     * @see app/code/core/Mage/Catalog/Block/Layer/Mage_Catalog_Block_Layer_View::_prepareLayout()
     */
    protected function _prepareLayout() {
        Mage::helper('mana_core/layout')->delayPrepareLayout($this);

        return $this;
    }
    public function delayedPrepareLayout()
    {
        $showState = 'all';
        if ($showInFilter = $this->getShowInFilter()) {
            if ($template = Mage::getStoreConfig('mana_filters/positioning/' . $showInFilter)) {
                $this->setTemplate($template);
            }
            $showState = Mage::getStoreConfig('mana_filters/positioning/show_state_' . $showInFilter);
        }
        if ($showState) {
            $stateBlock = $this->getLayout()->createBlock('mana_filters/state')
                    ->setLayer($this->getLayer())
                    ->setMode($showState);
            $this->setChild('layer_state', $stateBlock);
        }

        foreach (Mage::helper('mana_filters')->getFilterOptionsCollection() as $filterOptions) {
            if (Mage::helper('mana_filters')->canShowFilterInBlock($this, $filterOptions)) {
                $displayOptions = $filterOptions->getDisplayOptions();
                $block = $this->getLayout()->createBlock((string)$displayOptions->block, Mage::helper('mana_filters')->getFilterLayoutName($this, $filterOptions), array(
                    'filter_options' => $filterOptions,
                    'display_options' => $displayOptions,
                    'show_in_filter' => $this->getShowInFilter(),
                ))->setLayer($this->getLayer());
                if ($attribute = $filterOptions->getAttribute()) {
                    $block->setAttributeModel($attribute);
                }
                $block->setMode($this->_mode)->init();
                $this->setChild($filterOptions->getCode() . '_filter', $block);
            }
        }

        $this->getLayer()->apply();

        return $this;
    }
    
    public function getFilters() {
        $filters = array();
    	foreach (Mage::helper('mana_filters')->getFilterOptionsCollection() as $filterOptions) {
    		if ($filterOptions->getIsEnabledInSearch()) {
                if (Mage::helper('mana_filters')->canShowFilterInBlock($this, $filterOptions)) {
            	    $filters[] = $this->getChild($filterOptions->getCode() . '_filter');
                }
    		}
        }
        return $filters;
    }
    public function getClearUrl() {
        return Mage::helper('mana_filters')->getClearUrl();
    }

    public function getLayer() {
        if (Mage::helper('catalogsearch')->getEngine() instanceof Enterprise_Search_Model_Resource_Engine) {
            $helper = Mage::helper('enterprise_search');
            if ($helper->isThirdPartSearchEngine() && $helper->isActiveEngine()) {
                return Mage::getSingleton('enterprise_search/search_layer');
            }
        }

        return parent::getLayer();
    }

    protected function _construct() {
        if ($this->hasData('template')) {
            $this->setTemplate($this->getData('template'));
        }
        if (method_exists($this, '_initBlocks')) {
            $this->_initBlocks();
        }
        if (!Mage::registry('current_layer')) {
            Mage::register('current_layer', $this->getLayer());
        }
    }

    public function canShowBlock() {
        if ($this->canShowOptions()) {
            return true;
        }
        elseif ($state = $this->getChild('layer_state')){
            $appliedFilters = $this->getChild('layer_state')->getActiveFilters();
            return !empty($activeFilters);
        }
        else {
            return false;
        }
    }
}