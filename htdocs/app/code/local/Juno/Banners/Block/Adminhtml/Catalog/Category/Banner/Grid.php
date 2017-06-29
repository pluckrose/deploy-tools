<?php

class Juno_Banners_Block_Adminhtml_Catalog_Category_Banner_Grid extends Juno_Banners_Block_Adminhtml_Banner_Edit_Tab_Siblings
implements Mage_Adminhtml_Block_Widget_Tab_Interface 
{
	/**
	 * Generates the model collection used by the grid
	 *
	 * @return Juno_Banners_Model_Mysql4_Image_Collection
	 */
	protected function _getModelCollection()
	{
		return Mage::getResourceModel('banners/image_collection')
			->addImageCodeFilter($this->getCategoryBannerCode())
			->setOrderByPosition();
	}	
	
	/**
	 * Generates the image code used for the current category
	 *
	 * @return string
	 */
	public function getCategoryBannerCode()
	{
		return sprintf('category-%d', $this->getCategory()->getId());
	}
	
	/**
	 * Retrieves the current category model
	 *
	 * @return Mage_Catalog_Model_Category
	 */
	public function getCategory()
	{
		return Mage::registry('category');
	}

	/**
	 * Disables the row click callback function (JS)
	 *
	 * @return false
	 */
	public function getRowClickCallback()
	{
		return false;
	}

	/**
	 * Retrieve the label used for the tab relating to this block
	 *
	 * @return string
	 */
    public function getTabLabel()
    {
    	return $this->__('Associated Banner Images');
    }
    
    /**
     * Retrieve the title used by this tab
     *
     * @return string
     */
    public function getTabTitle()
    {
    	return $this->getTabLabel();
    }
    
	/**
	 * Forces the tab to display
	 *
	 * @return true
	 */
    public function canShowTab()
    {
    	return true;
    }
    
    /**
     * Stops the tab being hidden
     *
     * @return bool
     */
    public function isHidden()
    {
    	return false;
    }
}
