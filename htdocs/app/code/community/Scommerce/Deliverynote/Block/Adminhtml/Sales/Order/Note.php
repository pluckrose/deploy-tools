<?php

/**
 * Deliverynote Block to retrieve order delivery instruction
 *
 * @category	Scommerce
 * @package		Scommerce_Deliverynote
 * @author		Scommerce Mage <core@scommerce-mage.co.uk>
 */
class Scommerce_Deliverynote_Block_Adminhtml_Sales_Order_Note extends Mage_Adminhtml_Block_Template
{
    private $_note;

	/**
	 * Based on the object being viewed i.e. order, invoice etc then 
	 * lets get the note from the order if available
	 * 
	 * @return void
	*/
    private function _initNote()
    {
		$noteId = '';
		
        if (! is_null(Mage::registry('current_order'))) {
            $noteId = Mage::registry('current_order')->getData('delivery_note_id');
        }
        elseif(! is_null(Mage::registry('current_shipment'))) {
            $noteId = Mage::registry('current_shipment')->getOrder()->getData('delivery_note_id');  
        }
        elseif(! is_null(Mage::registry('current_invoice'))) {
            $noteId = Mage::registry('current_invoice')->getOrder()->getData('delivery_note_id'); 
        }
		elseif(! is_null(Mage::registry('current_creditmemo'))) {
			$noteId = Mage::registry('current_creditmemo')->getOrder()->getData('delivery_note_id'); 
		}
		
		if ($noteId != '') {
			$this->_note = Mage::getModel('deliverynote/note')->load($noteId)->getNote();
		}
    }

	/**
	 * Initialise the delivery instruction and return
	 *
	 * @return mixed bool|string
	*/
    protected function getNote()
    {
       if (is_null($this->_note)) {
            $this->_initNote();
       }
	   return empty($this->_note) ? false : $this->_note;
    }
}