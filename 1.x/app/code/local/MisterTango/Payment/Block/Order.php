<?php

/**
 * Class MisterTango_Payment_Block_Order
 */
class MisterTango_Payment_Block_Order extends Mage_Core_Block_Template
{

    /**
     * @var
     */
    private $order;

    /**
     * @var bool
     */
    private $initPayment = false;

    /**
     *
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param $initPayment
     * @return $this
     */
    public function setInitPayment($initPayment)
    {
        $this->initPayment = (bool)$initPayment;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isInitPayment()
    {
        return $this->initPayment;
    }

    /**
     * @return mixed
     */
    public function getCustomerEmail()
    {
        if (empty($this->order)) {
            return null;
        }

        $quote = Mage::getModel('sales/quote')->load($this->order->getQuoteId());

        return $quote->getBillingAddress()->getEmail();
    }

    /**
     * @param $orderId
     * @return mixed
     */
    public function getWebsocket($orderId)
    {
        return Mage::getModel('mtpayment/transaction')
            ->getCollection()
            ->addFieldToFilter('order_id', $orderId)
            ->getFirstItem()
            ->getWebsocket();
    }

    /**
     * @todo: this method should be reviewed
     *
     * @param $orderId
     * @return bool
     */
    public function isPaid($orderId)
    {
        $transactionId = Mage::getModel('mtpayment/transaction')
            ->getCollection()
            ->addFieldToFilter('order_id', $orderId)
            ->getFirstItem()
            ->getTransactionId();

        if (empty($transactionId)) {
            return false;
        }

        $callbackUuid = Mage::getModel('mtpayment/callback')
            ->getCollection()
            ->addFieldToFilter('transaction_id', $transactionId)
            ->getFirstItem()
            ->getCallbackUuid();

        if (empty($callbackUuid)) {
            return false;
        }

        return true;
    }
}
