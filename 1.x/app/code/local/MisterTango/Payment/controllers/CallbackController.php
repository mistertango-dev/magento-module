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

        if (empty($hash)) {
            die('Error occurred: Empty hash');
        }

        $data = json_decode(
            Mage::helper('mtpayment/utilities')->decrypt(
                $hash,
                Mage::helper('mtpayment/data')->getSecretKey()
            )
        );
        $data->custom = isset($data->custom) ? json_decode($data->custom) : null;

        if (empty($data->custom) || empty($data->custom->description)) {
            die('Error occurred: Custom description is empty');
        }

        $callback = Mage::getModel('mtpayment/callback')->load($data->callback_uuid);

        if ($callback->isEmpty()) {
            try {
                Mage::helper('mtpayment/order')->close(
	                $data->custom->description,
                    $data->custom->data->amount
                );
            } catch (Exception $e) {
                die('Error occurred: ' . $e->getMessage());
            }

            $callback
                ->setId($data->callback_uuid)
                ->setData('transaction_id', $data->custom->description)
                ->setData('amount', $data->custom->data->amount)
                ->save();
        }
    }
}
