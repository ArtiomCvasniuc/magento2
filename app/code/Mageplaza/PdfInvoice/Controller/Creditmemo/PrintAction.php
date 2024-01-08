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
 * @category   Mageplaza
 * @package    Mageplaza_PdfInvoice
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\PdfInvoice\Controller\Creditmemo;

use Mageplaza\PdfInvoice\Controller\AbstractPrint;
use Mageplaza\PdfInvoice\Model\Source\Type;

/**
 * Class PrintAction
 * @package Mageplaza\PdfInvoice\Controller\Creditmemo
 */
class PrintAction extends AbstractPrint
{
    /**
     * @return string
     */
    protected function getType()
    {
        return Type::CREDIT_MEMO;
    }
}
