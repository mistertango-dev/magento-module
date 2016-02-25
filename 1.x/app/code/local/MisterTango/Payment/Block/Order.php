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
     *
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
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
     * @return bool
     */
    public function isAllowedDifferentPayment()
    {
        $allow = true;

        foreach ($this->getOrder()->getStatusHistoryCollection(true) as $_item) {
            if ($_item->getStatus() != Mage::helper('mtpayment/data')->getStatusPending()) {
                $allow = false;
            }
        }

        return $allow;
    }
}
