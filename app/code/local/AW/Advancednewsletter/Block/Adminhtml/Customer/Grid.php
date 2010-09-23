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
class AW_Advancednewsletter_Block_Adminhtml_Customer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	private $tsp_rule = null;
	
	public function __construct()
	{
		parent::__construct();
		$this->setId('anCustomerGrid');
		$this->setUseAjax(true);
		$this->setDefaultSort('entity_id');
		$this->setSaveParametersInSession(true);
	}
	
	protected function _prepareCollection()
	{
		if (!Mage::helper('advancednewsletter')->extensionEnabled('AW_Marketsuite'))
		{
			$collection = Mage::getResourceModel('customer/customer_collection');
		}
		else
		{
			$filter = urldecode(base64_decode($this->getRequest()->getParam('filter')));
			$data = array();
			parse_str($filter, $data);
			
			if (!empty($data['tsp_rule'])) $this->tsp_rule = $data['tsp_rule'];
			
			$collection = Mage::getModel('marketsuite/filter')
				->exportCustomers($this->tsp_rule);
		}

		$collection
				->addNameToSelect()
				->addAttributeToSelect('email')
				->addAttributeToSelect('created_at')
				->addAttributeToSelect('group_id')
				->joinAttribute('billing_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
				->joinAttribute('billing_city', 'customer_address/city', 'default_billing', null, 'left')
				->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
				->joinAttribute('billing_region', 'customer_address/region', 'default_billing', null, 'left')
				->joinAttribute('billing_country_id', 'customer_address/country_id', 'default_billing', null, 'left');
		
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	public function getResetFilterButtonHtml()
	{
		if (!Mage::helper('advancednewsletter')->extensionEnabled('AW_Marketsuite'))
		{
			$html = '<a href='.AW_Advancednewsletter_Helper_Data::MSS_PATH.'>';
			$html .= Mage::helper('advancednewsletter')->__('Customers can be filtered using Market Segmenation Suite rules. Learn more.');
			$html .= '</a>';
		}
		else
		{
			$collection = Mage::getModel('marketsuite/filter')->getCollection();

			$html =
				'<span class="filter">'.
				Mage::helper('advancednewsletter')->__('Apply MSS rule: ').
				'<select id="tsp_rule" name="tsp_rule" style="width:100px">
						<option value="0">---</option>';

			foreach($collection as $item)
			{
				if (!$item->getIsActive()) continue;
				if ($this->tsp_rule==$item->getId()) $selected='selected=selected';
				else $selected='';
				$html .=  '<option '.$selected.' value='.$item->getId().'>'.$item->getName().'</option>';
			}

			$html .= '</select></span>';
		}
		
		return $html.$this->getChildHtml('reset_filter_button');
	}
	
	protected function _prepareColumns()
	{
		$this->addColumn('entity_id', array(
			'header'    => Mage::helper('customer')->__('ID'),
			'width'     => '50px',
			'index'     => 'entity_id',
			'type'  => 'number',
		));
		
		$this->addColumn('name', array(
			'header'    => Mage::helper('customer')->__('Name'),
			'index'     => 'name'
		));
		$this->addColumn('email', array(
			'header'    => Mage::helper('customer')->__('Email'),
			'width'     => '150',
			'index'     => 'email'
		));
		
		$groups = Mage::getResourceModel('customer/group_collection')
			->addFieldToFilter('customer_group_id', array('gt'=> 0))
			->load()
			->toOptionHash();
		
		$this->addColumn('group', array(
			'header'    =>  Mage::helper('customer')->__('Group'),
			'width'     =>  '100',
			'index'     =>  'group_id',
			'type'      =>  'options',
			'options'   =>  $groups,
		));
		
		$this->addColumn('Telephone', array(
			'header'    => Mage::helper('customer')->__('Telephone'),
			'width'     => '100',
			'index'     => 'billing_telephone'
		));
		
		$this->addColumn('billing_postcode', array(
			'header'    => Mage::helper('customer')->__('ZIP'),
			'width'     => '90',
			'index'     => 'billing_postcode',
		));
		
		$this->addColumn('billing_country_id', array(
			'header'    => Mage::helper('customer')->__('Country'),
			'width'     => '100',
			'type'      => 'country',
			'index'     => 'billing_country_id',
		));
		
		$this->addColumn('billing_region', array(
			'header'    => Mage::helper('customer')->__('State/Province'),
			'width'     => '100',
			'index'     => 'billing_region',
		));
		
		$this->addColumn('customer_since', array(
			'header'    => Mage::helper('customer')->__('Customer Since'),
			'type'      => 'datetime',
			'align'     => 'center',
			'index'     => 'created_at',
			'gmtoffset' => true
		));
		
		if (!Mage::app()->isSingleStoreMode())
		{
			$this->addColumn('website_id', array(
				'header'    => Mage::helper('customer')->__('Website'),
				'align'     => 'center',
				'width'     => '80px',
				'type'      => 'options',
				'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
				'index'     => 'website_id',
			));
		}
		
		$this->addColumn('action',
			array(
			'header'    =>  Mage::helper('customer')->__('Action'),
			'align'     => 'center',
			'width'     => '100',
			'type'      => 'action',
			'getter'    => 'getId',
			'actions'   => array(
				array(
					'caption'   => Mage::helper('customer')->__('View'),
					'url'       => array('base'=> 'adminhtml/customer/edit'),
					'field'     => 'id'
				)
			),
			'filter'    => false,
			'sortable'  => false,
			'index'     => 'stores',
			'is_system' => true,
		));
		
		return parent::_prepareColumns();
	}
	
	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('entity_id');
		$this->getMassactionBlock()->setFormFieldName('customer');
		
		$segments = Mage::getModel('advancednewsletter/segmentsmanagment')->getSegmentList();
		$additional =
			array(
			'visibility'    => array(
				'name'     => 'segment',
				'type'     => 'select',
				'class'    => 'required-entry',
				'label'    => Mage::helper('advancednewsletter')->__('Segment'),
				'values'   => $segments
			)
		);
		
		$this->getMassactionBlock()->addItem('newsletter_subscribe', array(
			'label'    => Mage::helper('advancednewsletter')->__('Subscribe to segment'),
			'url'      => $this->getUrl('*/*/massSubscribe'),
			'additional'   => $additional
		));
		
		$this->getMassactionBlock()->addItem('newsletter_unsubscribe', array(
			'label'    => Mage::helper('advancednewsletter')->__('Unsubscribe from segment'),
			'url'      => $this->getUrl('*/*/massUnsubscribe'),
			'additional'   => $additional
		));
		
		return $this;
	}
}
