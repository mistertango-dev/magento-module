<?php

namespace MisterTango\Payment\Controller\Order;

use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Model\Order;
use MisterTango\Payment\Block\Checkout;

/**
 * Class Review
 * @package MisterTango\Payment\Controller\Order
 */
class Review extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $_pageConfig;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * Review constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\Page\Config $pageConfig,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->_pageConfig = $pageConfig;
        $this->checkoutSession = $checkoutSession;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory ->create();

        /**
         * @var Checkout $blockInstance
         */
        $blockInstance = $resultPage->getLayout()->getBlock('checkoutBlock');
        $resultPage->getConfig()->getTitle()->set($blockInstance->getTitle());

        if (
            $blockInstance->hasOrder()
            && $blockInstance->getOrder()->getState() == Order::STATE_COMPLETE
            && $this->_objectManager->get('Magento\Checkout\Model\Session\SuccessValidator')->isValid()
        ) {
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_url->getUrl('checkout/onepage/success'));

            return $resultRedirect;
        }

        return $resultPage;
    }
}
