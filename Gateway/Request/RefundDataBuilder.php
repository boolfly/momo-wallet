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
use Magento\Sales\Model\Order\Creditmemo;
use Magento\SalesSequence\Model\Manager;

/**
 * Class TransactionIdDataBuilder
 *
 * @package Boolfly\MomoWallet\Gateway\Request
 */
class RefundDataBuilder extends AbstractDataBuilder implements BuilderInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var Rate
     */
    private $helperRate;

    /**
     * @var Manager
     */
    private $sequenceManager;

    /**
     * RefundDataBuilder constructor.
     *
     * @param ConfigInterface $config
     * @param Manager         $sequenceManager
     * @param Rate            $helperRate
     */
    public function __construct(
        ConfigInterface $config,
        Manager $sequenceManager,
        Rate $helperRate
    ) {
        $this->config          = $config;
        $this->helperRate      = $helperRate;
        $this->sequenceManager = $sequenceManager;
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
        $amount    = round((float)SubjectReader::readAmount($buildSubject), 2);
        $payment   = $paymentDO->getPayment();

        /** @var Creditmemo $creditMemo */
        $creditMemo = $payment->getCreditmemo();
        if ($creditMemo && !$creditMemo->getIncrementId()) {
            $this->setIncrementId($creditMemo);
        }

        return [
            self::AMOUNT => (string) $this->helperRate->getVndAmount($payment->getOrder(), $amount),
            self::ORDER_ID => $this->getCreditMemoPrefix() . $creditMemo->getIncrementId(),
            self::TRANSACTION_ID => $payment->getParentTransactionId()
        ];
    }

    /**
     * @param Creditmemo $creditMemo
     * @throws LocalizedException
     */
    private function setIncrementId($creditMemo)
    {
        $store   = $creditMemo->getStore();
        $storeId = $store->getId();
        if ($storeId === null) {
            $storeId = $store->getGroup()->getDefaultStoreId();
        }
        $creditMemo->setIncrementId(
            $this->sequenceManager->getSequence(
                $creditMemo->getEntityType(),
                $storeId
            )->getNextValue()
        );
    }

    /**
     * Get Credit Memo Prefix
     *
     * @return mixed
     */
    private function getCreditMemoPrefix()
    {
        return $this->config->getValue('credit_memo_prefix');
    }
}
