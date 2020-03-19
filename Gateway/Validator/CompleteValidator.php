<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Momo Wallet
 */
namespace Boolfly\MomoWallet\Gateway\Validator;

use Boolfly\MomoWallet\Gateway\Helper\Rate;
use Boolfly\MomoWallet\Gateway\Request\AbstractDataBuilder;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

/**
 * Class CompleteValidator
 *
 * @package Boolfly\MomoWallet\Gateway\Validator
 */
class CompleteValidator extends AbstractResponseValidator
{

    /**
     * @param array $validationSubject
     * @return \Magento\Payment\Gateway\Validator\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function validate(array $validationSubject)
    {
        $response      = SubjectReader::readResponse($validationSubject);
        $amount        = round(SubjectReader::readAmount($validationSubject), 2);
        $payment       = SubjectReader::readPayment($validationSubject);
        $amount        = $this->helperRate->getVndAmount($payment->getPayment()->getOrder(), $amount);
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
     * @param array               $response
     * @param array|number|string $amount
     * @return boolean
     */
    protected function validateTotalAmount(array $response, $amount)
    {
        return isset($response[self::TOTAL_AMOUNT])
            && (string)($response[self::TOTAL_AMOUNT]) === (string)$amount;
    }

    /**
     * @inheritDoc
     */
    protected function getSignatureArray()
    {
        return [
            AbstractDataBuilder::PARTNER_CODE,
            AbstractDataBuilder::ACCESS_KEY,
            AbstractDataBuilder::REQUEST_ID,
            self::TOTAL_AMOUNT,
            AbstractDataBuilder::ORDER_ID,
            AbstractDataBuilder::ORDER_INFO,
            self::ORDER_TYPE,
            self::TRANSACTION_ID,
            self::RESPONSE_MESSAGE,
            self::RESPONSE_LOCAL_MESSAGE,
            self::RESPONSE_TIME,
            self::ERROR_CODE,
            self::PAY_TYPE,
            AbstractDataBuilder::EXTRA_DATA
        ];
    }
}
