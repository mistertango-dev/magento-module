<?php

/**
 * Class MisterTango_Payment_InformationController
 */
class MisterTango_Payment_InformationController extends Mage_Core_Controller_Front_Action
{
    /**
     *
     */
    public function indexAction()
    {
        $id = $this->getRequest()->getParam('order');
        $session = Mage::getSingleton('checkout/session');
        $initPayment = $this->getRequest()->getParam('initpayment');

        if (empty($id)) {
          $id = $session->getLastOrderId();
        }

        $order = Mage::getModel('sales/order')->load($id);
        $quote = Mage::getModel('sales/quote')->load($order->getQuoteId());

        if ($order->isEmpty() || $quote->isEmpty()) {
            $this->norouteAction();

            return;
        }

        // Lets clear session if session quote is equal to specified order qoute and standard redirect is not enabled
        if (
            $session->getLastQuoteId() == $order->getQuoteId()
            && !Mage::helper('mtpayment/data')->isStandardRedirect()
        ) {
            $session->clear();
        }

        $this->loadLayout();

        $block = Mage::app()->getLayout()->getBlock('mtpayment.order');

        $block->setOrder($order);
        $block->setInitPayment($initPayment);

        $this->renderLayout();
    }
}
