<?php

/**
 * Class MisterTango_Payment_Helper_Utilities
 */
class MisterTango_Payment_Helper_Utilities extends Mage_Core_Helper_Abstract
{
    /**
     * @param $encoded_text
     * @param $key
     * @return string
     */
    function decrypt($encoded_text, $key)
    {
        if (strlen($key) == 30)
            $key .= "\0\0";

        $encoded_text = trim($encoded_text);
        $ciphertext_dec = base64_decode($encoded_text);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $iv_dec = substr($ciphertext_dec, 0, $iv_size);

        $ciphertext_dec = substr($ciphertext_dec, $iv_size);
        $sResult = @mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);

        return trim($sResult);
    }
}
