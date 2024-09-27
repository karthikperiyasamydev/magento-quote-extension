
define(
    [
        'Magento_Checkout/js/view/shipping',
        'QuoteMaster_QuoteStorm/js/quote/model/show-fields',
    ],
    function (
        Component,
        showFields,
    ) {
        'use strict';

        return Component.extend({
            allowToShowForm: showFields.showFields,
        });
    }
);
