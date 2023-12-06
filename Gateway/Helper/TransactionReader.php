<?php
/**
 * Copyright © Boolfly. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    info@boolfly.com
 * @project   Momo Wallet
 */

declare(strict_types=1);

namespace Boolfly\MomoWallet\Gateway\Helper;

use Boolfly\MomoWallet\Gateway\Request\AbstractDataBuilder;
use Boolfly\MomoWallet\Gateway\Validator\AbstractResponseValidator;

class TransactionReader
{
    /**
     * Is IPN request
     */
    private const IS_IPN = 'is_ipn';

    /**
     * Read Pay Url from transaction data
     *
     * @param array $transactionData
     * @return string
     */
    public static function readPayUrl(array $transactionData): string
    {
        if (empty($transactionData[AbstractResponseValidator::PAY_URL])) {
            throw new \InvalidArgumentException('Pay Url should be provided');
        }
        return $transactionData[AbstractResponseValidator::PAY_URL];
    }

    /**
     * Read Order Id from transaction data
     *
     * @param array $transactionData
     * @return string
     */
    public static function readOrderId(array $transactionData): string
    {
        if (empty($transactionData[AbstractDataBuilder::ORDER_ID])) {
            throw new \InvalidArgumentException('Order Id doesn\'t exit');
        }
        return $transactionData[AbstractDataBuilder::ORDER_ID];
    }

    /**
     * Check Is IPN from transaction data
     *
     * @param array $transactionData
     * @return bool
     */
    public static function isIpn(array $transactionData): bool
    {
        if (!empty($transactionData[self::IS_IPN]) && $transactionData[self::IS_IPN]) {
            return true;
        }
        return false;
    }
}
