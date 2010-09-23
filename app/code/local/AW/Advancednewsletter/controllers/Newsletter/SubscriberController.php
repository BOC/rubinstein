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
require_once 'Mage/Adminhtml/controllers/Newsletter/SubscriberController.php';

class AW_Advancednewsletter_Newsletter_SubscriberController extends Mage_Adminhtml_Newsletter_SubscriberController
{
    public function massUnsubscribeAction()
    {
        $subscribersIds = $this->getRequest()->getParam('subscriber');
        if (!is_array($subscribersIds)) {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('newsletter')->__('Please select subscriber(s)'));
        }
        else {
            try {
                foreach ($subscribersIds as $subscriberId) {
                    $subscriber = Mage::getModel('newsletter/subscriber')->load($subscriberId);
					Mage::getModel('advancednewsletter/subscriptions')->unsubscribeall($subscriber->getEmail());
			}
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully updated', count($subscribersIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    public function massSubscribeAction()
    {
        $subscribersIds = $this->getRequest()->getParam('subscriber');
        if (!is_array($subscribersIds)) {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('newsletter')->__('Please select subscriber(s)'));
        }
        else {
            try {
                foreach ($subscribersIds as $subscriberId) {
                    $subscriber = Mage::getModel('newsletter/subscriber')->load($subscriberId);
                    $subscriber->setSubscriberStatus(1)
                               ->save();
                    Mage::getModel('advancednewsletter/mailchimp')->subscribe($subscriber->getEmail(), $subscriber->getStoreId());
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully updated', count($subscribersIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    public function massDeleteAction()
    {
        $subscribersIds = $this->getRequest()->getParam('subscriber');
        if (!is_array($subscribersIds)) {
             Mage::getSingleton('adminhtml/session')->addError(Mage::helper('newsletter')->__('Please select subscriber(s)'));
        }
        else {
            try {
                foreach ($subscribersIds as $subscriberId) {
					$newsletter_subscriber = Mage::getModel('newsletter/subscriber')->load($subscriberId);
					Mage::getModel('advancednewsletter/subscriptions')->deleteSubscription($newsletter_subscriber->getEmail());
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($subscribersIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

	public function deleteAction()
	{
		$subscriber_id = $this->getRequest()->getParam('id');

		if (!$subscriber_id)
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('newsletter')->__('Please select subscriber(s)'));
		else
		{
			try
			{
                $newsletter_subscriber = Mage::getModel('newsletter/subscriber')->load($subscriber_id);
				Mage::getModel('advancednewsletter/subscriptions')->deleteSubscription($newsletter_subscriber->getEmail());

				Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('adminhtml')->__('Subscriber deleted'));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
		}

		$this->_redirect('*/*/index');
	}

    public function newAction() {
        $this->_forward('edit');
	}

    public function editAction()
    {
		$model = Mage::getModel('newsletter/subscriber');
        $subscription = Mage::getModel('advancednewsletter/subscriptions');

        if ($id = $this->getRequest()->getParam('id')) {
            $model->load($id);
            $subscriber = $subscription->getSubscriber($model->getSubscriberEmail());

			$subscriber->setSubscriberEmail($model->getSubscriberEmail());
			$subscriber->setSubscribedInStore($model->getStoreId());

			Mage::register('_current_subscriber', $subscriber);
		}

        $this->loadLayout();
        $this->_setActiveMenu('newsletter/subscriber');

        if ($model->getId()) {
            $breadcrumbTitle = Mage::helper('newsletter')->__('Edit Subscriber');
            $breadcrumbLabel = $breadcrumbTitle;
        }
        else {
            $breadcrumbTitle = Mage::helper('newsletter')->__('New Subscriber');
            $breadcrumbLabel = Mage::helper('newsletter')->__('New Subscriber');
        }

        $this->_addBreadcrumb($breadcrumbLabel, $breadcrumbTitle);

        if ($values = $this->_getSession()->getData('_current_subscriber', true)) {
            $model->addData($values);
        }

        $content = $this->getLayout()
            ->createBlock('advancednewsletter/adminhtml_subscriber_edit', 'subscriber_edit')
            ->setEditMode($model->getId() > 0);
        $this->_addContent($content);
        $this->renderLayout();

    }

    public function saveAction()
    {
		$request = $this->getRequest();
		
		$subscription = Mage::getModel('advancednewsletter/subscriptions');

        try {
            /* Standart newsletter subscription */
			$model = Mage::getModel('newsletter/subscriber');
			
            if ($request->getParam('id'))
			{
				$model->load($request->getParam('id'));

				$old_email = $model->getSubscriberEmail();
				$old_store = $model->getStoreId();
				
				$model
					->setEmail($request->getParam('subscriber_email'))
					->setStoreId($request->getParam('subscribed_in_store'))
					->save();
			}
			else
			{
				$old_email = $request->getParam('subscriber_email');
				$old_store = $request->getParam('subscribed_in_store');
				$model->subscribe($request->getParam('subscriber_email'));
				$model
					->loadByEmail($request->getParam('subscriber_email'))
					->setStoreId($request->getParam('subscribed_in_store'))
					->save();
			}

			/* If subscriber not exists in our table,  adding new, else updating existing */
			$segments = $request->getParam('segments_codes');

			$segments_codes=implode(',',$segments);
			if (!($tmp_sub = $subscription->getSubscriber($old_email)->getData()))
			{
				$subscription
					->setEmail($request->getParam('subscriber_email'))
					->setFirstName($request->getParam('first_name'))
					->setLastName($request->getParam('last_name'))
					->setSalutation($request->getParam('salutation'))
					->setPhone($request->getParam('phone'))
					->setSegmentsCodes($segments_codes)
					->save();
				if (Mage::getModel('newsletter/subscriber')->loadByEmail($request->getParam('subscriber_email'))->getStatus()!=2)
                    Mage::getModel('advancednewsletter/mailchimp')->subscribe($request->getParam('subscriber_email'), $request->getParam('subscribed_in_store'));
			}
			else
			{
				Mage::getModel('advancednewsletter/mailchimp')->delete($old_email, $old_store);
				$subscription
							->setData($request->getParams())
							->setEmail($request->getParam('subscriber_email'))
							->setSegmentsCodes($segments_codes)
							->setId($tmp_sub['id']);
				$subscription->save();
				if ($segments_codes)
				{
					Mage::getModel('newsletter/subscriber')->subscribe($request->getParam('subscriber_email'));
					Mage::getModel('newsletter/subscriber')->loadByEmail($request->getParam('subscriber_email'))
							->setStoreId($request->getParam('subscribed_in_store'))
							->save();
				}
				if (Mage::getModel('newsletter/subscriber')->loadByEmail($request->getParam('subscriber_email'))->getStatus()!=2)
                    Mage::getModel('advancednewsletter/mailchimp')->subscribe($request->getParam('subscriber_email'), $request->getParam('subscribed_in_store'));
			}

			$this->_getSession()->addSuccess(Mage::helper('adminhtml')->__('Successfully saved'));

            if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}

            $this->_redirect('*/*');
        }
        catch (Exception $e)
        {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_forward('new');
    }

    public function syncAction()
    {
       if (Mage::getModel('advancednewsletter/mailchimp')->unsubscribersReverseSync()&&Mage::getModel('advancednewsletter/mailchimp')->synchronizeAll())
       Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('advancednewsletter')->__('Successfully synchronized'));
       else
       Mage::getSingleton('adminhtml/session')->addError(Mage::helper('advancednewsletter')->__('Synchronization failed. Make sure that you configure Mailchimp correctly'));
       $this->getResponse()->setRedirect($this->_getRefererUrl());
    }
}
