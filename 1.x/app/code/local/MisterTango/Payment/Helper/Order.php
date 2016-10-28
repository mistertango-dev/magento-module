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
     * @return mixed
     * @throws Exception
     */
    public function open($transactionId, $amount, $websocket = null)
    {
        $orderId = Mage::getModel('mtpayment/transaction')
            ->getCollection()
            ->addFieldToFilter('transaction_id', $transactionId)
            ->getFirstItem()
            ->getOrderId();

        if ($orderId) {
            return $orderId;
        }

        $transaction = explode('_', $transactionId);

        if (count($transaction) == 2) {
            $quoteId = $transaction[0];

            $quote = Mage::getModel('sales/quote')->load($quoteId);

            if ($quote === null) {
                throw new Exception('Quote is required to process MisterTango open order');
            }

            $service = Mage::getModel('sales/service_quote', $quote->collectTotals());
            $service->submitAll();

            $order = $service->getOrder();

            $payment = $quote->getPayment();

            if (
                $payment
                && Mage::helper('mtpayment/data')->isStandardMode()
                && $order->getEmailSent() != '1'
                && $order->getCanSendNewEmailFlag()
            ) {
                try {
                    $order->sendNewOrderEmail();
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }

            Mage::getModel('mtpayment/transaction')
                ->setId($transactionId)
                ->setData('amount', $amount)
                ->setData('order_id', $order->getId())
                ->setData('websocket', $websocket)
                ->save();

            Mage::getSingleton('checkout/cart')->truncate();

            return $order->getId();
        }

        throw new Exception('Unable to determinate order ID');
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

        $totalPaidReal = bcdiv($amount, 1, 2);

        $message = Mage::helper('mtpayment')->__(
        	'MisterTango payment "%s".',
	        Mage::app()->getLocale()->currency($order->getOrderCurrencyCode())->toCurrency($totalPaidReal)
        );

	    $payment = $order->getPayment();

        if (empty($payment)) {
            throw new Exception('Order must have a valid payment');
        }

	    $payment
		    ->setTransactionId($transactionId)
            ->setPreparedMessage($message)
            ->setIsTransactionClosed(0)
            ->registerCaptureNotification($totalPaidReal);

	    $order->save();

	    $invoice = $payment->getCreatedInvoice();
        if ($invoice) {
	        $invoice->sendEmail();
        }
    }
}
