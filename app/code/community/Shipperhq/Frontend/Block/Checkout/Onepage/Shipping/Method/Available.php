<?php
/**
 *
 * Webshopapps Shipping Module
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
 * Shipper HQ Shipping
 *
 * @category ShipperHQ
 * @package ShipperHQ_Shipping_Carrier
 * @copyright Copyright (c) 2014 Zowta LLC (http://www.ShipperHQ.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author ShipperHQ Team sales@shipperhq.com
 */

/**
 * Provides proxy methods for helper block
 *
 * Supplies helper block with render information
 */
class Shipperhq_Frontend_Block_Checkout_Onepage_Shipping_Method_Available extends Mage_Checkout_Block_Onepage_Shipping_Method_Available
{
    /**
     * Returns a helper block, that is used to retrieve all data
     *
     * @return Shipperhq_Frontend_Block_Checkout_Helper
     */
    public function getHelperBlock()
    {
        if ($this->_helperBlock === null) {
            $this->_helperBlock = $this->getLayout()->createBlock(
                'shipperhq_frontend/checkout_helper',
                sprintf('%s.helper', $this->getNameInLayout())
            );
            
            $this->_helperBlock->init(array(
                'calendar_block' => 'shipperhq_calendar/checkout_onepage_shipping_method_calendar',
                'calendar_template' => 'shipperhq/calendar/checkout/onepage/shipping_method/calendar.phtml',
                'pickup_block' => 'shipperhq_pickup/checkout_onepage_shipping_method_storepickup',
                'pickup_template' => 'shipperhq/pickup/checkout/onepage/shipping_method/storepickup.phtml',
                'accessorials_block' => 'shipperhq_freight/checkout_onepage_shipping_method_accessorials',
                'accessorials_template' => 'shipperhq/freight/checkout/onepage/shipping_method/accessorials.phtml',
                'delivery_comments_block' => 'shipperhq_frontend/checkout_onepage_shipping_method_comments',
                'delivery_comments_template' => 'shipperhq/checkout/onepage/shipping_method/comments.phtml',
                'quote' => $this->getQuote(),
                'address' => $this->getAddress()
            ));
        }


        return $this->_helperBlock;
    }

    public function getCarrierGroupRates()
    {
        return $this->getHelperBlock()->getCarrierGroupRates();
    }

    public function getProcessedShippingRates()
    {
        return $this->getHelperBlock()->getProcessedShippingRates($this->getShippingRates());
    }

    /*
     * Retrieves the items details ready for printing to checkout when < 2 carrier groups are in checkout
     */
    public function getSingleItemDetails($shippingRates)
    {
        return $this->getHelperBlock()->getSingleItemDetails($shippingRates);
    }

    /**
     * Populates the location select dropdown
     * @return string
     */
    public function getShippingMethodsSelect($name, $id, $value, $rates, $sole)
    {
        return $this->getHelperBlock()->getShippingMethodsSelect($name, $id, $value, $rates, $sole);
    }


    public function isShippingMethodSelected($rates)
    {
        return $this->getHelperBlock()->isShippingMethodSelected($rates);
    }

    public function getMethodTitle($methodTitle, $methodDescription, $includeContainer)
    {
        return $this->getHelperBlock()->getMethodTitle($methodTitle, $methodDescription, $includeContainer);
    }

    public function getShippingPrice($price, $flag, $includeContainer = true)
    {
        return $this->getHelperBlock()->getShippingPrice($price, $flag, $includeContainer);
    }

    public function showToolTips() {
        return $this->getHelperBlock()->showToolTips();
    }

    public function getToolTipText()
    {
        return $this->getHelperBlock()->getToolTipText();
    }

    protected function getFormattedItemList($items)
    {
        return $this->getHelperBlock()->getFormattedItemList($items);
    }

    public function getCarrierGroupAddressShippingMethod($carrierGroup)
    {
        return $this->getHelperBlock()->getCarrierGroupAddressShippingMethod($carrierGroup);
    }

    public function showItemDescription()
    {
        return $this->getHelperBlock()->showItemDescription();
    }

    public function getStorepickupHtml($carrierCode, $carrierType, $carriergroup = null )
    {
        return $this->getHelperBlock()->getStorepickupHtml($carrierCode, $carrierType, $carriergroup);
    }

    public function getCalendarHtml($carriergroup, $code, $soleMethod = false)
    {
        return $this->getHelperBlock()->getCalendarHtml($carriergroup, $code, $soleMethod);
    }

    public function isPickupCarrier($rate)
    {
        return $this->getHelperBlock()->isPickupCarrier($rate);
    }

    public function isFreightCarrier($rate)
    {
        return $this->getHelperBlock()->isFreightCarrier($rate);
    }

    public function getFreightAccessorialsHtml($carrierCode, $carrierType, $carrerigroup = '')
    {
        return $this->getHelperBlock()->getFreightAccessorialsHtml($carrerigroup, $carrierCode, $carrierType);
    }

    public function showDeliveryComments()
    {
        return $this->getHelperBlock()->showDeliveryComments();
    }

    public function getDeliveryCommentsHtml()
    {
        return $this->getHelperBlock()->getDeliveryCommentsHtml();
    }
}