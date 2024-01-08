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

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\PdfInvoice\Helper\Data;
use Mageplaza\PdfInvoice\Helper\PrintProcess as HelperData;
use Mageplaza\PdfInvoice\Model\Template\Processor;

/**
 * Class Index
 * @package Mageplaza\PdfInvoice\Controller\Adminhtml\Template
 */
class Preview extends Action
{
    /** @type PageFactory */
    protected $resultPageFactory;

    /**
     * @var Processor
     */
    protected $templateProcessor;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Preview constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Processor $templateProcessor
     * @param HelperData $helperData
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Processor $templateProcessor,
        HelperData $helperData
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->templateProcessor = $templateProcessor;
        $this->helperData = $helperData;

        parent::__construct($context);
    }

    /**
     * @return Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Preview'));

        try {
            $templateType = $this->getRequest()->getParam('templateType', 'invoice');
            $templateHtml = $this->getRequest()->getParam('templateHtml');

            if (empty(trim($templateHtml))) {
                $this->_getSession()->setPdfInvoiceMessage(['type' => 'warning', 'message' => __('Please insert content to preview!')]);

                return $resultPage;
            }
            $templateHtml = $templateHtml . '<style>' . $this->getRequest()->getParam('templateCss', '') . '</style>';
            $data = $this->helperData->getDataProcess($templateType);
            $store = $data['store'];
            $processor = $this->templateProcessor->setVariable($data);

            $processor->setTemplateHtml($templateHtml);
            $contentPreview = $processor->processTemplate();
            $this->helperData->exportToPDF('demo.pdf', $contentPreview, $store->getId(), 'I');
        } catch (Exception $e) {
            $this->_getSession()->setPdfInvoiceMessage(['type' => 'error', 'message' => __('Can\'t preview. Please check HTML and Css in template.')]);
        }

        return $resultPage;
    }
}
