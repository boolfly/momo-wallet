<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Momo Wallet
 */
namespace Boolfly\MomoWallet\Gateway\Http\Converter;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Payment\Gateway\Http\ConverterException;
use Magento\Payment\Gateway\Http\ConverterInterface;
use Psr\Log\LoggerInterface;

/**
 * Class JsonToArray
 *
 * @package Boolfly\MomoWallet\Gateway\Http\Converter
 */
class JsonToArray implements ConverterInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * JsonToArray constructor.
     *
     * @param Json            $serializer
     * @param LoggerInterface $logger
     */
    public function __construct(
        Json $serializer,
        LoggerInterface $logger
    ) {
        $this->logger     = $logger;
        $this->serializer = $serializer;
    }

    /**
     * Converts gateway response to array structure
     *
     * @param mixed $response
     * @return array
     * @throws ConverterException
     */
    public function convert($response)
    {
        try {
            return $this->serializer->unserialize($response);
        } catch (\Exception $e) {
            $this->logger->critical('Can\'t read response from Momo');
            throw new ConverterException(__('Can\'t read response from Mo'));
        }
    }
}
