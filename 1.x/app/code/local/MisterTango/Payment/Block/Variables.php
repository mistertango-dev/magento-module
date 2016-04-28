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
}
