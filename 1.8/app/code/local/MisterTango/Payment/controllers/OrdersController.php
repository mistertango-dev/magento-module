<?php

/**
 * Class MisterTango_Payment_OrdersController
 */
class MisterTango_Payment_OrdersController extends Mage_Core_Controller_Front_Action
{

    /**
     *
     */
    public function validateOrderAction()
    {
        $isAjax = Mage::app()->getRequest()->isAjax();
        if ($isAjax) {
            if (!Mage::helper('customer')->isLoggedIn()) {
                $this->getResponse()->setBody(
                    Mage::helper('core')->jsonEncode(array(
                        'success' => false,
                        'error' => $this->__('No customer is logged in')
                    ))
                );

                return;
            }

            $transactionId = $this->getRequest()->getParam('transaction');
            $websocket = $this->getRequest()->getParam('websocket');
            $amount = $this->getRequest()->getParam('amount');

            $orderId = Mage::helper('mtpayment/order')->open($transactionId, $amount, $websocket);

            if (isset($orderId)) {
                Mage::getSingleton('checkout/session')->clear();
                Mage::getSingleton('checkout/session')->clearHelperData();

                $this->getResponse()->setBody(
                    Mage::helper('core')->jsonEncode(array(
                        'success' => true,
                        'order' => $orderId
                    ))
                );

                return;
            }

            $this->getResponse()->setBody(
                Mage::helper('core')->jsonEncode(array(
                    'success' => false,
                    'error' => $this->__('Invalid transaction')
                ))
            );

            return;
        }
    }

    /**
     *
     */
    public function validateTransactionAction()
    {
        $isAjax = Mage::app()->getRequest()->isAjax();
        if ($isAjax) {
            if (!Mage::helper('customer')->isLoggedIn()) {
                $this->getResponse()->setBody(
                    Mage::helper('core')->jsonEncode(array(
                        'success' => false,
                        'error' => $this->__('No customer is logged in')
                    ))
                );

                return;
            }

            $orderId = $this->getRequest()->getParam('order');
            $transactionId = $this->getRequest()->getParam('transaction');
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

            Mage::getModel('mtpayment/transaction')
                ->setId($transactionId)
                ->setData('amount', $amount)
                ->setData('order_id', $orderId)
                ->setData('websocket', $websocket)
                ->save();

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
    public function getHtmlTableOrderStatesAction()
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

            $this->getResponse()->setBody(
                Mage::helper('core')->jsonEncode(array(
                    'success' => true,
                    'html' => $block->toHtml()
                ))
            );
        }
    }
}
