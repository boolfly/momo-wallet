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
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

class GetPayUrlValidator extends AbstractResponseValidator
{
    /**
     * Validate
     *
     * @param array $validationSubject
     * @return ResultInterface
     */
    public function validate(array $validationSubject): ResultInterface
    {
        $response = SubjectReader::readResponse($validationSubject);
        $payment = SubjectReader::readPayment($validationSubject);
        $orderId = $payment->getOrder()->getOrderIncrementId();
        $errorMessages = [];
        $validationResult = $this->validateErrorCode($response)
            && $this->validateOrderId($response, $orderId)
            && $this->validateSignature($response);

        if (!$validationResult) {
            $errorMessages = [__('Something went wrong when get pay url.')];
        }

        return $this->createResult($validationResult, $errorMessages);
    }

    /**
     * Get signature array
     *
     * @return array
     */
    protected function getSignatureArray(): array
    {
        return [
            AbstractDataBuilder::ACCESS_KEY,
            AbstractDataBuilder::AMOUNT,
            self::RESPONSE_MESSAGE,
            AbstractDataBuilder::ORDER_ID,
            AbstractDataBuilder::PARTNER_CODE,
            self::PAY_URL,
            AbstractDataBuilder::REQUEST_ID,
            self::RESPONSE_TIME,
            self::RESULT_CODE
        ];
    }

    /**
     * Validate Order Id
     *
     * @param array $response
     * @param string $orderId
     * @return boolean
     */
    protected function validateOrderId(array $response, string $orderId): bool
    {
        return isset($response[AbstractDataBuilder::ORDER_ID])
            && (string)($response[AbstractDataBuilder::ORDER_ID]) === (string)$orderId;
    }
}
