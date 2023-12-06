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

use Magento\Framework\UrlInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class redirect url and Ipn url data builder
 */
class RedirectIpnUrlDataBuilder extends AbstractDataBuilder implements BuilderInterface
{
    /**
     * @var UrlInterface
     */
    private UrlInterface $urlBuilder;

    /**
     * RedirectIpnUrlDataBuilder constructor.
     *
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject): array
    {
        return [
            self::REDIRECT_URL => $this->urlBuilder->getUrl('momo/payment/return'),
            self::IPN_URL => $this->urlBuilder->getUrl('momo/payment/ipn')
        ];
    }
}
