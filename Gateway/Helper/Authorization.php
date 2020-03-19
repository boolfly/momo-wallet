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

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Payment\Gateway\ConfigInterface;
use Boolfly\MomoWallet\Gateway\Request\AbstractDataBuilder;

/**
 * Class Authorization
 *
 * @package Boolfly\MomoWallet\Gateway\Helper
 */
class Authorization
{
    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var string
     */
    protected $timestamp;

    /**
     * @var string
     */
    protected $params;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * Authorization constructor.
     * @param DateTime        $dateTime
     * @param Json            $serializer
     * @param ConfigInterface $config
     */
    public function __construct(
        DateTime $dateTime,
        Json $serializer,
        ConfigInterface $config
    ) {
        $this->dateTime   = $dateTime;
        $this->config     = $config;
        $this->serializer = $serializer;
    }

    /**
     * Set Parameter
     *
     * @param $params
     * @return $this
     */
    public function setParameter($params)
    {
        $params                                  = array_replace_recursive($params, $this->getPartnerInfo());
        $params[AbstractDataBuilder::REQUEST_ID] = $this->getTimestamp();
        $newParams                               = [];
        foreach ($this->getSignatureData() as $key) {
            if (!empty($params[$key])) {
                $newParams[$key] = $params[$key];
            }
        }
        if ($params[AbstractDataBuilder::REQUEST_TYPE] !== AbstractDataBuilder::PAY_URL_TYPE) {
            $newParams[AbstractDataBuilder::REQUEST_TYPE] = $params[AbstractDataBuilder::REQUEST_TYPE];
        }
        $newParams[AbstractDataBuilder::SIGNATURE] = $this->getSignature($newParams);
        if ($params[AbstractDataBuilder::REQUEST_TYPE] === AbstractDataBuilder::PAY_URL_TYPE) {
            $newParams[AbstractDataBuilder::REQUEST_TYPE] = $params[AbstractDataBuilder::REQUEST_TYPE];
        }

        $this->params = $this->serializer->serialize($newParams);

        return $this;
    }

    /**
     * Signature
     *
     * @param $params
     * @return string
     */
    public function getSignature($params)
    {
        return hash_hmac('sha256', urldecode(http_build_query($params)), $this->getSecretKey());
    }

    /**
     * @return array
     */
    public function getSignatureData()
    {
        return [
            AbstractDataBuilder::PARTNER_CODE,
            AbstractDataBuilder::ACCESS_KEY,
            AbstractDataBuilder::REQUEST_ID,
            AbstractDataBuilder::AMOUNT,
            AbstractDataBuilder::ORDER_ID,
            AbstractDataBuilder::TRANSACTION_ID,
            AbstractDataBuilder::ORDER_INFO,
            AbstractDataBuilder::RETURN_URL,
            AbstractDataBuilder::NOTIFY_URL,
            AbstractDataBuilder::EXTRA_DATA
        ];
    }

    /**
     * @return string
     */
    public function getParameter()
    {
        return $this->params;
    }

    /**
     * @return array
     */
    private function getPartnerInfo()
    {
        return [
            AbstractDataBuilder::PARTNER_CODE => $this->getPartnerCode(),
            AbstractDataBuilder::ACCESS_KEY => $this->getAccessKey()
        ];
    }

    /**
     * Get Header
     *
     * @return array
     */
    public function getHeaders()
    {
        return [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($this->getParameter())
        ];
    }

    /**
     * @return string
     */
    private function getTimestamp()
    {
        if ($this->timestamp === null) {
            $this->timestamp = (string)($this->dateTime->gmtTimestamp() * 1000);
        }

        return $this->timestamp;
    }

    /**
     * @return mixed
     */
    private function getAccessKey()
    {
        return $this->config->getValue('access_key');
    }

    /**
     * @return mixed
     */
    private function getSecretKey()
    {
        return $this->config->getValue('secret_key');
    }

    /**
     * @return mixed
     */
    private function getPartnerCode()
    {
        return $this->config->getValue('partner_code');
    }
}
