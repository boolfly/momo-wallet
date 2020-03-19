<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Momo Wallet
 */
namespace Boolfly\MomoWallet\Gateway\Http;

/**
 * Class TransferFactory
 *
 * @package Boolfly\MomoWallet\Gateway\Http
 */
class TransferFactory extends AbstractTransferFactory
{
    /**
     * @inheritdoc
     */
    public function create(array $request)
    {
        $header = $this->getAuthorization()
            ->setParameter($request)
            ->getHeaders();

        return $this->transferBuilder
            ->setMethod('POST')
            ->setHeaders($header)
            ->setBody($this->getAuthorization()->getParameter())
            ->setUri($this->getUrl())
            ->build();
    }

    /**
     * Get Url
     *
     * @return string
     */
    private function getUrl()
    {
        $prefix = $this->isSandboxMode() ? 'sandbox_' : '';
        $path   = $prefix . 'payment_url';

        return rtrim($this->config->getValue($path), '/') . '/' . $this->urlPath;
    }
}
