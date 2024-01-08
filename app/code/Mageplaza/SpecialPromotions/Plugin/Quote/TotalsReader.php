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
 * @package     Mageplaza_SpecialPromotions
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\SpecialPromotions\Plugin\Quote;

use Magento\Quote\Model\Quote;

/**
 * Class TotalsReader
 * @package Mageplaza\SpecialPromotions\Plugin\Quote
 */
class TotalsReader
{
    const DISCOUNT_DETAILS    = 'discount_details';
    const COLLECTOR_TYPE_CODE = 'discount';

    /**
     * @param Quote\TotalsReader $subject
     * @param callable $proceed
     * @param Quote $quote
     * @param array $total
     *
     * @return mixed
     */
    public function aroundFetch(
        \Magento\Quote\Model\Quote\TotalsReader $subject,
        callable $proceed,
        Quote $quote,
        array $total
    ) {
        $result = $proceed($quote, $total);
        if (empty($result)
            || !array_key_exists(self::COLLECTOR_TYPE_CODE, $result)
            || !array_key_exists(self::DISCOUNT_DETAILS, $total)
        ) {
            return $result;
        }

        $result[self::COLLECTOR_TYPE_CODE]->setFullInfo($total[self::DISCOUNT_DETAILS]);

        return $result;
    }
}
