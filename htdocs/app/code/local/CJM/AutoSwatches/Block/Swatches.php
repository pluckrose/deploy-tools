<?php

class CJM_AutoSwatches_Block_Swatches extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('autoswatches/swatches.phtml');
    }
}
