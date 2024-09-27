
define(
    [
        'jquery',
        'ko',
        'Magento_Ui/js/form/form',
        'Magento_Customer/js/model/customer',
        'QuoteMaster_QuoteStorm/js/quote/model/show-fields',
        'uiRegistry',
        'QuoteMaster_QuoteStorm/js/checkout-quote-data',
    ],
    function (
        $,
        ko,
        Component,
        customer,
        showFields,
        registry,
        checkoutQuoteData
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'QuoteMaster_QuoteStorm/quote/view/quote-fields'
            },
            showGuestField: showFields.showGuestField,
            isCustomerLoggedIn: customer.isLoggedIn,
            requestDetails: ko.observable(false),

            /**
             * Init component
             */
            initialize: function () {
                this._super();

                this.requestDetails.subscribe((requestDetails) => {
                    if(requestDetails) {
                        showFields.showFields(true);
                        showFields.showGuestField(false)
                    } else {
                        showFields.showFields(false)
                        showFields.showGuestField(true)
                    }
                });

                registry.async('checkoutProvider')(function (checkoutProvider) {
                    var quoteGuestFieldsData = checkoutQuoteData.getQuoteGuestFieldsData();
                    var quoteFieldData = checkoutQuoteData.getQuoteFieldData();

                    if (quoteGuestFieldsData) {
                        checkoutProvider.set(
                            'quoteGuestFieldData',
                            $.extend({}, checkoutProvider.get('quoteGuestFieldData'), quoteGuestFieldsData)
                        );
                    }
                    if (quoteFieldData) {
                        checkoutProvider.set(
                            'quoteFieldData',
                            $.extend({}, checkoutProvider.get('quoteFieldData'), quoteFieldData)
                        );
                    }
                    checkoutProvider.on('quoteGuestFieldData', function (quoteGuestFieldsData) {
                        checkoutQuoteData.setQuoteGuestFieldsData(quoteGuestFieldsData);
                    });
                    checkoutProvider.on('quoteFieldData', function (quoteFieldData) {
                        checkoutQuoteData.setQuoteFieldData(quoteFieldData);
                    });
                });
            },

            /**
             * Function to validate form fields
             */
            validateFields: function () {
                var emailValidationResult = false,
                    loginFormSelector = 'form[data-role=email-with-possible-login]';

                if(customer.isLoggedIn()) {
                    emailValidationResult = true;
                } else {
                    $(loginFormSelector).validation();
                    emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
                }

                this.source.set('params.invalid',false);
                if(this.showGuestField()) {
                    this.source.trigger('quoteGuestFieldData.data.validate');
                }

                if (!emailValidationResult) {
                    $(loginFormSelector + ' input[name=username]').trigger('focus');

                    return false;
                }

                return !(this.source.get('params.invalid')) && emailValidationResult;
            }

        });
    }
);
