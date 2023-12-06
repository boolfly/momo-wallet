/**
 * Copyright © Boolfly. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'Magento_Checkout/js/view/payment/default',
    'Magento_Paypal/js/action/set-payment-method',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Boolfly_MomoWallet/js/action/redirect-on-success',
    'Magento_Ui/js/model/messageList',
    'mage/translate'
], function ($, ko, Component, setPaymentMethodAction, additionalValidators, redirectOnSuccessAction, messageList, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Boolfly_MomoWallet/payment/momo-button'
        },
        redirectAfterPlaceOrder: true,
        placeOrderHandler: null,
        validateHandler: null,
        requestType: 'captureWallet',
        errorValidationRequestTypeMessage: ko.observable(false),
        paymentRequestType: 'input[name="payment[request_type]"]:checked',

        /**
         * Set place order handler
         * @param {Function} handler
         */
        setPlaceOrderHandler: function (handler) {
            this.placeOrderHandler = handler;
        },

        /**
         * Set validate handler
         * @param {Function} handler
         */
        setValidateHandler: function (handler) {
            this.validateHandler = handler;
        },

        /**
         * Context
         * @returns {Object}
         */
        context: function () {
            return this;
        },

        /**
         * Show legend
         * @returns {Boolean}
         */
        isShowLegend: function () {
            return true;
        },

        /**
         * Get code
         * @returns {String}
         */
        getCode: function () {
            return 'momo';
        },

        getData: function  () {
            return {
                'method': this.item.method,
                'additional_data': {
                    'request_type': this.requestType
                }
            };
        },

        /**
         * Is payment method active
         * @returns {Boolean}
         */
        isActive: function () {
            return true;
        },

        /**
         * Get Momo payment type information
         */
        getPaymentTypeInformation: function () {
            return this.getPaymentTypeList().map(value => ({
                type: value,
                logo: window.checkoutConfig.payment.momoWallet[value].logo,
                title: window.checkoutConfig.payment.momoWallet[value].title
            }));
        },

        /**
         * Logo Src
         * @returns {*}
         */
        getPaymentAcceptanceMarkSrc: function () {
            return window.checkoutConfig.payment.momoWallet.logoSrc;
        },

        /**
         * Get MoMo payment type list
         */
        getPaymentTypeList: function () {
            return window.checkoutConfig.payment.momoWallet.paymentTypeList.split(',');
        },

        /**
         * Place order.
         */
        placeOrder: function (data, event) {
            let self = this;

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
                        if (self.isSetPaymentRequestType()) {
                            if(self.validatePaymentRequestType()) {
                                self.placeOrder();
                            }
                            else
                            {
                                self.errorValidationRequestTypeMessage(
                                    $t('Please choose MoMo payment type.')
                                );
                            }
                        }
                        else {
                            self.placeOrder();
                        }
                    }
                );

                return false;
            }
        },

        /**
         * Set payment request type
         */
        setPaymentRequestType: function () {
            this.requestType = this.getPaymentRequestType();
        },

        /**
         * Get payment request type
         */
        getPaymentRequestType: function () {
            return $(this.paymentRequestType).val();
        },

        /**
         * Validate payment request type
         */
        validatePaymentRequestType: function () {
            return !!this.requestType;
        },

        /**
         * Select payment request type
         */
        selectPaymentRequestType: function () {
            this.requestType = this.getPaymentRequestType();
            this.errorValidationRequestTypeMessage(false);
        },

        /**
         * Is set payment request type
         */
        isSetPaymentRequestType: function () {
            return window.checkoutConfig.payment.momoWallet.paymentTypeList != null;
        }
    });
});