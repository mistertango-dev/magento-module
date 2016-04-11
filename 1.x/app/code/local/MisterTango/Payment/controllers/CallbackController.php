<?php

/**
 * Class MisterTango_Payment_CallbackController
 */
class MisterTango_Payment_CallbackController extends Mage_Core_Controller_Front_Action
{

    /**
     *
     */
    public function indexAction()
    {
        $this->loadLayout();

        $this->renderLayout();

        $hash = $this->getRequest()->getParam('hash');

        if ($hash !== false) {
            $data = json_decode(
                Mage::helper('mtpayment/utilities')->decrypt($hash, Mage::helper('mtpayment/data')->getSecretKey())
            );
            $data->custom = isset($data->custom) ? json_decode($data->custom) : null;

            if (isset($data->custom) && isset($data->custom->description)) {

                $transaction = explode('_', $data->custom->description);

                if (count($transaction) == 2) {
                    $callback = Mage::getModel('mtpayment/callback')->load($data->callback_uuid);

                    if ($callback->isEmpty()) {
                        $callback
                            ->setId($data->callback_uuid)
                            ->setData('transaction_id', $data->custom->description)
                            ->setData('amount', $data->custom->data->amount)
                            ->save();

                        try {
                            $transactionId = implode('_', $transaction);

                            Mage::helper('mtpayment/order')->close(
                                $transactionId,
                                $data->custom->data->amount
                            );
                        } catch (Exception $e) {
                            return;
                        }
                    }
                }
            }
        }
    }
}
