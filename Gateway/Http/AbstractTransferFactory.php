<?php
/**
 * Copyright © Boolfly. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author    info@boolfly.com
 * @project   Momo Wallet
 */

declare(strict_types=1);

namespace Boolfly\MomoWallet\Gateway\Http;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Boolfly\MomoWallet\Gateway\Helper\Authorization;

/**
 * Class AbstractTransferFactory
 */
abstract class AbstractTransferFactory implements TransferFactoryInterface
{
    /**
     * @var ConfigInterface
     */
    protected ConfigInterface $config;

    /**
     * @var TransferBuilder
     */
    protected TransferBuilder $transferBuilder;

    /**
     * Authenticate & generate Headers
     *
     * @var Authorization
     */
    private Authorization $authorization;

    /**
     * @var Json
     */
    protected Json $serializer;

    /**
     * @var null
     */
    protected $urlPath;

    /**
     * AbstractTransferFactory constructor.
     *
     * @param ConfigInterface $config
     * @param TransferBuilder $transferBuilder
     * @param Json $serializer
     * @param Authorization $authorization
     * @param null $urlPath
     */

    public function __construct(
        ConfigInterface $config,
        TransferBuilder $transferBuilder,
        Json $serializer,
        Authorization $authorization,
        $urlPath = null
    ) {
        $this->config = $config;
        $this->transferBuilder = $transferBuilder;
        $this->authorization = $authorization;
        $this->serializer = $serializer;
        $this->urlPath = $urlPath;
    }

    /**
     * Check is sandbox mode
     *
     * @return boolean
     */
    protected function isSandboxMode(): bool
    {
        return (bool)$this->config->getValue('sandbox_flag');
    }

    /**
     * Get authorization
     *
     * @return Authorization
     */
    protected function getAuthorization(): Authorization
    {
        return $this->authorization;
    }
}
