define([
    'jquery',
    'Magento_Ui/js/modal/confirm',
    'mage/translate',
    'Magento_Checkout/js/action/update-shopping-cart',
    'jquery-ui-modules/widget',
], function ($, confirm,$t,cartUpdate) {
    'use strict';

    $.widget('quotemaster.updateShoppingQuote', cartUpdate, {

        /**
         * Show confirmation dialog when trying to navigate away from the page
         * @param {string} nextPageUrl
         * @returns {void}
         */
        changePageConfirm: function (nextPageUrl) {
            confirm({
                title: $t('Are you sure you want to leave the page?'),
                content: $t('Changes you made to the quote will not be saved.'),
                actions: {
                    confirm: function () {
                        window.location.href = nextPageUrl;
                    }
                },
                buttons: [{
                    text: $t('Cancel'),
                    class: 'action-secondary action-dismiss',
                    click: function (event) {
                        this.closeModal(event);
                    }
                }, {
                    text: $t('Leave'),
                    class: 'action-primary action-accept',
                    click: function (event) {
                        this.closeModal(event, true);
                    }
                }]
            });
        }
    });

    return $.quotemaster.updateShoppingQuote;
});
