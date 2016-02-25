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
        $order = Mage::getModel('sales/order')->load($this->getRequest()->getParam('order'));
        $quote = Mage::getModel('sales/quote')->load($order->getQuoteId());

        if ($order->isEmpty() || $quote->isEmpty()) {
            $this->norouteAction();

            return;
        }

        $this->loadLayout();

        Mage::app()->getLayout()->getBlock('mtpayment.order')->setOrder($order);

        $this->renderLayout();
    }
}
