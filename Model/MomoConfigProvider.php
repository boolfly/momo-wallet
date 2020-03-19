<?php
/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Momo Wallet
 */
namespace Boolfly\MomoWallet\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\UrlInterface;
use Magento\Payment\Helper\Data as PaymentHelper;

/**
 * Class MomoConfigProvider
 *
 * @package Boolfly\MomoWallet\Model
 */
class MomoConfigProvider implements ConfigProviderInterface
{
    /**
     * Momo Logo
     */
    const MOMO_LOGO_SRC = 'https://developers.momo.vn/images/logo.png';

    /**
     * @var ResolverInterface
     */
    protected $localeResolver;

    /**
     * @var PaymentHelper
     */
    protected $paymentHelper;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * MomoConfigProvider constructor.
     *
     * @param ResolverInterface $localeResolver
     * @param PaymentHelper     $paymentHelper
     * @param UrlInterface      $urlBuilder
     */
    public function __construct(
        ResolverInterface $localeResolver,
        PaymentHelper $paymentHelper,
        UrlInterface $urlBuilder
    ) {
        $this->localeResolver = $localeResolver;
        $this->paymentHelper  = $paymentHelper;
        $this->urlBuilder     = $urlBuilder;
    }

    /**
     * @inheritdoc
     */
    public function getConfig()
    {
        $config = [
            'payment' => [
                'momoWallet' => [
                    'redirectUrl' => $this->urlBuilder->getUrl('momo/payment/start'),
                    'logoSrc' => self::MOMO_LOGO_SRC
                ]
            ]
        ];

        return $config;
    }
}
