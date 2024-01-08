var config = {
    config: {
        mixins: {
             'Magento_Checkout/js/action/set-billing-address': {
                'Tukan_Checkout/js/action/set-billing-address-mixin': true
            },
            'Magento_Checkout/js/action/set-shipping-information': {
                'Tukan_Checkout/js/action/set-shipping-information-mixin': true
            },
            'Magento_Checkout/js/action/create-shipping-address': {
                'Tukan_Checkout/js/action/create-shipping-address-mixin': true
            },
            'Magento_Checkout/js/action/place-order': {
                'Tukan_Checkout/js/action/set-billing-address-mixin': true
            },
            'Magento_Checkout/js/action/create-billing-address': {
                'Tukan_Checkout/js/action/set-billing-address-mixin': true
            }
        }
    }
};
