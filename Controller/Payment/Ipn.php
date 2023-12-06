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

use Magento\Framework\Serialize\SerializerInterface;
use Boolfly\MomoWallet\Gateway\Helper\TransactionReader;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectFactory;
use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Payment\Model\MethodInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Action\Context;
use Psr\Log\LoggerInterface;

class Ipn implements CsrfAwareActionInterface
{
    /**
     * @var CommandPoolInterface
     */
    private CommandPoolInterface $commandPool;

    /**
     * @var MethodInterface
     */
    private MethodInterface $method;

    /**
     * @var PaymentDataObjectFactory
     */
    private PaymentDataObjectFactory $paymentDataObjectFactory;

    /**
     * @var OrderFactory
     */
    private OrderFactory $orderFactory;

    /**
     * @var RequestInterface
     */
    private RequestInterface $getRequest;

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
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * Ipn constructor.
     *
     * @param MethodInterface $method
     * @param PaymentDataObjectFactory $paymentDataObjectFactory
     * @param OrderFactory $orderFactory
     * @param CommandPoolInterface $commandPool
     * @param Context $context
     * @param SerializerInterface $serializer
     */
    public function __construct(
        MethodInterface $method,
        PaymentDataObjectFactory $paymentDataObjectFactory,
        OrderFactory $orderFactory,
        CommandPoolInterface $commandPool,
        Context $context,
        SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
        $this->commandPool = $commandPool;
        $this->method = $method;
        $this->paymentDataObjectFactory = $paymentDataObjectFactory;
        $this->orderFactory = $orderFactory;
        $this->getRequest = $context->getRequest();
        $this->resultFactory = $context->getResultFactory();
        $this->objectManager = $context->getObjectManager();
        $this->messageManager = $context->getMessageManager();
    }

    /**
     * Execute
     *
     * @return Json|ResultInterface|ResponseInterface|null
     */
    public function execute(): Json|ResultInterface|ResponseInterface|null
    {
        if (!$this->getRequest->isPost()) {
            return null;
        }
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $data       = [
            'errors' => true,
            'messages' => __('Something went wrong while execute.'),
        ];
        try {
            $requestContent = $this->getRequest->getContent();
            $response = $this->serializer->unserialize($requestContent);
            $orderIncrementId = TransactionReader::readOrderId($response);
            $order            = $this->orderFactory->create()->loadByIncrementId($orderIncrementId);
            $payment          = $order->getPayment();
            ContextHelper::assertOrderPayment($payment);
            if ($payment->getMethod() === $this->method->getCode()) {
                $paymentDataObject = $this->paymentDataObjectFactory->create($payment);
                $this->commandPool->get('ipn')->execute(
                    [
                        'payment' => $paymentDataObject,
                        'response' => $response,
                        'is_ipn' => true,
                        'amount' => $order->getTotalDue()
                    ]
                );
                $data = [
                    'errors' => false,
                    'messages' => __('Success')
                ];
            }
        } catch (\Exception $e) {
            $this->objectManager->get(LoggerInterface::class)->critical($e->getMessage());
            $this->messageManager->addErrorMessage(__('Transaction has been declined. Please try again later.'));
            $resultJson->setHttpResponseCode(500);
        }

        return $resultJson->setData($data);
    }

    /**
     * Create exception in case CSRF validation failed.
     *
     * Return null if default exception will suffice.
     *
     * @param RequestInterface $request
     * @return InvalidRequestException|null
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * Perform custom request validation.
     *
     * Return null if default validation is needed.
     *
     * @param RequestInterface $request
     * @return boolean|null
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return  true;
    }
}
