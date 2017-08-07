<?php

namespace MisterTango\Payment\Controller\Callback;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use MisterTango\Payment\Model\Config;
use MisterTango\Payment\Model\Utils;

/**
 * Class Index
 * @package MisterTango\Payment\Controller\Callback
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var CurrencyInterface
     */
    private $_localeCurrency;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var OrderInterface
     */
    private $order;

    /**
     * @var OrderSender
     */
    private $orderSender;

    /**
     * Index constructor.
     * @param Context $context
     * @param Config $config
     * @param OrderInterface $order
     * @param OrderSender $orderSender
     */
    public function __construct(
        Context $context,
        CurrencyInterface $localeCurrency,
        Config $config,
        OrderInterface $order,
        OrderSender $orderSender
    ) {
        parent::__construct($context);

        $this->_localeCurrency = $localeCurrency;
        $this->config = $config;
        $this->order = $order;
        $this->orderSender = $orderSender;
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $hash = $this->getRequest()->getParam('hash');
        if (empty($hash)) {
            die('Error occurred: Empty hash');
        }

        $data = json_decode(
            Utils::decrypt(
                $hash,
                $this->config->getSecretKey()
            )
        );
        $data->custom = isset($data->custom) ? json_decode($data->custom) : null;
        if (empty($data->custom) || empty($data->custom->description)) {
            die('Error occurred: Custom description is empty');
        }

        $transaction = explode('_', $data->custom->description);
        if (count($transaction) != 2) {
            die('Error occurred: Transaction code is incorrect');
        }

        $order = $this->order->loadByIncrementId($transaction[0]);
        if (empty($order) || !$order instanceof Order) {
            die('Error occurred: Such order does not exist');
        }

        if ($data->custom->data->currency != $order->getOrderCurrencyCode()) {
            die('Error occurred: Currency codes does not match');
        }

        $transactionAmount = bcdiv($data->custom->data->amount, 1, 2);
        $orderGrandTotal = bcdiv($order->getGrandTotal(), 1, 2);
        if ($transactionAmount !== $orderGrandTotal) {
            die('Error occurred: Payment amount does not match to grand total');
        }

        if ($order->getState() == Order::STATE_PROCESSING) {
            try {
                $payment = $order->getPayment();
                if (empty($payment)) {
                    throw new \Exception('Order must have a valid payment');
                }

                $payment
                    ->setTransactionId($data->custom->description)
                    ->setPreparedMessage(
                        __(
                            'MisterTango payment "%1".',
                            $this
                                ->_localeCurrency
                                ->getCurrency($order->getOrderCurrencyCode())
                                ->toCurrency($transactionAmount)
                        )
                    )
                    ->setIsTransactionClosed(0)
                    ->registerCaptureNotification($transactionAmount)
                ;

                $order->save();

                $invoice = $payment->getCreatedInvoice();
                if ($invoice && !$order->getEmailSent()) {
                    $this->orderSender->send($order);
                    $order
                        ->addStatusHistoryComment(
                            __('You notified customer about invoice #%1.', $invoice->getIncrementId())
                        )
                        ->setIsCustomerNotified(true)
                        ->save()
                    ;
                }
            } catch (\Exception $e) {
                die('Error occurred: '.$e->getMessage());
            }
        }

        die('OK');
    }
}
