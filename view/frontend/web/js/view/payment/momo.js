/**
 * Copyright © Boolfly. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';

        rendererList.push(
            {
                type: 'momo',
                component: 'Boolfly_MomoWallet/js/view/payment/method-renderer/momo-wallet'
            }
        );

        /**
         * Add view logic here if needed
         */

        return Component.extend({});
    }
);
