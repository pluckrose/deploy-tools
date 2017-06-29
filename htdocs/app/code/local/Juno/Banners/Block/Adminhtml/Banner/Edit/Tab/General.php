<?php

class Juno_Banners_Block_Adminhtml_Banner_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
	
		$this->setForm($form);
		
		$fieldset = $form->addFieldset('banner_general', array('legend'=> $this->__('General Information')));

		$fieldset->addField('title', 'text', array(
			'name' => 'title',
			'label' => $this->__('Title'),
			'title' => $this->__('Title'),
			'class' => 'required-entry',
			'required' => true,
		));
		
		$fieldset->addField('code', 'text', array(
			'name' => 'code',
			'label' => $this->__('Code'),
			'title' => $this->__('Code'),
			'class' => 'required-entry',
			'required' => true,
		));

		$fieldset->addField('image_filename', 'hidden', array(
			'name' => 'image_filename',
		));
		
		$fieldset->addField('new_image_filename', 'image', array(
			'label' => $this->__('Banner'),
			'name' => 'new_image_filename',
			'class' => 'required-entry',
			'required' => true,
		));

		$fieldset->addField('url', 'text', array(
			'name' => 'url',
			'label' => $this->__('URL'),
			'title' => $this->__('URL'),
		));
		
		$fieldset->addField('short_description', 'text', array(
			'name' => 'short_description',
			'label' => $this->__('Short Description'),
			'title' => $this->__('Short Description'),
		));

		$fieldset->addField('description', 'editor', array(
			'name' => 'description',
			'label' => $this->__('Description'),
			'title' => $this->__('Description'),
			'style' => 'width:98%; height:200px;',

		));

		$fieldset->addField('status', 'select', array(
			'name' => 'status',
			'title' => $this->__('Enabled'),
			'label' => $this->__('Enabled'),
			'required' => true,
			'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
		));
		
		$fieldset->addField('sort_order', 'text', array(
			'name' => 'sort_order',
			'label' => $this->__('Sort Order'),
			'title' => $this->__('Sort Order'),
		));

		if (Mage::registry('jbanners_banner')) {
			$banner = Mage::registry('jbanners_banner');
				
			if ($banner->getImageFilename()) {
				$banner->setNewImageFilename($this->helper('banners')->getUploadFolder() . DS . $banner->getImageFilename());
			}

			$form->setValues($banner->getData());
		}

		return parent::_prepareForm();
	}
}
