define([
    'jquery',
    'underscore',
    'Magento_Customer/js/customer-data',
    'domReady!'
], function ($, _, customerData) {
    'use strict';

    var selectors = {
            formSelector: '#product_addtocart_form',
            productIdSelector: '#product_addtocart_form [name="product"]',
            itemIdSelector: '#product_addtocart_form [name="item"]'
        },
        cartData = customerData.get('cart'),
        quoteData = customerData.get('quote'),
        productId = $(selectors.productIdSelector).val(),
        itemId = $(selectors.itemIdSelector).val(),

        /**
         * Determine the current page
         *
         * @returns {String}
         */
        getCurrentPage = function () {
            if (window.location.href.indexOf('/checkout/cart/') !== -1) {
                return 'cart';
            } else if (window.location.href.indexOf('/quote/') !== -1) {
                return 'quote';
            } else {
                return 'other';
            }
        },

        /**
         * Set product options based on the provided data (cart or quote)
         *
         * @param {Object} data
         * @returns {Boolean}
         */
        setProductOptions = function (data) {
            var changedProductOptions;

            if (!(data && data.items && data.items.length && productId)) {
                return false;
            }

            changedProductOptions = _.find(data.items, function (item) {
                return item['item_id'] === itemId && item['product_id'] === productId;
            });

            changedProductOptions = changedProductOptions && changedProductOptions.options &&
                changedProductOptions.options.reduce(function (obj, val) {
                    obj[val['option_id']] = val['option_value'];
                    return obj;
                }, {});

            if (JSON.stringify(this.productOptions || {}) === JSON.stringify(changedProductOptions || {})) {
                return false;
            }

            this.productOptions = changedProductOptions;

            return true;
        },

        /**
         * Listen for updates to cart data or quote data and update selected options accordingly
         *
         */
        listen = function () {
            var currentPage = getCurrentPage();

            if (currentPage === 'cart') {
                cartData.subscribe(function (updateCartData) {
                    if (this.setProductOptions(updateCartData)) {
                        this.updateOptions();
                    }
                }.bind(this));

                $(selectors.formSelector).on(this.eventName, function () {
                    this.setProductOptions(cartData());
                    this.updateOptions();
                }.bind(this));
            } else if (currentPage === 'quote') {
                quoteData.subscribe(function (updateQuoteData) {
                    if (this.setProductOptions(updateQuoteData)) {
                        this.updateOptions();
                    }
                }.bind(this));

                $(selectors.formSelector).on(this.eventName, function () {
                    this.setProductOptions(quoteData());
                    this.updateOptions();
                }.bind(this));
            }
        },

        /**
         * Updater constructor function
         *
         */
        Updater = function (eventName, updateOptionsCallback) {
            if (this instanceof Updater) {
                this.eventName = eventName;
                this.updateOptions = updateOptionsCallback;
                this.productOptions = {};
            }
        };

    Updater.prototype.setProductOptions = setProductOptions;
    Updater.prototype.listen = listen;

    return Updater;
});
