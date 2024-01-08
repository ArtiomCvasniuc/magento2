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

namespace Mageplaza\SpecialPromotions\Plugin\Rule\Condition;

use Mageplaza\SpecialPromotions\Model\Rule\Condition\Order\Subselect;

/**
 * Class Combine
 * @package Mageplaza\SpecialPromotions\Plugin\Rule\Condition
 */
class Combine
{
    /**
     * @param \Magento\SalesRule\Model\Rule\Condition\Combine $subject
     * @param array $conditions
     *
     * @return array
     */
    public function afterGetNewChildSelectOptions(\Magento\SalesRule\Model\Rule\Condition\Combine $subject, $conditions)
    {
        $newConditions = [];
        foreach ($conditions as $condition) {
            $newConditions[] = $condition;
            if ($condition['value'] === \Magento\SalesRule\Model\Rule\Condition\Product\Subselect::class) {
                $newConditions[] = [
                    'value' => Subselect::class,
                    'label' => __('Orders subselection')
                ];
            }
        }

        return $newConditions;
    }
}
