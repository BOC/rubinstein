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
class AW_Advancednewsletter_Model_Subscriptions extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
    parent::_construct();
        $this->_init('advancednewsletter/subscriptions');
    }

    public function hasSubscriptions($email)
    {
        $row = $this->getSubscriber($email)->getData();

        if (!isset($row['segments_codes'])) return false;
        return true;
    }

    public function getSubscriber($email)
    {
        $collection = $this->getCollection();
        $collection->getSelect()
            ->where('email=?', $email);
        $row = $collection->getFirstItem();
		if (!$row) return $this;
		return $row;
    }

	public function subscribeCustomer($customer, $segments_array)
	{
		$anSubscriber = $this->getSubscriber($customer->getEmail());

		$this->subscribe($customer->getEmail(), $customer->getFirstname(), $customer->getLastname(), $anSubscriber->getSalutation(), $anSubscriber->getPhone(), $segments_array);
		
		Mage::getModel('newsletter/subscriber')
						->loadByEmail($customer->getEmail())
						->setStoreId($customer->getStoreId())
						->save();
	}

	public function subscribe($email, $firstname, $lastname, $salutation, $phone, $segments_array)
    {
		if (!is_array($segments_array)) $segments_array = array($segments_array);

		$row = $this->getSubscriber($email)->getData();

        /* Check if subscriber exist */
		if (empty($row['id']))
        {
			$ready_segments = implode(',', $segments_array);
		}
		else
		{
			$ready_segments_array = array();
			if (!empty($row['segments_codes'])) $ready_segments_array = explode(',', $row['segments_codes']);

			/* Empty $ready_segments_array if we have new segments and subscriber was subscribed to all */
			if (in_array(AW_Advancednewsletter_Helper_Data::AN_SEGMENTS_ALL, $ready_segments_array)&&count($segments_array))
				$ready_segments_array = array();

			foreach ($segments_array as $segment)
				$ready_segments_array[] = $segment;

			$ready_segments_array = array_unique($ready_segments_array);

			if (in_array(AW_Advancednewsletter_Helper_Data::AN_SEGMENTS_ALL, $ready_segments_array))
				$ready_segments_array = array(AW_Advancednewsletter_Helper_Data::AN_SEGMENTS_ALL);

			$ready_segments = implode(',', $ready_segments_array);
            $this->load($row['id']);
        }

		$this
            ->setEmail($email)
            ->setFirstName($firstname)
            ->setLastName($lastname)
			->setPhone($phone)
            ->setSegmentsCodes($ready_segments)
            ->save();

		/* Standart newsletter subscription checking */
		$newsletter_subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
		if (!$newsletter_subscriber->getId() || $newsletter_subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED) $newsletter_subscriber->subscribe($email);
		/* Subscriber is activated (status != 2) -- mailchimp sync */
		if ($newsletter_subscriber->getStatus()!=2)
		{
			Mage::getModel('advancednewsletter/mailchimp')->subscribe($email, $newsletter_subscriber->getStoreId());
		}
    }

    public function unsubscribe($email, $segment_code, $sync_needed = true)
    {
        if (!($row = $this->getSubscriber($email)->getData())) return false;
        if (empty($row['id'])) return false;

		if ($segment_code==AW_Advancednewsletter_Helper_Data::AN_SEGMENTS_ALL)
		{
			$this->unsubscribeall($email);
			return true;
		}

        $segments = explode(',', $row['segments_codes']);
        foreach($segments as $key => $segment)
        {
            if ($segment==$segment_code) unset($segments[$key]);
        }
        $segments = implode(',', $segments);
        $this
             ->load($row['id'])
             ->setSegmentsCodes($segments)
             ->save();

		$newsletter_subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
		if (!$segments)
        {
			$newsletter_subscriber->unsubscribe();
			if ($sync_needed)
				Mage::getModel('advancednewsletter/mailchimp')->unsubscribe($email, $newsletter_subscriber->getStoreId());
		}
		else
        {
			if ($sync_needed)
				Mage::getModel('advancednewsletter/mailchimp')->subscribe($email, $newsletter_subscriber->getStoreId());
		}

        return true;
    }

    public function unsubscribeall($email)
    {
        if (!($row = $this->getSubscriber($email)->getData())) return false;

        if (empty($row['id'])) return false;

        $this
            ->load($row['id'])
            ->setSegmentsCodes('')
            ->save();

		$newsletter_subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
		$newsletter_subscriber->unsubscribe();
		Mage::getModel('advancednewsletter/mailchimp')->unsubscribe($newsletter_subscriber->getEmail(), $newsletter_subscriber->getStoreId());

        return true;
    }

	/* function update segments for subscriber and then make sync with Mailchimp */
	public function  updateSegments($email, $segments_cut, $segments_paste)
	{
		/* Geting subscriber */
		$row = $this->getSubscriber($email)->getData();

		/* segments for cut and for paste from rule */
		$segments_cut = explode(';', $segments_cut);
		$segments_paste = explode(';', $segments_paste);

		if (empty($row['segments_codes'])) $segments = array();
		else $segments = explode(',', $row['segments_codes']);

		/* Cutting */
		foreach($segments as $key => $segment)
        {
            foreach($segments_cut as $segment_cut)
			{
				if ($segment_cut == AW_Advancednewsletter_Helper_Data::RULES_NO_CHANGE) break;
				if ($segment_cut == AW_Advancednewsletter_Helper_Data::AN_SEGMENTS_ALL) unset($segments[$key]);
				if ($segment==$segment_cut) unset($segments[$key]);
			}
        }

        /* Pasting */
		foreach($segments as $key => $segment)
        {
            foreach($segments_paste as $segment_paste)
				if ($segment==$segment_paste) unset($segments[$key]);
		}
		
		foreach($segments_paste as $segment_paste)
		{
			if ($segment_paste == AW_Advancednewsletter_Helper_Data::RULES_NO_CHANGE) break;
			$segments[]=$segment_paste;
		}

        if (count($segments)>1)
        {
            foreach ($segments as $key => $segment)
                if ($segment == AW_Advancednewsletter_Helper_Data::AN_SEGMENTS_ALL)
                    unset($segments[$key]);
        }

		$segments = implode(',', $segments);

		/* Checking for new subscriber or existing */
		if (!empty($row['id']))
		{
			if (!Mage::getModel('newsletter/subscriber')->loadByEmail($row['email'])->isSubscribed())
			{
				Mage::getModel('newsletter/subscriber')->loadByEmail($row['email'])->setStatus(1)->save();
			}
			$this->load($row['id']);
		}
		else
			Mage::getModel('newsletter/subscriber')->subscribe($email);

		/* Getting last and first names */
		$firstname = ''; $lastname = '';
		try
		{
			$collection = Mage::getResourceModel('newsletter/subscriber_collection');
			$collection
				->showCustomerInfo(true)
				->addFieldToFilter('subscriber_email', $email);

			$customer = $collection->getFirstItem();

			if ($customer->getCustomerFirstname()) $firstname = $customer->getCustomerFirstname();
			if ($customer->getCustomerLastname()) $lastname = $customer->getCustomerLastname();
		}
		catch (Exception $ex)
		{}
		/* Writing subscribers data */
		$this
            ->setEmail($email)
            ->setFirstName($firstname)
            ->setLastName($lastname)
            ->setSegmentsCodes($segments)
            ->save();

		/* Mailchimp sync */
		$subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
		if (!$segments)
        {
            $subscriber->unsubscribe();
            Mage::getModel('advancednewsletter/mailchimp')->unsubscribe($email, $subscriber->getStoreId());
        }
        else
        {
            if ($subscriber->getStatus()!=2)
                Mage::getModel('advancednewsletter/mailchimp')->subscribe($email, $subscriber->getStoreId());
        }

		return;
	}

    public function getCustomerSegments($customer_email)
    {
        if (!($row = $this->getSubscriber($customer_email)->getData())) return array();

        $collection_segments = Mage::getModel('advancednewsletter/segmentsmanagment')->getCollection();
        $collection_segments->getSelect()
             ->where('code in (?)', explode(',', $row['segments_codes']));
        $rows_segment = $collection_segments->getItems();

        $segments = array();
        foreach ($rows_segment as $key => $row_segment)
        {
            $segments[$key]['title'] = $row_segment->getTitle();
            $segments[$key]['code'] = $row_segment->getCode();
        }

        return $segments;
    }

	public function deleteSubscription($email)
	{
		$newsletter_subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
		Mage::getModel('advancednewsletter/mailchimp')->delete($newsletter_subscriber->getEmail(), $newsletter_subscriber->getStoreId());
		$newsletter_subscriber->delete();
		$this->getSubscriber($email)->delete();
	}

    public function subscriptionToSegmentDeleting($segment_code)
    {
        $collection = $this->getCollection();
        $collection->getSelect();
        $rows = $collection->getItems();

        foreach ($rows as $row)
            $this->unsubscribe($row->getEmail(), $segment_code, false);
    }

	public function subscriptionToSegmentReplacing($old_segment_code, $new_segment_code)
    {
		if (empty($old_segment_code)) return;
        $collection = $this->getCollection();
        $collection->getSelect();
        $rows = $collection->getItems();

        foreach ($rows as $row)
        {
			$row = $row->getData();
			$segments = explode(',', $row['segments_codes']);
			foreach($segments as $key => $segment)
			{
				if ($segment==$old_segment_code) $segments[$key] = $new_segment_code;
			}
			$segments = implode(',', $segments);
			$this
				 ->load($row['id'])
				 ->setSegmentsCodes($segments)
				 ->save();
		}
    }
}