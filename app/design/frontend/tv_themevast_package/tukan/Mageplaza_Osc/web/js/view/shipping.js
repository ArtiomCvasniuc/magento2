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
        'underscore',
        'Magento_Checkout/js/view/shipping',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/action/set-shipping-information',
        'Mageplaza_Osc/js/action/payment-total-information',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/action/select-billing-address',
        'Magento_Checkout/js/action/select-shipping-address',
        'Magento_Checkout/js/model/address-converter',
        'Magento_Checkout/js/model/shipping-rate-service',
        'Magento_Checkout/js/model/shipping-service',
        'Mageplaza_Osc/js/model/checkout-data-resolver',
        'Mageplaza_Osc/js/model/address/auto-complete',
        'Mageplaza_Osc/js/model/compatible/amazon-pay',
        'Magento_Customer/js/model/address-list',
        'rjsResolver',
        'mage/translate'
    ],
    function ($,
              _,
              Component,
              quote,
              customer,
              setShippingInformationAction,
              getPaymentTotalInformation,
              stepNavigator,
              additionalValidators,
              checkoutData,
              selectBillingAddress,
              selectShippingAddress,
              addressConverter,
              shippingRateService,
              shippingService,
              oscDataResolver,
              addressAutoComplete,
              amazonPay,
              addressList,
              resolver) {
        'use strict';

        oscDataResolver.resolveDefaultShippingMethod();

        /** Set shipping methods to collection */
        shippingService.setShippingRates(window.checkoutConfig.shippingMethods);

        return Component.extend({
            defaults: {
                template: 'Mageplaza_Osc/container/shipping',
                shippingMethodItemTemplate: 'Mageplaza_Osc/shipping-address/shipping-method-item'
            },
            currentMethod: null,
            isAmazonAccountLoggedIn: amazonPay.isAmazonAccountLoggedIn,
            initialize: function () {
                this._super();

                if (window.checkoutConfig.hasOwnProperty('amazonLogin')) {
                    this.isNewAddressAdded(this.isAmazonAccountLoggedIn());
                    this.isAmazonAccountLoggedIn.subscribe(function (value) {
                        this.isNewAddressAdded(value);
                    }, this);
                }

                /**
                 * Solve problem when customer has more than 1 addresses but no one is default shipping address
                 * Shipping address will not auto select the first one, so billing address throw error when trying to
                 * calculate isAddressSameAsShipping variable
                 */
                if (!quote.shippingAddress() && addressList().length >= 1) {
                    selectShippingAddress(addressList()[0]);
                }

                stepNavigator.steps.removeAll();

                //shippingRateService.estimateShippingMethod();
                additionalValidators.registerValidator(this);

                resolver(this.afterResolveDocument.bind(this));

                return this;
            },

            initObservable: function () {
                this._super();

                quote.shippingMethod.subscribe(function (oldValue) {
                    this.currentMethod = oldValue;
                }, this, 'beforeChange');

                quote.shippingMethod.subscribe(function (newValue) {
                    var isMethodChange = ($.type(this.currentMethod) !== 'object') ? true : this.currentMethod.method_code;
                    if ($.type(newValue) === 'object' && (isMethodChange !== newValue.method_code)) {
                        setShippingInformationAction();
                    } else if (shippingRateService.isAddressChange) {
                        shippingRateService.isAddressChange = false;
                        getPaymentTotalInformation();
                    }
                }, this);

                return this;
            },

            afterResolveDocument: function () {
                addressAutoComplete.register('shipping');
            },

            validate: function () {
                if (this.isAmazonAccountLoggedIn()) {
                    return true;
                }

                if (quote.isVirtual()) {
                    return true;
                }

                var shippingMethodValidationResult = true,
                    shippingAddressValidationResult = true,
                    loginFormSelector = 'form[data-role=email-with-possible-login]',
                    emailValidationResult = customer.isLoggedIn();

                if (!quote.shippingMethod()) {
                    this.errorValidationMessage($.mage.__('Please specify a shipping method.'));

                    shippingMethodValidationResult = false;
                }

                if (!customer.isLoggedIn()) {
                    $(loginFormSelector).validation();
                    emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
                }

                if (this.isFormInline) {
                    this.source.set('params.invalid', false);
                    this.source.trigger('shippingAddress.data.validate');

                    if (this.source.get('shippingAddress.custom_attributes')) {
                        this.source.trigger('shippingAddress.custom_attributes.data.validate');
                    }

                    if (this.source.get('params.invalid')) {
                        shippingAddressValidationResult = false;
                    }

                    this.saveShippingAddress();
                }

                return shippingMethodValidationResult && shippingAddressValidationResult && emailValidationResult;
            },
            saveShippingAddress: function () {
                var shippingAddress = quote.shippingAddress(),
                    addressData = addressConverter.formAddressDataToQuoteAddress(
                        this.source.get('shippingAddress')
                    );

                //Copy form data to quote shipping address object
                for (var field in addressData) {
                    if (addressData.hasOwnProperty(field) &&
                        shippingAddress.hasOwnProperty(field) &&
                        typeof addressData[field] != 'function' &&
                        _.isEqual(shippingAddress[field], addressData[field])
                    ) {
                        shippingAddress[field] = addressData[field];
                    } else if (typeof addressData[field] != 'function' && !_.isEqual(shippingAddress[field], addressData[field])) {
                        shippingAddress = addressData;
                        break;
                    }
                }

                if (customer.isLoggedIn()) {
                    shippingAddress.save_in_address_book = 1;
                }
                selectShippingAddress(shippingAddress);
            },

            saveNewAddress: function () {
                this.source.set('params.invalid', false);
                if (this.source.get('shippingAddress.custom_attributes')) {
                    this.source.trigger('shippingAddress.custom_attributes.data.validate');
                }

                if (!this.source.get('params.invalid')) {
                    this._super();
                }

                if (!this.source.get('params.invalid')) {
                    shippingRateService.isAddressChange = true;
                    shippingRateService.estimateShippingMethod();
                }
            },

            getAddressTemplate: function () {
                return 'Mageplaza_Osc/container/address/shipping-address';
            }
        });
    }
);

var interval = setTimeout(myFunction, 4000);
function myFunction() {
    if(document.getElementById("co-shipping-method-form")) {
        var customer_id = document.getElementById("customer-id").value;
        var seller_ids = document.getElementById("seller-ids").value;
        if(document.getElementsByClassName("shipping-address-item selected-item")[0]) {
            var selected_address = document.getElementsByClassName("shipping-address-item selected-item")[0].innerText;
        }
        else {
            if(document.getElementsByClassName("select")[0]) {
                document.getElementsByClassName("select")[0].setAttribute("id", "selected-address");
                document.getElementById("selected-address").setAttribute("onchange", "get_selected_address_not_auth();");
                var select = document.getElementById("selected-address");
                var selected_address = select[select.selectedIndex].text;
            }
        }
        selected_address = replaceDiacritics(selected_address);
        jQuery("#checkout-step-shipping_method").load("/checkoutShippingMethods.php", {address:selected_address, seller_ids:seller_ids, customer_id:customer_id});
    }
}

function get_selected_address_not_auth() {
    if(document.getElementById("checkout-step-shipping_method")) {
        var customer_id = document.getElementById("customer-id").value;
        var seller_ids = document.getElementById("seller-ids").value;
        if(document.getElementsByClassName("select")[0]) {
            document.getElementsByClassName("select")[0].setAttribute("id", "selected-address");
            var select = document.getElementById("selected-address");
            var selected_address = select[select.selectedIndex].text;
        }
        selected_address = replaceDiacritics(selected_address);
        jQuery("#checkout-step-shipping_method").load("/checkoutShippingMethods.php", {address:selected_address, seller_ids:seller_ids, customer_id:customer_id});
    }
    if(document.getElementById("checkout-step-shipping_method").innerText != '') {
        clearInterval(interval);
    }
}

function replaceDiacritics(s) {
    var s;

    var diacritics =[
        /[\300-\306]/g, /[\340-\346]/g,  // A, a
        /[\310-\313]/g, /[\350-\353]/g,  // E, e
        /[\314-\317]/g, /[\354-\357]/g,  // I, i
        /[\322-\330]/g, /[\362-\370]/g,  // O, o
        /[\331-\334]/g, /[\371-\374]/g,  // U, u
        /[\321]/g, /[\361]/g, // N, n
        /[\307]/g, /[\347]/g, // C, c
    ];

    var chars = ['A','a','E','e','I','i','O','o','U','u','N','n','C','c'];

    for (var i = 0; i < diacritics.length; i++) {
        s = s.replace(diacritics[i],chars[i]);
    }

    return s;
}

var interval = setTimeout(checkField, 4000);
function checkField() {
    if(document.getElementsByClassName("label")[13].innerText == 'Custom Field 1') {
        document.getElementsByClassName("label")[13].innerText = "Email";
    }
    if(document.getElementsByClassName("field col-mp mp-6 mp-clear not-required")[7]) {
        document.getElementsByClassName("field col-mp mp-6 mp-clear not-required")[7].style.display = "none";
    }
}

function showNewShippingAddressBlock() {
    if(document.getElementsByClassName("field col-mp mp-6 mp-clear not-required")[3]) {
        document.getElementsByClassName("field col-mp mp-6 mp-clear not-required")[3].remove();
        document.getElementById("shipping-save-in-address-book").remove();
    }
    document.getElementById("new-shipping-address-btn").style.display = "none";
    document.getElementById("opc-new-shipping-address").style.display = "block";
    document.getElementsByClassName("shipping-address-items")[0].setAttribute("id", "shipping-addresses-block");
}

function hideNewShippingAddressBlock() {
    document.getElementById("opc-new-shipping-address").style.display = "none";
    document.getElementById("new-shipping-address-btn").style.display = "block";
}

function saveNewShippingAddress() {
    var customer_id = document.getElementById("customer-id").value;

    var div = document.createElement("div");
    div.className = "shipping-address-item not-selected-item";
    div.id = "created-shipping-address";
    document.getElementById("shipping-addresses-block").appendChild(div);
    document.getElementById("created-shipping-address").setAttribute("data-bind", "css: isSelected() ? 'selected-item' : 'not-selected-item', click: selectAddress");

    var firstname = document.getElementsByClassName("input-text")[2].value;
    var lastname = document.getElementsByClassName("input-text")[3].value;
    var address = document.getElementsByClassName("input-text")[4].value;
    
    document.getElementsByClassName("select")[0].setAttribute("id", "selected-country");
    var select = document.getElementById("selected-country");
    var country = select.options[select.selectedIndex].value;
    var city = document.getElementsByClassName("input-text")[7].value;
    var postcode = document.getElementsByClassName("input-text")[8].value;
    var region = document.getElementsByClassName("input-text")[9].value;
    var phone = document.getElementsByClassName("input-text")[10].value;

    document.getElementById("created-shipping-address").innerText = firstname + ' ' + lastname + '\n' + address + '\n' + city + ', ' + region + ' ' + postcode + '\n' + country + '\n' + phone;

    jQuery.ajax({
        type:"POST",
        url: "/checkoutSaveCompanyData.php",
        data: "customer="+customer_id+"&firstname="+firstname+"&lastname="+lastname+"&address="+address+"&country="+country+"&city="+city+"&postcode="+postcode+"&region="+region+"&phone="+phone,
        success: '',
    });    

    document.getElementById("opc-new-shipping-address").style.display = "none";
}