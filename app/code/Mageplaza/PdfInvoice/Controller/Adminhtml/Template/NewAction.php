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
 * @package     Mageplaza_PdfInvoice
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\PdfInvoice\Controller\Adminhtml\Template;

use Mageplaza\PdfInvoice\Controller\Adminhtml\Template;

/**
 * Class NewAction
 * @package Mageplaza\PdfInvoice\Controller\Adminhtml\Template
 */
class NewAction extends Template
{
    /**
     * Forward to edit form
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
