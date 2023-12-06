<?php
/**
 * Copyright © Boolfly. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    info@boolfly.com
 * @project   Momo Wallet
 */

declare(strict_types=1);

namespace Boolfly\MomoWallet\Gateway\Command;

use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Gateway\Validator\ValidatorInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Payment\Gateway\Helper\SubjectReader;

class UpdateDetailsCommand implements CommandInterface
{
    /**
     * @var ValidatorInterface
     */
    protected ValidatorInterface $validator;

    /**
     * @var HandlerInterface
     */
    protected HandlerInterface $handler;

    /**
     * UpdateDetailsCommand constructor.
     *
     * @param ValidatorInterface $validator
     * @param HandlerInterface $handler
     */
    public function __construct(
        ValidatorInterface $validator,
        HandlerInterface $handler
    ) {
        $this->validator = $validator;
        $this->handler = $handler;
    }

    /**
     * Execute
     *
     * @param array $commandSubject
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
