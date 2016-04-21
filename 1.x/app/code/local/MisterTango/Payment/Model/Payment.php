<?php

/**
 * Class MisterTango_Payment_Model_Payment
 */
class MisterTango_Payment_Model_Payment extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'mtpayment';

    /**
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        if (Mage::helper('mtpayment/data')->isStandardMode()) {
            return Mage::getUrl(
                'mtpayment/information',
                array(
                    '_secure' => true,
                    'initpayment' => true
                )
            );
        }

        return '#mtpayment';
    }
}
