<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_PdfInvoice
 * @copyright   Copyright (c) 2017-2018 Mageplaza (https://www.mageplaza.com/)
 * @license     http://mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\PdfInvoice\Model\Template;

use Magento\Framework\App\ObjectManager;
use Mageplaza\PdfInvoice\Helper\Data;
use Zend\Mime\Part;
use Zend_Mime;

/**
 * Class TransportBuilder
 * @package Mageplaza\PdfInvoice\Model\Template
 */
class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    /**
     * @param $content
     * @param string $mimeType
     * @param string $disposition
     * @param string $encoding
     * @param string $filename
     * @return $this
     */
    public function addAttachment(
        $content,
        $mimeType = Zend_Mime::TYPE_OCTETSTREAM,
        $disposition = Zend_Mime::DISPOSITION_ATTACHMENT,
        $encoding = Zend_Mime::ENCODING_BASE64,
        $filename = 'pdf_invoice.pdf'
    ) {
        $objectManager = ObjectManager::getInstance();
        $configHelper = $objectManager->get(Data::class);
        if ($configHelper->versionCompare("2.2.8")) {
            $attachment = new Part($content);
            $attachment->type = $mimeType;
            $attachment->disposition = $disposition;
            $attachment->encoding = $encoding;
            $attachment->filename = $filename;

            return $attachment;
        } else {
            $this->message->createAttachment(
                $content,
                $mimeType,
                $disposition,
                $encoding,
                $filename
            );

            return $this;
        }
    }
}
