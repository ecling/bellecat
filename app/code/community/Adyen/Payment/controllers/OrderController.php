<?php

/**
 * Adyen Payment Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Adyen
 * @package    Adyen_Payment
 * @copyright    Copyright (c) 2011 Adyen (http://www.adyen.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Payment Gateway
 * @package    Adyen_Payment
 * @author     Adyen
 * @property   Adyen B.V
 * @copyright  Copyright (c) 2014 Adyen BV (http://www.adyen.com)
 */
class Adyen_Payment_OrderController extends Mage_Core_Controller_Front_Action
{
    /**
     * @var Adyen_Payment_Model_Adyen_Data_Server
     */
    const SOAP_SERVER = 'Adyen_Payment_Model_Adyen_Data_Server_Notification';

    const OPENINVOICE_SOAP_SERVER = 'Adyen_Payment_Model_Adyen_Data_Server_Openinvoice';

    /**
     * Redirect Block
     * need to be redeclared
     */
    protected $_redirectBlockType = 'adyen/redirect';

    public function placeAction()
    {
        try {
            $session = $this->_getCheckout();

            if($order_id = $this->getRequest()->getParam('id',null)){
                $order = Mage::getModel('sales/order')->load($order_id);
                $quoteId = $order->getQuoteId();
            }else{
                return false;
            }

            //redirect only if this order is never recorded
            $hasEvent = Mage::getResourceModel('adyen/adyen_event')
                ->getLatestStatus($order->getIncrementId());

            if (!empty($hasEvent) || !$order->getId() || empty($quoteId)) {
                $this->_redirect('/');
                return $this;
            }

            // redirect to payment page
            $this->getResponse()->setBody(
                $this->getLayout()
                    ->createBlock($this->_redirectBlockType)
                    ->setOrder($order)
                    ->toHtml()
            );

            //$session->setQuoteId(null);
        } catch (Exception $e) {
            $session->addException($e, Mage::helper('adyen')->__($e->getMessage()));
            $this->cancel();
        }
    }

    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }
}
