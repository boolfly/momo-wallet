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

use Magento\Directory\Helper\Data;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;

class Rate
{
    /**
     * Vietnam dong currency
     */
    private const CURRENCY_CODE = 'VND';

    /**
     * @var Data
     */
    private Data $helperData;

    /**
     * Rate constructor.
     *
     * @param Data $helperData
     */
    public function __construct(
        Data $helperData,
    ) {
        $this->helperData = $helperData;
    }

    /**
     * Get Vnd amount
     *
     * @param Order $order
     * @param float $amount
     * @return float
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getVndAmount(Order $order, float $amount): float
    {
        if ($this->isVietnamDong($order)) {
            return round($amount);
        } else {
            try {
                return round($this->helperData->currencyConvert(
                    $amount,
                    $order->getOrderCurrencyCode(),
                    self::CURRENCY_CODE
                ));
            } catch (\Exception $e) {
                throw new LocalizedException(
                    __('We can\'t convert base currency to %1. Please setup currency rates.', self::CURRENCY_CODE)
                );
            }
        }
    }

    /**
     * Check order is Vietnam Dong
     *
     * @param Order $order
     * @return boolean
     */
    private function isVietnamDong(Order $order): bool
    {
        return $order->getOrderCurrencyCode() === self::CURRENCY_CODE;
    }
}
