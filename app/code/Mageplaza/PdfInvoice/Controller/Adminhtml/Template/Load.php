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

namespace Mageplaza\PdfInvoice\Controller\Adminhtml\Template;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Filesystem;
use Mageplaza\PdfInvoice\Helper\Data;
use Mageplaza\PdfInvoice\Helper\PrintProcess as HelperData;
use Mageplaza\PdfInvoice\Model\Source\Type;

/**
 * Class Load
 * @package Mageplaza\PdfInvoice\Controller\Adminhtml\Template
 */
class Load extends Action
{
    /**
     * @type JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Load constructor.
     *
     * @param Context $context
     * @param Filesystem $filesystem
     * @param JsonFactory $resultJsonFactory
     * @param HelperData $helperData
     */
    public function __construct(
        Context $context,
        Filesystem $filesystem,
        JsonFactory $resultJsonFactory,
        HelperData $helperData
    ) {
        $this->filesystem = $filesystem;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helperData = $helperData;

        parent::__construct($context);
    }

    /**
     * @return $this|ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $templateHtml = $templateCss = '';
        $templateType = $this->getRequest()->getParam('templateType', Type::INVOICE);
        $templateId = $this->getRequest()->getParam('templateId', 'template1');

        try {
            $templateHtml = $this->helperData->getDefaultTemplateHtml($templateType, $templateId);
            $templateCss = $this->helperData->getDefaultTemplateCss($templateType, $templateId);
            $status = true;
            $message = __('Load message success!');
        } catch (Exception $e) {
            $status = false;
            $message = __("Cannot load template.");
        }

        /** @var Json $result */
        $result = $this->resultJsonFactory->create();

        return $result->setData(['status' => $status, 'message' => $message, 'templateHtml' => $templateHtml, 'templateCss' => $templateCss]);
    }
}
