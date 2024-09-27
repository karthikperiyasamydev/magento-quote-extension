var config = {
    map: {
        '*': {
            addToQuote: 'QuoteMaster_QuoteStorm/js/add-to-quote',
            catalogAddToCart: 'QuoteMaster_QuoteStorm/js/catalog-add-to-cart',
            disableButton: 'QuoteMaster_QuoteStorm/js/disable-button',
            requestQuote: 'QuoteMaster_QuoteStorm/js/quote/request-quote',
            shoppingQuote: 'QuoteMaster_QuoteStorm/js/shopping-quote',
            updateShoppingQuote: 'QuoteMaster_QuoteStorm/js/update-shopping-quote',
            'Magento_ConfigurableProduct/js/options-updater': 'QuoteMaster_QuoteStorm/js/options-updater',
        }
    },
    config: {
        mixins: {
            'Magento_Catalog/js/validate-product': {
                'QuoteMaster_QuoteStorm/js/validate-product-mixin': true
            }
        }
    }
};
