<?php

/**
 * Controller for Checkout.com Webhooks
 *
 * Class CheckoutApi_ChargePayment_ApiController
 *
 * @version 20151113
 */
class CheckoutApi_ChargePayment_ApiController extends Mage_Core_Controller_Front_Action
{
    /**
     * Routing for webhooks from Checkout.com
     *
     * @url chargepayment/api/webhook/
     *
     * @version 20151113
     */
    public function webhookAction()
    {
        $modelWebhook   = Mage::getModel('chargepayment/webhook');

        $isDebugCard    = Mage::getModel('chargepayment/creditCard')->isDebug();
        $isDebugJs     = Mage::getModel('chargepayment/creditCardJs')->isDebug();
        $isDebugKit     = Mage::getModel('chargepayment/creditCardKit')->isDebug();
        $isDebugHosted  = Mage::getModel('chargepayment/hosted')->isDebug();

        $isDebug        = $isDebugCard || $isDebugJs || $isDebugKit || $isDebugHosted ? true : false;

        if ($isDebug) {
            Mage::log(file_get_contents('php://input'), null, CheckoutApi_ChargePayment_Model_Webhook::LOG_FILE);
            Mage::log(json_decode(file_get_contents('php://input')), null, CheckoutApi_ChargePayment_Model_Webhook::LOG_FILE);
        }

        if (!$this->getRequest()->isPost()) {
            $this->getResponse()->setHttpResponseCode(400);
            return;
        }

        $request        = new Zend_Controller_Request_Http();
        $key            = $request->getHeader('Authorization');

        if (!$modelWebhook->isValidPublicKey($key)) {
            $this->getResponse()->setHttpResponseCode(401);
            return;
        }

        $data = json_decode(file_get_contents('php://input'));

        if (empty($data)) {
            $this->getResponse()->setHttpResponseCode(400);
            return;
        }

        $eventType          = $data->eventType;

        if (!$modelWebhook->isValidResponse($data)) {
            $this->getResponse()->setHttpResponseCode(400);
            return;
        }

        switch ($eventType) {
            case CheckoutApi_ChargePayment_Model_Webhook::EVENT_TYPE_CHARGE_CAPTURED:
                $result = $modelWebhook->captureOrder($data);
                break;
            case CheckoutApi_ChargePayment_Model_Webhook::EVENT_TYPE_CHARGE_REFUNDED:
                $result = $modelWebhook->refundOrder($data);
                break;
            case CheckoutApi_ChargePayment_Model_Webhook::EVENT_TYPE_CHARGE_VOIDED:
                $result = $modelWebhook->voidOrder($data);
                break;
            case CheckoutApi_ChargePayment_Model_Webhook::EVENT_TYPE_INVOICE_CANCELLED:
                $result = $modelWebhook->voidOrder($data);
                break;
            default:
                $this->getResponse()->setHttpResponseCode(500);
                return;
        }

        $httpCode = $result ? 200 : 400;

        $this->getResponse()->setHttpResponseCode($httpCode);
    }

    /**
     * Action for verify charge by payment token
     *
     * @url chargepayment/api/callback/?cko-payment-token=payment_token
     *
     * @version 20160219
     */
    public function callbackAction() {
        $responseToken  = (string)$this->getRequest()->getParam('cko-payment-token');
        $session        = Mage::getSingleton('chargepayment/session_quote');
        $isLocalPayment = $session->isCheckoutLocalPaymentTokenExist($responseToken);

        $modelWebhook   = Mage::getModel('chargepayment/webhook');
        $helper         = Mage::helper('chargepayment');

        if ($responseToken) {

            if ($isLocalPayment) {
                $this->_redirect('chargepayment/api/complete', array('_query' => 'token=' . $responseToken));
                return;
            }

            $result = $modelWebhook->authorizeByPaymentToken($responseToken);

            if ($result['is_admin'] === false) {
                $redirectUrl    = 'checkout/onepage/success';

                if ($result['error'] === true) {
                    $redirectUrl = Mage::helper('checkout/url')->getCheckoutUrl();
                    Mage::getSingleton('core/session')->addError('Please check you card details and try again. Thank you');

                    if(!is_null($result['order_increment_id'])){
                        $order = Mage::getModel('sales/order')->loadByIncrementId($result['order_increment_id']);
                        $order->cancel();
                        $order->addStatusHistoryComment('Order has been cancelled.');
                        $order->save();

                        /* Restore quote session */
                        $helper->restoreQuoteSession($order);
                    }

                    $order->sendNewOrderEmail();
                    $this->_redirectUrl($redirectUrl);
                    return;
                }
                $this->_redirect($redirectUrl);
            }

            return;
        }
    }

    /**
     * Fail page
     *
     * @url checkout/url
     *
     * @version 20161012
     */
    public function failAction(){
        $session        = Mage::getSingleton('chargepayment/session_quote');
        $redirectUrl    = Mage::helper('checkout/url')->getCheckoutUrl();

        Mage::getSingleton('core/session')->addError('Please check your payment details and try again. Thank you');

        if(!is_null($session->LastOrderIncrementId)){
            $order = Mage::getModel('sales/order')->loadByIncrementId($session->LastOrderIncrementId);
            $order->cancel();
            $order->setStatus('canceled');
            $order->setState('canceled');
            $order->addStatusHistoryComment('Order has been cancelled.');
            $order->save();

            $helper             = Mage::helper('chargepayment');
            $helper->restoreQuoteSession($order);
        }

        return $this->_redirectUrl($redirectUrl);
    }

    /**
     * Local Payment Complete Page
     *
     * @url chargepayment/api/complete
     *
     * @return Mage_Core_Controller_Varien_Action
     *
     * @version 20160426
     */
    public function completeAction() {
        $responseToken  = (string)$this->getRequest()->getParam('token');

        if (!$responseToken) {
            $this->norouteAction();
            return;
        }

        $session        = Mage::getSingleton('chargepayment/session_quote');
        $isLocalPayment = $session->isCheckoutLocalPaymentTokenExist($responseToken);

        if (!$isLocalPayment) {
            $this->norouteAction();
            return;
        }

        /* Clear checkout */
        Mage::getSingleton('checkout/session')->clear();

        $cart = Mage::getModel('checkout/cart');
        $cart->truncate()->save();

        $session->removeCheckoutLocalPaymentToken($responseToken);

        $this->loadLayout();

        $this->getLayout()
            ->getBlock('head')
            ->setTitle($this->__('Local Payment Completed (Checkout.com)'));

        $this->renderLayout();
    }

    /**
     * Action for verify charge by card token
     *
     * @url chargepayment/api/hosted/
     */
    public function hostedAction() {
        $cardToken          = (string)$this->getRequest()->getParam('cko-card-token');
        $orderIncrementId   = (string)$this->getRequest()->getParam('cko-context-id');
        $order              = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
        $helper             = Mage::helper('chargepayment');

        if (!$order->getId()) {
            $this->norouteAction();
            return;
        }

        if (!$cardToken) {
            Mage::getSingleton('core/session')->addError('Your payment has been cancelled. Please enter your card details and try again.');
            $result = array('status' => 'error', 'redirect' => Mage::helper('checkout/url')->getCheckoutUrl());
            $order->cancel();
            $order->addStatusHistoryComment('Order has been cancelled.');
            $order->save();

            /* Restore quote session */
            $helper->restoreQuoteSession($order);

            $this->_redirectUrl($result['redirect']);
            return;
        }

        $hostedModel    = Mage::getModel('chargepayment/hosted');

        $result         = $hostedModel->authorizeByCardToken($order, $cardToken);
        $session        = Mage::getSingleton('chargepayment/session_quote');

        switch($result['status']) {
            case 'success':
                $session
                    ->setHostedPaymentRedirect(NULL)
                    ->setHostedPaymentParams(NULL)
                    ->setHostedPaymentConfig(NULL)
                    ->setSecretKey(NULL);

                /*如果订单成功，则发送订单确认邮件*/
                if($order->getStatus()=='processing'){
                    $order->sendNewOrderEmail();
                }

                $this->_redirect($result['redirect']);
                break;
            case '3d':
                $session
                    ->setHostedPaymentRedirect(NULL)
                    ->setHostedPaymentConfig(NULL)
                    ->setHostedPaymentParams(NULL);

                $this->_redirectUrl($result['redirect']);
                break;
            case 'error':
            default:
                Mage::getSingleton('core/session')->addError('Please check you card details and try again. Thank you');
                $order->cancel();
                $order->addStatusHistoryComment('Order has been cancelled.');
                $order->save();

                /* Restore quote session */
                $helper->restoreQuoteSession($order);

                $this->_redirectUrl($result['redirect']);
                break;
        }

        return $this;
    }

    /**
     * Redirect Action for Hosted Payment
     *
     * @url chargepayment/api/redirect
     *
     * @return Mage_Core_Controller_Varien_Action
     */
    public function redirectAction() {
        $session        = Mage::getSingleton('chargepayment/session_quote');
        $redirectUrl    = $session->getHostedPaymentRedirect();

        if (empty($redirectUrl)) {
            $this->norouteAction();
            return $this;
        }

        $this->loadLayout();
        $this->renderLayout();
    }
}
