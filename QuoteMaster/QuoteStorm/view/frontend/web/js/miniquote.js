define([
    'uiComponent',
    'jquery',
    'Magento_Customer/js/customer-data',
    'mage/dropdown'
], function (Component, $, customerData) {
    'use strict';

    return Component.extend({
        quoteUrl: window.quote.quoteUrl,

        /**
         * Initialize the component
         * @inheritdoc
         */
        initialize: function () {
            this._super();
            this.quote = customerData.get('quote');
        },

        /**
         * Close the mini quote dropdown dialog
         * @returns {void}
         */
        closeMiniquote: function () {
            $('[data-block="miniquote"]').find('[data-role="dropdownDialog"]').dropdownDialog('close');
        },
    });
});
