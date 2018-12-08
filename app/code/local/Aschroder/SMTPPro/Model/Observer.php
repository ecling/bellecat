<?php
/**
 * Observer that logs emails after they have been sent
 *
 * @author Ashley Schroder (aschroder.com)
 * @copyright  Copyright (c) 2014 Ashley Schroder
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Aschroder_SMTPPro_Model_Observer extends Varien_Object {
    protected $_mailgun = array('sales_email_order_template','sales_email_order_guest_template');
	public function cleanOldLogs(Varien_Event_Observer $observer)
	{
		$lifetime = Mage::helper('smtppro')->getLogLifetimeDays();
	    $log = Mage::getModel('smtppro/email_log')->clean($lifetime);
	    return $this;
	}

    /**
     * Expects observer with data:
     * 'to', 'subject', 'template',
     * 'html', 'email_body'
     *
     * @param $observer
     */
    public function log($observer) {

        $event = $observer->getEvent();
        Mage::helper('smtppro')->logEmailSent(
            $event->getTo(),
            $event->getTemplate(),
            $event->getSubject(),
            $event->getEmailBody(),
            $event->getHtml());
    }

    /**
     * Expects observer with:
     * 'mail', the mail about to be sent
     * 'email', the email object initiating the send
     * 'transport', an initially empty transport Object, will be used if set.
     *
     * @param $observer
     */
    public function beforeSend($observer) {
        Mage::helper('smtppro')->log($observer->getEvent()->getMail());
        $observer->getEvent()->getTransport()->setTransport(Mage::helper('smtppro')->getTransport());
    }

    /**
     * Expects observer with:
     * 'mail', the mail about to be sent
     * 'template', the template being used
     * 'variables', the variables used in the template
     * 'transport', an initially empty transport Object, will be used if set.
     *
     * @param $observer
     */
    public function beforeSendTemplate($observer) {
        Mage::helper('smtppro')->log($observer->getEvent()->getMail());
        //$observer->getEvent()->getTransport()->setTransport(Mage::helper('smtppro')->getTransport());
        $template_id = $observer->getEvent()->getTemplate()->getId();
        if(in_array($template_id,$this->_mailgun)){
            $transport = Mage::helper('smtppro')->getMailgunTransport();
        }elseif($template_id=='sales_email_order_place_again') {
            $transport = Mage::helper('smtppro')->getPlaceTransport();
        }else{
            $transport = Mage::helper('smtppro')->getTransport();
        }
        $observer->getEvent()->getTransport()->setTransport($transport);
    }

    public function beforeSendQueue($observer) {
        Mage::helper('smtppro')->log($observer->getEvent()->getMail());
        $observer->getEvent()->getTransport()->setTransport(Mage::helper('smtppro')->getTransport());
    }
	
}
