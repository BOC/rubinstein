<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 * 
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Advancednewsletter
 * @copyright  Copyright (c) 2009-2010 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */
class AW_Advancednewsletter_IndexController extends Mage_Core_Controller_Front_Action
{
    /* Activation confirmation */
	public function confirmAction()
    {
        $id    = (int) $this->getRequest()->getParam('id');
        $code  = (string) $this->getRequest()->getParam('code');

        if ($id && $code) {
            $subscriber = Mage::getModel('newsletter/subscriber')->load($id);
            $session = Mage::getSingleton('core/session');

            if($subscriber->getId() && $subscriber->getCode()) {
                if($subscriber->confirm($code)) {
                    Mage::getModel('advancednewsletter/mailchimp')->subscribe($subscriber->getEmail(), $subscriber->getStoreId());
                    $session->addSuccess($this->__('Your subscription was successfully confirmed'));
                } else {
                    $session->addError($this->__('Invalid subscription confirmation code'));
                }
            } else {
                $session->addError($this->__('Invalid subscription ID'));
            }
        }

        $this->_redirectUrl(Mage::getBaseUrl());
    }

    public function subscribeajaxAction()
    {
        $this->_initLayoutMessages('customer/session');
        $this->getResponse()->setBody(
        $this->getLayout()->createBlock('advancednewsletter/subscribeajax')
               ->addData((array) Mage::getSingleton('customer/session')->getFormData())
               ->toHtml()
           );
    }

    public function subscribeAction() {
        
        if (preg_match('/^1.8/', Mage::getVersion())) {
        	$customerSession = Mage::getSingleton('customer/session');
            if (Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1 && !$customerSession->isLoggedIn()) {
				Mage::getSingleton('catalog/session')->addError($this->__('Sorry, but administrator denied subscription for guests. Please <a href="%s">register</a>.', Mage::getUrl('customer/account/create/')));
				$this->getResponse()->setRedirect($this->_getRefererUrl());
				return;
			}
        }

        if (Mage::getStoreConfig('advancednewsletter/formconfiguration/segmentsstyle')=="none") {
        	$this->default_subscribe();
    	}
        else {
        	$this->segment_subscribe();
        }

        //$this->getResponse()->setRedirect($this->_getRefererUrl());
	}
    
    protected function default_subscribe()
    {
        $session = Mage::getSingleton('customer/session');
        $request = $this->getRequest()->getPost();
        $params = $this->getRequest()->getParams();
        $validator = new Zend_Validate_EmailAddress();
 
        if ($session->isLoggedIn())
        {
            $customer_id = $session->getId();
            $customer = Mage::getModel('customer/customer')->load($customer_id);
            $firstname = $customer->getFirstname();
            $lastname = $customer->getLastname();
            $email = $customer->getEmail();
        }
        else
        {
            if (!$validator->isValid($request['email_an']))
                {
                Mage::getSingleton('catalog/session')->addError(Mage::helper('advancednewsletter')->__('Please, enter valid Email'));
                return;
                }
            if (isset($request['firstname_an'])) $firstname = $request['firstname_an'];
            else $firstname='';
            if (isset($request['lastname_an'])) $lastname = $request['lastname_an'];
            else $lastname='';
            if (isset($request['salutation_an'])) $salutation = $request['salutation_an'];
            else $salutation='';
            if (isset($request['email_an'])) $email = $request['email_an'];
            else $email='';
        }

        if (isset($request['salutation_an'])) $salutation = $request['salutation_an'];
        else $salutation='';

		if (isset($request['phone_an']))
		{
			if ($request['phone_an']=='') $phone = Mage::getModel('advancednewsletter/subscriptions')->getSubscriber($email)->getPhone();
			else $phone = $request['phone_an'];
		}
		else $phone='';

        $subscribe_segments = array();
		/*Default subscription -- all*/
		if (Mage::getStoreConfig('advancednewsletter/formconfiguration/defaultsubscription')=='all')
            $subscribe_segments[] = AW_Advancednewsletter_Helper_Data::AN_SEGMENTS_ALL;

		/*Default subscription -- store default*/
        if (Mage::getStoreConfig('advancednewsletter/formconfiguration/defaultsubscription')=='store_default')
        {
            $storesegments = Mage::getModel('advancednewsletter/segmentsmanagment')->getStoreDefaultSegments(Mage::app()->getStore()->getId());
            if ($storesegments)
            {
				foreach ($storesegments as $segment)
					$subscribe_segments[] = $segment['value'];
			}
            else
            {
                Mage::getSingleton('catalog/session')->addError(Mage::helper('advancednewsletter')->__('Sorry, but there is no subscription for this store news now. Try again later.'));
                return;
            }
        }
        /*Default subscription -- category default*/
		if (Mage::getStoreConfig('advancednewsletter/formconfiguration/defaultsubscription')=='category_default')
        {
            if ($params['category']=='') $params['category']='0';
            $catsegments = Mage::getModel('advancednewsletter/segmentsmanagment')->getCategoryDefaultSegments($params['category']);
			if ($catsegments)
            {
                foreach ($catsegments as $segment)
                    $subscribe_segments[] = $segment['value'];
            }
            else
            {
                Mage::getSingleton('catalog/session')->addError(Mage::helper('advancednewsletter')->__('Sorry, but there is no subscription for this category news now. Try again later.'));
                return;
            }
        }
        
		Mage::getModel('advancednewsletter/subscriptions')->subscribe($email, $firstname, $lastname, $salutation, $phone, $subscribe_segments);

        Mage::getSingleton('catalog/session')->addSuccess(Mage::helper('advancednewsletter')->__('You have been successfully subscribed'));
        return;
    }
    
    protected function segment_subscribe()
    {
        $session = Mage::getSingleton('customer/session');
        $request = $this->getRequest()->getPost();
      
        $validator = new Zend_Validate_EmailAddress(); 
        if (isset($request['segments_select'])) $segment = $request['segments_select'];
        else
        {
            Mage::getSingleton('catalog/session')->addError(Mage::helper('advancednewsletter')->__('Please, choose segment to subscribe'));
            return;
        }
        if ($session->isLoggedIn())
        {
            $customer_id = $session->getId();
            $customer = Mage::getModel('customer/customer')->load($customer_id);
            $firstname = $customer->getFirstname();
            $lastname = $customer->getLastname();
            $email = $customer->getEmail();
        }
        else
        {
            if (!$validator->isValid($request['email_an']))
                {
                Mage::getSingleton('catalog/session')->addError(Mage::helper('advancednewsletter')->__('Please, enter valid Email'));
                return;
                }
            if (isset($request['firstname_an'])) $firstname = $request['firstname_an'];
            else $firstname='';
            if (isset($request['lastname_an'])) $lastname = $request['lastname_an'];
            else $lastname='';
            if (isset($request['email_an'])) $email = $request['email_an'];
            else $email='';
        }

        if (isset($request['salutation_an'])) $salutation = $request['salutation_an'];
        else $salutation='';
        
		if (isset($request['phone_an']))
		{
			if ($request['phone_an']=='') $phone = Mage::getModel('advancednewsletter/subscriptions')->getSubscriber($email)->getPhone();
			else $phone = $request['phone_an'];
		}
		else $phone='';

        Mage::getModel('advancednewsletter/subscriptions')->subscribe($email, $firstname, $lastname, $salutation, $phone, $segment);

        Mage::getSingleton('catalog/session')->addSuccess(Mage::helper('advancednewsletter')->__('You have been successfully subscribed'));
        return;
    }

    public function unsubscribeAction() {
        $params = $this->getRequest()->getParams();
        if (Mage::getModel('advancednewsletter/subscriptions')->unsubscribe($params['email'], $params['code']))
            Mage::getSingleton('catalog/session')->addSuccess(Mage::helper('advancednewsletter')->__('You have been successfully unsubscribed'));
        else
            Mage::getSingleton('catalog/session')->addError(Mage::helper('advancednewsletter')->__('You haven\'t been unsubscribed. Try again later'));
        $this->getResponse()->setRedirect($this->_getRefererUrl());
    }

    public function unsubscribeallAction() {
        $params = $this->getRequest()->getParams();
        if (Mage::getModel('advancednewsletter/subscriptions')->unsubscribeall($params['email']))
            Mage::getSingleton('catalog/session')->addSuccess(Mage::helper('advancednewsletter')->__('You have been successfully unsubscribed'));
        else
            Mage::getSingleton('catalog/session')->addError(Mage::helper('advancednewsletter')->__('You haven\'t been unsubscribed. Try again later'));
        $this->getResponse()->setRedirect($this->_getRefererUrl());
    }
}