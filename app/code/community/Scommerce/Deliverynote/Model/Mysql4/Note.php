<?php
/**
 * Deliverynote Model Mysql4 Note
 *
 * @category	Scommerce
 * @package		Scommerce_Deliverynote
 * @author		Scommerce Mage <core@scommerce-mage.co.uk>
 */

class Scommerce_Deliverynote_Model_Mysql4_Note extends Mage_Core_Model_Mysql4_Abstract
{    
    protected function _construct()
    {
        $this->_init('deliverynote/note', 'delivery_note_id');
    }
}