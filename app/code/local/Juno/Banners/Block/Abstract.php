<?php

abstract class Juno_Banners_Block_Abstract extends Mage_Core_Block_Template
{
	public function setDynamicImageCode($imageCodeTemplate, $registryKey)
	{
		if ($model = Mage::registry($registryKey)) {
			$imageCode = sprintf($imageCodeTemplate, $model->getId());
			$this->setImageCode($imageCode);
		}
		
		return $this;
	}


}
