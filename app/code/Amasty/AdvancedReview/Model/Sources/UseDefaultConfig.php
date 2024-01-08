<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Model\Sources;

use Magento\Framework\Option\ArrayInterface;

class UseDefaultConfig implements ArrayInterface
{
    const USE_DEFAULT = '';

    const NO = 1;

    const YES = 2;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::USE_DEFAULT,
                'label' => __('Use Default Config')
            ],
            [
                'value' => self::NO,
                'label' => __('No')
            ],
            [
                'value' => self::YES,
                'label' => __('Yes')
            ]
        ];

        return $options;
    }
}
