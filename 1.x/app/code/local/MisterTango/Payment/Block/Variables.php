<?php

/**
 * Class MisterTango_Payment_Block_Variables
 */
class MisterTango_Payment_Block_Variables extends Mage_Core_Block_Template
{

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return Mage::helper('mtpayment/data')->getUsername();
    }

    /**
     * @return mixed
     */
    public function getSecretKey()
    {
        return Mage::helper('mtpayment/data')->getSecretKey();
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return Mage::helper('mtpayment/data')->isStandardRedirect()
            ?Mage::getUrl('checkout/onepage/success', array('_secure' => true))
            :Mage::getUrl('mtpayment/information', array('_secure' => true));
    }
}
