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

namespace Mageplaza\SpecialPromotions\Plugin;

use Mageplaza\SpecialPromotions\Helper\Data;
use Mageplaza\SpecialPromotions\Model\Rule\Action\Combine;
use Mageplaza\SpecialPromotions\Model\Rule\Action\CombineFactory;

/**
 * Class Rule
 * @package Mageplaza\SpecialPromotions\Plugin
 */
class Rule
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var CombineFactory
     */
    protected $actionCombineFactory;

    /**
     * Rule constructor.
     *
     * @param Data $helper
     * @param CombineFactory $actionCombineFactory
     */
    public function __construct(
        Data $helper,
        CombineFactory $actionCombineFactory
    ) {
        $this->helper = $helper;
        $this->actionCombineFactory = $actionCombineFactory;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule $subject
     * @param \Magento\SalesRule\Model\Rule\Condition\Product\Combine | Combine $action
     *
     * @return mixed
     */
    public function afterGetActionsInstance(\Magento\SalesRule\Model\Rule $subject, $action)
    {
        if ($this->helper->isEnabled()) {
            return $this->actionCombineFactory->create();
        }

        return $action;
    }
}
