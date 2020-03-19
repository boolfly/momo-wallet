<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Momo Wallet
 */
namespace Boolfly\MomoWallet\Gateway\Command;

use Magento\Payment\Gateway\Command;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Gateway\Validator\ValidatorInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Payment\Gateway\Helper\SubjectReader;

/**
 * Class UpdateDetailsCommand
 *
 * @package Boolfly\MomoWallet\Gateway\Command
 */
class UpdateDetailsCommand implements CommandInterface
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var HandlerInterface
     */
    protected $handler;

    /**
     * UpdateDetailsCommand constructor.
     *
     * @param ValidatorInterface $validator
     * @param HandlerInterface   $handler
     */
    public function __construct(
        ValidatorInterface $validator,
        HandlerInterface $handler
    ) {
        $this->validator = $validator;
        $this->handler   = $handler;
    }

    /**
     * @param array $commandSubject
     * @return Command\ResultInterface|void|null
     * @throws CommandException
     */
    public function execute(array $commandSubject)
    {
        $paymentDO = SubjectReader::readPayment($commandSubject);

        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();
        ContextHelper::assertOrderPayment($payment);
        $response = SubjectReader::readResponse($commandSubject);

        if ($this->validator) {
            $result = $this->validator->validate(
                array_merge(
                    $commandSubject,
                    [
                        'amount' => $payment->getOrder()->getTotalDue()
                    ]
                )
            );
            if (!$result->isValid()) {
                throw new CommandException(
                    __(implode("\n", $result->getFailsDescription()))
                );
            }
        }

        if ($this->handler) {
            $this->handler->handle($commandSubject, $response);
        }
    }
}
