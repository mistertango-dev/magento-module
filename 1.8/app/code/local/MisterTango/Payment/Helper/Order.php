<?php

/**
 * Class MisterTango_Payment_Helper_Order
 */
class MisterTango_Payment_Helper_Order extends Mage_Core_Helper_Abstract
{
    /**
     * @param $transactionId
     * @param $amount
     * @param null $websocket
     * @return bool|mixed
     */
    public function open($transactionId, $amount, $websocket = null)
    {
        $transaction = explode('_', $transactionId);

        if (count($transaction) == 2) {
            $quoteId = $transaction[0];

            $quote = Mage::getModel('sales/quote')->load($quoteId);
            $quote->collectTotals();
            $checkout = Mage::getSingleton('checkout/type_onepage');
            $checkout
                ->setQuote($quote)
                ->saveOrder();

            $order = Mage::getModel('sales/order')->load($checkout->getLastOrderId(), 'increment_id');

            Mage::getModel('mtpayment/transaction')
                ->setId($transactionId)
                ->setData('amount', $amount)
                ->setData('order_id', $order->getId())
                ->setData('websocket', $websocket)
                ->save();

            return $order->getId();
        }

        return null;
    }

    /**
     * @param $transactionId
     * @param $amount
     * @throws Exception
     */
    public function close($transactionId, $amount)
    {
        $orderId = Mage::getModel('mtpayment/transaction')
            ->getCollection()
            ->addFieldToFilter('transaction_id', $transactionId)
            ->getFirstItem()
            ->getOrderId();

        if (empty($orderId)) {
            $orderId = $this->open($transactionId, $amount);
        }

        $order = Mage::getModel('sales/order')->load($orderId);

        $payment = $order->getPayment();
        $payment->setTransactionId($transactionId);
        $payment->save();

        //@todo calculate total paid real from transactions
        $totalPaidReal = $amount;

        $state = Mage_Sales_Model_Order::STATE_HOLDED;
        $status = Mage::helper('mtpayment/data')->getStatusError();

        if (bcdiv($order->getGrandTotal(), 1, 2) == bcdiv($totalPaidReal, 1, 2)) {
            $state = Mage_Sales_Model_Order::STATE_PROCESSING;
            $status = Mage::helper('mtpayment/data')->getStatusSuccess();
        }

        //@todo generate invoice if possible
        //@todo create proper transactions
        $order->setState($state, $status);
        $order->save();
    }
}
