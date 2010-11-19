<?php

class Magestore_Magenotification_Adminhtml_MagenotificationController extends Mage_Adminhtml_Controller_Action
{

	public function readdetailAction()
	{
		include(Mage::getBaseDir() . DS .'lib'. DS .'PEAR'. DS .'HTTP'. DS .'HTTP.php');
		
		$id = $this->getRequest()->getParam('id');
		
		$notice = Mage::getModel('adminnotification/inbox')->load($id);
		
		$notice->setIsRead(1);
		
		$notice->save();
		
		$http = new HTTP();
		
		$http->redirect($notice->getUrl());
		
		return;
	}

}