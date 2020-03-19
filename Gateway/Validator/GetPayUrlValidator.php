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

use Boolfly\MomoWallet\Gateway\Request\AbstractDataBuilder;
use Magento\Payment\Gateway\Helper\SubjectReader;

/**
 * Class GetPayUrlValidator
 * @package Boolfly\MomoWallet\Gateway\Validator
 */
class GetPayUrlValidator extends AbstractResponseValidator
{
    /**
     * @param array $validationSubject
     * @return \Magento\Payment\Gateway\Validator\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function validate(array $validationSubject)
    {
        $response         = SubjectReader::readResponse($validationSubject);
        $payment          = SubjectReader::readPayment($validationSubject);
        $orderId          = $payment->getOrder()->getOrderIncrementId();
        $errorMessages    = [];
        $validationResult = $this->validateErrorCode($response)
            && $this->validateOrderId($response, $orderId)
            && $this->validateSignature($response);

        if (!$validationResult) {
            $errorMessages = [__('Something went wrong when get pay url.')];
        }

        return $this->createResult($validationResult, $errorMessages);
    }

    /**
     * @return array
     */
    protected function getSignatureArray()
    {
        return [
            AbstractDataBuilder::REQUEST_ID,
            AbstractDataBuilder::ORDER_ID,
            self::RESPONSE_MESSAGE,
            self::RESPONSE_LOCAL_MESSAGE,
            self::PAY_URL,
            self::ERROR_CODE,
            AbstractDataBuilder::REQUEST_TYPE
        ];
    }

    /**
     * Validate Order Id
     *
     * @param array   $response
     * @param $orderId
     * @return boolean
     */
    protected function validateOrderId(array $response, $orderId)
    {
        return isset($response[AbstractDataBuilder::ORDER_ID])
            && (string)($response[AbstractDataBuilder::ORDER_ID]) === (string)$orderId;
    }
}
