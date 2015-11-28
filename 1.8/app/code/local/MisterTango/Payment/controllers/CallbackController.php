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

        /**
         * Debug purpose only.
         */
        //$hash = 'gOjTKaYmeTyoOQ\/tMa1eFXIg\/EUkLRGfScpf4NugyF1b5N7KJXh8D86KLdcIv0WiIBjpvELcNUlpD7gFwaVedQKnz20Er9FxeTHOm1Ry5+laD+f3xgof3jshhwh\/JTbOwb0EFkzxEQderYrzV0r6amdrl4Vnxm3h+VRQYesv7Ll9q9Mw\/mhHbdNlcP4MVZKhQ5baY+Y1nWM4Jzbi8me\/nyvHUOE981zQ\/5WEudCHYFR22LAmVCdmZ+dGwO\/hhYs5ZIFHgJDBf67d\/ALoUiwUhvaLBHdiPn09Cx06ft2m1uzZiNt\/RuNoyaYEqjs7Rw4LkweqD80E0mK0cYZiPRy2ZeeMyj1mvx\/rGKcVBvEX60WiafIMzqCV91EfVYO\/dwDH8o\/zI78IRWZxaGzMccRfKBZ\/pTU6PB7kqUtFkymBH\/c3IVrCASklcay2G0NyiKNy30jvbOdfZG4USck3QzrGFyuMQZ6Gpvi1Bg2PmK2OFGx+HMvvLjflRAgVl8sUK9rd4ILPTG2+bCsiHCUmi3Myo8slC1FWH4h2zxGkQn9lGiALjYu3wGn6JxYRe45vdQHsWUoKGFJv\/kxYsHkXiV\/RXsKq2HEpyyfwycDaAhKWp+fFBe\/ikrhp1azjH5cIrLVQ1acjzC8pACwhfyjz4TsB5Uk2ZwvCQNDeWQN96mn4ncjrn+9ZtjIGJgzPgrQ4SeF0O0wq15tWl6oHnrWJ1ICN5xOKWnxO6jxiycDA1kaVWxrEz33JFtRpYj\/70NPLK\/0Z5EOwDwtWTsd77b3XBzVFpA==';

        if ($hash !== false) {
            $data = json_decode(
                Mage::helper('mtpayment/utilities')->decrypt($hash, Mage::helper('mtpayment/data')->getSecretKey())
            );
            $data->custom = isset($data->custom) ? json_decode($data->custom) : null;

            if (isset($data->custom) && isset($data->custom->description)) {
                /*
                 * Debug purpose only
                 */
                //$data->custom->description = '3_1448626711';
                //$data->custom->data->amount = '630.00';

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
