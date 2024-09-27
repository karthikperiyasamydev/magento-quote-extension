define([
    'jquery',
    'Magento_Ui/js/modal/confirm',
    'mage/translate',
    'Magento_Checkout/js/shopping-cart',
    'jquery-ui-modules/widget',
], function ($, confirm,$t,shoppingCart) {
    'use strict';

    $.widget('quotemaster.shoppingQuote', shoppingCart, {

        /**
         * Show confirmation dialog before clearing the cart
         * @returns {void}
         */
        _confirmClearCart: function () {
            var self = this;

            confirm({
                content: $.mage.__('Are you sure you want to remove all items from your quote?'),
                actions: {
                    confirm: function () {
                        self.clearCart();
                    }
                }
            });
        }
    });

    return $.quotemaster.shoppingQuote;
});
