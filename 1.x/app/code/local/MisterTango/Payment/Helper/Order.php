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
     *
     * @return bool|mixed
     */
    public function open($transactionId, $amount, $websocket = null)
    {
        $orderId = Mage::getModel('mtpayment/transaction')
            ->getCollection()
            ->addFieldToFilter('transaction_id', $transactionId)
            ->getFirstItem()
            ->getOrderId();

        $transaction = explode('_', $transactionId);

        if (count($transaction) == 2) {
            $quoteId = $transaction[0];

            $quote = Mage::getModel('sales/quote')->load($quoteId);

            if ($quote === null) {
                Mage::logException('Quote is required to process MisterTango open order');
            }

            $quote
                ->collectTotals()
                ->setIsActive(false)
                ->save();

            $order = Mage::getModel('sales/order')->loadByIncrementId($quote->getReservedOrderId());

            if (empty($orderId)) {
                Mage::getModel('mtpayment/transaction')
                    ->setId($transactionId)
                    ->setData('amount', $amount)
                    ->setData('order_id', $order->getId())
                    ->setData('websocket', $websocket)
                    ->save();
            }

            Mage::getSingleton('checkout/cart')->truncate();

            return $order->getId();
        }

        return $orderId;
    }

    /**
     * @param $transactionId
     * @param $amount
     *
     * @throws Exception
     */
    public function close($transactionId, $amount)
    {
        $orderId = $this->open($transactionId, $amount);

        $order = Mage::getModel('sales/order')->load($orderId);

        if ($order && Mage::helper('mtpayment/data')->isStandardMode() && $order->getCanSendNewEmailFlag()) {
            try {
                $order->sendNewOrderEmail();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        //@todo calculate total paid real from transactions
        $totalPaidReal = bcdiv($amount, 1, 2);

        $state = Mage_Sales_Model_Order::STATE_HOLDED;
        $status = Mage::helper('mtpayment/data')->getStatusError();
        $message = Mage::app()->getLocale()->currency($order->getOrderCurrencyCode())->toCurrency($totalPaidReal);

        // Save transaction so client can track payments. Message is payment amount.
        $payment = $order->getPayment();
        if (
            isset($payment)
            && is_callable(array($payment, 'setTransactionId'))
            && is_callable(array($payment, 'addTransaction'))
        ) {
            $payment->setTransactionId($transactionId);
            $payment->addTransaction(
                Mage_Sales_Model_Order_Payment_Transaction::TYPE_ORDER,
                null,
                false,
                $message
            );
            $payment->save();
        }

        // If everything is correct, set state and create invoice
        if (bcdiv($order->getGrandTotal(), 1, 2) == $totalPaidReal) {
            $state = Mage_Sales_Model_Order::STATE_PROCESSING;
            $status = Mage::helper('mtpayment/data')->getStatusSuccess();

            // Save invoice
            $invoice = $order->prepareInvoice();
            $invoice->setGrandTotal($totalPaidReal);
            $invoice->pay();
            $invoice->save();
        }

        // Save order
        $order->setState($state, $status);
        $order->save();
    }
}
