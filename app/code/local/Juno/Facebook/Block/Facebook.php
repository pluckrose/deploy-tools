<?php

class Juno_Facebook_Block_Facebook extends Mage_Core_Block_Template
{

	public function getLikeUs()
	{
		//return $this->getLayout()->createBlock('cms/block')->setBlockId('facebook_likeus')->toHtml();
		$html = $this->getLayout()->createBlock('cms/block')->setBlockId('facebook_likeus')->toHtml();
		$html .= "<style>".Mage::getStoreConfig('facebook/general/css')."</style>";
		return $html;
	}
	
	public function getThankyou()
	{
		//return $this->getLayout()->createBlock('cms/block')->setBlockId('facebook_thankyou')->toHtml();
		$html = $this->getLayout()->createBlock('cms/block')->setBlockId('facebook_thankyou')->toHtml();
		$html .= "<style>".Mage::getStoreConfig('facebook/general/css')."</style>";
		return $html;
	}
	
	public function getSignupForm()
	{
		$html = $this->getLayout()->createBlock('cms/block')->setBlockId('facebook_form')->toHtml();
		$html .= "<style>".Mage::getStoreConfig('facebook/general/css')."</style>";
		return $html;
	}
	
	public function getShareButton()
	{
		$_settings = Mage::getStoreConfig('facebook/general');

		if($_settings['sharebutton'] == 0){
			return;
		}

		$title 	 = urlencode($_settings['sharetitle']);
		$url 	 = urlencode($_settings['shareurl']);
		$summary = urlencode($_settings['sharedescription']);
		$image   = urlencode($_settings['shareimage']);

		$html = '<input type="submit" value="Share" class="sharebutton" onClick="window.open(\'http://www.facebook.com/sharer.php?s=100&amp;p[title]='.$title.'&amp;p[summary]='.$summary.'&amp;p[url]='.$url.'&amp;&amp;p[images][0]='.$image.'\',\'sharer\',\'toolbar=0,status=0,width=548,height=325\'); return false;" />';
		
		return $html;
	}
	
}
