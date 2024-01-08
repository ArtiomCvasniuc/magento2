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

namespace Mageplaza\SpecialPromotions\Plugin\Rule\Metadata;

use Mageplaza\SpecialPromotions\Model\Config\Source\RuleType;

/**
 * Class ValueProvider
 * @package Mageplaza\SpecialPromotions\Plugin\Rule\Condition
 */
class ValueProvider
{
    /**
     * @var RuleType
     */
    protected $ruleType;

    /**
     * ValueProvider constructor.
     *
     * @param RuleType $ruleType
     */
    public function __construct(RuleType $ruleType)
    {
        $this->ruleType = $ruleType;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule\Metadata\ValueProvider $subject
     * @param $meta
     *
     * @return mixed
     */
    public function afterGetMetadataValues(\Magento\SalesRule\Model\Rule\Metadata\ValueProvider $subject, $meta)
    {
        $meta['actions']['children']['simple_action']['arguments']['data']['config']['options'] = $this->ruleType->toOptionArray();

        return $meta;
    }
}
