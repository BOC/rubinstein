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
class AW_Advancednewsletter_Model_Mysql4_Queue extends Mage_Newsletter_Model_Mysql4_Queue
{
    public function addSubscribersToQueue(Mage_Newsletter_Model_Queue $queue, array $subscriberIds)
    {
        $segmentsids = Mage::getModel('advancednewsletter/templates')->getSegmentsByTemplateid($queue->getTemplateId());
        $segments_codes = Mage::getModel('advancednewsletter/segmentsmanagment')->getSegmentsCodesbyIds($segmentsids);

        foreach ($subscriberIds as $key=>$subscriberid)
        {
        $subscriber_base = Mage::getModel('newsletter/subscriber')->load($subscriberid);
        $subscriber = Mage::getModel('advancednewsletter/subscriptions')->getSubscriber($subscriber_base->getSubscriberEmail())->getData();
        
            $subscriber_to_send = false;
            foreach ($segments_codes as $segmentcode)
            {
                /* Subscription to some segments */
                if (isset($subscriber['segments_codes'])&&(Mage::helper('advancednewsletter')->search($subscriber['segments_codes'], $segmentcode)))
                {
                   $subscriber_to_send = true;
                   break;
                }
				
                /* Subscription to all. Its try then segments_codes is empty or ALL_SEGMENTS*/
                if (!isset($subscriber['segments_codes'])||$subscriber['segments_codes']==AW_Advancednewsletter_Helper_Data::AN_SEGMENTS_ALL)
                {
                   $subscriber_to_send = true;
                   break;
                }
            }
            if (!$subscriber_to_send) unset($subscriberIds[$key]);
        }
        if (count($subscriberIds)==0) {
            Mage::throwException(Mage::helper('newsletter')->__('No subscribers selected'));
        }

        if (!$queue->getId() && $queue->getQueueStatus()!=Mage_Newsletter_Model_Queue::STATUS_NEVER) {
            Mage::throwException(Mage::helper('newsletter')->__('Invalid queue selected'));
        }

        $select = $this->_getWriteAdapter()->select();
        $select->from($this->getTable('queue_link'),'subscriber_id')
            ->where('queue_id = ?', $queue->getId())
            ->where('subscriber_id in (?)', $subscriberIds);

        $usedIds = $this->_getWriteAdapter()->fetchCol($select);
        $this->_getWriteAdapter()->beginTransaction();
        try {
            foreach($subscriberIds as $subscriberId) {
                if(in_array($subscriberId, $usedIds)) {
                    continue;
                }
                $data = array();
                $data['queue_id'] = $queue->getId();
                $data['subscriber_id'] = $subscriberId;
                $this->_getWriteAdapter()->insert($this->getTable('queue_link'), $data);
            }
            $this->_getWriteAdapter()->commit();
        }
        catch (Exception $e) {
            $this->_getWriteAdapter()->rollBack();
        }

    }
}
