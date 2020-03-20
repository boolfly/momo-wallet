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
    'Boolfly_MomoWallet/js/action/redirect-on-success'
    ], function ($, Component, setPaymentMethodAction, additionalValidators, redirectOnSuccessAction) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Boolfly_MomoWallet/payment/momo-button'
            },
            redirectAfterPlaceOrder: true,
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

            /**
             * Logo Src
             * @returns {*}
             */
            getPaymentAcceptanceMarkSrc: function () {
                return window.checkoutConfig.payment.momoWallet.logoSrc;
            },

            /**
             * Place order.
             */
            placeOrder: function (data, event) {
                var self = this;

                if (event) {
                    event.preventDefault();
                }

                if (this.validate() &&
                    additionalValidators.validate() &&
                    this.isPlaceOrderActionAllowed() === true
                ) {
                    this.isPlaceOrderActionAllowed(false);
                    this.getPlaceOrderDeferredObject()
                        .done(
                            function () {
                                self.afterPlaceOrder();
                                if (self.redirectAfterPlaceOrder) {
                                    redirectOnSuccessAction.execute();
                                }
                            }
                        ).always(
                            function () {
                                self.isPlaceOrderActionAllowed(true);
                            }
                    );

                    return true;
                }

                return false;
            },
            /** Redirect to Momo */
            continueToMomo: function () {
                if (additionalValidators.validate()) {
                    var self = this;
                    //update payment method information if additional data was changed
                    this.selectPaymentMethod();
                    setPaymentMethodAction(this.messageContainer).done(
                        function () {
                            self.placeOrder();
                        }
                    );

                    return false;
                }
            }
        });
    });
