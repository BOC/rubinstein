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
class AW_Advancednewsletter_Block_Adminhtml_Subscriber_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
	  $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
        	'enctype' => 'multipart/form-data'
            )
      );

      $fieldset = $form->addFieldset('main_group', array('legend'=>Mage::helper('advancednewsletter')->__('Fields')));

        $fieldset->addField('first_name', 'text', array(
            'label'     => Mage::helper('advancednewsletter')->__('First Name'),
            'name'      => 'first_name',
        ));

        $fieldset->addField('last_name', 'text', array(
            'label'     => Mage::helper('advancednewsletter')->__('Last Name'),
            'name'      => 'last_name',
        ));

        $fieldset->addField('salutation', 'select', array(
            'label'     => Mage::helper('advancednewsletter')->__('Salutation'),
            'name'      => 'salutation',
			'values'    => array('0'=>Mage::helper('advancednewsletter')->__('Salutation 1'),
								'1'=>Mage::helper('advancednewsletter')->__('Salutation 2')
							)
        ));

        $fieldset->addField('subscriber_email', 'text', array(
            'label'     => Mage::helper('advancednewsletter')->__('Email'),
            'name'      => 'subscriber_email',
			'required'  => true,
        ));

        $fieldset->addField('phone', 'text', array(
            'label'     => Mage::helper('advancednewsletter')->__('Phone'),
            'name'      => 'phone',
        ));

		if (Mage::registry('_current_subscriber'))
		{
			$segments_codes = explode(',',Mage::registry('_current_subscriber')->getSegmentsCodes());
		}
		else
		$segments_codes = '';

		$fieldset->addField('segments_codes', 'multiselect', array(
            'name'      =>'segments_codes',
            'label'     => Mage::helper('newsletter')->__('Segment'),
            'title'     => Mage::helper('newsletter')->__('Segment'),
            'values' => Mage::getModel('advancednewsletter/segmentsmanagment')->getSegmentList(),
            'value' => $segments_codes,
			'required'  => true,
        ));

		$fieldset->addField('subscribed_in_store', 'select', array(
            'label'     => Mage::helper('advancednewsletter')->__('Subscribed in store'),
            'name'      => 'subscribed_in_store',
            'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
            'onchange'  => 'checkTarget()',
            'after_element_html' => '',
			'required'  => true,
        ));

        if ( Mage::getSingleton('adminhtml/session')->getSmtpconfigurationData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getSmtpconfigurationData());
            Mage::getSingleton('adminhtml/session')->getSmtpconfigurationData(null);
        } elseif ( Mage::registry('_current_subscriber') ) {
            $form->setValues(Mage::registry('_current_subscriber')->getData());
        }

      $form->setUseContainer(true);
      $this->setForm($form);
      return parent::_prepareForm();
  }
}