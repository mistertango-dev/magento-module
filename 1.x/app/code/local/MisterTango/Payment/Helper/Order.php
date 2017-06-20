<?php

/**
 * Class MisterTango_Payment_Helper_Order
 */
class MisterTango_Payment_Helper_Order extends Mage_Core_Helper_Abstract
{
    /**
     * @param $transactionId
     * @param $amount
     *
     * @throws Exception
     */
    public function close($transactionId, $amount)
    {
        $order = Mage::getModel('sales/order')->loadByIncrementId($transactionId);

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
