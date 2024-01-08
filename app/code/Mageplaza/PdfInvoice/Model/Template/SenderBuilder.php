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

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Sales\Model\Order\Email\Container\IdentityInterface;
use Magento\Sales\Model\Order\Email\Container\Template;
use Mageplaza\PdfInvoice\Helper\Data;
use Mageplaza\PdfInvoice\Helper\PrintProcess;
use Mageplaza\PdfInvoice\Model\Source\Type;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\Mail\Message;
use Zend\Mime\Part;
use Zend_Mime;

/**
 * Class SenderBuilder
 * @package Mageplaza\PdfInvoice\Model\Template
 */
class SenderBuilder extends \Magento\Sales\Model\Order\Email\SenderBuilder
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var PrintProcess
     */
    protected $printHelper;

    /**
     * SenderBuilder constructor.
     * @param Template $templateContainer
     * @param IdentityInterface $identityContainer
     * @param TransportBuilder $transportBuilder
     * @param Data $helper
     * @param PrintProcess $printHelper
     */
    public function __construct(
        Template $templateContainer,
        IdentityInterface $identityContainer,
        TransportBuilder $transportBuilder,
        Data $helper,
        PrintProcess $printHelper
    ) {
        $this->helper = $helper;
        $this->printHelper = $printHelper;

        parent::__construct($templateContainer, $identityContainer, $transportBuilder);
    }

    /**
     * @inheritdoc
     */
    public function send()
    {
        $attachPdf = $this->attachPDF();
        if ($attachPdf && $this->helper->versionCompare('2.2.8')) {
            # attach pdf, overide send function
            $this->configureEmailTemplate();
            $this->transportBuilder->addTo(
                $this->identityContainer->getCustomerEmail(),
                $this->identityContainer->getCustomerName()
            );
            $copyTo = $this->identityContainer->getEmailCopyTo();
            if (!empty($copyTo) && $this->identityContainer->getCopyMethod() === 'bcc') {
                foreach ($copyTo as $email) {
                    $this->transportBuilder->addBcc($email);
                }
            }
            #transport email
            $this->attachEmail($attachPdf);
        } else {
            parent::send();
        }
    }

    /**
     * @inheritdoc
     */
    public function sendCopyTo()
    {
        $attachPdf = $this->attachPDF();
        if ($attachPdf && $this->helper->versionCompare('2.2.8')) {
            $copyTo = $this->identityContainer->getEmailCopyTo();
            if (!empty($copyTo) && $this->identityContainer->getCopyMethod() === 'copy') {
                foreach ($copyTo as $email) {
                    $this->configureEmailTemplate();
                    $this->transportBuilder->addTo($email);
                    $this->attachEmail($attachPdf);
                }
            }
        } else {
            parent::sendCopyTo();
        }
    }

    /**
     * Attach pdf
     *
     * @return bool
     */
    public function attachPDF()
    {
        $templateVars = $this->templateContainer->getTemplateVars();
        $storeId = $templateVars['store']->getId();
        if ($this->helper->isEnabled($storeId)) {
            try {
                if (isset($templateVars['invoice'])) {
                    $invoice = $templateVars['invoice'];
                    $content = $this->printHelper->processPDFTemplate(Type::INVOICE, $templateVars, $storeId);
                    $fileName = 'Invoice' . $invoice->getIncrementId();
                } elseif (isset($templateVars['shipment'])) {
                    $shipment = $templateVars['shipment'];
                    $content = $this->printHelper->processPDFTemplate(Type::SHIPMENT, $templateVars, $storeId);
                    $fileName = 'Shipment' . $shipment->getIncrementId();
                } elseif (isset($templateVars['creditmemo'])) {
                    $creditmemo = $templateVars['creditmemo'];
                    $content = $this->printHelper->processPDFTemplate(Type::CREDIT_MEMO, $templateVars, $storeId);
                    $fileName = 'Creditmemo' . $creditmemo->getIncrementId();
                } else {
                    $order = $templateVars['order'];
                    $fileName = 'Order' . $order->getIncrementId();
                    $content = $this->printHelper->processPDFTemplate(Type::ORDER, $templateVars, $storeId);
                }
                if ($content) {
                    $attachment = $this->transportBuilder->addAttachment(
                        $content,
                        'application/pdf',
                        Zend_Mime::DISPOSITION_ATTACHMENT,
                        Zend_Mime::ENCODING_BASE64,
                        $fileName . '.pdf'
                    );

                    if ($this->helper->versionCompare('2.2.8')) {
                        return $attachment;
                    }
                }
            } catch (Exception $e) {
                $writer = new Stream(BP . '/var/log/pdfinvoice.log');
                $logger = new Logger();
                $logger->addWriter($writer);
                $logger->info($e->getMessage());
            }
        }

        return false;
    }

    /**
     * @param $attachPdf
     * @throws MailException
     * @throws  LocalizedException
     */
    public function attachEmail($attachPdf)
    {
        $transport = $this->transportBuilder->getTransport();
        $html = $transport->getMessage();
        $message = Message::fromString($html->getRawMessage());
        $bodyMessage = new Part($message->getBody());
        $bodyMessage->type = 'text/html';
        $bodyMessage->charset = 'utf-8';
        $bodyPart = new \Zend\Mime\Message();
        $bodyPart->setParts([$bodyMessage, $attachPdf]);
        #transport email with body part
        $transport->getMessage()->setBody($bodyPart);
        $transport->sendMessage();
    }
}
