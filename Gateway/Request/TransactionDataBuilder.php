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
 * Class TransactionDataBuilder
 *
 * @package Boolfly\MomoWallet\Gateway\Request
 */
class TransactionDataBuilder extends AbstractDataBuilder implements BuilderInterface
{
    /**
     * Method
     */
    const METHOD = 'method';

    /**
     * @var string
     */
    private $requestType;

    /**
     * TransactionDataBuilder constructor.
     *
     * @param $requestType
     */
    public function __construct(
        $requestType
    ) {
        $this->requestType = $requestType;
    }

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        return [
            self::REQUEST_TYPE => $this->requestType
        ];
    }
}
