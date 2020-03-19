<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Momo Wallet
 */
namespace Boolfly\MomoWallet\Gateway\Response;

use Magento\Framework\Exception\LocalizedException as LocalizedExceptionAlias;
use Magento\Sales\Model\Order\Payment;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Boolfly\MomoWallet\Gateway\Validator\AbstractResponseValidator;

/**
 * Class TransactionCompleteHandler
 *
 * @package Boolfly\MomoWallet\Gateway\Response
 */
class TransactionCompleteHandler implements HandlerInterface
{
    /**
     * @var array
     */
    private $additionalInformationMapping = [
        'transaction_id' => AbstractResponseValidator::TRANSACTION_ID
    ];

    /**
     * @param array $handlingSubject
     * @param array $response
     * @throws LocalizedExceptionAlias
     */
    public function handle(array $handlingSubject, array $response)
    {
        $paymentDO = SubjectReader::readPayment($handlingSubject);
        /** @var Payment $orderPayment */
        $orderPayment = $paymentDO->getPayment();
        $orderPayment->setTransactionId($response[AbstractResponseValidator::TRANSACTION_ID]);
        $orderPayment->setIsTransactionClosed(false);
        $orderPayment->setShouldCloseParentTransaction(true);

        foreach ($this->additionalInformationMapping as $informationKey => $responseKey) {
            if (isset($response[$responseKey])) {
                $orderPayment->setAdditionalInformation($informationKey, ucfirst($response[$responseKey]));
            }
        }
    }
}
