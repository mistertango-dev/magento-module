<?php

/**
 * Class MisterTango_Payment_Model_Transaction
 */
class MisterTango_Payment_Model_Transaction extends Mage_Core_Model_Abstract
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('mtpayment/transaction');
    }
}
