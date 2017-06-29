<?php

class Juno_Banners_Model_Mysql4_Image_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct()
	{
		$this->_init('banners/image');
	}
	
	public function addImageCodeFilter($imageCode)
	{
		return $this->addFieldToFilter('code' , $imageCode);
	}
	
	public function addImageIdExclusionFilter(array $imageIds)
	{
		return $this->addFieldToFilter('image_id', array('nin' => $imageIds));
	}
	
	public function setOrderByPosition()
	{
		return $this->addAttributeToSort('sort_order', 'ASC');
	}
	
	public function setOrderByCode()
	{
		return $this->addAttributeToSort('code', 'ASC');
	}
	
	public function addAttributeToSort($field, $dir = 'asc')
	{
		$this->getSelect()->order($field . ' ' . $dir);
		return $this;
	}
}
