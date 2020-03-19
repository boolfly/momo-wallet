<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Momo Wallet
 */
namespace Boolfly\MomoWallet\Gateway\Helper;

use Magento\Directory\Helper\Data;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Payment\Gateway\ConfigInterface;
use Boolfly\MomoWallet\Gateway\Request\AbstractDataBuilder;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Rate
 *
 * @package Boolfly\MomoWallet\Gateway\Helper
 */
class Rate
{
    /**
     * Vietnam dong currency
     */
    const CURRENCY_CODE = 'VND';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Data
     */
    private $helperData;

    /**
     * OrderDetailsDataBuilder constructor.
     *
     * @param ConfigInterface       $config
     * @param Data                  $helperData
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Data $helperData,
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        $this->helperData   = $helperData;
    }

    /**
     * @param Order  $order
     * @param $amount
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws LocalizedException
     */
    public function getVndAmount(Order $order, $amount)
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
     * @param Order $order
     * @return boolean
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function isVietnamDong($order)
    {
        return $order->getOrderCurrencyCode() === self::CURRENCY_CODE;
    }
}
