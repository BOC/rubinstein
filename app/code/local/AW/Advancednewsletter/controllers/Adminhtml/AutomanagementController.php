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
class AW_Advancednewsletter_Adminhtml_AutomanagementController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction() {
		$this->loadLayout()
				->_setActiveMenu('newsletter')
				->renderLayout();
	}

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('advancednewsletter/automanagement');
                $model->load($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('catalogrule')->__('Rule was successfully deleted'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalogrule')->__('Unable to find a page to delete'));
        $this->_redirect('*/*/');
    }

	public function editAction()
    {
        $id = $this->getRequest()->getParam('id');

        $model = Mage::getModel('advancednewsletter/automanagement');

        if ($id) {
            $model->load($id);
            if (! $model->getRuleId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('advancednewsletter')->__('This rule no longer exists'));
                $this->_redirect('*/*');
                return;
            }
        }

        $data = Mage::getSingleton('adminhtml/session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

		$model->getConditions()->setJsFormObject('automanagement_conditions_fieldset');

		$model->setData('segments_cut', explode(';', $model->getSegmentsCut()));
		$model->setData('segments_paste', explode(';', $model->getSegmentsPaste()));

        Mage::register('automanagement_data', $model);

		$this->loadLayout();
		$this->_setActiveMenu('newsletter');

		$block = $this->getLayout()->createBlock('advancednewsletter/adminhtml_automanagement_edit')
            ->setData('action', $this->getUrl('*/advancednewsletter_automanagement/save'));

        /*���������� ���������� ������ ��� ������� � ����� layout*/
        $this->getLayout()->getBlock('head')
            ->setCanLoadExtJs(true)
            ->setCanLoadRulesJs(true);

        $this
            ->_addContent($block)
            ->_addLeft($this->getLayout()->createBlock('advancednewsletter/adminhtml_automanagement_edit_tabs'))
            ->renderLayout();
    }

    public function saveAction()
    {
		if ($data = $this->getRequest()->getPost()) {
                    
			if (!isset($data['segments_cut'])||!isset($data['segments_paste']))
			{
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('advancednewsletter')->__('Remove and Move actions must be entered'));
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}

			$data['segments_cut'] = implode(';', $data['segments_cut']);
			$data['segments_paste'] = implode(';', $data['segments_paste']);
			try {
                $model = Mage::getModel('advancednewsletter/automanagement');

/* Disabled for Magento 1.2 */
//                if ($id = $this->getRequest()->getParam('rule_id')) {
//                    $model->load($id);
//                    if ($id != $model->getId()) {
//                        Mage::throwException(Mage::helper('advancednewsletter')->__('Wrong rule specified.'));
//                    }
//                }

                $data['conditions'] = $data['rule']['conditions'];

                unset($data['rule']);

                if (!empty($data['auto_apply'])) {
                    $autoApply = true;
                    unset($data['auto_apply']);
                } else {
                    $autoApply = false;
                }

				$model->loadPost($data);
				
                Mage::getSingleton('adminhtml/session')->setPageData($model->getData());
				$model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('advancednewsletter')->__('Rule was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setPageData(false);
                
                
                
                if ($autoApply) {
                    $this->_forward('applyRules');
                } else {
                    Mage::app()->saveCache(1, 'catalog_rules_dirty');
                    $this->_redirect('*/*/');
                }
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setPageData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('rule_id')));
                return;
            }
        }
        
        $this->_redirect('*/*/');
    }

    public function newConditionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('advancednewsletter/automanagement'))
            ->setPrefix('conditions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }
        /*echo $type;
        var_dump($model);
        die;*/

        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }
}