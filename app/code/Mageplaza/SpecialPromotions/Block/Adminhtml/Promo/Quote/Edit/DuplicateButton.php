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

namespace Mageplaza\SpecialPromotions\Block\Adminhtml\Promo\Quote\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit\GenericButton;

/**
 * Class DuplicateButton
 * @package Mageplaza\SpecialPromotions\Block\Adminhtml\Promo\Quote\Edit
 */
class DuplicateButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        $ruleId = $this->getRuleId();
        if ($ruleId && $this->canRender('duplicate')) {
            $data = [
                'label'      => __('Duplicate'),
                'class'      => 'duplicate',
                'on_click'   => 'location.href = \'' . $this->urlBuilder->getUrl(
                    '*/*/duplicate',
                    ['id' => $ruleId]
                ) . '\'',
                'sort_order' => 50,
            ];
        }

        return $data;
    }
}
