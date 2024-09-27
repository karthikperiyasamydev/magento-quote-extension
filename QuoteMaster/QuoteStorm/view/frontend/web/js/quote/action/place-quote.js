
define(
    [
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/place-order',
        'QuoteMaster_QuoteStorm/js/quote/model/resource-url-manager',
        'mage/translate',
        'Magento_Ui/js/model/messageList',
        'Magento_Customer/js/model/customer',
        'mage/url',
        'Magento_Customer/js/customer-data',
        'QuoteMaster_QuoteStorm/js/checkout-quote-data'
    ],
    function (
        $,
        quote,
        placeOrderService,
        resourceUrlManager,
        $t,
        globalMessageList,
        customer,
        url,
        customerData,
        checkoutQuoteData
    ) {
        'use strict';

        return function (checkoutAsGuest) {
            var quoteRequestParams,quoteData;

            /**
             * Handles successful quote placement
             * Redirects to the success URL and invalidates the quote data
             */
            function handleSuccess()
            {
                var url = resourceUrlManager.getUrlForRedirectOnSuccess();
                customerData.invalidate(['quote']);
                // window.location.replace(url);
            }

            /**
             * Handles errors during quote placement
             * Redirects to the customer login page
             */
            function handleError()
            {
                window.location.replace(url.build('customer/account/login/'));
            }


            quoteRequestParams = {
                cartId: quote.getQuoteId(),
                form_key: $.mage.cookies.get('form_key'),
                customer_email: quote.guestEmail == null ? customer.customerData.email : quote.guestEmail,
                check_as_guest: checkoutAsGuest,
                clear_quote: true
            };

            quoteData = {
                quote_guest_data: JSON.stringify(checkoutQuoteData.getQuoteGuestFieldsData()),
                quote_field_data: JSON.stringify(checkoutQuoteData.getQuoteFieldData())
            }

            /**
             * Retrieves the request URL for placing the quote
             *
             * @returns {string}
             */
            function getRequestUrl()
            {
                return resourceUrlManager.getUrlForPlaceQuote(quoteRequestParams);
            }

            return placeOrderService(getRequestUrl(), quoteData, globalMessageList).done(
                function (result) {
                    if (result.success) {
                        var clearData = {
                            'quoteGuestFieldData': null,
                            'quoteFieldData': null
                        };
                        customerData.set('checkout-quote-data',clearData);
                        handleSuccess();
                    } else {
                        handleError();
                    }
                }
            ).fail(
                function (response) {
                    if (response.status === 404 || response.status === 403) {
                        location.reload();
                    }
                }
            );
        };
    }
);
