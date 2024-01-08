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

namespace Mageplaza\PdfInvoice\Block\Adminhtml;

use Magento\Backend\Block\Widget\Context;

/**
 * Class Message
 * @package Mageplaza\PdfInvoice\Block\Adminhtml
 */
class Message extends \Magento\Backend\Block\Template
{
    /**
     * Message constructor.
     *
     * @param Context $context
     * @param array $data
     */
    public function __construct(Context $context, array $data = [])
    {
        parent::__construct($context, $data);
    }

    /**
     * Get pdf invoice message
     * @return mixed
     */
    public function getPdfInvoiceMessage()
    {
        $message = $this->_backendSession->getPdfInvoiceMessage();
        if ($message) {
            $this->_backendSession->unsPdfInvoiceMessage();
        }

        return $message;
    }
}
