<?php
/**
 * Copyright © Boolfly. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Boolfly\MomoWallet\Gateway\Http\Client;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\ConverterInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;
use Magento\Framework\HTTP\Adapter\CurlFactory;

class Curl implements ClientInterface
{
    private const HTTP_VERSION_1_1 = '1.1';

    /**
     * @var ?ConverterInterface
     */
    private ?ConverterInterface $converter;

    /**
     * @var Logger
     */
    private Logger $logger;

    /**
     * @var CurlFactory
     */
    private CurlFactory $curlFactory;

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * Http client construct
     *
     * @param Logger $logger
     * @param CurlFactory $curlFactory
     * @param SerializerInterface $serializer
     * @param ?ConverterInterface $converter
     */
    public function __construct(
        Logger $logger,
        CurlFactory $curlFactory,
        SerializerInterface $serializer,
        ConverterInterface $converter = null
    ) {
        $this->converter = $converter;
        $this->logger = $logger;
        $this->curlFactory = $curlFactory;
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    public function placeRequest(TransferInterface $transferObject): array
    {
        $log = [
            'request' => $this->converter ?
                $this->converter->convert($transferObject->getBody()) : $transferObject->getBody(),
            'request_uri' => $transferObject->getUri()
        ];
        $curl = $this->curlFactory->create();
        $curl->setOptions($this->serializer->unserialize($transferObject->getBody()));
        $curl->write(
            $transferObject->getMethod(),
            $transferObject->getUri(),
            self::HTTP_VERSION_1_1,
            $transferObject->getHeaders(),
            $transferObject->getBody()
        );
        $response = $curl->read();

        $responseBody = substr($response, strpos($response, '{'));
        $result = $this->converter ? $this->converter->convert($responseBody) : $responseBody;
        $log['response'] = $result;
        $this->logger->debug($log);

        return $result;
    }
}
