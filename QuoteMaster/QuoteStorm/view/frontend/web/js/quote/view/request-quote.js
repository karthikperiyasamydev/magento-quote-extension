define(
    [
        'jquery',
        'ko',
        'Magento_Ui/js/form/form',
        'QuoteMaster_QuoteStorm//js/quote/model/shipping-save-processor/default',
        'Magento_Checkout/js/checkout-data',
        'Magento_Customer/js/model/customer',
        'QuoteMaster_QuoteStorm//js/quote/action/place-quote',
    ],
    function (
        $,
        ko,
        Component,
        setShippingInformationAction,
        checkoutData,
        customer,
        placeQuote
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'QuoteMaster_QuoteStorm/quote/view/request-quote'
            },
            shippingSelector: '.checkout-shipping-address',
            billingSelector: '.billing-address-form',
            quoteFieldsSelector: '#quote-fields',
            shippingReady: ko.observable(false),
            billingReady: ko.observable(false),
            quotationReady: ko.observable(false),
            readyToRequest: null,
            isCustomerLoggedIn: customer.isLoggedIn,

            /**
             * Initialize component
             */
            initialize: function () {
                this._super();
                var self = this;
                self.readyToRequest = ko.computed(function () {
                    return (self.shippingReady() && self.billingReady() && self.quotationReady());
                }, this);
                self.readyToRequest.subscribe(function (readyToRequest) {
                    if (readyToRequest === true) {
                        console.log("called");
                        var checkoutAsGuest = true;
                        if(self.getQuoteFieldsModel().requestDetails()) {
                            checkoutAsGuest = false
                        }
                        placeQuote(checkoutAsGuest);
                        self.resetForm();
                    }
                });
            },

            /**
             * Validate quote fields
             */
            validateQuote: function () {
                var self = this;
                if(self.isCustomerLoggedIn()) {
                    self.checkShippingAddress();
                } else {
                    if(!self.getQuoteFieldsModel().requestDetails()) {
                        self.quotationReady(self.getQuoteFieldsModel().validateFields());
                        self.shippingReady(true);
                        self.billingReady(true);
                    } else {
                        self.checkShippingAddress();
                    }
                }
            },

            /**
             * Get the shipping address model
             */
            getShippingModel: function () {
                return ko.dataFor($(this.shippingSelector)[0]);
            },

            /**
             * Get the billing address model
             */
            getBillingModel: function () {
                return ko.dataFor($(this.billingSelector)[0]);
            },

            /**
             * Check if the shipping address is visible
             */
            hasShippingAddress: function () {
                return $(this.shippingSelector).is(":visible");
            },

            /**
             * Check if the billing address is visible
             */
            hasBillingAddress: function () {
                return $(this.billingSelector).is(":visible");
            },

            /**
             * Get the quote fields model
             */
            getQuoteFieldsModel: function () {
                return ko.dataFor($(this.quoteFieldsSelector)[0]);
            },

            /**
             * Check and update the billing address
             */
            checkBillingAddress: function () {
                var self = this;
                if (!self.getBillingModel().isAddressSameAsShipping()) {
                    self.getBillingModel().updateAddress().done(function () {
                        if (!self.getBillingModel().source.get('params.invalid')) {
                            self.billingReady(true);
                            self.quotationReady(true);
                        } else {
                            self.billingReady(false);
                            self.quotationReady(false);
                        }
                    });
                } else {
                    self.getBillingModel().updateByShippingAddress().done(function () {
                        self.billingReady(true);
                        self.quotationReady(true);
                    });
                }
            },

            /**
             * Check and validate the shipping address
             */
            checkShippingAddress: function () {
                var self = this;
                if (self.getShippingModel().validateShippingInformation()) {
                    setShippingInformationAction.saveShippingInformation().done(function () {
                        self.shippingReady(true);
                        self.checkBillingAddress();
                    });
                } else {
                    self.shippingReady(false);
                    self.checkBillingAddress();
                }
            },

            /**
             * Reset the form fields
             */
            resetForm: function () {
                var self = this;
                self.shippingReady(false);
                self.billingReady(false);
                self.quotationReady(false);
            },
        });
    }
);
