<?php

/**
 * Controller for Checkout.com Webhooks
 *
 * Class CheckoutApi_ChargePayment_ApiController
 *
 * @version 20151113
 */
class CheckoutApi_ChargePayment_OrderController extends Mage_Core_Controller_Front_Action
{
    public function placeAction() {
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
