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

namespace Mageplaza\PdfInvoice\Block\Creditmemo;

use Mageplaza\PdfInvoice\Block\PdfItems;
use Mageplaza\PdfInvoice\Model\Source\Type;

/**
 * Class Items
 * @package Mageplaza\PdfInvoice\Block\Creditmemo
 */
class Items extends PdfItems
{
    /**
     * Get invoice type
     * @return string
     */
    public function getCreditmemoType()
    {
        return Type::CREDIT_MEMO;
    }
}
