<?php

/**
 * Class MisterTango_Payment_CheckoutController
 */
class MisterTango_Payment_CheckoutController extends Mage_Core_Controller_Front_Action
{

    /**
     *
     */
    public function getPaymentDataAction()
    {
        $isAjax = Mage::app()->getRequest()->isAjax();
        if ($isAjax) {
            $transaction = Mage::helper('mtpayment/data')->getTransactionId();
            $customerEmail = Mage::getSingleton('checkout/session')->getQuote()->getBillingAddress()->getEmail();
            $amount = number_format(Mage::helper('checkout/cart')->getQuote()->getGrandTotal(), 2, '.', '');
            $currency = Mage::app()->getStore()->getCurrentCurrencyCode();
            $language = Mage::app()->getStore()->getLanguageCode();

            $this->getResponse()->setBody(
                Mage::helper('core')->jsonEncode(array(
                    'success' => true,
                    'transaction' => $transaction,
                    'customerEmail' => $customerEmail,
                    'amount' => $amount,
                    'currency' => $currency,
                    'language' => $language,
                ))
            );
        }
    }
}
