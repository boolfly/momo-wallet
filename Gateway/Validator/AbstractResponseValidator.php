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

use Boolfly\MomoWallet\Gateway\Helper\Authorization;
use Boolfly\MomoWallet\Gateway\Helper\Rate;
use Boolfly\MomoWallet\Gateway\Request\AbstractDataBuilder;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

/**
 * Class AbstractResponseValidator
 */
abstract class AbstractResponseValidator extends AbstractValidator
{
    /**
     * The amount that was authorised for this transaction
     */
    public const TOTAL_AMOUNT = 'amount';

    /**
     * The transaction type that this transaction was processed under
     * One of: Purchase, MOTO, Recurring
     */
    public const TRANSACTION_TYPE = 'transactionType';

    /**
     * Pay Url
     */
    public const PAY_URL = 'payUrl';

    /**
     * Transaction Id
     */
    public const TRANSACTION_ID = 'transId';

    /**
     * Result Code
     */
    public const RESULT_CODE = 'resultCode';

    /**
     * Error Code
     */
    public const ERROR_CODE = 'errorCode';

    /**
     * Error Code Accept
     */
    public const ERROR_CODE_ACCEPT = '0';

    /**
     * Message
     */
    public const RESPONSE_MESSAGE = 'message';

    /**
     * Local Response
     */
    public const RESPONSE_LOCAL_MESSAGE = 'localMessage';

    /**
     * Order Type
     */
    public const ORDER_TYPE = 'orderType';

    /**
     * Response Time
     */
    public const RESPONSE_TIME = 'responseTime';

    /**
     * Pay type: qr or web
     */
    public const PAY_TYPE = 'payType';

    /**
     * @var Rate
     */
    protected Rate $helperRate;

    /**
     * @var Authorization
     */
    protected Authorization $authorization;

    /**
     * AbstractResponseValidator constructor.
     *
     * @param ResultInterfaceFactory $resultFactory
     * @param Authorization          $authorization
     * @param Rate                   $helperRate
     */
    public function __construct(
        ResultInterfaceFactory $resultFactory,
        Authorization $authorization,
        Rate $helperRate
    ) {
        parent::__construct($resultFactory);
        $this->helperRate = $helperRate;
        $this->authorization = $authorization;
    }

    /**
     * Get signature array
     *
     * @return array
     */
    abstract protected function getSignatureArray(): array;

    /**
     * Validate error code
     *
     * @param array $response
     * @return boolean
     */
    protected function validateErrorCode(array $response): bool
    {
        return isset($response[self::RESULT_CODE]) &&
            ((string)$response[self::RESULT_CODE] === (string)self::ERROR_CODE_ACCEPT);
    }

    /**
     * Validate transaction id
     *
     * @param array $response
     * @return boolean
     */
    protected function validateTransactionId(array $response): bool
    {
        return isset($response[self::TRANSACTION_ID])
            && $response[self::TRANSACTION_ID];
    }

    /**
     * Validate Signature
     *
     * @param array $response
     * @return boolean
     */
    protected function validateSignature(array $response): bool
    {
        $newParams = [];
        foreach ($this->getSignatureArray() as $param) {
            if (isset($response[$param])) {
                $newParams[$param] = $response[$param];
            }
        }
        $signature = $this->authorization->getSignature($newParams);
        if (
            !empty($response[AbstractDataBuilder::SIGNATURE])
            && $response[AbstractDataBuilder::SIGNATURE] === $signature
        ) {
            return  true;
        }

        return false;
    }
}
