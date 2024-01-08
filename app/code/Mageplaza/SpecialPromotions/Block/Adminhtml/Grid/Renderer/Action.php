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

namespace Mageplaza\SpecialPromotions\Block\Adminhtml\Grid\Renderer;

use Magento\Framework\DataObject;

/**
 * Class Action
 * @package Mageplaza\SpecialPromotions\Block\Adminhtml\Grid\Renderer
 */
class Action extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Action
{
    /**
     * @param DataObject $row
     *
     * @return string
     */
    public function render(DataObject $row)
    {
        $this->getColumn()->setActions(
            [
                [
                    'url'     => $this->getUrl('sales_rule/promo_quote/edit', ['id' => $row->getId()]),
                    'caption' => __('Edit'),
                ],
            ]
        );

        return parent::render($row);
    }
}
