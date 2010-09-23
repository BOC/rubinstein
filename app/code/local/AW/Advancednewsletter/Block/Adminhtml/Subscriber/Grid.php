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
class AW_Advancednewsletter_Block_Adminhtml_Subscriber_Grid extends Mage_Adminhtml_Block_Newsletter_Subscriber_Grid
{
	protected function _prepareCollection()
    {
        $collection = Mage::getResourceSingleton('newsletter/subscriber_collection')->getData();

        $collection = Mage::getResourceSingleton('advancednewsletter/subscriber_collection');

		$collection
            ->showCustomerInfo(true)
            ->addSubscriberTypeField()
            ->showStoreInfo()
            ->showSegments();

		if($this->getRequest()->getParam('queue', false)) {
            $collection->useQueue(Mage::getModel('newsletter/queue')
                ->load($this->getRequest()->getParam('queue')));
        }

        $this->setCollection($collection);

        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('subscriber_id', array(
            'header'    => Mage::helper('newsletter')->__('ID'),
            'index'     => 'subscriber_id'
        ));

        $this->addColumn('email', array(
            'header'    => Mage::helper('newsletter')->__('Email'),
            'index'     => 'subscriber_email'
        ));

        $this->addColumn('type', array(
            'header'    => Mage::helper('newsletter')->__('Type'),
            'index'     => 'type',
            'type'      => 'options',
            'options'   => array(
                1  => Mage::helper('newsletter')->__('Guest'),
                2  => Mage::helper('newsletter')->__('Customer')
            )
        ));

        $this->addColumn('firstname', array(
            'header'    => Mage::helper('newsletter')->__('Customer First name'),
            'index'     => 'first_name',
            'default'   =>    '----',
			'filter_condition_callback' => array($this, 'filter_firstname_callback'),
        ));

        $this->addColumn('lastname', array(
            'header'    => Mage::helper('newsletter')->__('Customer Last name'),
            'index'     => 'last_name',
            'default'   =>    '----',
			'filter_condition_callback' => array($this, 'filter_lastname_callback'),
        ));

		$this->addColumn('phone', array(
            'header'    => Mage::helper('newsletter')->__('Phone'),
            'index'     => 'phone',
            'default'   =>    '----',
			'filter_condition_callback' => array($this, 'filter_phone_callback'),
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('newsletter')->__('Status'),
            'index'     => 'subscriber_status',
            'type'      => 'options',
            'options'   => array(
                Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE   => Mage::helper('newsletter')->__('Not activated'),
                Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED   => Mage::helper('newsletter')->__('Subscribed'),
                Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED => Mage::helper('newsletter')->__('Unsubscribed'),
            )
        ));

        $this->addColumn('website', array(
            'header'    => Mage::helper('newsletter')->__('Website'),
            'index'     => 'website_id',
            'type'      => 'options',
            'options'   => $this->_getWebsiteOptions()
        ));

        $this->addColumn('group', array(
            'header'    => Mage::helper('newsletter')->__('Store'),
            'index'     => 'group_id',
            'type'      => 'options',
            'options'   => $this->_getStoreGroupOptions()
        ));

        $this->addColumn('store', array(
            'header'    => Mage::helper('newsletter')->__('Store View'),
            'index'     => 'store_id',
            'type'      => 'options',
            'options'   => $this->_getStoreOptions()
        ));

		$this->addColumn('segments_codes', array(
            'header'    => Mage::helper('newsletter')->__('Segment'),
            'index'     => 'segments_codes',
            'filter' => false,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('customer')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('customer')->__('XML'));
        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }
	
	protected function filter_firstname_callback($collection, $column)
    {
		$val = $column->getFilter()->getValue();

    	if(!@val) return;
        $cond = array();

        if(@$val)
            $cond = "i.first_name LIKE '%".$val."%'";

        $collection->getSelect()->where($cond);
    }
	
	protected function filter_lastname_callback($collection, $column)
    {
        $val = $column->getFilter()->getValue();

    	if(!@val) return;
        $cond = array();

        if(@$val)
            $cond = "i.last_name LIKE '%".$val."%'";

        $collection->getSelect()->where($cond);
    }

    protected function filter_phone_callback($collection, $column)
    {
        $val = $column->getFilter()->getValue();

    	if(!@val) return;
        $cond = array();

        if(@$val)
            $cond = "i.phone LIKE '%".$val."%'";

        $collection->getSelect()->where($cond);
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

  protected function _prepareMassaction()
  {
      parent::_prepareMassaction();

      $this->getMassactionBlock()->addItem('subscribe', array(
             'label'    => Mage::helper('advancednewsletter')->__('Subscribe'),
             'url'      => $this->getUrl('*/*/massSubscribe'),
        ));

      $this->getMassactionBlock()->addItem('delete', array(
             'label'        => Mage::helper('advancednewsletter')->__('Delete'),
             'url'          => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('advancednewsletter')->__('Are you sure?')
        ));
  }
}
