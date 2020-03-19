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
    protected $config;

    /**
     * @var TransferBuilder
     */
    protected $transferBuilder;

    /**
     * Authenticate & generate Headers
     *
     * @var Authorization
     */
    private $authorization;

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * @var null
     */
    protected $urlPath;

    /**
     * AbstractTransferFactory constructor.
     *
     * @param ConfigInterface $config
     * @param TransferBuilder $transferBuilder
     * @param Json            $serializer
     * @param Authorization   $authorization
     * @param null            $urlPath
     */
    public function __construct(
        ConfigInterface $config,
        TransferBuilder $transferBuilder,
        Json $serializer,
        Authorization $authorization,
        $urlPath = null
    ) {
        $this->config          = $config;
        $this->transferBuilder = $transferBuilder;
        $this->authorization   = $authorization;
        $this->serializer      = $serializer;
        $this->urlPath         = $urlPath;
    }

    /**
     * @return boolean
     */
    protected function isSandboxMode()
    {
        return (bool)$this->config->getValue('sandbox_flag');
    }

    /**
     * @return Authorization
     */
    protected function getAuthorization()
    {
        return $this->authorization;
    }
}
