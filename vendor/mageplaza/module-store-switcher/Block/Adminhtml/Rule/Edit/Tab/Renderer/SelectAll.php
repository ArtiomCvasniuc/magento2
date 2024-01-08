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

namespace Mageplaza\StoreSwitcher\Block\Adminhtml\Rule\Edit\Tab\Renderer;

use Magento\Backend\Block\Template;

/**
 * Class SelectAll
 * @package Mageplaza\StoreSwitcher\Block\Adminhtml\Rule\Edit\Tab\Renderer
 */
class SelectAll extends Template
{
    /**
     * Path to template file.
     *
     * @var string
     */
    protected $_template = 'Mageplaza_StoreSwitcher::rule/selectall.phtml';
}
