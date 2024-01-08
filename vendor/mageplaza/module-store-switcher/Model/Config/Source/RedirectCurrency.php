<?php
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
 * @package     Mageplaza_StoreSwitcher
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\StoreSwitcher\Model\Config\Source;

use Magento\Directory\Model\Currency;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class RedirectCurrency
 * @package Mageplaza\StoreSwitcher\Model\Config\Source
 */
class RedirectCurrency implements ArrayInterface
{
    /**
     * @var Currency
     */
    protected $_currencyModel;

    /**
     * RedirectCurrency constructor.
     *
     * @param Currency $currencyModel
     */
    public function __construct(Currency $currencyModel)
    {
        $this->_currencyModel = $currencyModel;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $currencies = $this->_currencyModel->getConfigAllowCurrencies();
        $options    = [];

        $options[] = [
            'label' => '--',
            'value' => ''
        ];
        foreach ($currencies as $currency) {
            $options[] = ['label' => $currency, 'value' => $currency];
        }

        return $options;
    }
}
