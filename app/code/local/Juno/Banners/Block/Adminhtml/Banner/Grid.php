<?php

class Juno_Banners_Block_Adminhtml_Banner_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('bannerGrid');
		$this->setDefaultSort('store_name');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(false);
	}

	protected function _prepareCollection()
	{
		$this->setCollection($this->_getModelCollection());
		return parent::_prepareCollection();
	}

	protected function _getModelCollection()
	{
		return Mage::getResourceModel('banners/image_collection')
			->setOrderByCode()
			->setOrderByPosition();
	}

	
	protected function _prepareColumns()
	{
		$this->addColumn('image_id', array(
			'header'    => $this->__('ID'),
			'align'     =>'right',
			'width'     => '50px',
			'index'     => 'image_id',
		));

		$this->addColumn('image_filename', array(
			'header'    => $this->__('Image'),
			'align'     =>'right',
			'width'     => '50px',
			'type' 		=> 'image',
			'index'     => 'image_filename',
		));
		
		$this->_columns['image_filename']->setFrameCallback(array($this, 'convertToHtml'));
		
		$this->addColumn('code', array(
			'header'    => $this->__('Code'),
			'index'     => 'code',
		));
		
		$this->addColumn('title', array(
			'header'    => $this->__('Title'),
			'index'     => 'title',
		));
		
		return parent::_prepareColumns();
	}
	
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

	/**
	 * Convert the image src to HTML
	 * This function is called automatically via a callback
	 *
	 *
	 */
	public function convertToHtml($renderedValue)
	{
		$url = Mage::helper('banners')->getUploadUrl() . $renderedValue;
		
		return sprintf('<a href="%s" target="_blank"><img src="%s" alt="" width="90" /></a>', $url, $url);
	}
}
