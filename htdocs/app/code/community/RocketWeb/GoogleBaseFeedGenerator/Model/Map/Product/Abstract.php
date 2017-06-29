<?php

/**
 * RocketWeb
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category  RocketWeb
 * @package   RocketWeb_GoogleBaseFeedGenerator
 * @copyright Copyright (c) 2012 RocketWeb (http://rocketweb.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author    RocketWeb
 */

/**
 * @method Mage_Catalog_Model_Product getProduct() Current product or null
 */
class RocketWeb_GoogleBaseFeedGenerator_Model_Map_Product_Abstract extends Varien_Object
{
    protected $skip = false;
    protected $_assocs;
    protected $_assoc_ids;
    protected $_columns_map = null;
    protected $_cache_shipping = null;
    protected $_cache_map_values = array();
    protected $_assoc_columns_inherit = array('name' => 'title', 'description' => 'description', 'image' => 'image_link', 'url' => 'link');

    /**
     * @return $this
     */
    public function initialize()
    {
        $this->setData('store_currency_code', Mage::app()->getStore($this->getData('store_code'))->getDefaultCurrencyCode());
        $this->setData('images_url_prefix', Mage::app()->getStore($this->getData('store_id'))->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA, false) . 'catalog/product');
        $this->setData('images_path_prefix', Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath());
        $this->setOptionProcessor(Mage::getModel('googlebasefeedgenerator/map_option', array('map' => $this)));

        return $this;
    }

    public function clearInstance()
    {
        unset($this->_assocs);
        unset($this->_assoc_ids);
        unset($this->_columns_map);
    }

    /**
     * @return array
     */
    public function map()
    {
        $this->_beforeMap();
        $rows = $this->_map();
        $ret = $this->_afterMap($rows);
        return $ret;
    }

    /**
     * Implement product options on top of complex product variants.
     *
     * @return $this
     */
    public function _beforeMap()
    {
        $this->_setAssocMaps();
        return $this;
    }

    /**
     * @param $rows
     * @return array
     */
    public function _afterMap($rows)
    {
        reset($rows);
        $this->_checkEmptyColumns(current($rows));
        $this->_cache_map_values = array();
        return $rows;
    }

    /**
     * Product Options are implemented for simple products for now.
     * @return bool
     */
    protected function _isAllowProductOptions()
    {
        if ($this->getConfig()->isAllowProductOptions($this->getStoreId())) {
            $categs = $this->getConfig()->getOptionCategoryIds($this->getStoreId());
            $match = $categs ? array_intersect($categs, $this->getProduct()->getCategoryIds()) : array();

            if (!$categs || ($categs && $match)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Builds the associated maps from assocs array
     *
     * @return $this
     */
    protected function _setAssocMaps()
    {
        $assocMapArr = array();
        if (count($this->_assocs)) {
            foreach ($this->_assocs as $assoc) {
                $assocMap = $this->_getAssocMapModel($assoc);
                if ($assocMap->checkSkipSubmission(true)) {
                    continue;
                }
                $assocMapArr[$assoc->getId()] = $assocMap;
            }
        }
        $this->setAssocMaps($assocMapArr);
        return $this;
    }

    /**
     * @param $rows
     * @return $this
     */
    protected function _checkEmptyColumns($row)
    {
        $skip_column_empty = $this->getConfig()->getMultipleSelectVar('skip_column_empty', $this->getStoreId(), 'filters');

        foreach ($skip_column_empty as $column) {
            if (isset($row[$column]) && $row[$column] == "") {
                $this->setSkip(sprintf("product id %d product sku %s, skipped - by product skip rule, has %s empty.", $this->getProduct()->getId(), $this->getProduct()->getSku(), $column));
                break;
            }
        }

        return $this;
    }

    /**
     * Forms product's data row. [column] => [value]
     * @return array
     */
    protected function _map()
    {
        $rows = array();

        // Map current product
        $fields = array();
        foreach ($this->_columns_map as $column => $arr) {
            $fields[$column] = $this->mapColumn($column);
        }
        $rows[] = $fields;


        // Map product options
        if ($this->_isAllowProductOptions()) {
            $rows = $this->getOptionProcessor()->process($rows);
        }

        return $rows;
    }

    /**
     * Used in all complex products to iterate through all children products
     *
     * @param Mage_Catalog_Model_Product $product
     * @return RocketWeb_GoogleBaseFeedGenerator_Model_Map_Product_Abstract
     */
    protected function _getAssocMapModel($product)
    {
        $mapModel = null;
        $key = strtolower($this->getProduct()->getTypeId());
        $dir = ucfirst($key);

        if ($this->getProduct()->isConfigurable() && $this->getConfig()->isSimplePricingEnabled($this->getProduct())) {
            $key .= '_scp';
            $dir .= DS. 'Scp';
        }
        $file_path = Mage::getConfig()->getModuleDir('model', 'RocketWeb_GoogleBaseFeedGenerator')
                   .DS . 'Model'. DS. 'Map'. DS. 'Product'. DS. $dir. DS. 'Associated.php';

        if (file_exists($file_path)) {
            $mapModel = Mage::getModel('googlebasefeedgenerator/map_product_'. $key. '_associated');
        }

        if (is_null($mapModel)) {
            $mapModel = Mage::getModel('googlebasefeedgenerator/map_product_abstract_associated');
        }

        return $mapModel->addData(array('store_id' => $this->getData('store_id'),
                                        'website_id' => $this->getData('website_id'),
                                        'store_code' => $this->getData('store_code'),
                                        'product' => $product))
            ->setColumnsMap($this->_columns_map)
            ->setEmptyColumnsReplaceMap($this->getEmptyColumnsReplaceMap())
            ->setParentMap($this)
            ->initialize()
            ->setData('store_currency_code', $this->getData('store_currency_code'));
    }

    /**
     * Maps one column from a row
     *
     * @param  string $column
     * @return string
     */
    public function mapColumn($column)
    {
        if (!array_key_exists($column, $this->_columns_map)) {
            return "";
        }

        // Return from cache as there are few columns like price, sale_price and shipping who would share the same info.
        if (array_key_exists($column, $this->_cache_map_values) && !empty($this->_cache_map_values[$column])) {
            return $this->_cache_map_values[$column];
        }

        $arr = $this->_columns_map[$column];
        $args = array('map' => $arr);

        // Run overwrite attributes for apparel
        if ($this->hasParentMap()) {
            $overwriteAttributes = explode(',', $this->getConfigVar('attribute_overwrites', 'configurable_products'));
            if (array_key_exists('attribute', $args['map']) && in_array($args['map']['attribute'], $overwriteAttributes)) {
                return $this->getParentMap()->mapColumn($column);
            }
        }

        /*
           Column methods are required in a few cases.
           e.g. When child needs to get value from parent first. Further if value is empty takes value from it's own mapColumn* method.
           Can loop infinitely if misused.
        */
        $method = 'mapColumn' . $this->_camelize($column);
        if (method_exists($this, $method)) {
            $value = $this->$method($args);
        } else {
            $value = $this->getCellValue($args);
        }

        // Run replace empty rules if no value so far
        if ($value == "") {
            $value = $this->_mapEmptyValues($args);
        }

        $this->_cache_map_values[$column] = $value;
        return $value;
    }

    /**
     * @param $args
     * @return mixed|string
     */
    protected function _mapEmptyValues($args)
    {
        $value = '';
        $column = $args['map']['column'];

        // Avoid infinite loop, and not process if already replaced
        if ( isset($this->_columns_map[$column]) && array_key_exists('empty_replaced', $this->_columns_map[$column])) {
            return $value;
        }

        if (count($this->getEmptyColumnsReplaceMap())) {

            // Go through replacement rules and pick the one matching current column.
            foreach ($this->getEmptyColumnsReplaceMap() as $arr) {
                if ($column == $arr['column']) {

                    $this->_columns_map[$column]['empty_replaced'] = true;

                    if (!empty($arr['static']) && (!$arr['attribute'] || $arr['attribute'] == 'rw_gbase_directive_static_value')) {
                        $value = $arr['static'];
                    } else {
                        // Map it again but this time against the new attribute / directive
                        $method = 'mapColumn' . $this->_camelize($column);
                        if (method_exists($this, $method)) {
                            $value = $this->$method(array('map' => $arr));
                        } else {
                            $value = $this->getCellValue(array('map' => $arr));
                        }
                    }
                }

            }
        }

        return $value;
    }

    /**
     * @param array $params
     * @return string
     */
    public function mapColumnImageLink($params = array())
    {
        return $this->_mapColumnImage($params);
    }

    /**
     * @param array $params
     * @return string
     */
    public function mapColumnAdditionalImageLink($params = array())
    {
        return $this->_mapColumnImage($params);
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function mapColumnTitle($params = array())
    {
        $type = -1;

        // Determine the type
        if ($this->hasParentMap()) {
            switch ($this->getParentMap()->getProduct()->getTypeId()) {
                case Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE:
                    $type = $this->getConfigVar('associated_products_title', 'configurable_products');
                    break;
                case Mage_Catalog_Model_Product_Type::TYPE_GROUPED:
                    $type = $this->getConfigVar('associated_products_title', 'grouped_products');
                    break;
            }
        }

        $value = $this->_mapColumnByProductType($type, $params);
        return $value;
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function mapColumnDescription($params = array())
    {
        $type = -1;

        // Determine the type
        if ($this->hasParentMap()) {
            switch ($this->getParentMap()->getProduct()->getTypeId()) {
                case Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE:
                    $type = $this->getConfigVar('associated_products_description', 'configurable_products');
                    break;
                case Mage_Catalog_Model_Product_Type::TYPE_GROUPED:
                    $type = $this->getConfigVar('associated_products_description', 'grouped_products');
                    break;
            }
        }

        return $this->_mapColumnByProductType($type, $params);
    }

    /**
     * @param array $params
     * @return mixed
     */
    protected function _mapColumnImage($params = array())
    {
        $type = -1;

        // Determine the type
        if ($this->hasParentMap()) {
            switch ($this->getParentMap()->getProduct()->getTypeId()) {
                case Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE:
                    $type = $this->getConfigVar('associated_products_image_link', 'configurable_products');
                    break;
                case Mage_Catalog_Model_Product_Type::TYPE_GROUPED:
                    $type = $this->getConfigVar('associated_products_image_link', 'grouped_products');
                    break;
            }
        }

        return $this->_mapColumnByProductType($type, $params);
    }

    /**
     * Implements the inheritance parent/associate
     *
     * @param  $type
     * @param  array $params
     * @return mixed|string
     */
    protected function _mapColumnByProductType($type, $params = array())
    {
        switch ($type) {
            case RocketWeb_GoogleBaseFeedGenerator_Model_Source_Product_Associated::FROM_PARENT:
                $value = $this->getParentMap() ? $this->getParentMap()->mapColumn($params['map']['column']) : '';
                break;
            case RocketWeb_GoogleBaseFeedGenerator_Model_Source_Product_Associated::FROM_ASSOCIATED:
                $value = $this->getCellValue($params);
                break;
            case RocketWeb_GoogleBaseFeedGenerator_Model_Source_Product_Associated::FROM_ASSOCIATED_PARENT:
                $value = $this->getCellValue($params);
                // Run replace empty rules if no value so far
                if ($value == "") {
                    $value = $this->_mapEmptyValues($params);
                }
                if ($value == '' && $this->getParentMap()) {
                    $value = $this->getParentMap()->mapColumn($params['map']['column']);
                }
                break;
            case RocketWeb_GoogleBaseFeedGenerator_Model_Source_Product_Associated::FROM_PARENT_ASSOCIATED:
                $value = $this->getParentMap() ? $this->getParentMap()->mapColumn($params['map']['column']) : '';
                if ($value == '') {
                    $value = $this->getCellValue($params);
                }
                break;

            default:
                $value = $this->getCellValue($params);
                // Run replace empty rules if no value so far
                if ($value == "") {
                    $value = $this->_mapEmptyValues($params);
                }
                if ($value == '' && $this->getParentMap()) {
                    $value = $this->getParentMap()->mapColumn($params['map']['column']);
                }
        }

        return $value;
    }

    /**
     * Gets value either from directive method or attribute method.
     *
     * @param  array $args
     * @return mixed
     */
    public function getCellValue($args = array())
    {
        $arr = $args['map'];

        if ($this->getConfig()->isDirective($arr['attribute'], $this->getStoreId()) && !isset($args['skip_directive'])) {
            $method = 'mapDirective' . $this->_camelize(str_replace('rw_gbase_directive', '', $arr['attribute']));
            if (method_exists($this, $method)) {
                $value = $this->$method($args);
            } else {
                $value = "";
            }
        } else {
            $method = 'mapAttribute' . $this->_camelize($arr['attribute']);
            if (method_exists($this, $method)) {
                $value = $this->$method($args);
            } else {
                $value = $this->mapAttribute($args);
            }
        }

        return $value;
    }

    /**
     * Process any other attribute.
     *
     * @param  array $params
     * @return string
     */
    protected function mapAttribute($params = array())
    {
        // @var $product Mage_Catalog_Model_Product
        $product = $this->getProduct();

        $map = $params['map'];

        // Get attribute value
        $attribute = $this->getGenerator()->getAttribute($map['attribute']);
        if ($attribute === false) {
            Mage::throwException(sprintf('Couldn\'t find attribute \'%s\'.', $map['attribute']));
        }

        $value = $this->getAttributeValue($product, $attribute);
        return $this->cleanField($value, $params);
    }

    /**
     * Does not do anything other than returns the static value
     * @param array $params
     * @return string
     */
    protected function mapDirectiveStaticValue($params = array())
    {
        $value = isset($params['map']['param']) ? $params['map']['param'] : "";
        return $this->cleanField($value, $params);
    }

    /**
     * @param array $params
     * @return string
     */
    protected function mapDirectiveProductReviewAverage($params = array())
    {
        // @var $product Mage_Catalog_Model_Product
        if ($parent = $this->getParentMap()) {
            $product = $parent->getProduct();
        } else {
            $product = $this->getProduct();
        }

        $avg = 0;
        $summaryData = Mage::getModel('review/review_summary')->setStoreId($this->getData('store_id'))
            ->load($product->getId());
        if (isset($summaryData['rating_summary'])) {
            $avg = $summaryData['rating_summary'] > 0 ? $summaryData['rating_summary'] * 5 / 100 : 0;
        }

        return $this->cleanField($avg, $params);
    }

    /**
     * @param array $params
     * @return string
     */
    protected function mapDirectiveProductReviewCount($params = array())
    {
        // @var $product Mage_Catalog_Model_Product
        if ($parent = $this->getParentMap()) {
            $product = $parent->getProduct();
        } else {
            $product = $this->getProduct();
        }

        $reviewSummary = Mage::getModel('review/review_summary')
            ->setStoreId($this->getData('store_id'))
            ->load($product->getId());

        return $this->cleanField($reviewSummary->getData('reviews_count'), $params);
    }

    /**
     * @param array $params
     * @return string
     */
    protected function mapDirectiveId($params = array())
    {
        $cell = $this->getProduct()->getId();
        if ($params['map']['param']) {
            $cell .= preg_replace('/[^a-zA-Z0-9]/', "", $this->getStoreCode());
        }
        return $this->cleanField($cell, $params);
    }

    /**
     * @param array $params
     * @return string
     */
    protected function mapDirectiveUrl($params = array())
    {
        // @var $product Mage_Catalog_Model_Product
        $product = $this->getProduct();
        $add_to_url = array_key_exists('param', $params['map']) ? $params['map']['param'] : '';

        $url = $product->getProductUrl();
        $pieces = parse_url($product->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK));

        if (strpos($url, $pieces['host']) === false) {
            $url = $pieces['scheme'] . '://' . $pieces['host'] . $url;
        } else {
            $pieces = parse_url($url);
            $url = $pieces['scheme'] . '://' . $pieces['host'] . $pieces['path'];
        }

        $cell = $url . $add_to_url;
        $this->_findAndReplace($cell, $params['map']['column']);
        return $cell;
    }

    /**
     * @param array $params
     * @return string
     */
    protected function mapDirectiveImageLink($params = array())
    {
        // @var $product Mage_Catalog_Model_Product
        $product = $this->getProduct();
        $image_type = (array_key_exists('param', $params['map']) && !empty($params['map']['param'])) ? $params['map']['param'] : 'image';

        $image = $product->getData($image_type);
        if ($image != 'no_selection' && $image != "") {
            $cell = $this->getData('images_url_prefix') . '/' . ltrim($image, '/');
        } else {
            $cell = '';
        }

        $this->_findAndReplace($cell, $params['map']['column']);
        return $cell;
    }

    /**
     * Implement MagicToolbox_Magic360 main image
     * @param array $params
     * @return string
     */
    protected function mapDirectiveImageLink360Magic($params = array()) {

        if (!$this->_getHelper()->isModuleEnabled('MagicToolbox_Magic360')) {
            return $this->mapDirectiveImageLink($params);
        }

        // @var $product Mage_Catalog_Model_Product
        $product = $this->getProduct();
        $images = Mage::getModel('googlebasefeedgenerator/thirdparty_magic360')->getProductImages($product);
        $first = reset($images);
        $cell = $first['medium'];

        $this->_findAndReplace($cell, $params['map']['column']);
        return $cell;
    }

    /**
     * Toybanana_Extimages implementation
     *
     * @param  array $params
     * @return string
     */
    protected function mapDirectiveExternalImageLink($params = array())
    {
        if (!$this->_getHelper()->isModuleEnabled('Toybanana_Extimages')) {
            return $this->mapDirectiveImageLink($params);
        }

        $image = '';
        if (Mage::getStoreConfig('ExtImages/general/enabled', $this->getStoreId()) && $this->getProduct()->getData('use_external_images')) {
            $imageObj = $this->_getHelper('catalog/image')->init($this->getProduct(), 'image');
            $image = $imageObj->getRawUrl();

            if (empty($image)) {
                if (array_key_exists('image_link', $this->_columns_map) && $this->_columns_map['image_link']['attribute'] == 'rw_gbase_directive_external_image_link') {
                    $image = $this->getProduct()->getData('image_external_url');
                }
            }
        }

        $this->_findAndReplace($image, $params['map']['column']);
        return $image;
    }

    /**
     * @param array $params
     * @return string
     */
    protected function mapDirectiveCategoryImageLink($params = array())
    {
        $image = '';
        // @var $product Mage_Catalog_Model_Product
        $product = $this->getProduct();

        foreach ($product->getCategoryIds() as $id) {
            $category = Mage::getModel('catalog/category')->setStoreId($this->getStoreId())->load($id);
            if ($image = $category->getImageUrl()) {
                break;
            }
        }

        $this->_findAndReplace($image, $params['map']['column']);
        return $image;
    }

    /**
     * @param array $params
     * @return string
     */
    protected function mapDirectiveAdditionalImageLink($params = array())
    {
        // @var $product Mage_Catalog_Model_Product
        $product = $this->getProduct();
        $image_type = (array_key_exists('param', $params['map']) && !empty($params['map']['param'])) ? $params['map']['param'] : 'image';

        if (($base_image = $product->getData($image_type)) != "") {
            $base_image = $this->getData('images_url_prefix') . '/' . ltrim($base_image, '/');
        }

        $urls = array();
        $c = 0;
        $media_gal_imgs = $product->getMediaGallery('images');

        if (is_array($media_gal_imgs) || $media_gal_imgs instanceof Varien_Data_Collection) {
            foreach ($media_gal_imgs as $image) {
                if (++$c > 10) {
                    break;
                }
                // Skip disabled images
                if ($image['disabled']) {
                    continue;
                }
                $image['file'] = str_replace(DS, '/', $image['file']);
                $img = $this->getData('images_url_prefix') . '/' . ltrim($image['file'], '/');

                // Skip base image.
                if (strcmp($base_image, $img) == 0) {
                    continue;
                }

                $urls[] = $img;
            }
        }
        $cell = implode(",", $urls);
        $this->_findAndReplace($cell, $params['map']['column']);
        return $cell;
    }

    /**
     * Implement MagicToolbox_Magic360 additional images
     * @param array $params
     * @return string
     */
    protected function mapDirectiveAdditionalImageLink360Magic($params = array())
    {
        if (!$this->_getHelper()->isModuleEnabled('MagicToolbox_Magic360')) {
            return $this->mapDirectiveAdditionalImageLink($params);
        }

        // @var $product Mage_Catalog_Model_Product
        $product = $this->getProduct();
        $images = Mage::getModel('googlebasefeedgenerator/thirdparty_magic360')->getProductImages($product);

        array_shift($images);
        foreach ($images as $image) {
            $cell[] = $image['medium'];
        }
        $cell = implode(',', $cell);
        $this->_findAndReplace($cell, $params['map']['column']);
        return $cell;
    }

    /**
     * Computes prices for given or current product.
     * It returns an array of 4 prices: price and special_price, buth including and excluding tax
     *
     * @return mixed
     */
    public function getPrices()
    {
        if ($this->hasData('price_array')) {
            return $this->getData('price_array');
        }

        /** @var Mage_Weee_Helper_Data $weeeHelper */
        $weeeHelper = $this->_getHelper('weee');
        /** @var RocketWeb_GoogleBaseFeedGenerator_Helper_Tax $taxHelper */
        $taxHelper = $this->_getHelper('googlebasefeedgenerator/tax');
        $helper = $this->_getHelper();
        $store = Mage::app()->getStore($this->getStoreId());
        $algorithm = $taxHelper->getConfig()->getAlgorithm($store);
        $isVersion1702OrLess = version_compare(Mage::getVersion(), '1.7.0.2', '<=');

        /** @var Mage_Catalog_Model_Product $product */
        $product = $this->getProduct();
        if ($helper->isModuleEnabled('Aitoc_Aitcbp')) {
            $product = $product->load($product->getid());
        }

        $qtyIncrements = $helper->getQuantityIcrements($product);

        // Compute Weee tax
        $weeeExcludingTax = $weeeHelper->getAmountForDisplay($product);
        $weeeIncludingTax = $weeeExcludingTax;
        if ($weeeHelper->isTaxable()) {
            $weeeIncludingTax = $weeeHelper->getAmountInclTaxes($weeeHelper->getProductWeeeAttributesForRenderer($product, null, null, null, true));
        }

        $prices = array();
        // Compute equivalent to default/template/catalog/product/price.phtml
        $price = $product->getPrice();
        $convertedPrice = $store->convertPrice($price);
        $prices['p_excl_tax'] = $taxHelper->getPrice($product, $convertedPrice);
        $prices['p_incl_tax'] = $taxHelper->getPrice($product, $convertedPrice, true);

        $catalogRulesPrice = $this->getPriceByCatalogRules();
        $finalPrice = $catalogRulesPrice ? min($catalogRulesPrice, $product->getFinalPrice()) : $product->getFinalPrice();
        $convertedFinalPrice = $store->convertPrice($finalPrice);

        $prices['sp_excl_tax'] = $taxHelper->getPrice($product, $convertedFinalPrice);
        $prices['sp_incl_tax'] = $taxHelper->getPrice($product, $convertedFinalPrice, true);

        if ($algorithm !== Mage_Tax_Model_Calculation::CALC_UNIT_BASE && $qtyIncrements > 1.0) {
            // We need to multiply base before calculating tax for whole ((itemPrice * qty) + vat = total)
            $prices['p_excl_tax'] *= $qtyIncrements;
            $prices['p_incl_tax'] = $taxHelper->getPrice($product, $prices['p_excl_tax'], true);

            $prices['sp_excl_tax'] *= $qtyIncrements;
            $prices['sp_incl_tax'] = $taxHelper->getPrice($product, $prices['sp_excl_tax'], true);
        } else if ($qtyIncrements > 1.0) {
            // We just need to multiply incl_tax/excl_tax prices
            foreach ($prices as $code => $price) {
                $prices[$code] = $price * $qtyIncrements;
            }
        }

        foreach ($prices as $code => $price) {
            if (strpos($code, '_incl_') !== false) {
                $price = $price + $weeeIncludingTax;
            } else {
                $price = $price + $weeeExcludingTax;
            }
            /**
             * Version <= 1.7.0.2 doesn't use roundPrice()
             * so we need to change it
             */
            if (!$isVersion1702OrLess) {
                $price = $store->roundPrice($price);
            }
            $prices[$code] = $price;
        }

        $this->setData('price_array', $prices);
        return $this->getData('price_array');
    }

    /**
     * @param array $params
     * @return string
     */
    protected function mapDirectivePrice($params = array())
    {
        if ($price = $this->getOverwriteAttributeValue($this->getGenerator()->getAttribute('price'))) {
            return $price;
        }

        $prices = $this->getPrices();
        $helper = $this->_getHelper();
        $includingTax = array_key_exists('param', $params['map']) ? (boolean)$params['map']['param'] : true;
        $price = $includingTax ? $prices['p_incl_tax'] : $prices['p_excl_tax'];

        /** @var Mage_Catalog_Model_Product $product */
        $product = $this->getProduct();

        // equivalent to default/template/catalog/product/msrp_price.phtml
        if ($helper->hasMsrp($product)) {
            $qtyIncrements = $helper->getQuantityIcrements($product);
            // This will probably need some fixing on 1.7.0.2 versions
            $price = Mage::app()->getStore($this->getStoreId())->convertPrice($product->getMsrp()*$qtyIncrements);
        }

        return ($price > 0) ? sprintf("%.2F", $price). ' '. $this->getData('store_currency_code') : '';
    }

    /**
     * @param  array $params
     * @return string
     */
    protected function mapDirectiveSalePrice($params = array())
    {
        if (!$this->hasSpecialPrice()) {
            return '';
        }
        $prices = $this->getPrices();
        $includingTax = array_key_exists('param', $params['map']) ? (boolean)$params['map']['param'] : true;
        $price = $includingTax ? $prices['sp_incl_tax'] : $prices['sp_excl_tax'];

        return ($price > 0) ? sprintf("%.2F", $price). ' '. $this->getData('store_currency_code') : '';
    }

    /**
     * @param  array $params
     * @return string
     */
    protected function mapDirectiveSalePriceEffectiveDate($params = array())
    {
        $cell = "";
        $dates = $this->_getSalePriceEffectiveDates();
        if (is_array($dates)) {
            $cell = $this->formatDateInterval($dates);
        }

        if (!empty($cell)) {
            $this->_findAndReplace($cell, $params['map']['column']);
        }

        return $cell;
    }

    /**
     * Get an array of sale price effective dates from catalog rules or product's special price
     *
     * @return false|Zend_Date[]
     */
    protected function _getSalePriceEffectiveDates()
    {
        $product = $this->getProduct();

        if ($this->hasPriceByCatalogRules($product)) {
            return $this->_getCatalogRuleEffectiveDates($product);
        } else if ($this->hasSpecialPrice(false)) {
            return $this->_getSpecialPriceEffectiveDates($product);
        }
    }

    /**
     * @param Zend_Date[] $dates ('start', 'end')
     * @return string
     */
    public function formatDateInterval($dates)
    {
        if (is_array($dates) && array_key_exists('start', $dates) && array_key_exists('end', $dates)) {
            return $dates['start']->toString(Zend_Date::ISO_8601) . "/" . $dates['end']->toString(Zend_Date::ISO_8601);
        } else {
            return '';
        }
    }

    /**
     * Retrieves the start and end date for the product's special price, if they exist.
     *
     * @see self::hasSpecialPrice() - you should check to see if the product is using a special price
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return false|Zend_Date[] 'start','end'
     */
    protected function _getSpecialPriceEffectiveDates($product)
    {
        $special_from_date = $product->getSpecialFromDate();
        $special_to_date = $product->getSpecialToDate();
        if ((empty($special_from_date) && empty($special_to_date))) {
            return false;
        }

        $cDate = Mage::app()->getLocale()->date(null, null, Mage::app()->getLocale()->getDefaultLocale());
        $timezone = Mage::app()->getStore($this->getStoreId())->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);

        // From Date
        if (is_empty_date($special_from_date)) {
            $special_from_date = $cDate->toString('yyyy-MM-dd HH:mm:ss');
        }
        $fromDate = new Zend_Date(null, null, Mage::app()->getLocale()->getDefaultLocale());
        if ($timezone) {
            $fromDate->setTimezone($timezone);
        }
        $fromDate->setDate(substr($special_from_date, 0, 10), 'yyyy-MM-dd');
        $fromDate->setTime(substr($special_from_date, 11, 8), 'HH:mm:ss');

        // To Date
        if (is_empty_date($product->getSpecialToDate())) {
            $special_to_date = $cDate->toString('yyyy-MM-dd HH:mm:ss');
        }
        $toDate = new Zend_Date(null, null, Mage::app()->getLocale()->getDefaultLocale());
        if ($timezone) {
            $toDate->setTimezone($timezone);
        }
        $toDate->setDate(substr($special_to_date, 0, 10), 'yyyy-MM-dd');
        $toDate->setTime('23:59:59', 'HH:mm:ss');
        if (is_empty_date($product->getSpecialToDate())) {
            $toDate->add(365, Zend_Date::DAY);
        }

        return array(
            'start' => $fromDate,
            'end' => $toDate
        );
    }

    /**
     * Retrieves the start and end date for the catalog rule that applies to the product.
     * If there's no rule, or if the rule doesn't have dates, it defaults 365 days
     *
     * @see self::hasPriceByCatalogRules() - you should first check if the product has catalog rules
     *
     * @param  Mage_Catalog_Model_Product $product
     * @param  null|string|Zend_Date $date day to use when looking up prices and start of interval, defaults to today
     * @return false|Zend_Date[] 'start','end'
     */
    protected function _getCatalogRuleEffectiveDates($product, $date = null)
    {
        $read = $this->getTools()->getConnRead();

        if ($date == null) {
            $date = Mage::app()->getLocale()->storeTimeStamp($this->getData('store_id'));
        }
        $date = new Zend_Date($date);

        $wId = Mage::app()->getStore($this->getData('store_id'))->getWebsiteId();

        $select = $read->select()
            ->from(
                Mage::getResourceModel('catalogrule/rule')->getTable('catalogrule/rule_product_price'),
                array('latest_start_date', 'earliest_end_date')
            )
            ->where('rule_date=?', Varien_Date::formatDate($date, false))
            ->where('website_id=?', $wId)
            ->where('product_id=?', $product->getId())
            ->where('rule_price=?', $this->getPriceByCatalogRules())
            ->where('customer_group_id=?', Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
        $rule = $read->fetchRow($select);

        $dates = array();

        if ($rule['latest_start_date']) {
            $dates['start'] = new Zend_Date($rule['latest_start_date'], 'yyyy-MM-dd');
        } else {
            $dates['start'] = clone $date;
            $dates['start']->setTime('00:00:00', 'HH:mm:ss');
        }

        if ($rule['earliest_end_date']) {
            $dates['end'] = new Zend_Date($rule['earliest_end_date'], 'yyyy-MM-dd');
        } else {
            $dates['end'] = new Zend_Date(null, null, Mage::app()->getLocale()->getDefaultLocale());
            $timezone = Mage::app()->getStore($this->getStoreId())->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);
            if ($timezone) {
                $dates['end']->setTimezone($timezone);
            }
            $dates['end']->setDate($date->toString('yyyy-MM-dd'), 'yyyy-MM-dd');
            $dates['end']->setTime('23:59:59', 'HH:mm:ss');
            $dates['end']->add(365, Zend_Date::DAY);
        }

        return $dates;
    }

    /**
     * @param null $product
     * @return bool
     */
    public function hasPriceByCatalogRules()
    {
        $has = false;
        $product = $this->getProduct();

        if ($this->getConfigVar('apply_catalog_price_rules', 'columns')) {
            $rules_price = $this->getPriceByCatalogRules();

            if (round($product->getPrice(), 2) != round($rules_price, 2)) {
                $special_price = $product->getSpecialPrice();
                $has_special_price = $this->hasSpecialPrice(false);

                if ($has_special_price && $special_price > 0 && floatval($special_price) < floatval($rules_price)) {
                    $has = false;
                } else {
                    $has = true;
                }
            }
        }

        return $has;
    }

    /**
     * When computing the special price, we send the $price parameter from associated items
     * @return mixed
     */
    public function getPriceByCatalogRules($price = null)
    {
        if (is_null($price)) {
            $price = $this->getProduct()->getPrice();
        }

        return Mage_Catalog_Model_Product_Type_Price::calculatePrice(
                $price,
                false, false, false, false,
                $this->getWebsiteId(),
                Mage_Customer_Model_Group::NOT_LOGGED_IN_ID,
                $this->getProduct()->getId()
            );
    }

    /**
     * @param bool|true $process_rules
     * @param null $product
     * @return bool
     * @throws Zend_Date_Exception
     */
    public function hasSpecialPrice($process_rules = true, $product = null)
    {
        $has = false;
        if (is_null($product)) {
            $product = $this->getProduct();
        }

        if ($process_rules && $this->hasPriceByCatalogRules()) {
            $has = true;
        }
        elseif ($this->_getHelper()->hasMsrp($product))
        {
            $has = true;
        }
        else
        {
            $special_price = $this->getAttributeValue($product, $this->getGenerator()->getAttribute('special_price'));
            if ($special_price > 0) {
                $cDate = Mage::app()->getLocale()->date(null, null, Mage::app()->getLocale()->getDefaultLocale());
                $timezone = Mage::app()->getStore($this->getStoreId())->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);

                // From Date
                $fromDate = new Zend_Date(null, null, Mage::app()->getLocale()->getDefaultLocale());
                if ($timezone) {
                    $fromDate->setTimezone($timezone);
                }

                $special_from_date = $product->getSpecialFromDate();
                if (is_empty_date($special_from_date)) {
                    $special_from_date = $cDate->toString('yyyy-MM-dd HH:mm:ss');
                }

                $fromDate->setDate(substr($special_from_date, 0, 10), 'yyyy-MM-dd');
                $fromDate->setTime(substr($special_from_date, 11, 8), 'HH:mm:ss');

                // To Date
                $toDate = new Zend_Date(null, null, Mage::app()->getLocale()->getDefaultLocale());
                if ($timezone) {
                    $toDate->setTimezone($timezone);
                }

                $special_to_date = $product->getSpecialToDate();
                if (is_empty_date($special_to_date)) {
                    $special_to_date = $cDate->toString('yyyy-MM-dd HH:mm:ss');
                }
                if (is_empty_date($special_to_date)) {
                    $toDate->add(365, Zend_Date::DAY);
                }

                $toDate->setDate(substr($special_to_date, 0, 10), 'yyyy-MM-dd');
                $toDate->setTime(substr($special_to_date, 11, 8), 'HH:mm:ss');

                if (($fromDate->compare($cDate) == -1 || $fromDate->compare($cDate) == 0) && ($toDate->compare($cDate) == 1 || $toDate->compare($cDate) == 0)) {
                    $has = true;
                }
            }
        }

        return $has;
    }

    /**
     * @param array $params
     * @return string
     */
    protected function mapDirectiveQuantity($params = array())
    {
        return $this->cleanField(sprintf('%d', $this->getInventoryCount()), $params);
    }

    /**
     * @return float|int
     */
    protected function getInventoryCount()
    {
        $v = 0;
        $stockQty = floatval(
            Mage::getModel('cataloginventory/stock_item')
                ->setStoreId($this->getStoreId())
                ->loadByProduct($this->getProduct())
                ->getQty()
        );
        if ($stockQty > 0) {
            $v = $stockQty;
        }
        return $v;
    }

    /**
     * @param array $params
     * @return string
     */
    protected function mapDirectiveAvailability($params = array())
    {
        // @var $product Mage_Catalog_Model_Product
        $product = $this->getProduct();

        if ($this->getConfigVar('use_default_stock', 'columns')) {
            $cell = $this->getConfig()->getOutOfStockStatus();
            $stockItem = Mage::getModel('cataloginventory/stock_item');
            $stockItem->setStoreId($this->getStoreId());
            $stockItem->getResource()->loadByProductId($stockItem, $product->getId());
            $stockItem->setOrigData();

            if ($stockItem->getId() && $stockItem->getIsInStock()) {
                $cell = $this->getConfig()->getInStockStatus();
            }
        } else {
            $stock_attribute = $this->getGenerator()->getAttribute($this->getConfigVar('stock_attribute_code', 'columns'));
            if ($stock_attribute === false) {
                Mage::throwException(sprintf('Invalid attribute for Availability column. Please make sure proper attribute is set under the setting "Alternate Stock/Availability Attribute.". Provided attribute code \'%s\' could not be found.', $this->getConfigVar('stock_attribute_code', 'columns')));
            }

            $stock_status = trim(strtolower($this->getAttributeValue($product, $stock_attribute)));
            if (array_search($stock_status, $this->getConfig()->getAllowedStockStatuses()) === false) {
                $stock_status = $this->getConfig()->getOutOfStockStatus();
            }

            $cell = $stock_status;
        }

        return $this->cleanField($cell, $params);
    }

    /**
     * @param array $params
     * @return mixed
     */
    protected function mapDirectiveExpirationDate($params = array())
    {
        $days = intval($params['map']['param']) - 1;
        $days = $days < 0 ? 0 : $days;
        $date = date('Y-m-d', Mage::getModel('core/date')->timestamp(time()) + 3600 * 24 * $days);
        return $this->cleanField($date, $params);
    }

    /**
     * @param array $params
     * @return string
     */
    protected function mapAttributeDescription($params = array())
    {
        $max_len = (($max_len = $this->getConfigVar('max_description_length', 'columns')) > 10000 ? 10000 : $max_len);
        return $this->getTruncatedAttribute($params, $max_len);
    }

    /**
     * @param array $params
     * @return string
     */
    protected function mapAttributeName($params = array())
    {
        $max_len = $this->getConfigVar('max_title_length', 'columns');
        return $this->getTruncatedAttribute($params, $max_len);
    }

    /**
     * Implements mapAttribute but it truncates the result with max_len
     *
     * @param  $params
     * @param  int $max_len
     * @return string
     */
    protected function getTruncatedAttribute($params, $max_len = 0)
    {
        $map = $params['map'];
        // @var $product Mage_Catalog_Model_Product
        $product = $this->getProduct();
        $attribute = $this->getGenerator()->getAttribute($map['attribute']);

        if ($attribute === false) {
            Mage::throwException(sprintf('Couldn\'t find attribute \'%s\'.', $map['attribute']));
        }

        $value = $this->getAttributeValue($product, $attribute);
        $value = $this->cleanField($value, $params);

        if ($max_len > 0) {
            $ref = '';
            $value = Mage::helper('core/string')->truncate($value, $max_len, '', $ref, false);
        }

        return $value;
    }

    /**
     * @param array $params
     * @return string
     */
    protected function mapDirectiveShippingWeight($params = array())
    {
        $map = $params['map'];
        $map['attribute'] = 'weight';
        $unit = $map['param'];

        // @var $product Mage_Catalog_Model_Product
        $product = $this->getProduct();

        // Get weight attribute
        $weight_attribute = $this->getGenerator()->getAttribute($map['attribute']);
        if ($weight_attribute === false) {
            Mage::throwException(sprintf('Couldn\'t find attribute \'%s\'.', $map['attribute']));
        }

        $weight = number_format((float)$this->getAttributeValue($product, $weight_attribute), 2);
        $weight .= $weight ? ' ' . $unit : '';

        return $this->cleanField($weight, $params);
    }

    /**
     * @param array $params
     * @return string
     */
    public function mapDirectiveGoogleCategoryByCategory($params = array())
    {
        $map_by_category = $this->getConfig()->getMapCategorySorted('google_product_category_by_category', $this->getStoreId());
        $value = $this->matchByCategory($map_by_category, $this->getProduct()->getCategoryIds());

        $this->_findAndReplace($value, $params['map']['column']);
        return html_entity_decode($value);
    }

    /**
     * @param array $params
     * @return string
     */
    public function mapDirectiveProductTypeByCategory($params = array())
    {
        $map_by_category = $this->getConfig()->getMapCategorySorted('product_type_by_category', $this->getStoreId());
        $value = $this->matchByCategory($map_by_category, $this->getProduct()->getCategoryIds());

        $this->_findAndReplace($value, $params['map']['column']);
        return html_entity_decode($value);
    }

    /**
     * @param array $params
     * @return string
     */
    public function mapDirectivePriceBuckets($params = array())
    {
        $values = array();
        $buckets = $this->getConfigVar('adwords_price_buckets', 'columns');

        if ($buckets) {
            $prices = $this->getPrices();
            $price = $this->hasSpecialPrice() ? $prices['sp_excl_tax'] : $prices['p_excl_tax'];
            foreach ($buckets as $bucket) {
                if (floatval($bucket['price_from']) <= floatval($price) && floatval($price) < floatval($bucket['price_to'])) {
                    array_push($values, $bucket['bucket_name']);
                }
            }
        }

        $cell = implode(',', $values);
        $this->_findAndReplace($cell, $params['map']['column']);
        return $cell;
    }

    /**
     * @param array $params
     * @return string
     */
    protected function mapDirectiveShipping($params = array())
    {
        if (!is_null($this->_cache_shipping)) {
            return $this->_cache_shipping;
        }

        if (!$this->getConfigVar('enabled', 'shipping')) {
            $this->_cache_shipping = $cell = "";
            return $cell;
        }

        $allowed_countries = $this->getConfig()->getShippingAllowedCountries($this->getStoreId());
        if (!(is_array($allowed_countries) && count($allowed_countries) > 0)) {
            $this->_cache_shipping = $cell = "";
            return $cell;
        }

        // @var $product Mage_Catalog_Model_Product
        $product = $this->getProduct();

        // if we have parent, let's use shipping from parent
        if ($this->getParentMap()) return $this->getParentMap()->mapDirectiveShipping();

        if ($this->getConfigVar('cache_enabled', 'shipping') && !$this->getGenerator()->getTestMode()) {
            $Cache = Mage::getModel('googlebasefeedgenerator/shipping_cache')
                ->setStoreId($this->getStoreId())
                ->setConfig($this->getConfig());
            if (($data = $Cache->hit($product->getId(), $this->getStoreId())) !== false) {
                $this->_cache_shipping = $cell = $data;
                return $cell;
            }
        }

        // @var $Shipping RocketWeb_GoogleBaseFeedGenerator_Model_Map_Product_Shipping
        $Shipping = Mage::getModel(
            'googlebasefeedgenerator/map_shipping',
            array('store_id' => $this->getStoreId(),
                'website_id' => $this->getWebsiteId(),
                'config' => $this->getConfig(),
                'columns_map' => $this->getGenerator()->getColumnsMap(),
                'map_product' => $this,
            )
        );
        if (count($Shipping->getAllowedCarriers()) <= 0) {
            $this->_cache_shipping = $cell = "";
            return $cell;
        }
        if (is_object($this->getParentMap()) && $this->getParentMap()->getProduct() && $this->getParentMap()->getProduct()->getId() != $product->getId()) {
            $Shipping->setItem($product, $this->getParentMap()->getProduct());
        } else {
            $Shipping->setItem($product);
        }
        $Shipping->collectRates();
        $cell = $Shipping->getFormatedValue();
        $this->_cache_shipping = $cell;

        if ($this->getConfigVar('cache_enabled', 'shipping') && !$this->getGenerator()->getTestMode()) {
            $Cache->miss($product->getId(), $this->getStoreId(), $cell);
        }

        return $this->cleanField($cell, $params);
    }

    /**
     * Maps the product's category tree to the
     * product_type property
     *
     * @param  array $params
     * @return string
     */
    protected function mapDirectiveProductTypeMagentoCategory($params = array())
    {
        $value = $this->hasParentMap() ? $this->getParentMap()->mapDirectiveProductTypeMagentoCategory($params) : '';
        if (!empty($value)) return $value;

        $product = $this->getProduct();
        $params['store_id'] = $this->getStoreId();
        $categoryCollection = $this->getConfig()->getProductCategoriesByStore($product, $params['store_id']);
        $cats = $categoryCollection->getSize();

        if ($this->hasParentMap() && !$cats) {
            return $this->getParentMap()->mapColumn($params['map']['column']);
        } elseif (!$cats) {
            return '';
        }
        $value = Mage::getSingleton('googlebasefeedgenerator/config')->getProductCategoryTree($product, $params);


        $max_values = !empty($params['map']['param']) ? $params['map']['param'] : 3;
        return $this->cleanField(implode(',', array_slice($value, 0, $max_values)), $params);
    }

    /**
     * Returns true for bundle items, and flase for the others.
     *
     * @param array $params
     * @return string
     */
    protected function mapDirectiveIsBundle($params = array())
    {
        return 'FALSE';
    }

    /**
     * This method adds support fr the AheadWorks Shop By Brand extension
     * available here: http://ecommerce.aheadworks.com/magento-extensions/shop-by-brand.html
     * @param  array $params [description]
     * @return string         Returns the Title of the Manufacturer/Brand
     */
    protected function mapAttributeAwShopbybrandBrand($params = array())
    {
        $attribute_id = $this->getProduct()->getData('aw_shopbybrand_brand');
        $aw_model = Mage::getModel('awshopbybrand/brand')->load($attribute_id);
        return $this->cleanField($aw_model->getTitle(), $params);
    }

    /**
     * Cleans field by Google Shopping specs.
     *
     * @param  string $field
     * @return string
     */
    protected function cleanField($field, $params = null)
    {
        // Find and Replace
        if (!is_null($params) && array_key_exists('map', $params) && array_key_exists('column', $params['map'])) {
            $this->_findAndReplace($field, $params['map']['column']);
        }

        if (extension_loaded('mbstring')) {
            mb_convert_encoding($field, mb_detect_encoding($field, mb_detect_order(), true), "UTF-8");
        }

        $field = strtr(
            $field, array(
                "\"" => "&quot;",
                "'" => "&#39;",
                "’" => "&rsquo;",
                "’" => "&#8217;",
                "‘" => "&#8216;",
                "\t" => " ",
                "\n" => " ",
                "\r" => " ",
            )
        );

        $field = strip_tags($field, '>');
        if (extension_loaded('mbstring')) {
            $field = preg_replace_callback("/(&#?[a-z0-9]{2,8};)/i", array($this->_getHelper(), 'htmlEntitiesToUtf8Callback'), $field);
        }
        $field = preg_replace('/\s\s+/', ' ', $field);
        $field = str_replace(PHP_EOL, "", $field);
        $field = trim($field);

        return $field;
    }

    /**
     * Find a replace logic
     *
     * @param $string
     * @param $column
     */
    protected function _findAndReplace(&$string, $column)
    {
        if (!$this->getConfig()->hasData('find_and_replace')) {

            $def = array('find' => array(), 'replace' => array());
            $find_and_replace = array('-all-' => $def);

            $current_img = $this->getConfigVar('find_and_replace', 'filters');
            if (!empty($current_img) && !is_array($current_img)) {
                $current_img = unserialize($current_img);
            }

            if (is_array($current_img) && count($current_img)) {
                foreach ($current_img as $item) {
                    if (empty($item['columns'])) {
                        array_push($find_and_replace['-all-']['find'], $item['find']);
                        array_push($find_and_replace['-all-']['replace'], $item['replace']);
                    } else {
                        if (!array_key_exists($item['columns'], $find_and_replace)) {
                            $find_and_replace[$item['columns']] = $def;
                        }
                        array_push($find_and_replace[$item['columns']]['find'], $item['find']);
                        array_push($find_and_replace[$item['columns']]['replace'], $item['replace']);
                    }
                }
            }
            $this->getConfig()->setData('find_and_replace', $find_and_replace);

        } elseif ($this->getConfig()->hasData('find_and_replace')) {
            $find_and_replace = $this->getConfig()->getData('find_and_replace');
        }

        // Find and replace
        if (array_key_exists((string)$column, $find_and_replace)) {
            $string = str_replace($find_and_replace[$column]['find'], $find_and_replace[$column]['replace'], $string);
        }
        if (count($find_and_replace['-all-']['find'])) {
            $string = str_replace($find_and_replace['-all-']['find'], $find_and_replace['-all-']['replace'], $string);
        }
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return RocketWeb_GoogleBaseFeedGenerator_Model_Map_Product_Abstract
     */
    public function checkSkipSubmission($nodups = false, $type = 'configurable')
    {
        if ((!$nodups && $this->isDuplicate()) || $this->getProduct()->getData('rw_google_base_skip_submi') == 1) {
            $this->setSkip(sprintf(
                "product id %d sku %s, ".$type." associated, skipped - product has 'Skip from Being Submitted' = 'Yes'.",
                $this->getProduct()->getId(), $this->getProduct()->getSku()
            ));
        }

        return $this->isSkip();
    }

    /**
     * Fetch associated products ids of configurable product.
     * Filtered by current store_id (website_id) and status (enabled).
     *
     * @param  Mage_Catalog_Model_Product $product
     * @param  string $store_id
     * @return array | false
     */
    public function loadAssocIds($product, $store_id)
    {
        $as = false;
        $assoc_ids = array();

        if ($product->isConfigurable() || $product->getTypeId() == RocketWeb_GoogleBaseFeedGenerator_Model_Map_Product_Subscription_Configurable::PRODUCT_TYPE_SUBSCTIPTION_CONFIGURABLE) {
            $as = $this->getTools()->getConfigurableChildsIds($product->getId());
        } elseif ($product->isGrouped() || $product->getTypeId() == RocketWeb_GoogleBaseFeedGenerator_Model_Map_Product_Subscription_Grouped::PRODUCT_TYPE_SUBSCTIPTION_GROUPED) {
            $as = $this->getTools()->getGroupedChildsIds($product->getId());
        }

        if ($as === false) {
            return $assoc_ids;
        }

        $as = $this->getTools()->getProductInStoresIds($as);

        foreach ($as as $assocId => $s) {
            $attribute = $this->getGenerator()->getAttribute('status');
            $status = $this->getTools()->getProductAttributeValueBySql($attribute, $attribute->getBackendType(), $assocId, $store_id);

            if ($status != Mage_Catalog_Model_Product_Status::STATUS_ENABLED) {
                continue;
            }
            if (is_array($s) && array_search($store_id, $s) !== false) {
                $assoc_ids[] = $assocId;
            }
        }

        return $assoc_ids;
    }

    /**
     * @param $arr
     * @return $this
     */
    public function setColumnsMap($arr)
    {
        $this->_columns_map = $arr;
        return $this;
    }

    /**
     * @return RocketWeb_GoogleBaseFeedGenerator_Model_Config
     */
    public function getConfig()
    {
        return $this->getGenerator()->getConfig();
    }

    /**
     * @param string $key
     * @param string $section
     * @return mixed
     */
    public function getConfigVar($key, $section = 'file')
    {
        return $this->getGenerator()->getConfigVar($key, $section);
    }

    /**
     * Y-m-d H:i:s to timestamp
     *
     * @param int $date
     */
    public function dateToTime($date)
    {

        return mktime(
            substr($date, 11, 2),
            substr($date, 14, 2),
            substr($date, 17, 2),
            substr($date, 5, 2),
            substr($date, 8, 2),
            substr($date, 0, 4)
        );
    }

    /**
     * @return RocketWeb_GoogleBaseFeedGenerator_Model_Tools
     */
    public function getTools()
    {
        return $this->getGenerator()->getTools();
    }

    /**
     * @param $msg
     * @param null $level
     * @return mixed
     */
    public function log($msg, $level = null)
    {
        return $this->getGenerator()->log($msg, $level);
    }

    /**
     * @param $product Mage_Catalog_Model_Product
     * @param $attribute
     * @return string
     */
    public function getAttributeValue($product, $attribute)
    {
        // Overwrite the attributes of configurable items, but less the ones that makes up the configurable options
        // If we try to overwrite the options attribute, the product will not be detected as apparel anymore.
        $overwriteValue = $this->getOverwriteAttributeValue($attribute);
        if ($overwriteValue) {
            return $overwriteValue;
        }

        if ($attribute->getSourceModel() == 'eav/entity_attribute_source_boolean') {
            $value = $product->getData($attribute->getAttributeCode()) ? 'Yes' : 'No';
        }
        elseif ($attribute->getFrontendInput() == "select" || $attribute->getFrontendInput() == "multiselect") {
            $value = $this->getAttributeSelectValue($product, $attribute, $this->getStoreId());
        } else {
            $value = $product->getData($attribute->getAttributeCode());
        }

        return $value;
    }

    /**
     * Overwrite the attribute with parent's value. if attribute is set to be overwritten
     *
     * @param Varien_Object $attribute
     * @return bool
     */
    public function getOverwriteAttributeValue(Varien_Object $attribute)
    {
        $overwriteAttributes = explode(',', $this->getConfigVar('attribute_overwrites', 'configurable_products'));
        if (is_object($attribute) && in_array($attribute->getData('attribute_code'), $overwriteAttributes)) {

            if ($this->hasParentMap() && $parent = $this->getParentMap()) {

                // eliminate configurable attributes from matching array
                $configurableAttributes = array();
                if ($rows = $this->getTools()->getOptionCodes($parent->getProduct()->getId())) {
                    foreach ($rows as $attr) {
                        if (is_array($attr) && array_key_exists('attribute_code', $attr)) {
                            $configurableAttributes[] = $attr['attribute_code'];
                        }
                    }
                }
                // return the parent value if the attribute is marked a overwrite
                if (!in_array($attribute->getData('attribute_code'), $configurableAttributes)) {
                    return $parent->getAttributeValue($parent->getProduct(), clone $attribute);
                }
            }
        }

        return false;
    }

    /**
     * Gets option text value from product for attributes with frontend_type select.
     * Multiselect values are by default imploded with comma.
     * By default gets option text from admin store (recommended - english values in feed).
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return string
     */
    protected function getAttributeSelectValue($product, $attribute, $store_id = null)
    {
        if (is_null($store_id)) {
            $store_id = Mage_Core_Model_App::ADMIN_STORE_ID;
        }

        // Try to get the value from the custom source model
        if ($attribute->hasData('source_model') && $attribute->getData('source_model')
            && strpos($attribute->getData('source_model'), 'eav/entity_attribute_source') === false
        ) {
            $source_model = Mage::getModel($attribute->getData('source_model'));
            if (!$source_model) {
                $this->log(sprintf('Invalid source model in attribute "%s" > "%s"', $attribute->getData('attribute_code'), $attribute->getData('source_model')));
            } else {
                $options = $source_model->getAllOptions();
                $productAttributeData = $product->getData($attribute->getData('attribute_code'));

                // If attribute has a custom source_model which returns an array
                if (is_array($productAttributeData)) {
                    $attributeOptions = array();
                    foreach ($productAttributeData as $attributeValue) {
                        foreach ($options as $option) {
                            if ($attributeValue == $option['value']) {
                                $attributeOptions[] = $option['label'];
                            }
                        }
                    }
                    return implode(', ', $attributeOptions);
                }

                foreach ($options as $option) {
                    if ($productAttributeData == $option['value']) {
                        return $option['label'];
                    }
                }
            }
        }

        // Get the value from the mage eav/entity_attribute_source
        $attributeValueId = $this->getTools()->getProductAttributeValueBySql($attribute, $attribute->getBackendType(), $product->getId(), $store_id);
        $ret = $this->getTools()->getProductAttributeSelectValue($attribute, $attributeValueId, $store_id);
        return (strcasecmp($ret, "No") == 0 ? '' : $ret);
    }

    /**
     * Singleton by $storeId of generator class
     *
     * @return RocketWeb_GoogleBaseFeedGenerator_Model_Generator
     */
    public function getGenerator()
    {
        $registryKey = '_singleton/googlebasefeedgenerator/generator_store_' . $this->getStoreId();

        if (!Mage::registry($registryKey)) {
            Mage::register($registryKey, Mage::getModel('googlebasefeedgenerator/generator', $this->getData()));
        }

        return Mage::registry($registryKey);
    }

    /**
     * @param $map_by_category
     * @param $category_ids
     * @return string
     */
    public function matchByCategory($map_by_category, $category_ids)
    {
        $value = '';
        if (empty($category_ids)) {
            return $value;
        }

        $category_tree = Mage::getSingleton('googlebasefeedgenerator/config')->getCategoriesTreeIds();

        // order category map
        $category_map = array();
        if (count($map_by_category)) {
            $i=0;
            foreach ($map_by_category as $arr) {
                if (!empty($arr['order'])) {
                    $category_map[intval($arr['order']). ++$i] = $arr;
                } else {
                    $category_map[] = $arr;
                }
            }
        }

        // match logic
        if (count($category_map) > 0) {
            foreach ($category_map as $arr) {
                if (array_search($arr['category'], $category_ids) !== false) {
                    $value = $arr['value'];
                    break;
                }
                // match in parent categories
                foreach ($category_ids as $id) {
                    if (array_key_exists($id, $category_tree) && array_search($arr['category'], $category_tree[$id]) !== false) {
                        $value = $arr['value'];
                        break;
                    }
                }
            }
        }

        return $value;
    }

    /**
     * @param $value
     * @param $product
     * @param $codes
     * @param $typeId
     * @return string
     */
    protected function addUrlUniqueParams($value, $product)
    {
        if (!$this->hasParentMap()) {
            return $value;
        }
        $parent_product = $this->getParentMap()->getProduct();
        switch ($parent_product->getTypeId()) {
            case Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE:
                $codes = $this->getTools()->getOptionCodes($parent_product->getId());
                foreach ($codes as $attribute_id => $attribut_code) {
                    $data = $product->getData($attribut_code);
                    if (empty($data)) {
                        $this->setSkip(sprintf("product id %d product sku %s, can't fetch data from attribute: '%s' ('%s') to make create url.", $this->getProduct()->getId(), $this->getProduct()->getSku(), $attribut_code, $data));
                        return $value;
                    }
                    $params[$attribute_id] = $data;
                }
                break;
            case Mage_Catalog_Model_Product_Type::TYPE_BUNDLE :
                $codes = $this->getTools()->getOptionCodes($parent_product->getId());
                $params = $codes;
                break;
            case Mage_Catalog_Model_Product_Type::TYPE_GROUPED:
                $params = array('prod_id' => $product->getId());
                break;
            default:
                $params = array();
        }

        $urlinfo = parse_url($value);
        if ($urlinfo !== false) {
            if (isset($urlinfo['query'])) {
                $urlinfo['query'] .= '&' . http_build_query($params);
            } else {
                $urlinfo['query'] = http_build_query($params);
            }
            $new = "";
            foreach ($urlinfo as $k => $v) {
                if ($k == 'scheme') {
                    $new .= $v . '://';
                } elseif ($k == 'port') {
                    $new .= ':' . $v;
                } elseif ($k == 'query') {
                    $new .= '?' . $v;
                } elseif ($k == 'fragment') {
                    $new .= '#' . $v;
                } else {
                    $new .= $v;
                }
            }
            if (parse_url($new) === false) {
                $this->setSkip(sprintf("product id %d product sku %s, failed to form new url: %s from old url %s.", $this->getProduct()->getId(), $this->getProduct()->getSku(), $new, $value));
            } else {
                $value = $new;
            }
        }

        return $value;
    }

    public function getChildrenCount() {
        return 0;
    }

    /**
     * @return $this
     */
    public function setSkip($skip_message)
    {
        if ($this->getConfigVar('auto_skip')) {
            $this->skip = true;
            $this->getGenerator()->updateCountSkip($this->getChildrenCount());

            if ($this->getConfigVar('log_skip')) {
                $this->log($skip_message);
            }
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isSkip()
    {
        return $this->skip;
    }

    /**
     * @return $this
     */
    public function unSkip()
    {
        $this->skip = false;
        return $this;
    }

    /**
     * @param $parentRow
     * @param $rows
     */
    protected function mergeVariantValuesToParent(&$parentRow, $rows)
    {
        $inherit_columns = array('size' => array(), 'color' => array(), 'gender' => array(),
            'age_group' => array(), 'material' => array(), 'pattern' => array());

        // When isAllowConfigurableAssociatedMode() is off, need to map associated apparel columns before merge
        if (empty($rows)) {
            $maps = $this->getAssocMaps();
            if (!count($maps) && $this->hasVariants()) {
                foreach ($this->getVariants() as $assoc) {
                    $maps[] = $this->_getAssocMapModel($assoc);
                }
            }
            foreach ($maps as $assocMap) {
                $row = array();
                foreach (array_keys($inherit_columns) as $column) {
                    if (array_key_exists($column, $parentRow)) {
                        $row[$column] = $assocMap->mapColumn($column);
                    }
                }
                array_push($rows, $row);
            }
        }

        foreach ($rows as $row) {
            foreach ($inherit_columns as $column => $v) {
                if (!array_key_exists($column, $row)) {
                    continue;
                }
                if (!in_array($row[$column], $inherit_columns[$column])) {
                    array_push($inherit_columns[$column], $row[$column]);
                }
            }
        }

        foreach ($inherit_columns as $column => $v) {
            if (!array_key_exists($column, $parentRow)) {
                continue;
            }
            if (count($inherit_columns[$column])) {
                $parentRow[$column] = implode(', ', $inherit_columns[$column]);
            }
        }
    }

    /**
     * @param $params
     * @param $attributes_codes
     * @return string
     */
    public function mapDirectiveVariantAttributes($params = array())
    {
        $attributes_codes = $params['map']['param'];

        if (count($attributes_codes) == 0) {
            return '';
        }

        $cell = '';
        $map = $params['map'];
        $product = $this->getProduct();

        // Try to match the proper attribute by looking at what product has loaded
        foreach ($attributes_codes as $attr_code) {
            if (!empty($attr_code) && $product->hasData($attr_code)) {
                $attribute = $this->getGenerator()->getAttribute($attr_code);
                $v = $this->cleanField($this->getAttributeValue($product, $attribute), $params);
                if ($v != "") {
                    $cell .= empty($cell) ? $v : $this->getConfigVar('attribute_merge_value_separator', 'configurable_products') . $v;
                }
            }
        }

        // Try get from parent as it may be a non super-attribute value.
        if ($cell == "" && $this->hasParentMap()) {
            $cell = $this->getParentMap()->mapColumn($map['column']);
        }

        // Multi-select attributes - comma replace
        return str_replace(",", " /", $cell);
    }

    /**
     * Map the product option directive, receives through params the option_id to be mapped.
     * @param array $params
     * @return string
     */
    protected function mapDirectiveProductOption($params = array())
    {
        $values = array();
        $names = is_array($params['map']['param']) ? $params['map']['param'] : array($params['map']['param']);
        $options = $this->getOptionProcessor()->getOptions(array($params['map']['column'] => $names));

        foreach ($options as $option) {
            foreach ($option->getValues() as $val) {
                $values[] = $val->getTitle();
            }
        }


        return implode(',', $values);
    }

    /**
     *
     * @param array $params
     * @return string
     */
    protected function mapDirectiveIdentifierExists($params = array())
    {
        $identifiers = array('brand', 'mpn', 'gtin');
        foreach ($identifiers as $column) {
            if (!array_key_exists($column, $this->_cache_map_values) && array_key_exists($column, $this->_columns_map)) {
                $this->mapColumn($column);
            }
        }

        $score = 0;
        foreach ($identifiers as $column) {
            if (array_key_exists($column, $this->_cache_map_values) && $this->_cache_map_values[$column] != '') {
                $score++;
            }
        }

        return $score > 1 ? "TRUE" : "FALSE";
    }

    /*
     * Aims to group associated products of configurable into products that vary size, color, material or pattern,
     * setting the impression that there are several configurable products instead of just one.
     * For configurable products, returns the parent SKU of the product suffixed by non-variant attribute values.
     * returns empty value for other products.
     *
     * @param array $params
     * @return mixed
     */
    public function mapDirectiveItemGroupId($params = array())
    {
        if (!$this->hasParentMap()) {
            return '';
        }

        $variable_columns = array();
        $sku = $this->getParentMap()->getProduct()->getSku();

        // Find out what which attributes are been used in the map, and which may vary.
        $options = $this->getTools()->getOptionCodes($this->getParentMap()->getProduct()->getId());
        foreach ($this->_columns_map as $column => $map) {

            if (in_array($column, array('color', 'size', 'material', 'pattern'))) {
                if (in_array($map['attribute'], $options)) {
                    $variable_columns[$map['attribute']] = $column;
                }

                if ($map['attribute'] == 'rw_gbase_directive_concatenate') {
                    $expr = $map['param'];
                    preg_match_all('/\{\{(.*?)\}\}/is', $expr, $attributes);
                    if (isset($attributes[1])) {
                        $map['param'] = $attributes[1];
                    }
                }

                if (is_array($map['param'])) {
                    foreach ($map['param'] as $val) {
                        $val = strtolower($val);
                        if (in_array($val, $options)) {
                            $variable_columns[$val] = $column;
                        }
                    }
                } elseif (in_array($map['param'], $options)) {
                    $variable_columns[$map['param']] = $column;
                }
            }
        }

        // Suffix the Parent SKU with non-variable option values
        $suffixes = array();
        $diff = array_diff(array_values($options), array_keys($variable_columns));
        foreach ($diff as $attr_code) {
            $suffixes[] = $this->getAttributeValue($this->getProduct(), $this->getGenerator()->getAttribute($attr_code));
        }

        $skuAndSuffixes = $sku . '-'. implode('-', $suffixes);
        if (count($suffixes) && strlen($skuAndSuffixes) >= 50) {
            if (strlen($sku) >= 9) {
                // We hash the whole string since sha1() returns 40 chars and we hit 50 total
                $skuAndSuffixes = sha1($skuAndSuffixes);
            } else {
                // For at least a bit easier readability we hash only suffixes
                $onlySuffixes = implode('-', $suffixes);
                $skuAndSuffixes = $sku . '-' . sha1($onlySuffixes);
            }
        }

        return count($suffixes) ? $skuAndSuffixes : $sku;
    }

    /**
     * @param array $params
     * @return string
     */
    public function mapDirectiveConcatenate($params = array())
    {
        $orig_params = $params;
        $expr = $params['map']['param'];
        preg_match_all('/\{\{(.*?)\}\}/is', $expr, $attributes);

        if (!isset($attributes[1])) {
            $this->log('Invalid expression in Concatenate directive. Could not find product attributes');
            return $expr;
        }

        // Get value for each identified attribute
        $values = array();
        foreach ($attributes[1] as $k => $attr_code) {

            $column = array_key_exists($attr_code, $this->_assoc_columns_inherit) ? $this->_assoc_columns_inherit[$attr_code] : $attr_code;
            $params['map']['column'] = $column;

            switch ($column) {
                case 'link':
                    $params['map']['attribute'] = 'rw_gbase_directive_url';
                    break;
                case 'image_link':
                    $params['map']['attribute'] = 'rw_gbase_directive_image_link';
                    break;
                default:
                    $params['map']['attribute'] = $attr_code;
            }
            $params['map']['param'] = array_key_exists($column, $this->_columns_map) ? $this->_columns_map[$column]['param'] : '';

            try {
                if ($this->hasParentMap() && in_array($attr_code, array_keys($this->_assoc_columns_inherit))) {

                    // Apply attribute associated product inheritance
                    $type = -1;
                    switch ($this->getParentMap()->getProduct()->getTypeId()) {
                        case Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE:
                            $type = $this->getConfigVar('associated_products_'. $column, 'configurable_products');
                            break;
                        case Mage_Catalog_Model_Product_Type::TYPE_GROUPED:
                            $type = $this->getConfigVar('associated_products_'. $column, 'grouped_products');
                            break;
                    }

                    if ($column == $params['map']['column']) {
                        $params['skip_directive'] = true;
                    }
                    $values[$k] = $this->_mapColumnByProductType($type, $params);

                    if (empty($values[$k])) {
                        $values[$k] = $this->_mapEmptyValues($params);
                    }
                } else {

                    // Regular get the attribute value
                    $values[$k] = $this->getCellValue($params);

                    if (empty($values[$k])) {
                        $values[$k] = $this->_mapEmptyValues($params);
                    }
                    if (empty($values[$k]) && $this->hasParentMap()) {
                        $values[$k] = $this->getParentMap()->getCellValue($params);
                    }
                }

            } catch (Exception $e) {
                $this->log(sprintf('Invalid attribute name in Concatenate directive. Could not find product attribute matching {{%s}}', $attr_code));
                $values[$k] = $attr_code;
            }

        }

        // replace expression placeholders
        $implodedValues = implode('', $values);
        if (!empty($implodedValues)) {
            foreach ($values as $k => $val) {
                $expr = str_replace($attributes[0][$k], $val, $expr);
            }
        } else {
            $expr = '';
        }

        return $this->cleanField($expr, $orig_params);
    }

    /**
     * @return bool
     */
    public function isDuplicate()
    {
        $process = Mage::getModel('googlebasefeedgenerator/process')->load($this->getProduct()->getEntityId(), 'item_id');
        $process->setParentItemId($this->hasParentMap() ? $this->getParentMap()->getProduct()->getEntityId() : $process->getParentItemId());

        if ($process->getId()) {
            if ($process->getStatus() == RocketWeb_GoogleBaseFeedGenerator_Model_Process::STATUS_PROCESSED && intval($process->getParentItemId()) > 0) {
                $this->setSkip(sprintf('Product SKU %s, ID %d is been omitted because it has been already processed as part of product ID %d', $this->getProduct()->getSku(), $this->getProduct()->getEntityId(), $process->getParentItemId()));
                return true;
            } else {
                $process->process();
            }
        } else {
            $process->addData(array('store_id' => $this->getStoreId(), 'item_id' => $this->getProduct()->getEntityId()))
                ->process();
        }

        return false;
    }

    /**
     * @param string $name
     * @return RocketWeb_GoogleBaseFeedGenerator_Helper_Data
     */
    protected function _getHelper($name = 'googlebasefeedgenerator')
    {
        return Mage::helper($name);
    }
}
