/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Osc
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define(
    [
        'jquery',
        'ko',
        'underscore',
        'Magento_Checkout/js/view/billing-address',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/checkout-data',
        'Mageplaza_Osc/js/model/osc-data',
        'Magento_Checkout/js/action/create-billing-address',
        'Magento_Checkout/js/action/select-billing-address',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/action/set-billing-address',
        'Magento_Checkout/js/model/address-converter',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Ui/js/model/messageList',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'Mageplaza_Osc/js/model/address/auto-complete',
        'Mageplaza_Osc/js/model/compatible/amazon-pay',
        'uiRegistry',
        'mage/translate',
        'rjsResolver',
        'Mageplaza_Osc/js/model/paypal_express_compatible',
        'Magento_Customer/js/model/address-list',
        'Magento_Checkout/js/model/shipping-rates-validator'
    ],
    function ($,
              ko,
              _,
              Component,
              quote,
              checkoutData,
              oscData,
              createBillingAddress,
              selectBillingAddress,
              customer,
              setBillingAddressAction,
              addressConverter,
              additionalValidators,
              globalMessageList,
              checkoutDataResolver,
              addressAutoComplete,
              amazonPay,
              registry,
              $t,
              resolver,
              paypalExpressCompatible,
              addressList,
              addressPostCodeValidator) {
        'use strict';

        var observedElements = [],
            canShowBillingAddress = window.checkoutConfig.oscConfig.showBillingAddress;

        var newAddressOption = {
                /**
                 * Get new address label
                 * @returns {String}
                 */
                getAddressInline: function () {
                    return $t('New Address');
                },
                customerAddressId: null
            },
            addressOptions = addressList().filter(function (address) {
                return address.getType() === 'customer-address';
            });

        addressOptions.push(newAddressOption);

        return Component.extend({
            defaults: {
                template: ''
            },
            isCustomerLoggedIn: customer.isLoggedIn,
            isAmazonAccountLoggedIn: amazonPay.isAmazonAccountLoggedIn,
            quoteIsVirtual: quote.isVirtual(),
            addressOptions: addressOptions,

            canUseShippingAddress: ko.computed(function () {
                return !quote.isVirtual() && quote.shippingAddress() &&
                    quote.shippingAddress().canUseForBilling() && canShowBillingAddress;
            }),

            /**
             * @return {exports}
             */
            initialize: function () {
                var self = this;

                this._super();

                this.initFields();

                additionalValidators.registerValidator(this);

                registry.async('checkoutProvider')(function (checkoutProvider) {
                    var billingAddressData = checkoutData.getBillingAddressFromData();

                    if (billingAddressData) {
                        checkoutProvider.set(
                            'billingAddress',
                            $.extend({}, checkoutProvider.get('billingAddress'), billingAddressData)
                        );
                    }
                    checkoutProvider.on('billingAddress', function (billingAddress) {
                        checkoutData.setBillingAddressFromData(billingAddress);
                    });
                });

                quote.shippingAddress.subscribe(function (newAddress) {
                    if (self.isAddressSameAsShipping()) {
                        selectBillingAddress(newAddress);
                    }
                });

                resolver(this.afterResolveDocument.bind(this));

                return this;
            },

            afterResolveDocument: function () {
                this.saveBillingAddress();

                addressAutoComplete.register('billing');
                paypalExpressCompatible.togglePlaceOrderButton(quote.paymentMethod());
            },

            /**
             * @return {Boolean}
             */
            useShippingAddress: function () {
                if (this.isAddressSameAsShipping()) {
                    selectBillingAddress(quote.shippingAddress());
                    checkoutData.setSelectedBillingAddress(null);
                    if (window.checkoutConfig.reloadOnBillingAddress) {
                        setBillingAddressAction(globalMessageList);
                    }
                } else {
                    this.updateAddress();
                }

                return true;
            },

            /**
             *
             * @param address
             */
            onAddressChange: function (address) {
                this.isAddressFormVisible(address === newAddressOption);

                if (!this.isAddressSameAsShipping() && canShowBillingAddress) {
                    this.updateAddress();
                }
            },

            /**
             * Update address action
             */
            updateAddress: function () {
                var self = this,
                    newBillingAddress, selectedAddress, addressData;

                if (this.selectedAddress() && !this.isAddressFormVisible()) {
                    newBillingAddress = createBillingAddress(this.selectedAddress());

                    selectedAddress = {
                        customerAddressId: this.selectedAddress().customerAddressId,
                        customerId: this.selectedAddress().customerId,
                        sameAsBilling: this.selectedAddress().sameAsBilling,
                        regionId: this.selectedAddress().regionId,
                        getAddressInline: function () {
                            return self.selectedAddress().getAddressInline();
                        }
                    };

                    selectBillingAddress($.extend(newBillingAddress, selectedAddress));
                    checkoutData.setSelectedBillingAddress(this.selectedAddress().getKey());
                } else {
                    addressData = this.source.get('billingAddress');

                    if (customer.isLoggedIn() && !this.customerHasAddresses) {
                        this.saveInAddressBook(1);
                        addressData.save_in_address_book = 1;
                    } else {
                        addressData.save_in_address_book = 0;
                    }

                    if (addressData.custom_attributes) {
                        _.each(addressData.custom_attributes, function (value, key) {
                            if (_.isEmpty(value)) {
                                delete addressData.custom_attributes[key];
                            }
                        });
                    }
                    newBillingAddress = createBillingAddress(addressData);

                    // New address must be selected as a billing address
                    selectBillingAddress(newBillingAddress);
                    checkoutData.setSelectedBillingAddress(newBillingAddress.getKey());
                    checkoutData.setNewCustomerBillingAddress(addressData);
                }
                if (window.checkoutConfig.reloadOnBillingAddress) {
                    setBillingAddressAction(globalMessageList);
                }
            },

            /**
             * Perform postponed binding for fieldset elements
             */
            initFields: function () {
                var self = this,
                    addressFields = window.checkoutConfig.oscConfig.addressFields,
                    fieldsetName = 'checkout.steps.shipping-step.billingAddress.billing-address-fieldset';

                $.each(addressFields, function (index, field) {
                    registry.async(fieldsetName + '.' + field)(self.bindHandler.bind(self));
                });

                return this;
            },

            bindHandler: function (element) {
                var self = this;

                if (element.component.indexOf('/group') !== -1) {
                    $.each(element.elems(), function (index, elem) {
                        registry.async(elem.name)(function () {
                            self.bindHandler(elem);
                        });
                    });
                } else {
                    element.on('value', this.saveBillingAddress.bind(this, element));
                    observedElements.push(element);
                }
            },

            saveBillingAddress: function (element) {
                var addressFlat, newBillingAddress;
                var fieldName = element ? element.index : null;

                if (fieldName === 'postcode') {
                    addressPostCodeValidator.postcodeValidation(element);
                }

                if (this.isAddressSameAsShipping()) {
                    return;
                }

                if (!canShowBillingAddress && !this.quoteIsVirtual) {
                    selectBillingAddress(quote.shippingAddress());
                } else if (this.isAddressFormVisible()) {
                    addressFlat = addressConverter.formDataProviderToFlatData(
                        this.collectObservedData(),
                        'billingAddress'
                    );

                    if (customer.isLoggedIn() && !this.customerHasAddresses) {
                        this.saveInAddressBook(1);
                    }
                    addressFlat.save_in_address_book = this.saveInAddressBook() ? 1 : 0;
                    newBillingAddress = createBillingAddress(addressFlat);

                    // New address must be selected as a billing address
                    selectBillingAddress(newBillingAddress);
                    checkoutData.setSelectedBillingAddress(newBillingAddress.getKey());
                    checkoutData.setNewCustomerBillingAddress(addressFlat);
                    if (window.checkoutConfig.reloadOnBillingAddress && fieldName === 'country_id') {
                        setBillingAddressAction(globalMessageList);
                    }
                }
            },

            /**
             * Collect observed fields data to object
             *
             * @returns {*}
             */
            collectObservedData: function () {
                var observedValues = {};

                $.each(observedElements, function (index, field) {
                    observedValues[field.dataScope] = field.value();
                });

                return observedValues;
            },

            validate: function () {

                if (this.isAmazonAccountLoggedIn()) {
                    return true;
                }

                if (this.isAddressSameAsShipping()) {
                    oscData.setData('same_as_shipping', true);
                    return true;
                }

                if (!this.isAddressFormVisible()) {
                    return true;
                }

                this.source.set('params.invalid', false);
                this.source.trigger('billingAddress.data.validate');

                if (this.source.get('billingAddress.custom_attributes')) {
                    this.source.trigger('billingAddress.custom_attributes.data.validate');
                }

                oscData.setData('same_as_shipping', false);
                return !this.source.get('params.invalid');
            },
            getAddressTemplate: function () {
                return 'Mageplaza_Osc/container/address/billing-address';
            },
        });
    }
);

function show_billing_block(k) {
    if (k === 1) {
        console.error(1)
        document.getElementById("billing-checked").classList.remove("d-none");
        document.getElementById("billing-unchecked").classList.add("d-none");
        document.getElementById("billing-shipping-the-same").classList.add("active-tab");
        document.getElementById("billing-shipping-the-same").classList.remove("border-2");
        document.getElementById("billing-shipping-the-same").removeAttribute("onclick");
        document.getElementById("billing-shipping-the-same").setAttribute("onclick", "show_billing_block(2);");
        document.getElementById("billing").classList.add("d-none");
    }
    if (k === 2) {
        console.error(2)
        document.getElementById("billing-checked").classList.add("d-none");
        document.getElementById("billing-unchecked").classList.remove("d-none");
        document.getElementById("billing-shipping-the-same").classList.remove("active-tab");
        document.getElementById("billing-shipping-the-same").classList.add("border-2");
        document.getElementById("billing-shipping-the-same").removeAttribute("onclick");
        document.getElementById("billing-shipping-the-same").setAttribute("onclick", "show_billing_block(1);");
        document.getElementById("billing").classList.remove("d-none");
        document.getElementById("company-data-block").style.display = "none";
        document.getElementById("company-purchase-yes").classList.add("d-none");
        document.getElementById("company-purchase-no").classList.remove("d-none");
        document.getElementById("company-purchase").classList.remove("active-tab");
        document.getElementById("company-purchase").classList.add("border-2");
        document.getElementById("company-purchase").removeAttribute("onclick");
        document.getElementById("company-purchase").setAttribute("onclick", "companyPurchase(1)");
    }
}

var interval = setInterval(checkBilling, 1000);

function checkBilling() {
    if (document.getElementById("billing_address_id")) {
        var select = document.getElementById("billing_address_id");
        for (var i = 0; i < select.length; i++) {
            var text = select[i].text;
            var billing = document.getElementById("billing-addresses");
            if (text != 'New Address') {
                text = text.replace(",", "\n");
                var div = document.createElement("div");
                if (i == 0) {
                    div.className = "shipping-address-item selected-item";
                } else {
                    div.className = "shipping-address-item not-selected-item";
                }
                div.innerText = text;
                div.id = "billing-address-" + i;
                billing.appendChild(div);
                document.getElementById("billing-address-" + i).setAttribute("onclick", "setActiveBillingAddress(" + i + ")");
            }
        }
        document.getElementById("new-billing-address").style.display = "block";
        clearInterval(interval);
    }
}

function companyPurchase(k) {
    var company_name = document.getElementById("company_name").value;
    var company_country = document.getElementById("company_country").value;
    var company_city = document.getElementById("company_city").value;
    var company_address = document.getElementById("company_address").value;
    var company_cui = document.getElementById("company_cui").value;
    var company_reg_number = document.getElementById("company_reg_number").value;
    var company_bank = document.getElementById("company_bank").value;
    var company_account = document.getElementById("company_account").value;
    var company_responsible = document.getElementById("company_responsible").value;
    document.getElementById("checkout-company-name").innerText = company_name;
    document.getElementById("checkout-company-country").innerText = company_country;
    document.getElementById("checkout-company-city").innerText = company_city;
    document.getElementById("checkout-company-address").innerText = company_address;
    document.getElementById("checkout-company-cui").innerText = company_cui;
    document.getElementById("checkout-company-reg-number").innerText = company_reg_number;
    document.getElementById("checkout-company-bank").innerText = company_bank;
    document.getElementById("checkout-company-account").innerText = company_account;
    document.getElementById("checkout-company-responsible").innerText = company_responsible;
    document.getElementById("checkout-company-name-edit").value = company_name;
    document.getElementById("checkout-company-country-edit").value = company_country;
    document.getElementById("checkout-company-city-edit").value = company_city;
    document.getElementById("checkout-company-address-edit").value = company_address;
    document.getElementById("checkout-company-cui-edit").value = company_cui;
    document.getElementById("checkout-company-reg-number-edit").value = company_reg_number;
    document.getElementById("checkout-company-bank-edit").value = company_bank;
    document.getElementById("checkout-company-account-edit").value = company_account;
    document.getElementById("checkout-company-responsible-edit").value = company_responsible;
    if (k == 1) {
        document.getElementById("company-purchase-no").classList.add("d-none");
        document.getElementById("company-purchase-yes").classList.remove("d-none");
        document.getElementById("company-purchase").removeAttribute("onclick");
        document.getElementById("company-purchase").setAttribute("onclick", "companyPurchase(2);");
        document.getElementById("company-purchase").classList.remove("border-2");
        document.getElementById("company-purchase").classList.add("active-tab");
        document.getElementById("company-data-block").style.display = "block";

        if (document.getElementById("billing-checked").classList.contains("d-none")) {
            document.getElementById("billing-shipping-the-same").classList.remove("border-2");
            document.getElementById("billing-shipping-the-same").classList.add("active-tab");
            document.getElementById("billing-shipping-the-same").removeAttribute("onclick");
            document.getElementById("billing-shipping-the-same").setAttribute("onclick", "show_billing_block(2)");
            document.getElementById("billing-checked").classList.remove("d-none");
            document.getElementById("billing-unchecked").classList.add("d-none");
        }

        document.getElementById("billing").classList.add("d-none");
    }
    if (k == 2) {
        document.getElementById("company-purchase-yes").classList.add("d-none");
        document.getElementById("company-purchase-no").classList.remove("d-none");
        document.getElementById("company-purchase").removeAttribute("onclick");
        document.getElementById("company-purchase").setAttribute("onclick", "companyPurchase(1);");
        document.getElementById("company-purchase").classList.remove("active-tab");
        document.getElementById("company-purchase").classList.add("border-2");
        document.getElementById("company-data-block").style.display = "none";
    }
}

function edit_company_data() {
    document.getElementById("company-data").style.display = "none";
    document.getElementById("company-data-edit").style.display = "block";
}

function setActiveBillingAddress(k) {
    var count = document.getElementById("billing_address_id").length;
    for (var i = 0; i <= count; i++) {
        if (document.getElementById("billing-address-" + i)) {
            document.getElementById("billing-address-" + i).classList.remove("selected-item");
            document.getElementById("billing-address-" + i).classList.remove("not-selected-item");
        }
    }
    document.getElementById("billing-address-" + k).classList.add("selected-item");
}

function save_company_data() {
    document.getElementById("company-data-save-success").style.display = "block";

    setTimeout(function () {
        document.getElementById("company-data-save-success").style.display = "none";
    }, 3000);

    document.getElementById("company-data-edit").style.display = "none";
    document.getElementById("company-data").style.display = "block";

    document.getElementById("checkout-company-name").innerText = document.getElementById("checkout-company-name-edit").value;
    document.getElementById("checkout-company-country").innerText = document.getElementById("checkout-company-country-edit").value;
    document.getElementById("checkout-company-city").innerText = document.getElementById("checkout-company-city-edit").value;
    document.getElementById("checkout-company-address").innerText = document.getElementById("checkout-company-address-edit").value;
    document.getElementById("checkout-company-cui").innerText = document.getElementById("checkout-company-cui-edit").value;
    document.getElementById("checkout-company-reg-number").innerText = document.getElementById("checkout-company-reg-number-edit").value;
    document.getElementById("checkout-company-bank").innerText = document.getElementById("checkout-company-bank-edit").value;
    document.getElementById("checkout-company-account").innerText = document.getElementById("checkout-company-account-edit").value;
    document.getElementById("checkout-company-responsible").innerText = document.getElementById("checkout-company-responsible-edit").value;

    var customer_id = document.getElementById("customer-id").value;
    var company_name = document.getElementById("checkout-company-name-edit").value;
    var company_country = document.getElementById("checkout-company-country-edit").value;
    var company_city = document.getElementById("checkout-company-city-edit").value;
    var company_address = document.getElementById("checkout-company-address-edit").value;
    var company_cui = document.getElementById("checkout-company-cui-edit").value;
    var company_reg_number = document.getElementById("checkout-company-reg-number-edit").value;
    var company_bank = document.getElementById("checkout-company-bank-edit").value;
    var company_account = document.getElementById("checkout-company-account-edit").value;
    var company_responsible = document.getElementById("checkout-company-responsible-edit").value;

    jQuery.ajax({
        type: "POST",
        url: "/checkoutSaveCompanyData.php",
        data: "customer_id=" + customer_id + "&company_name=" + company_name + "&company_country=" + company_country + "&company_city=" + company_city + "&company_address=" + company_address + "&company_cui=" + company_cui + "&company_reg_number=" + company_reg_number + "&company_bank=" + company_bank + "&company_account=" + company_account + "&company_responsible=" + company_responsible,
    });
}

function showNewBillingAddressBlock() {
    document.getElementById("new-billing-address").style.display = "none";
    document.getElementById("new-billing-address-block").style.display = "block";
    document.getElementById("shipping-save-in-address-book").style.display = "none";
}

function hideNewBillingAddressBlock() {
    document.getElementById("new-billing-address-block").style.display = "none";
    document.getElementById("new-billing-address").style.display = "block";
}

function saveNewBillingAddress() {
    document.getElementById("new-billing-address").style.display = "none";
    document.getElementById("new-billing-address-block").style.display = "none";

    var k = 0;
    for (var i = 0; i < 10; i++) {
        if (document.getElementById("billing-address-" + i)) {
            k++;
        }
    }
    var count = k;

    var div = document.createElement("div");
    div.id = "billing-address-" + count;
    div.className = "shipping-address-item not-selected-item";
    document.getElementById("billing-addresses").appendChild(div);
    document.getElementById("billing-address-" + count).setAttribute("onclick", "setActiveBillingAddress(" + count + ")")

    var firstname = document.getElementsByClassName("input-text")[12].value;
    var lastname = document.getElementsByClassName("input-text")[13].value;
    var address = document.getElementsByClassName("input-text")[14].value;
    document.getElementsByClassName("select")[3].setAttribute("id", "selected-country");
    var select = document.getElementById("selected-country");
    var country = select.options[select.selectedIndex].text;
    var city = document.getElementsByClassName("input-text")[17].value;
    var postcode = document.getElementsByClassName("input-text")[18].value;
    if (document.getElementsByClassName("select")[4]) {
        document.getElementsByClassName("select")[4].setAttribute("id", "selected-region");
        var select = document.getElementById("selected-region");
        var region = select.options[select.selectedIndex].text;
    }

    var region = document.getElementsByClassName("input-text")[19].value;

    document.getElementById("billing-address-" + count).innerText = firstname + ' ' + lastname + '\n' + address + ', ' + city + ', ' + region + postcode + ', ' + country;
}

var main_Content = document.querySelector('#maincontent');
if(main_Content) {
    main_Content.classList.add('container');
    main_Content.classList.remove('page-main');
}

function close_edit_company_data() {
    document.getElementById("company-data").style.display = "block";
    document.getElementById("company-data-edit").style.display = "none";
}

// for tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
})
