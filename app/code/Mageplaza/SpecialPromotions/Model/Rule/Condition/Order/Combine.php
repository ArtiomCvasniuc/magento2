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

namespace Mageplaza\SpecialPromotions\Model\Rule\Condition\Order;

use Magento\Rule\Model\Condition\Context;
use Mageplaza\SpecialPromotions\Model\Rule\Condition\Order;

/**
 * Combine conditions for order.
 *
 * @package Mageplaza\SpecialPromotions\Model\Rule\Condition\Order
 */
class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * Combine constructor.
     *
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->setType(__CLASS__);
    }

    /**
     * Get new child select options
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'value' => __CLASS__,
                    'label' => __('Conditions Combination'),
                ],
                [
                    'value' => Order::class . '|status',
                    'label' => __('Order Status')
                ],
                [
                    'value' => Order::class . '|created_at',
                    'label' => __('Order Created Date')
                ],
                [
                    'value' => Order::class . '|period',
                    'label' => __('Order Created Within (days)')
                ]
            ]
        );

        return $conditions;
    }
}
