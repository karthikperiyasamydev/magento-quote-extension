
define(
    [
        'mage/url',
    ],
    function (url) {
        "use strict";

        return {

            /**
             * Generate the URL for placing a quote request.
             *
             * @param {Object} params
             * @return {String}
             */
            getUrlForPlaceQuote: function (params) {
                var newUrl = 'quote/quote/requestQuote';
                return url.build(newUrl) + this.prepareParams(params);
            },

            /**
             * Generate the URL for redirecting to the success page after quote placement.
             *
             * @return {String}
             */
            getUrlForRedirectOnSuccess: function () {
                var newUrl = 'quote/quote/success';
                return url.build(newUrl);
            },

            /**
             * Prepare the URL parameters from the provided object.
             *
             * @param {Object} params
             * @return {String}
             */
            prepareParams: function (params) {
                var result = '?';

                _.each(params, function (value, key) {
                    result += key + '=' + value + '&';
                });

                return result.slice(0, -1);
            }
        };
    }
);
