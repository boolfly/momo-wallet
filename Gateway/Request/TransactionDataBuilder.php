<?php
/**
 * Copyright © Boolfly. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    info@boolfly.com
 * @project   Momo Wallet
 */

declare(strict_types=1);

namespace Boolfly\MomoWallet\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

class TransactionDataBuilder extends AbstractDataBuilder implements BuilderInterface
{
    /**
     * Method
     */
    public const METHOD = 'method';

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject): array
    {
        $paymentDO = $buildSubject['payment'];
        $payment = $paymentDO->getPayment();

        return [
            self::REQUEST_TYPE => $payment->getAdditionalInformation('request_type')
        ];
    }
}
