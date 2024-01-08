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
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\PdfInvoice\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Mageplaza\PdfInvoice\Helper\Data;

/**
 * Class Order
 * @package Mageplaza\PdfInvoice\Model\Source
 */
class Order implements OptionSourceInterface
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Order constructor.
     *
     * @param Data $helperData
     */
    public function __construct(Data $helperData)
    {
        $this->helperData = $helperData;
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function toOptionArray()
    {
        return $this->helperData->getTemplates(Type::ORDER);
    }
}
