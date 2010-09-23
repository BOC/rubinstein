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
class AW_Advancednewsletter_Model_Mailchimp
{
    /* Mailchimp configuration options */
	const MAILCHIMP_ENABLED = "advancednewsletter/mailchimpconfig/mailchimpenabled";
	const MAILCHIMP_AUTOSYNC = "advancednewsletter/mailchimpconfig/autosync";
	const MAILCHIMP_APIKEY = "advancednewsletter/mailchimpconfig/apikey";
	const MAILCHIMP_LISTID = "advancednewsletter/mailchimpconfig/listid";
	const MAILCHIMP_XMLRPC = "advancednewsletter/mailchimpconfig/xmlrpc";

	protected function connect($apikey, $xmlrpcurl)
    {
        if(!$apikey||!$xmlrpcurl) return false;

        if(substr($apikey, -4) != '-us1' && substr($apikey, -4) != '-us2') return false;

        list($key, $dc) = explode('-',$apikey,2);
        if (!$dc) $dc = 'us1';
        list($aux, $host) = explode('http://',$xmlrpcurl);
        $api_host = 'http://'.$dc.'.'.$host;

        return $api_host;
    }

	private function getStoreSettings($store_id)
	{
		$keys = array(
            self::MAILCHIMP_ENABLED,
            self::MAILCHIMP_AUTOSYNC,
			self::MAILCHIMP_APIKEY,
			self::MAILCHIMP_LISTID,
			self::MAILCHIMP_XMLRPC
			);
		
		if ($store_id)
			$settings_stores = Mage::helper('advancednewsletter/storesettings')->getSettings($keys, 0);
		else
			$settings_stores = Mage::helper('advancednewsletter/storesettings')->getSettings($keys, 0, true);
		
		return $settings_stores[$store_id];
	}

	private function connection($store_settings, $autosync = false)
	{
		if (!$store_settings[self::MAILCHIMP_ENABLED]) return false;
		if (!$store_settings[self::MAILCHIMP_LISTID]) return false;
		if (!$store_settings[self::MAILCHIMP_AUTOSYNC]&&!$autosync) return false;
		
		$apikey = $store_settings[self::MAILCHIMP_APIKEY];
		$xmlrpcurl = $store_settings[self::MAILCHIMP_XMLRPC];

		if(!$apikey||!$xmlrpcurl) return false;

		try
        {$client = new Zend_XmlRpc_Client($this->connect($apikey, $xmlrpcurl));}
		catch(Exception $e) {return false;}

		return $client;
	}

	private function loadSegmentsToMailchimp($client, $store_settings)
	{
		$apikey = $store_settings[self::MAILCHIMP_APIKEY];
		$listId = $store_settings[self::MAILCHIMP_LISTID];

		$segments = Mage::getModel('advancednewsletter/segmentsmanagment')->getSegmentList();
		foreach ($segments as $segment)
        {
            try{$client->call('listInterestGroupAdd', array($apikey, $listId, $segment['value']));}
            catch(Exception $e) {continue;}
        }
	}

	public function subscribe($email, $store_id)
	{
		$store_settings = $this->getStoreSettings($store_id);

		$apikey = $store_settings[self::MAILCHIMP_APIKEY];
		$listId = $store_settings[self::MAILCHIMP_LISTID];

		if (!($client = $this->connection($store_settings))) return false;
		$this->loadSegmentsToMailchimp($client, $store_settings);
		
		$subscriber = Mage::getModel('advancednewsletter/subscriptions')->getSubscriber($email)->getData();

		$merges = array(
				'FNAME'=>$subscriber['first_name'],
				'LNAME'=>$subscriber['last_name'],
				'INTERESTS'=>$subscriber['segments_codes']
				);

		try{$client->call('listSubscribe', array($apikey, $listId, $email, $merges, 'html', null, true, true, false));}
		catch(Exception $e) {return false;}

		return true;
	}

	public function unsubscribe($email, $store_id)
	{
		$store_settings = $this->getStoreSettings($store_id);

		$apikey = $store_settings[self::MAILCHIMP_APIKEY];
		$listId = $store_settings[self::MAILCHIMP_LISTID];

		if (!($client = $this->connection($store_settings))) return false;
		$this->loadSegmentsToMailchimp($client, $store_settings);

		$subscriber = Mage::getModel('advancednewsletter/subscriptions')->getSubscriber($email)->getData();

		/* If subscriber is not subscribed in this store, return false */
		$subscriber_data = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
		if ($store_id>0&&intval($subscriber_data->getStoreId())!=$store_id) return false;

		try{$client->call('listUnsubscribe', array($apikey, $listId, $email, false, false, false));}
		catch(Exception $e) {return false;}

		return true;
	}

	public function delete($email, $store_id)
	{
		$store_settings = $this->getStoreSettings($store_id);

		$apikey = $store_settings[self::MAILCHIMP_APIKEY];
		$listId = $store_settings[self::MAILCHIMP_LISTID];

		if (!($client = $this->connection($store_settings))) return false;

		try{$client->call('listUnsubscribe', array($apikey, $listId, $email, true, false, false)); return true;}
		catch(Exception $e) {return false;}

		return true;
	}

	public function synchronizeAll()
	{
		$keys = array(
            self::MAILCHIMP_ENABLED,
            self::MAILCHIMP_AUTOSYNC,
			self::MAILCHIMP_APIKEY,
			self::MAILCHIMP_LISTID,
			self::MAILCHIMP_XMLRPC
			);
			
		$settings_stores = Mage::helper('advancednewsletter/storesettings')->getSettings($keys, 0);

		$syncCounter = 0;

        foreach ($settings_stores as $store_id => $store_settings)
		{
            $apikey = $store_settings[self::MAILCHIMP_APIKEY];
			$listId = $store_settings[self::MAILCHIMP_LISTID];

			if (!($client = $this->connection($store_settings, true))) continue;
            
            $syncCounter++;
            
			$this->loadSegmentsToMailchimp($client, $store_settings);

			/* Sync native Newsletter subscribers with AN subscribers */
			$subscribers = Mage::getResourceModel('newsletter/subscriber_collection')
							->showCustomerInfo(true)
							->getData();

			foreach ($subscribers as $subscriber)
			{
				try
				{
					Mage::getModel('advancednewsletter/subscriptions')
								->setEmail($subscriber['subscriber_email'])
								->setLastName($subscriber['customer_lastname'])
								->setFirstName($subscriber['customer_firstname'])
								->save();
				}
				catch(Exception $e){continue;}
			}

			/* Sync with Mailchimp */
			$subscribers = Mage::getModel('advancednewsletter/subscriptions')
							->getCollection()
							->load()
							->getData();

			foreach ($subscribers as $subscriber)
			{
				/* Getting native newsletter subscriber */
				$subscriber_data = Mage::getModel('newsletter/subscriber')->loadByEmail($subscriber['email']);
				/* Check if not subscribed (1) or unsubscribed (2) then continue */
				if ($subscriber_data->getStatus()!='1'&&$subscriber_data->getStatus()!='3') continue;
				/* Check subscribers store_id for multistore support */
				if ($store_id>0&&intval($subscriber_data->getStoreId())!=$store_id) continue;

				$merges = array(
					'FNAME'=>$subscriber['first_name'],
					'LNAME'=>$subscriber['last_name'],
					'INTERESTS'=>$subscriber['segments_codes']
					);
				try{$client->call('listSubscribe', array($apikey, $listId, $subscriber['email'], $merges, 'html', null, true, true, false));}
				catch(Exception $e) {continue;}
			}

			$subscribers = Mage::getResourceModel('newsletter/subscriber_collection')->getData();

			foreach ($subscribers as $subscriber)
			{
				if ($subscriber['subscriber_status']=='3')
				try{$client->call('listUnsubscribe', array($apikey, $listId, $subscriber['subscriber_email'], false, false, false));}
				catch(Exception $e) {continue;}
			}
		}

		if ($syncCounter == 0) return false;
        return true;
	}

	public function unsubscribersReverseSync()
	{
		$keys = array(
            self::MAILCHIMP_ENABLED,
            self::MAILCHIMP_AUTOSYNC,
			self::MAILCHIMP_APIKEY,
			self::MAILCHIMP_LISTID,
			self::MAILCHIMP_XMLRPC
			);
		$settings_stores = Mage::helper('advancednewsletter/storesettings')->getSettings($keys, 0);

		$syncCounter = 0;

        foreach ($settings_stores as $store_id => $store_settings)
		{
			$apikey = $store_settings[self::MAILCHIMP_APIKEY];
			$listId = $store_settings[self::MAILCHIMP_LISTID];

			if (!($client = $this->connection($store_settings, true))) continue;
            $syncCounter++;

			try{
				$unsubscribers = $client->call('listMembers', array($apikey, $listId, 'unsubscribed', null, 0, 500));
				foreach ($unsubscribers as $unsubscriber)
				{
					if (!Mage::getModel('newsletter/subscriber')->loadByEmail($unsubscriber['email'])->getData()) continue;
					$subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($unsubscriber['email']);
					$subscriber->unsubscribe();
				}
			}
			catch(Exception $e)
			{continue;}
		}

        if ($syncCounter == 0) return false;
        return true;
	}
}