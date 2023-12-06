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

use Boolfly\MomoWallet\Gateway\Helper\Rate;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\SalesSequence\Model\Manager;

class RefundDataBuilder extends AbstractDataBuilder implements BuilderInterface
{
    /**
     * @var ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * @var Rate
     */
    private Rate $helperRate;

    /**
     * @var Manager
     */
    private Manager $sequenceManager;

    /**
     * RefundDataBuilder constructor.
     *
     * @param ConfigInterface $config
     * @param Manager $sequenceManager
     * @param Rate $helperRate
     */
    public function __construct(
        ConfigInterface $config,
        Manager $sequenceManager,
        Rate $helperRate
    ) {
        $this->config = $config;
        $this->helperRate = $helperRate;
        $this->sequenceManager = $sequenceManager;
    }

    /**
     * Build
     *
     * @param array $buildSubject
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function build(array $buildSubject): array
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
     * Set increment id
     *
     * @param Creditmemo $creditMemo
     * @throws LocalizedException
     */
    private function setIncrementId(Creditmemo $creditMemo)
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
     * @return string
     */
    private function getCreditMemoPrefix(): string
    {
        return (string)$this->config->getValue('credit_memo_prefix');
    }
}
