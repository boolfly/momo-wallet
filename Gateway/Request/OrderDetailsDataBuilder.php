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

use Boolfly\MomoWallet\Gateway\Helper\Rate;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class OrderDetailsDataBuilder
 *
 * @package Boolfly\MomoWallet\Gateway\Request
 */
class OrderDetailsDataBuilder extends AbstractDataBuilder implements BuilderInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var Rate
     */
    private $helperRate;

    /**
     * OrderDetailsDataBuilder constructor.
     *
     * @param ConfigInterface       $config
     * @param StoreManagerInterface $storeManager
     * @param Rate                  $helperRate
     */
    public function __construct(
        ConfigInterface $config,
        StoreManagerInterface $storeManager,
        Rate $helperRate
    ) {
        $this->config       = $config;
        $this->helperRate   = $helperRate;
        $this->storeManager = $storeManager;
    }

    /**
     * @param array $buildSubject
     * @return array
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function build(array $buildSubject)
    {
        $paymentDO = SubjectReader::readPayment($buildSubject);
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $paymentDO->getPayment();
        $order   = $payment->getOrder();
        return [
            self::ORDER_ID => $order->getIncrementId(),
            self::ORDER_INFO => $this->storeManager->getStore()->getName(),
            self::EXTRA_DATA => $this->getExtraData(),
            self::AMOUNT => (string) $this->helperRate->getVndAmount($order, round((float)SubjectReader::readAmount($buildSubject), 2)),
        ];
    }

    /**
     * @return string
     */
    private function getExtraData()
    {
        return 'merchantName=' . $this->config->getValue('merchant_name');
    }
}
