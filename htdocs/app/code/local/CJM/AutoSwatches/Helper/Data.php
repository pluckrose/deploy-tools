<?php

class CJM_AutoSwatches_Helper_Data extends Mage_Core_Helper_Abstract
{	
	public function getSortedByPosition($array)
	{
        $new = '';
        $new1 = '';
        foreach ($array as $k=>$na)
            $new[$k] = serialize($na);
        $uniq = array_unique($new);
        foreach($uniq as $k=>$ser)
            $new1[$k] = unserialize($ser);
        if(isset($new1)){
        	return $new1; }
        else {
        	return '';}
    }
	
	public function getSwatchList()
	{
		$swatch_attributes = Mage::helper('autoswatches')->getSwatchAttributes();
		$html = '';
		
		$count = count($swatch_attributes);
		
		for($i = 0; $i < $count; $i++) {
			
			if($i == $count-1) {
				$html .= $swatch_attributes[$i];
			} else {
				$html .= $swatch_attributes[$i].'&nbsp;&#8226;&nbsp;';
			}
		}
		return $html;
	}
	
	public function getSwatchAttributes()
	{
		$swatch_attributes = array();
		$swatchattributes = Mage::getStoreConfig('auto_swatches/autoswatchesgeneral/colorattributes',Mage::app()->getStore());
		$swatch_attributes = explode(",", $swatchattributes);
		
		 foreach($swatch_attributes as &$attribute) {
         	$attribute = Mage::getModel('eav/entity_attribute')->load($attribute)->getAttributeCode();
		 }
		 unset($attribute);
	
		return $swatch_attributes;
	}
	
	public function getSwatchSize()
	{
		$swatchsize = Mage::getStoreConfig('auto_swatches/autoswatchesgeneral/size');
		if ($swatchsize == ""){
			$swatchsize = 15;}
		return $swatchsize;
	}
	
	public function getSwatchUrl($optionId)
    {
        $uploadDir = Mage::getBaseDir('media') . DIRECTORY_SEPARATOR . 
                                                    'autoswatches' . DIRECTORY_SEPARATOR . 'swatches' . DIRECTORY_SEPARATOR;
        if (file_exists($uploadDir . $optionId . '.jpg'))
        {
            return Mage::getBaseUrl('media') . '/' . 'autoswatches' . '/' . 'swatches' . '/' . $optionId . '.jpg';
        }
        return '';
    }
	
	public function getSwatchHtml($attributeCode, $atid, $_product)
	{
		$storeId = Mage::app()->getStore();
		$frontendlabel = 'null';
		$html = '';
		$cnt = 1;
		$_option_vals = array();				
		$_colors = array();
		$hide = Mage::getStoreConfig('auto_swatches/autoswatchesgeneral/hidedropdown', $storeId);
		$frontText = Mage::getStoreConfig('auto_swatches/autoswatchesgeneral/dropdowntext', $storeId);
		$swatchsize = Mage::helper('autoswatches')->getSwatchSize();
		
		if($hide == 0) {
			$html = $html.'<div class="swatchesContainerPadded"><ul id="ul-attribute'.$atid.'">'; }
		else {
			$html = $html.'<div class="swatchesContainer"><ul id="ul-attribute'.$atid.'">'; }			
                				
        $_collection = Mage::getResourceModel('eav/entity_attribute_option_collection')->setPositionOrder('asc')->setAttributeFilter($atid)->setStoreFilter(0)->load();
		
		foreach( $_collection->toOptionArray() as $_cur_option ) {
			$_option_vals[$_cur_option['value']] = array(
				'internal_label' => $_cur_option['label'],
				'order' => $cnt
			);
			$cnt++;
        }

		$configAttributes = $_product->getTypeInstance(true)->getConfigurableAttributesAsArray($_product);
    	
    	foreach($configAttributes as $attribute) { 
			if($attribute['attribute_code'] == $attributeCode) {
				foreach($attribute["values"] as $value) {
         			array_push($_colors, array(
         				'id' => $value['value_index'],
         				'frontlabel' => $value['store_label'],
         				'adminlabel' => $_option_vals[$value['value_index']]['internal_label'],
         				'order' => $_option_vals[$value['value_index']]['order']
         			));
         		}
         		break;
         	}
     	}
				
		$_color_swatch = Mage::helper('autoswatches')->getSortedByPosition($_colors);
		$_color_swatch = array_values($_color_swatch);
		
		foreach ($_color_swatch as $key => $val) { 
   			 $sortSingle[$key] = $_color_swatch[$key]['order']; } 

		asort ($sortSingle); 
		reset ($sortSingle); 
		
		while (list ($singleKey, $singleVal) = each ($sortSingle)) { 
    		$newArr[] = $_color_swatch[$singleKey]; } 

		$_color_swatch = $newArr;  
		
		foreach($_color_swatch as $_inner_option_id)
 		{
			$theId = $_inner_option_id['id'];
			$adminLabel = $_inner_option_id['adminlabel'];
			$altText = $_inner_option_id['frontlabel'];
			if($frontText == 0) {
				$frontendlabel = $altText; }
			else {
				$frontendlabel = 'null'; }
			
			preg_match_all('/((#?[A-Za-z0-9]+))/', $adminLabel, $matches);
				
			if ( count($matches[0]) > 0 )
			{
				$color_value = $matches[1][count($matches[0])-1];
				$findme = '#';
				$pos = strpos($color_value, $findme);
				
				if (Mage::helper('autoswatches')->getSwatchUrl($theId)):
					$swatchimage = Mage::helper('autoswatches')->getSwatchUrl($theId);
					$html = $html.'<li class="swatchContainer">';
					$html = $html.'<img src="'.$swatchimage.'" id="swatch'.$theId.'" class="swatch" alt="'.$altText.'" width="'.$swatchsize.'px" height="'.$swatchsize.'px" title="'.$altText.'" ';
					$html = $html.'onclick="colorSelected';
					$html = $html."('attribute".$atid."','".$theId."','".$frontendlabel."')";
					$html = $html.'" />';
					$html = $html.'</li>';
				elseif($pos !== false):
              		$html = $html.'<li class="swatchContainer">';
					$html = $html.'<div id="swatch'.$theId.'" title="'.$altText.'" class="swatch" style="background-color:'.$color_value.'; width:'.$swatchsize.'px; height:'.$swatchsize.'px;" ';
					$html = $html.' onclick="colorSelected';
					$html = $html."('attribute".$atid."','".$theId."','".$frontendlabel."')";
					$html = $html.'">';
					$html = $html.'</div></li>';
				else:
					$swatchimage = Mage::helper('autoswatches')->getSwatchUrl('empty');
					$html = $html.'<li class="swatchContainer">';
					$html = $html.'<img src="'.$swatchimage.'" id="swatch'.$theId.'" class="swatch" alt="'.$altText.'" width="'.$swatchsize.'px" height="'.$swatchsize.'px" title="'.$altText.' - Please Upload Swatch!" ';
					$html = $html.'onclick="colorSelected';
					$html = $html."('attribute".$atid."','".$theId."','".$frontendlabel."')";
					$html = $html.'" />';
					$html = $html.'</li>';
				endif;
			}
 		}
		$html = $html.'</ul></div><p class="float-clearer"></p>';
		return $html;	
	}
	
	public function getSwatchImg($option)
    {
        return Mage::helper('autoswatches')->getSwatchUrl($option->getId());
    }
	
	public function getAttribOptions()
    {
   		$optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')->setAttributeFilter(Mage::registry('entity_attribute')->getId())->setPositionOrder('asc', true)->load();
        return $optionCollection;
    }
	
	public function gettheUrl()
    {
       $pageURL = 'http';
 		
 		if(isset($_SERVER["HTTPS"])) {
 			if ($_SERVER["HTTPS"] == "on") {
				$pageURL .= "s";}
		}
 		
 		$pageURL .= "://";
 		
 		if ($_SERVER["SERVER_PORT"] != "80") {
  			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 		} else {
  			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 		}
		
		return $pageURL;
    }

}
