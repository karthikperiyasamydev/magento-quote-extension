

define([
    'jquery',
    'mage/translate',
    'jquery-ui-modules/widget',
    'Magento_Catalog/js/catalog-add-to-cart'
], function ($, $t) {
    "use strict";

    $.widget('quotestorm.catalogAddToCart', $.mage.catalogAddToCart, {
        options: {
            addToQuoteButtonSelector: '.action.toquote',
            addToQuoteButtonTextDefault: $t('Add to Quote'),
            addToQuoteFormAction: null,
            miniquoteSelector: '[data-block="miniquote"]',
            defaultAction: '',
            defaultButtonSelector: '',
            defaultButtonTextDefault: '',
            defaultMiniSelector: '',
        },

        /**
         * Initialize the widget
         * @inheritdoc
         */
        _create: function () {
            this._super();
            this._setDefaultValues();
        },

        /**
         * Set default values for action URLs and button selectors
         * @protected
         */
        _setDefaultValues: function () {
            this.options.defaultAction = this.element.attr('action');
            this.options.defaultButtonSelector = this.options.addToCartButtonSelector;
            this.options.defaultButtonTextDefault = this.options.addToCartButtonTextDefault;
            this.options.defaultMiniSelector = this.options.minicartSelector;
        },

        /**
         * Prepare the form for submitting to the cart
         * @protected
         */
        _prepareForCartSubmit: function () {
            this.element.attr('action', this.options.defaultAction);
            this.options.addToCartButtonSelector = this.options.defaultButtonSelector;
            this.options.addToCartButtonTextDefault = this.options.defaultButtonTextDefault;
            this.options.minicartSelector = this.options.defaultMiniSelector;
        },

        /**
         * Prepare the form for submitting to the quote
         * @protected
         */
        _prepareForQuoteSubmit: function () {
            this.element.attr('action', this.options.addToQuoteFormAction);
            this.options.addToCartButtonSelector = this.options.addToQuoteButtonSelector;
            this.options.addToCartButtonTextDefault = this.options.addToQuoteButtonTextDefault;
            this.options.minicartSelector = this.options.miniquoteSelector;
        },

        /**
         * Initialize event handlers for button clicks
         * @protected
         */
        _init: function () {
            this.element.find(this.options.addToCartButtonSelector).on(
                'click',
                this._prepareForCartSubmit.bind(this)
            );
            this.element.find(this.options.addToQuoteButtonSelector).on(
                'click',
                this._prepareForQuoteSubmit.bind(this)
            );
        },

        /**
         * Submit the form
         * @param {Object} form - The form element to submit
         */
        submitForm: function (form) {
            this._super(form);
        }
    });

    return $.quotestorm.catalogAddToCart;
});
