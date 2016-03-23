<?php

/**
 * Class MisterTango_Payment_Block_Button
 */
class MisterTango_Payment_Block_Button extends Mage_Core_Block_Template
{

    /**
     * @return mixed
     */
    public function getCustomerEmail()
    {
        return Mage::getSingleton('checkout/session')
            ->getQuote()
            ->getBillingAddress()
            ->getEmail();
    }

    /**
     * @return string
     */
    public function getCurrentCurrencyIsoCode()
    {
        return Mage::app()->getStore()->getCurrentCurrencyCode();
    }

    /**
     * @return mixed
     */
    public function getTransactionId()
    {
        return Mage::helper('mtpayment/data')->generateTransactionId();
    }

    /**
     * @param bool|false $formatted
     * @return mixed
     */
    public function getGrandTotal($formatted = false)
    {
        $grandTotal = Mage::helper('checkout/cart')->getQuote()->getGrandTotal();

        return $formatted?Mage::helper('core')->formatPrice($grandTotal, false):$grandTotal;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if ($quote) {
            $payment = $quote->getPayment();
            if ($payment && $payment->getMethod() == 'mtpayment') {
                $this->setTemplate('mtpayment/onepage/review/button.phtml');
            }
        }

        return parent::_toHtml();
    }
}
