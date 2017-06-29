<?php
 /**
  *
  *
  **/

class Juno_Facebook_FrameController extends Mage_Core_Controller_Front_Action
{
	/**
	 * Index action, return error.
	 */
	public function indexAction()
	{
		die('Error.');
	}
	
	/**
	 * Show the signup form page.
	 */
	public function formAction()
	{
		$this->loadLayout();
        $this->renderLayout();
	}
	
	/**
	 * Show the likeus page.
	 */
	public function likeusAction()
	{
		$this->loadLayout();
        $this->renderLayout();
	}
	
	/**
	 * Show the likeus page.
	 */
	public function saveAction()
	{
		$args = Mage::app()->getRequest()->getParams();
		if(Mage::getModel('facebook/facebook')->saveData($args)){
			$this->_redirectUrl('https://www.oaktreegardencentre.com/facebook/frame/thankyou/');
			return;
		}
		$this->_redirect('*/*/form', $args);
	}
	
	/**
	 * Show the likeus page.
	 */
	public function thankyouAction()
	{
		$this->loadLayout();
        $this->renderLayout();
	}
	
}
