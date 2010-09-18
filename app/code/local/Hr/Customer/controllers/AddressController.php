<?php
/**
 * Customer address controller
 *
 * @category   Hr
 * @package    Hr_Customer
 * @author     Xavier Muselet <xmuselet@brandonlinecommerce.com>
 */
class Hr_Customer_AddressController extends Mage_Core_Controller_Front_Action
{
    /**
     * Retrieve customer session object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    /**
     * Customer addresses list
     */
    public function indexAction()
    {
        if (count($this->_getSession()->getCustomer()->getAddresses())) {
            $this->loadLayout();
            $this->_initLayoutMessages('customer/session');
            $this->_initLayoutMessages('catalog/session');

            if ($block = $this->getLayout()->getBlock('address_book')) {
                $block->setRefererUrl($this->_getRefererUrl());
            }
            $this->renderLayout();
        }
        else {
            $this->getResponse()->setRedirect(Mage::getUrl('*/*/new'));
        } 
    }

    public function editAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

	public function bookAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
    public function formPostAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/');
        }
        // Save data
        if ($this->getRequest()->isPost()) {
            $address = Mage::getModel('customer/address')
                ->setData($this->getRequest()->getPost())
                ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
            $addressId = $this->getRequest()->getParam('id');
            if ($addressId) {
                $customerAddress = $this->_getSession()->getCustomer()->getAddressById($addressId);
                if ($customerAddress->getId() && $customerAddress->getCustomerId() == $this->_getSession()->getCustomerId()) {
                    $address->setId($addressId);
                }
                else {
                    $address->setId(null);
                }
            }
            else {
                $address->setId(null);
            }
            try {
                $accressValidation = $address->validate();
                if (true === $accressValidation) {
                    $address->save();
                    $this->_getSession()->addSuccess($this->__('The address has been saved.'));
                   	$this->getResponse()->setRedirect(Mage::getUrl('*/*/book'));
                    return;
                } else {
                    $this->_getSession()->setAddressFormData($this->getRequest()->getPost());
                    if (is_array($accressValidation)) {
                        foreach ($accressValidation as $errorMessage) {
                            $this->_getSession()->addError($errorMessage);
                        }
                    } else {
                        $this->_getSession()->addError($this->__('Cannot save the address.'));
                    }
                }
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
                    ->addException($e, $e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->setAddressFormData($this->getRequest()->getPost())
                    ->addException($e, $this->__('Cannot save address.'));
            }
        }
        $this->getResponse()->setRedirect(Mage::getUrl('*/*/edit'));
    }

    public function deleteAction()
    {
        $addressId = $this->getRequest()->getParam('id', false);

        if ($addressId) {
            $address = Mage::getModel('customer/address')->load($addressId);

            // Validate address_id <=> customer_id
            if ($address->getCustomerId() != $this->_getSession()->getCustomerId()) {
                $this->_getSession()->addError($this->__('The address does not belong to this customer.'));
                $this->getResponse()->setRedirect(Mage::getUrl('*/*/index'));
                return;
            }

            try {
                $address->delete();
                $this->_getSession()->addSuccess($this->__('The address has been deleted.'));
            }
            catch (Exception $e){
                $this->_getSession()->addError($this->__('An error occurred while deleting the address.'));
            }
        }
		$this->getResponse()->setRedirect(Mage::getUrl('*/*/book'));
    }
}
