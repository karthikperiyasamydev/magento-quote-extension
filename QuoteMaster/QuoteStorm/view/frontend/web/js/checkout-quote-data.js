
define([
    'jquery',
    'Magento_Customer/js/customer-data',
], function ($, storage) {
    'use strict';

    var cacheKey = 'checkout-quote-data',

        /**
         * Save data to customer data storage
         *
         * @param {Object} data
         */
        saveData = function (data) {
            storage.set(cacheKey, data);
        },

        /**
         * Initialize quote data structure
         *
         * @returns {Object}
         */
        initData = function () {
            return {
                'quoteGuestFieldData': null,
                'quoteFieldData': null,
            };
        },

        /**
         * Retrieve quote data from storage
         *
         * @returns {Object}
         */
        getData = function () {
            var data = storage.get(cacheKey)();

            if ($.isEmptyObject(data)) {
                data = initData();
                saveData(data);
            }
            return data;
        };

    return {

        /**
         * Set quote guest fields data in storage
         *
         * @param {Object} data
         */
        setQuoteGuestFieldsData: function (data) {
            var obj = getData();

            obj.quoteGuestFieldData = data;
            saveData(obj);
        },

        /**
         * Get quote guest fields data from storage
         *
         * @returns {Object|null}
         */
        getQuoteGuestFieldsData: function () {
            return getData().quoteGuestFieldData;
        },

        /**
         * Set quote field data in storage
         *
         * @param {Object} data
         */
        setQuoteFieldData: function (data) {
            var obj = getData();

            obj.quoteFieldData = data;
            saveData(obj);
        },

        /**
         * Get quote field data from storage
         *
         * @returns {Object|null}
         */
        getQuoteFieldData: function () {
            return getData().quoteFieldData;
        },
    };
});
