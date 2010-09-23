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
if (!preg_match('/^1.2/', Mage::getVersion()))
{
    class AW_Advancednewsletter_Model_Mysql4_Template extends Mage_Newsletter_Model_Mysql4_Template
    {}
}
else
{
    class AW_Advancednewsletter_Model_Mysql4_Template extends Mage_Newsletter_Model_Mysql4_Template
    {
        public function save(Mage_Newsletter_Model_Template $template)
        {
            $this->_write->beginTransaction();
            try {
                $data = $this->_prepareSave($template);
                if($template->getId() && (($template->getTemplateActual()==0 && !is_null($template->getTemplateActual())) || !$this->checkUsageInQueue($template))) {
                    $this->_write->update($this->_templateTable, $data,
                        $this->_write->quoteInto('template_id=?',$template->getId()));
                } else if ($template->getId()) {
                    $this->_write->update($this->_templateTable, $data,
                        $this->_write->quoteInto('template_id=?',$template->getId()));
                } else {
                    $this->_write->insert($this->_templateTable, $data);
                    $template->setId($this->_write->lastInsertId($this->_templateTable));
                }

                $this->_write->commit();
            }
            catch (Exception $e) {
                $this->_write->rollBack();
                //echo $e->getMessage();
                throw $e;
            }
        }
    }
}
