<?php
/**
 * @category    Mana
 * @package     ManaPro_FilterTree
 * @copyright   Copyright (c) http://www.manadev.com
 * @license     http://www.manadev.com/license  Proprietary License
 */
/**
 * @author Mana Team
 *
 */
/** @noinspection PhpUndefinedClassInspection */
class ManaPro_FilterTree_Model_Category extends Mana_Filters_Model_Filter_Category {
    protected function _getItemsData() {
        $key = $this->getLayer()->getStateKey() . '_SUBCATEGORIES';
        $data = $this->getLayer()->getAggregator()->getCacheData($key);

        if ($data === null) {
            $data = $this->_getCategoryItemsDataRecursively(
                $this->getLayer()->getCurrentCategory()->getData(),
                $this->getChildrenCollection(
                    $this->getLayer()->getCurrentCategory(),
                    $this->getLayer()->getProductCollection()
                )
            );
            $tags = $this->getLayer()->getStateTags();
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
        return $data;
    }

    public function getChildrenCollection($category, $products) {

        /* @var $resource Mage_Catalog_Model_Resource_Eav_Mysql4_Category */
        $resource = $category->getResource();
        $categoryIds = $resource->getChildren($category);

        $collection = $category->getCollection();

        /* @var $_conn Varien_Db_Adapter_Pdo_Mysql */
        $_conn = $collection->getConnection();

        /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */
        if (Mage::helper('catalog/category_flat')->isEnabled()) {
            $storeId = $category->getStoreId();
            $collection->getSelect()
                ->reset(Zend_Db_Select::COLUMNS)
                ->columns('main_table.*')
                ->joinLeft(
                    array('url_rewrite' => $collection->getTable('core/url_rewrite')),
                        'url_rewrite.category_id=main_table.entity_id AND url_rewrite.is_system=1 AND ' .
                                $_conn->quoteInto(
                                    'url_rewrite.product_id IS NULL AND url_rewrite.store_id=? AND url_rewrite.id_path LIKE "category/%"',
                                    $storeId),
                    array('request_path' => 'url_rewrite.request_path'));
        }
        else {
            $collection
                ->addAttributeToSelect('url_key')
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('all_children')
                ->addAttributeToSelect('is_anchor')
                ->joinUrlRewrite();
        }
        $collection
            ->addAttributeToFilter('is_active', 1)
            ->addIdFilter($categoryIds)
            ->setOrder('position', 'ASC')
            ->load();

        /** @noinspection PhpParamsInspection */
        $this->addCountToCategories($collection, $products, true);

        $result = array();
        foreach ($collection as $category) {
            $result[] = $category->getData();
        }
        ob_start();
        var_dump($result);
        Mage::log(ob_get_clean(), Zend_Log::DEBUG, 'category.log');
        return $result;
    }
    /**
     * @param Mage_Catalog_Model_Category $category
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $products
     * @return array
     */
    protected function _getCategoryItemsDataRecursively($category, $children) {
        $data = array();

        foreach ($children as $childCategory) {
            /* @var $childCategory Mage_Catalog_Model_Category */
            if ($childCategory['is_active'] &&
                $childCategory['level'] == $category['level'] + 1 &&
                strpos($childCategory['path'], $category['path']) === 0 &&
                $childCategory['product_count'])
            {
                $data[] = array(
                    'label' => Mage::helper('core')->htmlEscape($childCategory['name']),
                    'value' => $childCategory['entity_id'],
                    'count' => $childCategory['product_count'],
                    'm_selected' => false, // filled out during apply phase
                    'items' => $this->_getCategoryItemsDataRecursively($childCategory, $children),
                );
            }
        }
        return $data;
    }
    protected function _initItems() {
        $this->_items = $this->_initItemsRecursively($this->_getItemsData());
        if ($this->_categoryId) {
            $this->_markSelectedCategoryRecursively($this->getItems());
        }
        return $this;
    }
    protected function _initItemsRecursively($data) {
        $items = array();
        foreach ($data as $itemData) {
            $items[] = $item = $this->_createItemEx($itemData);
            $item->setItems($this->_initItemsRecursively($itemData['items']));
        }
        /* @var $ext Mana_Filters_Helper_Extended */
        $ext = Mage::helper(strtolower('Mana_Filters/Extended'));
        $items = $ext->processFilterItems($this, $items);

        return $items;
    }
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock) {
        parent::apply($request, $filterBlock);

        return $this;
    }

    protected function _markSelectedCategoryRecursively($items) {
        foreach ($items as $item) {
            if ($item->getValue() == $this->_categoryId)  {
                $item->setMSelected(true);
            }
            $this->_markSelectedCategoryRecursively($item->getItems());
        }
    }
}