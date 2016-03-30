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

        if (empty($id)) {
          $id = Mage::getSingleton('checkout/session')->getLastOrderId();
        }

        $order = Mage::getModel('sales/order')->load($id);
        $quote = Mage::getModel('sales/quote')->load($order->getQuoteId());

        if ($order->isEmpty() || $quote->isEmpty()) {
            $this->norouteAction();

            return;
        }

        $this->loadLayout();

        $block = Mage::app()->getLayout()->getBlock('mtpayment.order');

        $block->setOrder($order);
        $block->setInitPayment($this->getRequest()->getParam('initpayment'));

        $this->renderLayout();
    }
}
