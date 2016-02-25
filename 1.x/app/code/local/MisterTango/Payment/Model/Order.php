<?php

class MisterTango_Payment_Model_Order extends Mage_Payment_Model_Method_Abstract
{
    /**
     * @param $id_transaction
     * @param $amount
     * @param null $id_websocket
     * @return bool
     */
    public function openOrder($id_transaction, $amount, $id_websocket = null)
    {
        $transaction = explode('_', $id_transaction);

        if (count($transaction) == 2) {
            $id_cart = $transaction[0];

            $is_valid_order = parent::validateOrder(
                (int) $id_cart,
                (int) Configuration::get(self::NAME_OS_PENDING),
                (float) 0,
                $this->displayName,
                '',
                array(),
                null,
                true
            );

            $this->addTransaction($id_transaction, $id_websocket, $this->currentOrder, $amount);

            return $is_valid_order;
        }

        return false;
    }

    /**
     * @param $id_transaction
     * @param $amount
     */
    public function closeOrder($id_transaction, $amount)
    {
        $id_order = Db::getInstance()->getValue(
            'SELECT `id_order`
			FROM `'._DB_PREFIX_.'transactions_mistertango`
			WHERE `id_transaction` = \''.pSQL($id_transaction).'\''
        );

        if (empty($id_order)) {
            $this->openOrder($id_transaction, $amount);

            $id_order = Db::getInstance()->getValue(
                'SELECT `id_order`
                FROM `'._DB_PREFIX_.'transactions_mistertango`
                WHERE `id_transaction` = \''.pSQL($id_transaction).'\''
            );
        }

        $order = new Order($id_order);

        $state = Configuration::get('PS_OS_PAYMENT');

        $total_paid_real = $order->total_paid_real + $amount;

        if (bcdiv($order->total_paid, 1, 2) != bcdiv($total_paid_real, 1, 2)) {
            $state = Configuration::get('PS_OS_ERROR');
        }

        $order->addOrderPayment((float) $amount, $this->displayName, $id_transaction);
        $order->save();

        $new_history = new OrderHistory();
        $new_history->id_order = (int) $order->id;
        $new_history->changeIdOrderState((int) $state, $order, true);
        $new_history->addWithemail(true, array());
    }
}
