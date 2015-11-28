<?php

/**
 * Class MisterTango_Payment_Helper_Data
 */
class MisterTango_Payment_Helper_Data extends Mage_Payment_Helper_Data
{
    const XML_PATH_USERNAME = 'payment/mtpayment/mrtango_username';
    const XML_PATH_SECRET_KEY = 'payment/mtpayment/mrtango_secret_key';
    const XML_PATH_STATUS_PENDING = 'payment/mtpayment/status_pending';
    const XML_PATH_STATUS_SUCCESS = 'payment/mtpayment/status_success';
    const XML_PATH_STATUS_ERROR = 'payment/mtpayment/status_error';

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

    /**
     * @return mixed
     */
    public function getStatusPending()
    {
        return Mage::getStoreConfig(self::XML_PATH_STATUS_PENDING);
    }

    /**
     * @return mixed
     */
    public function getStatusSuccess()
    {
        return Mage::getStoreConfig(self::XML_PATH_STATUS_SUCCESS);
    }

    /**
     * @return mixed
     */
    public function getStatusError()
    {
        return Mage::getStoreConfig(self::XML_PATH_STATUS_ERROR);
    }
}
