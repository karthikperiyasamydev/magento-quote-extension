define([
    'jquery',
    'jquery-ui-modules/widget'
], function ($) {
    'use strict';

    $.widget('mage.disableButton', {
        options: {
            quoteButtonId: ''
        },

        /**
         * Initialize the widget
         * @inheritdoc
         */
        _create: function () {
            this.element.on('submit', this.submitForm.bind(this));
        },

        /**
         * Handle the form submission
         * Disables the quote button if the form is valid
         * @returns {void}
         */
        submitForm: function () {
            if (this.element.valid()) {
                $(this.options.quoteButtonId).prop('disabled', true);
            }
        }

    });

    return $.mage.disableButton;
});
