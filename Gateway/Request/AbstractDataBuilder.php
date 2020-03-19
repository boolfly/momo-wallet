<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Momo Wallet
 */
namespace Boolfly\MomoWallet\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class AbstractDataBuilder
 * @package Boolfly\MomoWallet\Gateway\Request
 */
abstract class AbstractDataBuilder implements BuilderInterface
{
    /**
     * Pay Url
     */
    const PAY_URL_TYPE = 'captureMoMoWallet';

    /**@#+
     * Momo AIO Url path
     *
     * @const
     */
    const PAY_URL_PATH = 'gw_payment/transactionProcessor';

    /**
     * Refund Url Path
     */
    const REFUND_TYPE = 'refundMoMoWallet';

    /**
     * Transaction Type: Refund
     */
    const REFUND = 'refund';

    /**
     * Transaction Id
     */
    const TRANSACTION_ID = 'transId';

    /**
     * Access Key
     */
    const ACCESS_KEY = 'accessKey';

    /**
     * Secret key
     */
    const SECRET_KEY = 'secretKey';

    /**
     * Partner code
     */
    const PARTNER_CODE = 'partnerCode';

    /**
     * Request Id
     */
    const REQUEST_ID = 'requestId';

    /**
     * Order Info
     */
    const ORDER_INFO = 'orderInfo';

    /**
     * Return Url
     */
    const RETURN_URL = 'returnUrl';

    /**
     * Notify Url
     */
    const NOTIFY_URL = 'notifyUrl';

    /**
     * Extra Data
     */
    const EXTRA_DATA = 'extraData';

    /**
     * Request Type
     */
    const REQUEST_TYPE = 'requestType';

    /**
     * Signature
     */
    const SIGNATURE = 'signature';

    /**
     * Merchant Ref
     */
    const ORDER_ID = 'orderId';

    /**
     * Amount
     */
    const AMOUNT = 'amount';
}
