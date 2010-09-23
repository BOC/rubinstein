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
class AW_Advancednewsletter_Model_Template extends Mage_Newsletter_Model_Template
{
    public function send($subscriber, array $variables = array(), $name=null, Mage_Newsletter_Model_Queue $queue=null)
    {
        $variables['subscriber'] = $variables['subscriber']->getData();
        $variables['subscriber']['awunsubscribe']="<a href=".Mage::getBaseUrl()."advancednewsletter/index/unsubscribeall/email/".$subscriber->getSubscriberEmail().">Unsubscribe</a>";
        $variables['subscriber']['awloginpage']="<a href=".Mage::getBaseUrl()."customer/account/login/>Manage subscriptions</a>";

        $templates_smtp = Mage::getModel('advancednewsletter/templates')->getSmtpById($this->getTemplateId());
        if ($templates_smtp>0)
        {
            $smtp_info = Mage::getModel('advancednewsletter/smtpconfiguration')->getSmtpInfo($templates_smtp)->getData();

            if ($smtp_info['usessl'])
                $config = array('ssl' => 'tls', 'port' => $smtp_info['port'], 'auth' => 'login', 'username' => $smtp_info['user_name'], 'password' => $smtp_info['password']);
            else
                $config = array('port' => $smtp_info['port'], 'auth' => 'login', 'username' => $smtp_info['user_name'], 'password' => $smtp_info['password']);
            $transport = new Zend_Mail_Transport_Smtp($smtp_info['server_name'], $config);
        }
        else $transport=null;

        if (!$this->isValidForSend())
        {
            return false;
        }

        $email = '';
        if ($subscriber instanceof Mage_Newsletter_Model_Subscriber)
        {
            $email = $subscriber->getSubscriberEmail();
            if (is_null($name) && ($subscriber->hasCustomerFirstname() || $subscriber->hasCustomerLastname()) )
            {
                $name = $subscriber->getCustomerFirstname() . ' ' . $subscriber->getCustomerLastname();
            }
        }
        else
        {
            $email = (string) $subscriber;
        }

        $ANsubscriber = Mage::getModel('advancednewsletter/subscriptions')->getSubscriber($email);

        if(Mage::getStoreConfig('advancednewsletter/formconfiguration/displaysalutation'))
        {
            $sal_id = $ANsubscriber->getSalutation();
            if ($sal_id==1) $salutation=Mage::getStoreConfig('advancednewsletter/formconfiguration/salutation2');
            else $salutation=$salutation=Mage::getStoreConfig('advancednewsletter/formconfiguration/salutation1');
            ;

            $name = $salutation.' '.$name;
        }

        if (Mage::getStoreConfigFlag(Mage_Newsletter_Model_Subscriber::XML_PATH_SENDING_SET_RETURN_PATH))
        {
            $this->getMail()->setReturnPath($this->getTemplateSenderEmail());
        }

        ini_set('SMTP', Mage::getStoreConfig('system/smtp/host'));
        ini_set('smtp_port', Mage::getStoreConfig('system/smtp/port'));
        $mail = $this->getMail();
        $mail->addTo($email, $name);
        $text = $this->getProcessedTemplate($variables['subscriber'], true);

        if ($this->isPlain())
        {
            $mail->setBodyText($text);
        }
        else
        {
            $mail->setBodyHTML($text);
        }

        $mail->setSubject($this->getProcessedTemplateSubject($variables));
        $mail->setFrom($this->getTemplateSenderEmail(), $this->getTemplateSenderName());

        Mage::helper('advancednewsletter/logger')->setActive(1);

        try
        {
            if ($transport) $mail->send($transport);
            else $mail->send();
            $this->_mail = null;
            if (!is_null($queue))
            {
                $subscriber->received($queue);
            }
            Mage::helper('advancednewsletter/logger')->log("Message to ".$email." sent correctly");
        }
        catch (Exception $e)
        {
            if ($subscriber instanceof Mage_Newsletter_Model_Subscriber)
            {
                // If letter sent for subscriber, we create a problem report entry
                Mage::helper('advancednewsletter/logger')->log("Message to ".$email." didn't send. Error: ".$e->getMessage());

                /* Because of bug in problem reports in standart newsletter, comments are here.
				Then it will be fixed, remove comments */
                /*
				$problem = Mage::getModel('newsletter/problem');
                $problem->addSubscriberData($subscriber);
                if (!is_null($queue)) {
                    $problem->addQueueData($queue);
                }
                $problem->addErrorData($e);
                $problem->save();
                */

                if (!is_null($queue))
                {
                    $subscriber->received($queue);
                }
            } else
            {
                // Otherwise throw error to upper level
                throw $e;
            }
            return false;
        }

        return true;
    }

    protected function _afterLoad()
    {
        $an_templates = Mage::getModel('advancednewsletter/templates')
            ->getTemplate($this->getTemplateId());

        $this
            ->setSegments(explode(',', $an_templates->getSegmentsIds()))
            ->setSmtpAccount($an_templates->getSmtpId());

        return parent::_afterLoad();
    }

    protected function _afterSave()
    {
        if (!is_null($this->getSegments())) $segments = implode(',', $this->getSegments());
        else $segments = '';

        Mage::getModel('advancednewsletter/templates')
            ->getTemplate($this->getTemplateId())
            ->setTemplateId($this->getTemplateId())
            ->setSmtpId($this->getSmtpAccount())
            ->setSegmentsIds($segments)
            ->save();

        return parent::_afterSave();
    }
}
