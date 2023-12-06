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

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Payment\Gateway\ConfigInterface;
use Boolfly\MomoWallet\Gateway\Request\AbstractDataBuilder;
use Magento\Framework\Encryption\EncryptorInterface;

class Authorization
{
    /**
     * @var DateTime
     */
    protected DateTime $dateTime;

    /**
     * @var string
     */
    protected $timestamp;

    /**
     * @var string
     */
    protected string $params;

    /**
     * @var ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * @var Json
     */
    private Json $serializer;

    /**
     * @var EncryptorInterface
     */
    private EncryptorInterface $encryptor;

    /**
     * Authorization constructor.
     *
     * @param DateTime $dateTime
     * @param Json $serializer
     * @param ConfigInterface $config
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        DateTime $dateTime,
        Json $serializer,
        ConfigInterface $config,
        EncryptorInterface $encryptor
    ) {
        $this->dateTime = $dateTime;
        $this->config = $config;
        $this->serializer = $serializer;
        $this->encryptor = $encryptor;
    }

    /**
     * Set Parameter
     *
     * @param array $params
     * @return $this
     */
    public function setParameter(array $params): static
    {
        $params = array_replace_recursive($params, $this->getPartnerInfo());
        $params[AbstractDataBuilder::REQUEST_ID] = $this->getTimestamp();
        $newParams = [];
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
     * @param array $params
     * @return string
     */
    public function getSignature(array $params): string
    {
        if (!isset($params[AbstractDataBuilder::ACCESS_KEY])) {
            $accessKey = [AbstractDataBuilder::ACCESS_KEY => $this->getAccessKey()];
            $params = $accessKey + $params;
        }

        $this->encryptor->setNewKey($this->getSecretKey());
        return $this->encryptor->hash(urldecode(http_build_query($params)));
    }

    /**
     * Get signature data
     *
     * @return array
     */
    public function getSignatureData(): array
    {
        return [
            AbstractDataBuilder::ACCESS_KEY,
            AbstractDataBuilder::AMOUNT,
            AbstractDataBuilder::EXTRA_DATA,
            AbstractDataBuilder::IPN_URL,
            AbstractDataBuilder::ORDER_ID,
            AbstractDataBuilder::ORDER_INFO,
            AbstractDataBuilder::PARTNER_CODE,
            AbstractDataBuilder::REDIRECT_URL,
            AbstractDataBuilder::REQUEST_ID,
            AbstractDataBuilder::REQUEST_TYPE
        ];
    }

    /**
     * Get parameter
     *
     * @return string
     */
    public function getParameter(): string
    {
        return $this->params;
    }

    /**
     * Get partner info
     *
     * @return array
     */
    private function getPartnerInfo(): array
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
    public function getHeaders(): array
    {
        return [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($this->getParameter())
        ];
    }

    /**
     * Get timestamp
     *
     * @return string
     */
    private function getTimestamp(): string
    {
        if ($this->timestamp === null) {
            $this->timestamp = (string)($this->dateTime->gmtTimestamp() * 1000);
        }
        return $this->timestamp;
    }

    /**
     * Get access key
     *
     * @return string
     */
    private function getAccessKey(): string
    {
        return (string)$this->config->getValue('access_key');
    }

    /**
     * Get secret key
     *
     * @return string
     */
    private function getSecretKey(): string
    {
        return (string)$this->config->getValue('secret_key');
    }

    /**
     * Get partner code
     *
     * @return string
     */
    private function getPartnerCode(): string
    {
        return (string)$this->config->getValue('partner_code');
    }
}
