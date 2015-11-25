<?php

/**
 * Class MisterTango_Payment_Helper_Data
 */
class MisterTango_Payment_Helper_Data extends Mage_Payment_Helper_Data
{
    const XML_PATH_USERNAME = 'payment/mtpayment/mrtango_username';
    const XML_PATH_SECRET_KEY = 'payment/mtpayment/mrtango_secret_key';

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return Mage::getStoreConfig(self::XML_PATH_USERNAME);
    }

    /**
     * @return mixed
     */
    public function getSecretKey()
    {
        return Mage::getStoreConfig(self::XML_PATH_SECRET_KEY);
    }

    /**
     * @return string
     */
    public function generateTransactionId()
    {
        return Mage::getSingleton('checkout/session')->getQuoteId() . '_' . time();
    }
}
