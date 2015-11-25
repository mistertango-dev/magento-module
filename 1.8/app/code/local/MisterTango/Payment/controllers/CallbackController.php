<?php

class MisterTango_Payment_CallbackController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();

        $this->renderLayout();

        /*$hash = Tools::getValue('hash');

        if ($hash !== false) {
            $data = json_decode(
                Mage::helper('mtpayment/utilities')->decode($hash, Configuration::get(MisterTango::NAME_SECRET_KEY))
            );
            $data->custom = isset($data->custom) ? Tools::json_decode($data->custom) : null;

            if (isset($data->custom) && isset($data->custom->description)) {
                $message = '';
                $transaction = explode('_', $data->custom->description);

                if (count($transaction) == 2) {
                    if ($mrTango->isNotDuplicateCallback($data->callback_uuid)) {
                        $mrTango->addCallback($data);

                        try {
                            $id_cart = $transaction[0];
                            $id_transaction = implode('_', $transaction);

                            $mrTango->setTransactionDetail(
                                $id_transaction,
                                $data->custom->type
                            );

                            $mrTango->closeOrder(
                                $id_transaction,
                                $data->custom->data->amount
                            );
                        } catch (Exception $e) {
                            die('OK');
                        }
                    }
                }
            }
        }*/
    }
}
