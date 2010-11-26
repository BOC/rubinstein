<?php

class Magestore_Magenotification_Model_Observer
{
	public function controllerActionPredispatch($observer)
	{
		Mage::getModel('magenotification/magenotification')->checkUpdate();
		return;
	}
}