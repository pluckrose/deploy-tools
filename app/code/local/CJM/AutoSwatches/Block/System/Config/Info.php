<?php

class CJM_AutoSwatches_Block_System_Config_Info
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		$html = '<div style="background:url(\'http://chadjmorgan.com/magedev/mage_blank_back.jpg\') scroll #ccc;border:1px solid #CCCCCC;margin-bottom:10px;padding:10px 5px 5px 20px;">
		<ul>
			<li>
				<h4>HOW TO USE AUTO SWATCHES</h4>
				<p style="font-size:10px; color:#666666;">
				&#8226;&nbsp;Select the attribute codes you want to use swatches with below. Ctrl+click to select multiple attributes.<br>&nbsp;&nbsp;Remember, using more than three swatch attributes per product has not been fully tested to work!<br>&#8226;&nbsp;Once the attribute codes are selected, go to Catalog->Attributes->Manage Attributes and select one of your swatch attributes.<br>&nbsp;&nbsp;You will now see a new tab called "Manage Swatches" where you can manage, edit and view your swatches!<br><br>

				</p></li></ul></div>';
        
        return $html;
    }
}
