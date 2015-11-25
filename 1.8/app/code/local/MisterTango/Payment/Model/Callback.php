<?php

/**
 * Class MisterTango_Payment_Model_Callback
 */
class MisterTango_Payment_Model_Callback extends Mage_Payment_Model_Method_Abstract
{
    /**
     * @param $data
     */
    public function insert($data)
    {
        Db::getInstance()->insert(
            'callbacks_mistertango',
            array(
                'uuid_callback' => pSQL($data->callback_uuid),
                'id_transaction' => pSQL($data->custom->description),
                'amount' => pSQL($data->custom->data->amount),
            )
        );
    }

    /**
     * @param $uuid
     * @return bool
     */
    public function isNotDuplicate($uuid)
    {
        $has_duplicate = (bool) Db::getInstance()->getValue(
            'SELECT 1 FROM `'._DB_PREFIX_.'callbacks_mistertango`
            WHERE `uuid_callback` = \''.pSQL($uuid).'\''
        );

        return $has_duplicate ? false : true;
    }
}
