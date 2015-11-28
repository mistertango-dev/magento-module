<?php

/**
 * Class MisterTango_Payment_Block_Order
 */
class MisterTango_Payment_Block_Order extends Mage_Core_Block_Template
{

    /**
     * @var
     */
    private $order;

    /**
     *
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }
}
