<?php

namespace MisterTango\Payment\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 * @package MisterTango\Payment\Model
 */
class Config
{
    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->_scopeConfig->getValue('payment/mtpayment/username', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getSecretKey()
    {
        return $this->_scopeConfig->getValue('payment/mtpayment/secret_key', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getCallbackUrl()
    {
        $callbackUrl = trim(
            $this->_scopeConfig->getValue('payment/mtpayment/callback_url', ScopeInterface::SCOPE_STORE)
        );

        return empty($callbackUrl) ? null : $callbackUrl;
    }
}
