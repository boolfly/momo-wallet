<?php
/**
 * Copyright © Boolfly. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    info@boolfly.com
 * @project   Momo Wallet
 */

declare(strict_types=1);

namespace Boolfly\MomoWallet\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\UrlInterface;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Helper\Data as PaymentHelper;

class MomoConfigProvider implements ConfigProviderInterface
{
    /**
     * Pay with ATM title
     */
    private const CAPTURE_WALLET_TITLE = 'Ví MoMo';

    /**
     * Pay with ATM title
     */
    private const PAY_ATM_TITLE = 'Thẻ ATM nội địa';

    /**
     * Pay with ATM title
     */
    private const PAY_CC_TITLE = 'Thẻ Visa/Mastercard/JCB';

    /**
     * @var ResolverInterface
     */
    protected ResolverInterface $localeResolver;

    /**
     * @var PaymentHelper
     */
    protected PaymentHelper $paymentHelper;

    /**
     * @var UrlInterface
     */
    protected UrlInterface $urlBuilder;

    /**
     * @var ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * MomoConfigProvider constructor.
     *
     * @param ResolverInterface $localeResolver
     * @param PaymentHelper $paymentHelper
     * @param UrlInterface $urlBuilder
     * @param ConfigInterface $config
     */
    public function __construct(
        ResolverInterface $localeResolver,
        PaymentHelper $paymentHelper,
        UrlInterface $urlBuilder,
        ConfigInterface $config
    ) {
        $this->localeResolver = $localeResolver;
        $this->paymentHelper = $paymentHelper;
        $this->urlBuilder = $urlBuilder;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function getConfig(): array
    {
        return [
            'payment' => [
                'momoWallet' => [
                    'redirectUrl' => $this->urlBuilder->getUrl('momo/payment/start'),
                    'logoSrc' => $this->config->getValue('momo_logo'),
                    'paymentTypeList' => $this->config->getValue('payment_type'),
                    'captureWallet' => [
                        'logo' => $this->config->getValue('capture_wallet_logo'),
                        'title' => self::CAPTURE_WALLET_TITLE
                    ],
                    'payWithATM' => [
                        'logo' => $this->config->getValue('pay_atm_logo'),
                        'title' => self::PAY_ATM_TITLE
                    ],
                    'payWithCC' => [
                        'logo' => $this->config->getValue('pay_cc_logo'),
                        'title' => self::PAY_CC_TITLE
                    ]
                ]
            ]
        ];
    }
}
