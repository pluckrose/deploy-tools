<?php
/**
 * Deliverynote Block Admin Create Order Note
 *
 * @category	Scommerce
 * @package		Scommerce_Deliverynote
 * @author		Scommerce Mage <core@scommerce-mage.co.uk>
 */

class Scommerce_Deliverynote_Block_Adminhtml_Sales_Order_Create_Note extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    public function getNote()
    {
        return $this->htmlEscape($this->getQuote()->getDeliveryNote());
    }
}