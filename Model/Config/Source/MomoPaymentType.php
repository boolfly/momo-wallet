<?php
/**
 * Copyright © Boolfly. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    info@boolfly.com
 * @project   Momo Wallet
 */

declare(strict_types=1);

namespace Boolfly\MomoWallet\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * @api
 * @since 100.0.2
 */
class MomoPaymentType implements OptionSourceInterface
{
    /**
     * Capture wallet
     */
    private const CAPTURE_WALLET = 'captureWallet';

    /**
     * Pay with ATM
     */
    private const PAY_WITH_ATM = 'payWithATM';

    /**
     * Pay with CC
     */
    private const PAY_WITH_CC = 'payWithCC';

    /**
     * @inheritdoc
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::CAPTURE_WALLET, 'label' => __('MoMo E-Wallet Payments')],
            ['value' => self::PAY_WITH_ATM, 'label' => __('Local ATM Card Payments')],
            ['value' => self::PAY_WITH_CC, 'label' => __('Credit Card Payments')]
        ];
    }
}
