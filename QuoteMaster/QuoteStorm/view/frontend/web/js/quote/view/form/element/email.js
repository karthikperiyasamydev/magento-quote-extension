/*
 * Copyright (c) 2024. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magento_Checkout/js/view/form/element/email',
        'Magento_Customer/js/model/customer',
    ],
    function (
        $,
        ko,
        Component,
        customer
    ) {
        'use strict';

        return Component.extend({
            /**
             * Initialize observable properties and fetch the customer's email.
             *
             * @return {Object}
             */
            initObservable: function () {
                this._super();
                this.getEmail();

                return this;
            },

            /**
             * Retrieve the customer's email if they are logged in and set it to the observable.
             */
            getEmail: function () {
                if(this.isCustomerLoggedIn()) {
                    this.email(customer.customerData.email);
                }
            }
        });
    }
);
