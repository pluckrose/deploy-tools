<?php
/**
 * Deliverynote Note Block
 *
 * @category	Scommerce
 * @package		Scommerce_Deliverynote
 * @author		Scommerce Mage <core@scommerce-mage.co.uk>
 */

class Scommerce_Deliverynote_Block_Note extends Mage_Core_Block_Template
{
	/**
	 * If the character count is enabled in the configuration then return 
	 * the maximum characters allowed from the system configuration.
	 *
	 * @return mixed int|bool
	*/
	public function getCharacterCount()
	{
		if (Mage::helper("deliverynote")->getCharacterCount()>0) {
			return Mage::helper("deliverynote")->getCharacterCount();
		}
		return false;
	}
	
	/**
	 * Precautionary hide delivery note box when there is no shipping method
	 * methods available
	 *
	 * @return bool
	*/
	public function canShow()
	{
		if (count(Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getShippingRatesCollection()) && Mage::helper("deliverynote")->getEnabled()) {
			return true;
		}
		return false;
	}
}