<?php
 /************************************************************
  * *
  *  * Copyright © Boolfly. All rights reserved.
  *  * See COPYING.txt for license details.
  *  *
  *  * @author    info@boolfly.com
  * *  @project   Momo Wallet
  */
use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Boolfly_MomoWallet',
    __DIR__
);
