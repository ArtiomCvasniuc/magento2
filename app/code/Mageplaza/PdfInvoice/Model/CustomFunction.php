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

namespace Mageplaza\PdfInvoice\Model;

use DateTime;
use Exception;
use Magento\Sales\Model\Order;

/**
 * Class CustomFunction
 * @package Mageplaza\PdfInvoice\Model
 */
class CustomFunction extends Order
{
    /**
     * Format Date
     *
     * @param $date
     *
     * @return string
     * Usage: {{var pdfInvoiceCustom.formatDate($invoice.created_at)}}
     * @throws Exception
     */
    public function formatDate($date)
    {
        $dateTime = $this->timezone->formatDateTime(
            new DateTime($date),
            2,
            2,
            null,
            $this->timezone->getConfigTimezone('store', $this->getStore())
        );

        try {
            $currentDate = (new DateTime($dateTime));

            return $currentDate->format('M d, Y');
        } catch (Exception $e) {
            return $dateTime;
        }
    }
}
