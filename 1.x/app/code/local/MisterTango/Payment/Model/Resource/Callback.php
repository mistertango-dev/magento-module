<?php

/**
 * Class MisterTango_Payment_Model_Callback
 */
class MisterTango_Payment_Model_Resource_Callback extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Primary key auto increment flag
     *
     * @var bool
     */
    protected $_isPkAutoIncrement = false;

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('mtpayment/callback', 'callback_uuid');
    }
}
