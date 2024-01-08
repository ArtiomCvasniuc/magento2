<?php

namespace Tukan\Checkout\Plugin\Block\Account\Dashboard;


class Address extends \Magento\Customer\Block\Account\Dashboard\Address {
    public function getPrimaryBillingAddress() {
        return $this->currentCustomerAddress->getDefaultBillingAddress();
    }

    public function getAddressPostUrl($addressId)
    {
        return $this->_urlBuilder->getUrl(
            'customer/address/formPost',
            ['_secure' => true, 'id' => $addressId]
        );
    }
}
