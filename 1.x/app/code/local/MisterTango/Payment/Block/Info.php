<?php

/**
 * Class MisterTango_Payment_Block_Info
 */
class MisterTango_Payment_Block_Info extends Mage_Payment_Block_Info
{
	/**
	 * @return string
	 */
    public function getMethodCode()
    {
        return $this->getInfo()->getMethodInstance()->getCode();
    }
}
