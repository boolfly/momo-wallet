<?php
/**
 * Copyright © Boolfly. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    info@boolfly.com
 * @project   Momo Wallet
 */

declare(strict_types=1);

namespace Boolfly\MomoWallet\Gateway\Validator;

use Boolfly\MomoWallet\Gateway\Request\AbstractDataBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Validator\ResultInterface;

class CompleteValidator extends AbstractResponseValidator
{
    /**
     * Validate
     *
     * @param array $validationSubject
     * @return ResultInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function validate(array $validationSubject): ResultInterface
    {
        $response = SubjectReader::readResponse($validationSubject);
        $amount = round(SubjectReader::readAmount($validationSubject), 2);
        $payment = SubjectReader::readPayment($validationSubject);
        $amount = $this->helperRate->getVndAmount($payment->getPayment()->getOrder(), $amount);
        $errorMessages = [];

        $validationResult = $this->validateTotalAmount($response, $amount)
            && $this->validateTransactionId($response)
            && $this->validateErrorCode($response)
            && $this->validateSignature($response);

        if (!$validationResult) {
            $errorMessages = [__('Transaction has been declined. Please try again later.')];
        }

        return $this->createResult($validationResult, $errorMessages);
    }

    /**
     * Validate total amount.
     *
     * @param array $response
     * @param float $amount
     * @return boolean
     */
    protected function validateTotalAmount(array $response, float $amount): bool
    {
        return isset($response[self::TOTAL_AMOUNT])
            && (string)($response[self::TOTAL_AMOUNT]) === (string)$amount;
    }

    /**
     * @inheritDoc
     */
    protected function getSignatureArray(): array
    {
        return [
            AbstractDataBuilder::ACCESS_KEY,
            self::TOTAL_AMOUNT,
            AbstractDataBuilder::EXTRA_DATA,
            self::RESPONSE_MESSAGE,
            AbstractDataBuilder::ORDER_ID,
            AbstractDataBuilder::ORDER_INFO,
            self::ORDER_TYPE,
            AbstractDataBuilder::PARTNER_CODE,
            self::PAY_TYPE,
            AbstractDataBuilder::REQUEST_ID,
            self::RESPONSE_TIME,
            self::RESULT_CODE,
            self::TRANSACTION_ID
        ];
    }
}
