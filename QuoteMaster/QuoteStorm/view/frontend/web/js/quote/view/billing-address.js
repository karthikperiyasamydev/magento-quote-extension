
define([
        'jquery',
        'ko',
        'underscore',
        'Magento_Ui/js/form/form',
        'Magento_Customer/js/model/customer',
        'Magento_Customer/js/model/address-list',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/create-billing-address',
        'Magento_Checkout/js/action/select-billing-address',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'Magento_Customer/js/customer-data',
        'QuoteMaster_QuoteStorm/js/quote/model/billing-save-processor/default',
        'mage/translate',
        'Magento_Checkout/js/model/billing-address-postcode-validator',
        'Magento_Checkout/js/model/address-converter',
        'QuoteMaster_QuoteStorm/js/quote/model/show-fields',
        'Magento_Ui/js/model/messageList',
    ],
    function (
        $,
        ko,
        _,
        Component,
        customer,
        addressList,
        quote,
        createBillingAddress,
        selectBillingAddress,
        checkoutData,
        checkoutDataResolver,
        customerData,
        setBillingAddressAction,
        $t,
        billingAddressPostcodeValidator,
        addressConverter,
        showFields,
        messageList
    ) {
        'use strict';

        var countryData = customerData.get('directory-data'),
            addressOptions = addressList().filter(function (address) {
                return address.getType() === 'customer-address';
            });

        return Component.extend({
            defaults: {
                links: {
                    isAddressFormVisible: '${$.billingAddressListProvider}:isNewAddressSelected'
                }
            },
            currentBillingAddress: quote.billingAddress,
            customerHasAddresses: addressOptions.length > 0,
            allowToShowForm: showFields.showFields,

            /**
             * Init component
             */
            initialize: function () {
                this._super();
                checkoutDataResolver.resolveBillingAddress();
                billingAddressPostcodeValidator.initFields(this.get('name') + '.form-fields');
            },

            /**
             * Initialize observables for the component
             * @return {exports.initObservable}
             */
            initObservable: function () {
                this._super()
                    .observe({
                        selectedAddress: null,
                        isAddressDetailsVisible: false,
                        isAddressFormVisible: !customer.isLoggedIn() || !addressOptions.length,
                        isAddressSameAsShipping: !quote.isVirtual(),
                        saveInAddressBook: 1
                    });

                quote.billingAddress.subscribe(function (newAddress) {
                    if (quote.isVirtual()) {
                        this.isAddressSameAsShipping(false);
                    }

                    if (newAddress != null && newAddress.saveInAddressBook !== null) {
                        this.saveInAddressBook(newAddress.saveInAddressBook);
                    } else {
                        this.saveInAddressBook(1);
                    }
                }, this);


                return this;
            },

            /**
             * Computed observable to check if shipping address can be used for billing
             */
            canUseShippingAddress: ko.computed(function () {
                return !quote.isVirtual() && quote.shippingAddress() && quote.shippingAddress().canUseForBilling();
            }),

            /**
             * Get address display text
             * @param {Object} address
             * @return {*}
             */
            addressOptionsText: function (address) {
                return address.getAddressInline();
            },

            /**
             * Determine if the shipping address should be used
             * @return {Boolean}
             */
            useShippingAddress: function () {
                if (this.isAddressSameAsShipping()) {
                    selectBillingAddress(quote.shippingAddress());
                    this.isAddressDetailsVisible(false);
                } else {
                    quote.billingAddress(null);
                    this.isAddressDetailsVisible(true);
                }
                checkoutData.setSelectedBillingAddress(null);

                return true;
            },

            /**
             * Update address action
             */
            updateAddress: function () {
                var addressData, newBillingAddress;

                if (this.selectedAddress() && !this.isAddressFormVisible()) {
                    selectBillingAddress(this.selectedAddress());
                    checkoutData.setSelectedBillingAddress(this.selectedAddress().getKey());
                    return setBillingAddressAction(messageList);
                } else {
                    this.source.set('params.invalid', false);
                    this.source.trigger(this.dataScopePrefix + '.data.validate');

                    if (this.source.get(this.dataScopePrefix + '.custom_attributes')) {
                        this.source.trigger(this.dataScopePrefix + '.custom_attributes.data.validate');
                    }

                    if (!this.source.get('params.invalid')) {
                        addressData = this.source.get(this.dataScopePrefix);

                        if (customer.isLoggedIn() && !this.customerHasAddresses) { //eslint-disable-line max-depth
                            this.saveInAddressBook(1);
                        }
                        addressData['save_in_address_book'] = this.saveInAddressBook() ? 1 : 0;
                        newBillingAddress = createBillingAddress(addressData);
                        // New address must be selected as a billing address
                        selectBillingAddress(newBillingAddress);
                        checkoutData.setSelectedBillingAddress(newBillingAddress.getKey());
                        checkoutData.setNewCustomerBillingAddress(addressData);
                        return setBillingAddressAction(messageList);
                    } else {
                        return $.Deferred().reject();
                    }
                }
            },

            /**
             * Update billing address to match the shipping address
             */
            updateByShippingAddress: function () {
                selectBillingAddress(quote.shippingAddress());
                return setBillingAddressAction(messageList);
            },

            /**
             * Get country name by ID
             * @param {Number} countryId
             * @return {*}
             */
            getCountryName: function (countryId) {
                return countryData()[countryId] !== undefined ? countryData()[countryId].name : ''; //eslint-disable-line
            },

            /**
             * Get code
             * @param {Object} parent
             * @returns {String}
             */
            getCode: function (parent) {
                return _.isFunction(parent.getCode) ? parent.getCode() : 'shared';
            },

        });
    });
