<?php


class Juno_Banners_Block_Adminhtml_Banner_Edit_Tab_Siblings extends Juno_Banners_Block_Adminhtml_Banner_Grid
{
	public function __construct()
	{
		parent::__construct();
		
		if ($banner = Mage::registry('jbanners_banner')) {
			$this->setImageCode($banner->getCode())
				->setImageId($banner->getId());
		}
		
		$this->setPagerVisibility(false);
		$this	->setFilterVisibility(false);
	}

	protected function _getModelCollection()
	{
		return Mage::getResourceModel('banners/image_collection')
			->addImageCodeFilter($this->getImageCode())
			->addImageIdExclusionFilter(array($this->getImageId()))
			->setOrderByPosition();
	}

	protected function _prepareColumns()
	{
		parent::_prepareColumns();
		
		$this->addColumn('action', array(
			'header' => '&nbsp;',
			'width' => '50px',
			'type' => 'action',
			'getter' => 'getId',
			'align' => 'center',
			'actions' => array(
				array(
					'caption' => $this->__('Edit'),
					'url' => array('base' => 'banners/adminhtml_banner/edit'),
					'field' => 'id',
					'target' => '_blank',
				)
			),
			'filter' => false,
			'sortable' => false,
			'index' => 'edit_action',
		));

		return $this;
	}
	
	public function getRowUrl($row)
	{
		return '#';
	}
}
