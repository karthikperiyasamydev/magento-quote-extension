
define(
    [
        'ko',
        'uiComponent',
        'Magento_Customer/js/model/customer',
    ],
    function (ko, Component,customer) {
        "use strict";

        return Component.extend({
            showFields: ko.observable(customer.isLoggedIn()),
            showGuestField: ko.observable(!customer.isLoggedIn()),

            /**
             * Initializes the component by calling the parent constructor.
             */
            initialize: function () {
                this._super();
            },

        })();
    }
);
