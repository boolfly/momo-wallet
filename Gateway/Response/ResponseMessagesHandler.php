<?php
/**
 * Copyright © Boolfly. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    info@boolfly.com
 * @project   Momo Wallet
 */

declare(strict_types=1);

namespace Boolfly\MomoWallet\Gateway\Response;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Payment;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Helper\ContextHelper;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Boolfly\MomoWallet\Gateway\Validator\AbstractResponseValidator;

class ResponseMessagesHandler implements HandlerInterface
{
    /**
     * Handle
     *
     * @param array $handlingSubject
     * @param array $response
     * @throws LocalizedException
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = SubjectReader::readPayment($handlingSubject);
        /** @var Payment $payment */
        $payment = $paymentDO->getPayment();
        ContextHelper::assertOrderPayment($payment);

        $responseCode = $response[AbstractResponseValidator::ERROR_CODE];
        $messages     = $response[AbstractResponseValidator::RESPONSE_MESSAGE];
        $state        = $this->getState($responseCode);

        if ($state) {
            $payment->setAdditionalInformation(
                'approve_messages',
                $messages
            );
        } else {
            $payment->setIsTransactionPending(false);
            $payment->setIsFraudDetected(true);
            $payment->setAdditionalInformation('error_messages', $messages);
        }
    }

    /**
     * Get state
     *
     * @param integer $responseCode
     * @return boolean
     */
    protected function getState(int $responseCode): bool
    {
        if ((string)$responseCode !== '0') {
            return false;
        }
        return true;
    }
}
