<?php
/**
 * Copyright © Boolfly. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    info@boolfly.com
 * @project   Momo Wallet
 */

declare(strict_types=1);

namespace Boolfly\MomoWallet\Controller\Payment;

use Boolfly\MomoWallet\Gateway\Helper\TransactionReader;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Webapi\Exception;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectFactory;
use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\PaymentFailuresInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class Start implements ActionInterface
{
    /**
     * @var CommandPoolInterface
     */
    private CommandPoolInterface $commandPool;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var PaymentDataObjectFactory
     */
    private PaymentDataObjectFactory $paymentDataObjectFactory;

    /**
     * @var Session
     */
    private Session $checkoutSession;

    /**
     * @var PaymentFailuresInterface
     */
    private PaymentFailuresInterface $paymentFailures;

    /**
     * @var OrderRepositoryInterface
     */
    private OrderRepositoryInterface $orderRepository;

    /**
     * @var ResultFactory
     */
    private ResultFactory $resultFactory;

    /**
     * @var ObjectManagerInterface
     */
    private ObjectManagerInterface $objectManager;

    /**
     * @var ManagerInterface
     */
    private ManagerInterface $messageManager;

    /**
     * @var RedirectInterface
     */
    private RedirectInterface $redirect;

    /**
     * @var ResponseInterface
     */
    private ResponseInterface $response;

    /**
     * Start constructor.
     *
     * @param Context $context
     * @param CommandPoolInterface $commandPool
     * @param LoggerInterface $logger
     * @param OrderRepositoryInterface $orderRepository
     * @param PaymentDataObjectFactory $paymentDataObjectFactory
     * @param Session $checkoutSession
     * @param PaymentFailuresInterface|null $paymentFailures
     */
    public function __construct(
        Context $context,
        CommandPoolInterface $commandPool,
        LoggerInterface $logger,
        OrderRepositoryInterface $orderRepository,
        PaymentDataObjectFactory $paymentDataObjectFactory,
        Session $checkoutSession,
        PaymentFailuresInterface $paymentFailures = null
    ) {
        $this->commandPool = $commandPool;
        $this->logger = $logger;
        $this->paymentDataObjectFactory = $paymentDataObjectFactory;
        $this->checkoutSession = $checkoutSession;
        $this->resultFactory = $context->getResultFactory();
        $this->objectManager = $context->getObjectManager();
        $this->messageManager = $context->getMessageManager();
        $this->redirect = $context->getRedirect();
        $this->response = $context->getResponse();
        $this->paymentFailures = $paymentFailures ?: $this->objectManager->get(PaymentFailuresInterface::class);
        $this->orderRepository = $orderRepository;
    }

    /**
     * Execute
     *
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $controllerResult = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            $orderId = $this->checkoutSession->getLastOrderId();
            if ($orderId) {
                /** @var Order $order */
                $order   = $this->orderRepository->get($orderId);
                $payment = $order->getPayment();
                ContextHelper::assertOrderPayment($payment);
                $paymentDataObject = $this->paymentDataObjectFactory->create($payment);
                $commandResult     = $this->commandPool->get('get_pay_url')->execute(
                    [
                        'payment' => $paymentDataObject,
                        'amount' => $order->getTotalDue(),
                    ]
                );

                $payUrl = TransactionReader::readPayUrl($commandResult->get());
                if ($payUrl) {
                    $this->response->setRedirect($payUrl);
                }
            }
        } catch (\Exception $e) {
            $this->paymentFailures->handle((int)$this->checkoutSession->getLastQuoteId(), $e->getMessage());
            $this->logger->critical($e);

            $this->messageManager->addErrorMessage(__('Sorry, but something went wrong.'));
            return $this->redirect('checkout/cart/*');
        }
    }

    /**
     * Get error response
     *
     * @param ResultInterface $controllerResult
     * @return ResultInterface
     */
    private function getErrorResponse(ResultInterface $controllerResult): ResultInterface
    {
        $controllerResult->setHttpResponseCode(Exception::HTTP_BAD_REQUEST);
        $controllerResult->setData(['message' => __('Sorry, but something went wrong')]);
        return $controllerResult;
    }

    /**
     * Set redirect into response
     *
     * @param string $path
     * @param array $arguments
     * @return ResponseInterface
     */
    private function redirect(string $path, array $arguments = []): ResponseInterface
    {
        $this->redirect->redirect($this->response, $path, $arguments);
        return $this->response;
    }
}
