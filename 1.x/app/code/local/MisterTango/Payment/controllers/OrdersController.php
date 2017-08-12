<?php

/**
 * Class MisterTango_Payment_OrdersController
 */
class MisterTango_Payment_OrdersController extends Mage_Core_Controller_Front_Action
{
    /**
     *
     */
    public function validateTransactionAction()
    {
        $isAjax = Mage::app()->getRequest()->isAjax();
        if ($isAjax) {
            $orderId = $this->getRequest()->getParam('order');
            $currentTransactionId = $this->getRequest()->getParam('transaction');
            $websocket = $this->getRequest()->getParam('websocket');
            $amount = $this->getRequest()->getParam('amount');

            $order = Mage::getModel('sales/order')->load($orderId);

            if ($order->isEmpty()) {
                $this->getResponse()->setBody(
                    Mage::helper('core')->jsonEncode(array(
                        'success' => false,
                        'error' => $this->__('Order is invalid')
                    ))
                );

                return;
            }

	        $existingTransactionId = Mage::getModel('mtpayment/transaction')
	            ->getCollection()
	            ->addFieldToFilter('transaction_id', $currentTransactionId)
	            ->getFirstItem()
	            ->getId();

            if (!$existingTransactionId) {
	            Mage::getModel('mtpayment/transaction')
	                ->setId($currentTransactionId)
	                ->setData('amount', $amount)
	                ->setData('order_id', $orderId)
	                ->setData('websocket', $websocket)
	                ->save();
            }

            $this->getResponse()->setBody(
                Mage::helper('core')->jsonEncode(array(
                    'success' => true,
                    'order' => $order->getId()
                ))
            );

            return;
        }
    }

    /**
     *
     */
    public function getHtmlTableOrderStatusesAction()
    {
        $isAjax = Mage::app()->getRequest()->isAjax();
        if ($isAjax) {

            $customer = Mage::helper('customer')->getCustomer();
            $order = Mage::getModel('sales/order')->load($this->getRequest()->getParam('order'));
            $quote = Mage::getModel('sales/quote')->load($order->getQuoteId());

            if ($order->isEmpty() || $quote->isEmpty() || $quote->getCustomerId() != $customer->getId()) {
                $this->getResponse()->setBody(
                    Mage::helper('core')->jsonEncode(array(
                        'success' => false,
                        'error' => $this->__('Bad parameters')
                    ))
                );
            }

            $this->loadLayout();
            $block = $this->getLayout()->createBlock('mtpayment/order')->setTemplate('mtpayment/order.phtml');

            $block->setOrder($order);

            $redirect = false;
            $session = Mage::getSingleton('checkout/session');
            $lastQuoteId = $session->getLastQuoteId();
            $lastOrderId = $session->getLastOrderId();
            $lastRecurringProfiles = $session->getLastRecurringProfileIds();

            if (($lastQuoteId || ($lastOrderId && !empty($lastRecurringProfiles))) && $order->getTotalPaid() > 0) {
                $redirect = Mage::helper('mtpayment/data')->getUrlSuccess();
            }

            $this->getResponse()->setBody(
                Mage::helper('core')->jsonEncode(array(
                    'success' => true,
                    'html' => $block->toHtml(),
                    'redirect' => $redirect
                ))
            );
        }
    }
}
