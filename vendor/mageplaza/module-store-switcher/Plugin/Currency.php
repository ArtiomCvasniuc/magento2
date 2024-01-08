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

namespace Mageplaza\StoreSwitcher\Plugin;

use Magento\Store\Model\Store;
use Mageplaza\StoreSwitcher\Helper\Data as HelperData;

/**
 * Class Currency
 * @package Mageplaza\StoreSwitcher\Plugin
 */
class Currency
{
    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * Currency constructor.
     *
     * @param HelperData $helperData
     */
    public function __construct(HelperData $helperData)
    {
        $this->_helperData = $helperData;
    }

    /**
     * @param Store $store
     * @param $result
     *
     * @return mixed
     * @SuppressWarnings(Unused)
     */
    public function afterGetCurrentCurrencyCode(Store $store, $result)
    {
        if ($this->_helperData->isEnabled()) {
            $rule = $this->_helperData->getMatchingRule();

            if ($rule && $rule->getCurrency()) {
                return $rule->getCurrency();
            }
        }

        return $result;
    }
}
