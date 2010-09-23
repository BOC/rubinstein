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
class AW_Advancednewsletter_Adminhtml_SegmentsmanagmentController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction() {
        $this->loadLayout()
				->_setActiveMenu('newsletter')
				->renderLayout();
	}

    public function newAction() {
        $this->_forward('edit');
	}

    public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('advancednewsletter/segmentsmanagment')->load($id);

        if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

            if (!isset($data['display_in_store'])) $data['display_in_store'] = '';
            else $data['display_in_store'] = explode(',', $data['display_in_store']);
            if (!isset($data['display_in_category'])) $data['display_in_category'] = '';
            else $data['display_in_category'] = explode(',', $data['display_in_category']);

			Mage::register('segmentsmanagment_data', $model->getData());

			$this->loadLayout();
			$this->_setActiveMenu('advancednewsletter/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('advancednewsletter/adminhtml_segmentsmanagment_edit'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('advancednewsletter')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {

            if (!isset($data['code'])||!isset($data['title'])||strpos($data['code'],'_')===0||preg_match("/[<>, ]/", $data['code'])||preg_match("/[<>,]/", $data['title']))
            {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('advancednewsletter')->__('Segment code and title are required fields and can not include \',<>\' and _ at the code beginning. Spaces mustn\'t be used in segment code'));
                $this->_redirect('*/*/');
                return;
            }

			if (!isset($data['display_in_store'])) $data['display_in_store'] = '';
            else $data['display_in_store'] = implode(',', $data['display_in_store']);
            if (!isset($data['display_in_category'])) $data['display_in_category'] = '';
            else $data['display_in_category'] = implode(',', $data['display_in_category']);

            $model = Mage::getModel('advancednewsletter/segmentsmanagment');

			$id = $this->getRequest()->getParam('id');
			$old_segment_code = '';
			if ($id)
			{
				$model->load($id);
				if ($model->getCode()!=$data['code']) $old_segment_code = $model->getCode();
			}
			
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));

			try {
				if ($model->getCreatedTime() == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}

				$model->save();
				Mage::getModel('advancednewsletter/subscriptions')->subscriptionToSegmentReplacing($old_segment_code, $data['code']);
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('advancednewsletter')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('advancednewsletter')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$segment = Mage::getModel('advancednewsletter/segmentsmanagment')->load($this->getRequest()->getParam('id'));
                Mage::getModel('advancednewsletter/subscriptions')->subscriptionToSegmentDeleting($segment->getCode());
				Mage::getModel('advancednewsletter/templates')->deleteSegmentFromTemplates($segment->getId());
                $model = Mage::getModel('advancednewsletter/segmentsmanagment');
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $segmentIds = $this->getRequest()->getParam('segment');
        if(!is_array($segmentIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($segmentIds as $segmentId) {
                    $segment = Mage::getModel('advancednewsletter/segmentsmanagment')->load($segmentId);
                    Mage::getModel('advancednewsletter/subscriptions')->subscriptionToSegmentDeleting($segment->getCode());
					Mage::getModel('advancednewsletter/templates')->deleteSegmentFromTemplates($segment->getId());
                    $segment->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($segmentIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
}