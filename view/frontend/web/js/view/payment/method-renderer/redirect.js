/************************************************************
 * *
 *  * Copyright © Boolfly. All rights reserved.
 *  * See COPYING.txt for license details.
 *  *
 *  * @author    info@boolfly.com
 * *  @project   Momo Wallet
 */
define([
    'jquery',
    'Magento_Checkout/js/view/payment/default',
    'Magento_Paypal/js/action/set-payment-method',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Magento_Customer/js/customer-data'
    ], function ($, Component, setPaymentMethodAction, additionalValidators, customerData) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Boolfly_MomoWallet/payment/momo-button'
            },
            placeOrderHandler: null,
            validateHandler: null,

            /**
             * @param {Function} handler
             */
            setPlaceOrderHandler: function (handler) {
                this.placeOrderHandler = handler;
            },

            /**
             * @param {Function} handler
             */
            setValidateHandler: function (handler) {
                this.validateHandler = handler;
            },

            /**
             * @returns {Object}
             */
            context: function () {
                return this;
            },

            /**
             * @returns {Boolean}
             */
            isShowLegend: function () {
                return true;
            },

            /**
             * @returns {String}
             */
            getCode: function () {
                return 'momo';
            },

            /**
             * @returns {Boolean}
             */
            isActive: function () {
                return true;
            },

            getPaymentAcceptanceMarkSrc: function () {
                return window.checkoutConfig.payment.momoWallet.logoSrc;
            },

            /** Redirect to Momo */
            continueToMomo: function () {
                if (additionalValidators.validate()) {
                    //update payment method information if additional data was changed
                    this.selectPaymentMethod();
                    setPaymentMethodAction(this.messageContainer).done(
                        function () {
                            customerData.invalidate(['cart']);
                            $.mage.redirect(
                                window.checkoutConfig.payment.momoWallet.redirectUrl
                            );
                        }
                    );

                    return false;
                }
            }
        });
    });
