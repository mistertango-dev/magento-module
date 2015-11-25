<?php

/**
 * Class MisterTango_Payment_Model_Transaction
 */
class MisterTango_Payment_Model_Transaction extends Mage_Payment_Model_Method_Abstract
{
    /**
     * @param $id_transaction
     * @param $id_websocket
     * @param $id_order
     * @param $amount
     */
    public function insert($id_transaction, $id_websocket, $id_order, $amount)
    {
        Db::getInstance()->insert(
            'transactions_mistertango',
            array(
                'id_transaction' => pSQL($id_transaction),
                'id_websocket' => pSQL($id_websocket),
                'id_order' => pSQL((int) $id_order),
                'amount' => pSQL((float) $amount),
            )
        );
    }

    /**
     * @param $id_order
     * @return array
     */
    public function getLast($id_order)
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
