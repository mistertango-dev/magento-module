<?php

namespace MisterTango\Payment\Block;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use MisterTango\Payment\Model\Config;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * Class Checkout
 * @package MisterTango\Payment\Block
 */
class Checkout extends Template
{
    /**
     * @var string
     */
    private $locale;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var CartInterface
     */
    private $quote;

    /**
     * @var Order
     */
    private $order;

    /**
     * Info constructor.
     * @param Template\Context $context
     * @param array $data
     * @param ResolverInterface $localeResolver
     * @param Config $config
     * @param CartRepositoryInterface $quoteRepository
     * @throws NotFoundException
     */
    public function __construct(
        Template\Context $context,
        array $data = [],
        ResolverInterface $localeResolver,
        Config $config,
        CartRepositoryInterface $quoteRepository,
        OrderInterface $order
    ) {
        parent::__construct($context, $data);
        $this->locale = $localeResolver->getLocale();
        $this->config = $config;
        $this->quote = $quoteRepository->get($this->getRequest()->getParam('quote_id'));
        $this->order = $order->loadByIncrementId($this->quote->getReservedOrderId());

        if (empty($this->quote)) {
            throw new NotFoundException(__('Quote not found.'));
        }
    }

    /**
     * @return bool
     */
    public function isAutoOpen()
    {
        return $this->getRequest()->getParam('auto') == 'open';
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return \Magento\Quote\Api\Data\CartInterface
     */
    public function getQuote()
    {
        return $this->quote;
    }

    /**
     * @return bool
     */
    public function hasOrder()
    {
        return isset($this->order);
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return bool
     */
    public function isOrderPaid()
    {
        return $this->hasOrder() && $this->getOrder()->getTotalPaid() > 0;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTitle()
    {
        return __('Order #%1 review', $this->getQuote()->getReservedOrderId());
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->quote->getReservedOrderId() . '_' . date('YmdHis');
    }

    /**
     * @return string
     */
    public function getTransactionEmail()
    {
        return $this->quote->getCustomerEmail();
    }

    /**
     * @return float
     */
    public function getTransactionAmount()
    {
        return $this->quote->getGrandTotal();
    }

    /**
     * @return string
     */
    public function getTransactionCurrency()
    {
        return $this->quote->getQuoteCurrencyCode();
    }
}
