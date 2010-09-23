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
class AW_Advancednewsletter_Block_Adminhtml_Newslettertemplates_Edit_Form extends Mage_Adminhtml_Block_Newsletter_Template_Edit_Form
{
    protected function _prepareForm()
    {
		$model  = Mage::registry('_current_template');
		if (!$model instanceof Mage_Newsletter_Model_Template)
			return Mage_Adminhtml_Block_Widget_Form::_prepareForm();

        $form   = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'post'
        ));

        $fieldset   = $form->addFieldset('base_fieldset', array(
            'legend'    => Mage::helper('newsletter')->__('Template Information'),
            'class'     => 'fieldset-wide'
        ));

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array(
                'name'      => 'id',
                'value'     => $model->getId(),
            ));
        }

        $fieldset->addField('code', 'text', array(
            'name'      => 'code',
            'label'     => Mage::helper('newsletter')->__('Template Name'),
            'title'     => Mage::helper('newsletter')->__('Template Name'),
            'required'  => true,
            'value'     => $model->getTemplateCode(),
        ));

        $fieldset->addField('subject', 'text', array(
            'name'      => 'subject',
            'label'     => Mage::helper('newsletter')->__('Template Subject'),
            'title'     => Mage::helper('newsletter')->__('Template Subject'),
            'required'  => true,
            'value'     => $model->getTemplateSubject(),
        ));

        $fieldset->addField('sender_name', 'text', array(
            'name'      =>'sender_name',
            'label'     => Mage::helper('newsletter')->__('Sender Name'),
            'title'     => Mage::helper('newsletter')->__('Sender Name'),
            'required'  => true,
            'value'     => $model->getTemplateSenderName(),
        ));

        $fieldset->addField('sender_email', 'text', array(
            'name'      =>'sender_email',
            'label'     => Mage::helper('newsletter')->__('Sender Email'),
            'title'     => Mage::helper('newsletter')->__('Sender Email'),
            'class'     => 'validate-email',
            'required'  => true,
            'value'     => $model->getTemplateSenderEmail(),
			'after_element_html' => '<p><small>If you don\'t use SSL, this address must match the SMTP <br />account address due to spam protection requirements</small></p>',
        ));

        $fieldset->addField('segments', 'multiselect', array(
            'name'      =>'segments',
            'label'     => Mage::helper('newsletter')->__('Segment'),
            'title'     => Mage::helper('newsletter')->__('Segment'),
            'values' => Mage::getModel('advancednewsletter/segmentsmanagment')->getSegments(),
            'value' => $model->getSegments()
        ));

        $fieldset->addField('smtp_account', 'select', array(
            'name'      =>'smtp_account',
            'label'     => Mage::helper('newsletter')->__('SMTP account'),
            'title'     => Mage::helper('newsletter')->__('SMTP account'),
            'values' => Mage::getModel('advancednewsletter/smtpconfiguration')->getSMTPs(),
            'value' => $model->getSmtpAccount()
        ));

		if (preg_match('/^1.4/', Mage::getVersion()))
		{
			$widgetFilters = array('is_email_compatible' => 1);
			$wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(array('widget_filters' => $widgetFilters));
			if ($model->isPlain()) {
				$wysiwygConfig->setEnabled(false);
			}
		

			$fieldset->addField('text', 'editor', array(
				'name'      => 'text',
				'wysiwyg'   => (!$model->isPlain()),
				'label'     => Mage::helper('newsletter')->__('Template Content'),
				'title'     => Mage::helper('newsletter')->__('Template Content'),
				'theme'     => 'advanced',
				'required'  => true,
				'state'     => 'html',
				'style'     => 'height:36em;',
				'value'     => $model->getTemplateText(),
				'config'    => $wysiwygConfig
			));

			if (!$model->isPlain()) {
					$fieldset->addField('template_styles', 'textarea', array(
						'name'          =>'styles',
						'label'         => Mage::helper('newsletter')->__('Template Styles'),
						'container_id'  => 'field_template_styles',
						'value'         => $model->getTemplateStyles()
					));
				}
		}
		else
		{
			$fieldset->addField('text', 'editor', array(
				'name'      => 'text',
				'wysiwyg'   => (!$model->isPlain()),
				'label'     => Mage::helper('newsletter')->__('Template Content'),
				'title'     => Mage::helper('newsletter')->__('Template Content'),
				'theme'     => 'advanced',
				'required'  => true,
				'state'     => 'html',
				'style'     => 'height:36em;',
				'value'     => $model->getTemplateText(),
			));
		}
		
        $form->setAction(parent::getUrl('*/*/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return Mage_Adminhtml_Block_Widget_Form::_prepareForm();
    }
}
