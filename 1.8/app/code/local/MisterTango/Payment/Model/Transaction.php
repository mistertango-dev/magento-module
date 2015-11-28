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

    /**
     * @param $id_order
     * @return array
     */
    public function getLastForOrder($id_order)
    {
        if (empty($id_order)) {
            return array();
        }

        $transaction = Db::getInstance()->getRow(
            'SELECT * FROM `'._DB_PREFIX_.'transactions_mistertango` WHERE `id_order` = \''.pSQL($id_order).'\''
        );

        return $transaction;
    }
}
