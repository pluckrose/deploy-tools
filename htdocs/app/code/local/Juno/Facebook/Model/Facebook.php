<?php

class Juno_Facebook_Model_Facebook extends Juno_Facebook_Model_Abstract
{

    const XML_PATH_EMAIL_ADMIN_QUOTE_NOTIFICATION = 'facebook/general/emailtemplate';

	public function saveData($args)
	{
		$customer = Mage::getModel('customer/customer');
		$customer->setWebsiteId(Mage::app()->getWebsite()->getId());
		$customer->loadByEmail($args['email']);

		if(!$customer->getId()) {
			$customer->setEmail($args['email']); 
			$customer->setFirstname($args['firstname']);
			$customer->setLastname($args['lastname']);
			$customer->setPassword($customer->generatePassword(10));
			$customer->setGroupId($this->getGroupId('Facebook New'));
		} else {
			if($customer->getGroupId() != $this->getGroupId('Facebook New')){
				$customer->setGroupId($this->getGroupId('Facebook Existing'));
			}
		}
		try{
			//the save the data and send the new account email.
			$customer->save();
			$customer->setConfirmation(null);
			$customer->save(); 
			//$customer->sendNewAccountEmail();
            $this->sendFangateEmail($args);
		} catch(Exception $ex){
			
		}
		$subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($args['email']);
	    if (!$subscriber->getId()
			|| $subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED
	        || $subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
	
	       	$subscriber->setStatus(Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED);
	        $subscriber->setSubscriberEmail($args['email']);
	       	$subscriber->setSubscriberConfirmCode($subscriber->RandomSequence());
	    }
	    $subscriber->setStoreId(Mage::app()->getStore()->getId());
	    $subscriber->setCustomerId($customer->getId());

	    try {
	        $subscriber->save();
	        return true;
	    }
	    catch (Exception $e) {
	        throw new Exception($e->getMessage());
	    }
		
		return false;
	}

    /**
     * Send a custom transactional email to the customers who signup via the Fangate.
     */
    protected function sendFangateEmail($args, $templateConfigPath = self::XML_PATH_EMAIL_ADMIN_QUOTE_NOTIFICATION)
    {
        if(!$args['email'])
            return;

        $translate = Mage::getSingleton('core/translate');

        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);
        $mailTemplate = Mage::getModel('core/email_template');

        /* @var $mailTemplate Mage_Core_Model_Email_Template */
        $template = Mage::getStoreConfig($templateConfigPath, Mage::app()->getStore()->getId());
        $mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>Mage::app()->getStore()->getId()))
            ->sendTransactional(
                $template,
                Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_IDENTITY, Mage::app()->getStore()->getId()),
                $args['email'],
                $args['name'],
                array('args'=>$args)
            );
        $translate->setTranslateInline(true);
        return $this;
    }

	public function getGroupId($name)
	{
		$read = Mage::getModel('core/resource')->getConnection('core_read');
		return $read->fetchOne($read->select()->from('customer_group', 'customer_group_id')->where('customer_group_code = ?',$name)->limit(1));
	}
	
}
