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

namespace Mageplaza\PdfInvoice\Helper;

use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\State;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\ObjectManagerInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Shipment;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData;
use Mageplaza\PdfInvoice\Helper\Data as HelperData;
use Mageplaza\PdfInvoice\Model\CustomFunction;
use Mageplaza\PdfInvoice\Model\Source\Type;
use Mageplaza\PdfInvoice\Model\Template\Processor;
use Mageplaza\PdfInvoice\Model\TemplateFactory;
//use Mageplaza\PdfInvoice\Helper\ConfigVariables;
//use Mpdf\Config\ConfigVariables;
//use Mpdf\Config\FontVariables;
//use Mpdf\Mpdf;
use Mpdf\MpdfException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

/**
 * Class PrintProcess
 * @package Mageplaza\PdfInvoice\Helper
 */
class PrintProcess extends AbstractData
{
    const MAGEPLAZA_DIR = 'var/mageplaza';

    /**
     * Module registry
     *
     * @var ComponentRegistrarInterface
     */
    private $componentRegistrar;

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @var string
     */
    protected $templateStyles = '';

    /**
     * @var TemplateFactory
     */
    protected $templateFactory;

    /**
     * @var $templateVars
     */
    protected $templateVars;

    /**
     * @var Processor
     */
    protected $templateProcessor;

    /**
     * @var string
     */
    protected $mode;

    /**
     * @var string
     */
    protected $fileName = 'PdfInvoice';

    /**
     * @var State
     */
    protected $state;

    /**
     * @var CustomFunction
     */
    protected $customFunction;

    /**
     * @var PaymentHelper
     */
    protected $paymentHelper;

    /**
     * @var Renderer
     */
    protected $addressRenderer;

    /**
     * @var Creditmemo
     */
    protected $creditmemo;

    /**
     * @var CreditmemoRepositoryInterface
     */
    protected $creditmemoRepository;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var Invoice
     */
    protected $invoice;

    /**
     * @var InvoiceRepositoryInterface
     */
    protected $invoiceRepository;

    /**
     * @var Shipment
     */
    protected $shipment;

    /**
     * @var ShipmentRepositoryInterface
     */
    protected $shipmentRepository;

    /**
     * @var string
     */
    protected $comment;

    /**
     * PrintProcess constructor.
     *
     * @param Context $context
     * @param Order $order
     * @param State $state
     * @param Invoice $invoice
     * @param Shipment $shipment
     * @param Data $helperData
     * @param Filesystem $fileSystem
     * @param Creditmemo $creditmemo
     * @param Renderer $addressRenderer
     * @param PaymentHelper $paymentHelper
     * @param Processor $templateProcessor
     * @param DirectoryList $directoryList
     * @param CustomFunction $customFunction
     * @param TemplateFactory $templateFactory
     * @param StoreManagerInterface $storeManager
     * @param ObjectManagerInterface $objectManager
     * @param OrderRepositoryInterface $orderRepository
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param ComponentRegistrarInterface $componentRegistrar
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param CreditmemoRepositoryInterface $creditmemoRepository
     */
    public function __construct(
        Context $context,
        Order $order,
        State $state,
        Invoice $invoice,
        Shipment $shipment,
        HelperData $helperData,
        Filesystem $fileSystem,
        Creditmemo $creditmemo,
        Renderer $addressRenderer,
        PaymentHelper $paymentHelper,
        Processor $templateProcessor,
        DirectoryList $directoryList,
        CustomFunction $customFunction,
        TemplateFactory $templateFactory,
        StoreManagerInterface $storeManager,
        ObjectManagerInterface $objectManager,
        OrderRepositoryInterface $orderRepository,
        InvoiceRepositoryInterface $invoiceRepository,
        ComponentRegistrarInterface $componentRegistrar,
        ShipmentRepositoryInterface $shipmentRepository,
        CreditmemoRepositoryInterface $creditmemoRepository
    ) {
        $this->order = $order;
        $this->state = $state;
        $this->invoice = $invoice;
        $this->shipment = $shipment;
        $this->creditmemo = $creditmemo;
        $this->helperData = $helperData;
        $this->fileSystem = $fileSystem;
        $this->paymentHelper = $paymentHelper;
        $this->directoryList = $directoryList;
        $this->customFunction = $customFunction;
        $this->addressRenderer = $addressRenderer;
        $this->templateFactory = $templateFactory;
        $this->orderRepository = $orderRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->templateProcessor = $templateProcessor;
        $this->componentRegistrar = $componentRegistrar;
        $this->shipmentRepository = $shipmentRepository;
        $this->creditmemoRepository = $creditmemoRepository;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * Check default template
     *
     * @param $templateId
     *
     * @return bool
     */
    public function checkDefaultTemplate($templateId)
    {
        return in_array($templateId, array_keys($this->helperData->getTemplatesConfig()));
    }

    /**
     * @param $templateType
     * @param $templateId
     *
     * @return string
     * @throws FileSystemException
     */
    public function getDefaultTemplateHtml($templateType, $templateId)
    {
        return $this->readFile($this->getTemplatePath($templateType, $templateId));
    }

    /**
     * @param $templateType
     * @param $templateId
     *
     * @return string
     * @throws FileSystemException
     */
    public function getDefaultTemplateCss($templateType, $templateId)
    {
        return $this->readFile($this->getTemplatePath($templateType, $templateId, '.css'));
    }

    /**
     * Get default template path
     *
     * @param $templateType
     * @param $templateId
     * @param string $type
     *
     * @return string
     */
    public function getTemplatePath($templateType, $templateId, $type = '.html')
    {
        return $this->getBaseTemplatePath() . $templateType . "/" . $templateId . $type;
    }

    /**
     * @param $relativePath
     *
     * @return string
     * @throws FileSystemException
     */
    public function readFile($relativePath)
    {
        $rootDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::ROOT);

        return $rootDirectory->readFile($relativePath);
    }

    /**
     * @param $templateId
     * @param $type
     *
     * @return string
     * @throws FileSystemException
     */
    public function getTemplateHtml($templateId, $type)
    {
        if ($this->checkDefaultTemplate($templateId)) {
            $this->templateStyles = $this->getDefaultTemplateCss($type, $templateId, '.css');

            return $this->getDefaultTemplateHtml($type, $templateId);
        } else {
            $templateModel = $this->templateFactory->create();
            $template = $templateModel->load($templateId);
            if ($template->getId()) {
                $this->templateStyles = $template->getTemplateStyles();

                return $template->getTemplateHtml();
            }
        }
    }

    /**
     * Process pdf template for each type
     *
     * @param $type
     * @param $templateVars
     * @param $storeId
     *
     * @return string
     * @throws FileSystemException
     * @throws MpdfException
     */
    public function processPDFTemplate($type, $templateVars, $storeId)
    {
        if ($this->helperData->isEnableAttachment($type, $storeId)) {
            $templateId = $this->helperData->getPdfTemplate($type, $storeId);
            $templateHtml = $this->getTemplateHtml($templateId, $type);
            $templateVars[$type . 'Note'] = $this->helperData->getPdfNote($type, $storeId);

            return $this->getPDFContent($templateHtml, $templateVars, 'S', $storeId);
        }

        return '';
    }

    /**
     * Set template vars
     *
     * @param $data
     *
     * @return mixed
     */
    public function setTemplateVars($data)
    {
        return $this->templateVars = $data;
    }

    /**
     * Get store id
     * @return mixed
     */
    public function getStoreId()
    {
        $store = $this->templateVars['store'];

        return $store->getId();
    }

    /**
     * Get PDF Content
     *
     * @param $html
     * @param $templateVars
     * @param string $dest
     * @param null $storeId
     *
     * @return string
     * @throws MpdfException
     */
    public function getPDFContent($html, $templateVars, $dest = 'S', $storeId = null)
    {
        $processor = $this->templateProcessor->setVariable(
            $this->addCustomTemplateVars($templateVars, $storeId)
        );
        $processor->setTemplateHtml($html . '<style>' . $this->templateStyles . '</style>');
        $processor->setStore($storeId);

        $html = $processor->processTemplate();
        if ($this->getMode() == 'prints') {
            return $html;
        }

        return $this->exportToPDF($this->fileName . '.pdf', $html, $storeId, $dest);
    }

    /**
     * Set mode print
     *
     * @param $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * Get mode print
     * @return mixed
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Print all pdf of  all store view
     *
     * @param $type
     * @param $ids
     * @param null $storeId
     *
     * @throws Exception
     * @throws LocalizedException
     * @throws MpdfException
     */
    public function printAllPdf($type, $ids, $storeId = null)
    {
        if (is_array($ids)) {
            if (count($ids) == 1) {
                /** Get the first key and item of $ids*/
                $item = reset($ids);
                $sid = key($ids);

                /** When all of selected items are belong to only one store, export the pdf file*/
                $this->getMpdfContent($sid, $item, $type)->Output($type . "s.pdf", 'D');
            } else {
                /** Create directory var/mageplaza/pdfinvoice and var/mageplaza/tmp if not exists*/
                if (!file_exists(self::MAGEPLAZA_DIR)) {
                    mkdir(self::MAGEPLAZA_DIR);
                }
                if (!file_exists(self::MAGEPLAZA_DIR . DIRECTORY_SEPARATOR . 'pdfinvoice')) {
                    mkdir(self::MAGEPLAZA_DIR . DIRECTORY_SEPARATOR . 'pdfinvoice');
                }
                if (!file_exists(self::MAGEPLAZA_DIR . DIRECTORY_SEPARATOR . 'tmp')) {
                    mkdir(self::MAGEPLAZA_DIR . DIRECTORY_SEPARATOR . 'tmp');
                }

                foreach ($ids as $sid => $item) {
                    /** Each store export one pdf file*/
                    $this->getMpdfContent($sid, $item, $type)->Output(self::MAGEPLAZA_DIR . DIRECTORY_SEPARATOR . 'pdfinvoice' . DIRECTORY_SEPARATOR . $sid . "-" . $type . "s.pdf", 'F');
                }
                /** Zip all of exported pdf files and download*/
                $this->downloadFile($this->PackFile());
            }
        }
    }

    /**
     * Get Mpdf content
     *
     * @param $sid
     * @param $item
     * @param $type
     *
     * @return Mpdf
     * @throws Exception
     * @throws LocalizedException
     * @throws MpdfException
     */
    public function getMpdfContent($sid, $item, $type)
    {
        $pageSize = $this->helperData->getPageSize($sid);
        $mpdf = $this->createMpdf($pageSize);
        if ($this->helperData->isEnablePageNumber($sid)) {
            $mpdf->setFooter('Page {PAGENO} of {nb}&nbsp;&nbsp;&nbsp;&nbsp;');
        }
        $this->setMode('prints');
        foreach ($item as $id) {
            $html = $this->printPdf($type, $id);
            $mpdf->WriteHTML($html);
            if ($id != end($item)) {
                $mpdf->addPage();
            }
        }

        return $mpdf;
    }

    /***
     * Export zip file to Frontend
     *
     * @param $zipFile
     */
    public function downloadFile($zipFile)
    {
        if (file_exists($zipFile)) {
            header("Content-type: application/zip");
            header("Content-Disposition: attachment; filename=file.zip");
            header("Pragma: no-cache");
            header("Expires: 0");
            readfile($zipFile);
        }
    }

    /***
     * Pack to zip file and delete all file .pdf
     * @return string
     */
    public function PackFile()
    {
        // Get real path for our folder
        $rootPath = realpath(self::MAGEPLAZA_DIR . DIRECTORY_SEPARATOR . 'pdfinvoice');
        $zipPath = realpath(self::MAGEPLAZA_DIR . DIRECTORY_SEPARATOR . 'tmp');

        // Initialize archive object
        $zip = new ZipArchive();
        $zipFile = $zipPath . DIRECTORY_SEPARATOR . 'file.zip';
        $zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $filesToDelete = [];

        // Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            // Skip directories (they would be added automatically)
            if (!$file->isDir()) {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);
                $zip->addFile($filePath, $relativePath);
                $filesToDelete[] = $filePath;
            }
        }

        $zip->close();

        // Delete all files from var/Mageplaza/PdfInvoice
        foreach ($filesToDelete as $file) {
            unlink($file);
        }

        return $zipFile;
    }

    /**
     * @param $storeId
     *
     * @return int
     * @throws LocalizedException
     */
    public function checkStoreId($storeId)
    {
        if ($this->state->getAreaCode() == Area::AREA_FRONTEND) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        return $storeId;
    }

    /**
     * get invoice ids
     *
     * @param $orderId
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getInvoiceIds($orderId)
    {
        $order = $this->order->load($orderId);
        $ids = [];
        foreach ($order->getInvoiceCollection() as $invoice) {
            $currentStoreId = $this->storeManager->getStore()->getId();
            $ids[$currentStoreId][] = $invoice->getId();
        }

        return $ids;
    }

    /**
     * Get Shipment ids
     *
     * @param $orderId
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getShipmentIds($orderId)
    {
        $order = $this->order->load($orderId);
        $ids = [];
        foreach ($order->getShipmentsCollection() as $shipment) {
            $currentStoreId = $this->storeManager->getStore()->getId();
            $ids[$currentStoreId][] = $shipment->getId();
        }

        return $ids;
    }

    /**
     * Get credit memo ids
     *
     * @param $orderId
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getCreditmemoIds($orderId)
    {
        $order = $this->order->load($orderId);
        $ids = [];
        foreach ($order->getCreditmemosCollection() as $creditmemo) {
            $currentStoreId = $this->storeManager->getStore()->getId();
            $ids[$currentStoreId][] = $creditmemo->getId();
        }

        return $ids;
    }

    /**
     * @param $fileName
     * @param $html
     * @param $storeId
     * @param $dest
     *
     * @return string
     * @throws MpdfException
     */
    public function exportToPDF($fileName, $html, $storeId, $dest)
    {
        $pageSize = $this->helperData->getPageSize($storeId) ? $this->helperData->getPageSize($storeId) : "A4";
        $mpdf = $this->createMpdf($pageSize);
        if ($this->helperData->isEnablePageNumber($storeId)) {
            $mpdf->setFooter('Page {PAGENO} of {nb}&nbsp;&nbsp;&nbsp;&nbsp;');
        }
        $mpdf->WriteHTML($html);
        $content = $mpdf->Output($fileName, $dest);

        return $content;
    }

    /**
     * Create mpdf
     *
     * @param $pageSize
     *
     * @return Mpdf
     * @throws MpdfException
     */
    public function createMpdf($pageSize)
    {
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        $config = [
            'mode'              => 'utf-8',
            'format'            => $pageSize,
            'default_font_size' => 0,
            'margin_left'       => 0,
            'margin_right'      => 0,
            'margin_top'        => 0,
            'margin_bottom'     => 5,
            'margin_header'     => 0,
            'margin_footer'     => 0,
            'fontDir'           => array_merge($fontDirs, [$this->getFontDirectory()]),
            'fontdata'          => $fontData + [
                    'roboto' => [
                        'R'  => 'Roboto-Regular.ttf',
                        'B'  => 'Roboto-Bold.ttf',
                        'I'  => 'Roboto-Italic.ttf',
                        'BI' => 'Roboto-BoldCondensedItalic.ttf'
                    ],
                    'lato'   => [
                        'R'  => 'lato/Lato-Regular.ttf',
                        'B'  => 'lato/Lato-Bold.ttf',
                        'I'  => 'lato/Lato-Italic.ttf',
                        'BI' => 'lato/Lato-BoldItalic.ttf'
                    ]
                ],
            'default_font'      => 'lato',
            'orientation'       => 'P',
            'tempDir'           => BP . '/var/tmp'
        ];

        return new Mpdf($config);
    }

    /**
     * Add custom template vars
     *
     * @param $templateVars
     * @param $storeId
     *
     * @return mixed
     */
    public function addCustomTemplateVars($templateVars, $storeId)
    {
        if (!empty($this->getLogoUrl($storeId))) {
            $templateVars['logo_url'] = $this->getLogoUrl($storeId);
        }

        $templateVars['logo_white_url'] = $this->getLogoUrl($storeId, 'white');
        $templateVars['businessInformation'] = $this->getBusinessInformation($storeId);
        $templateVars['pdfInvoiceDesign'] = new DataObject($this->helperData->getPdfDesign('', $storeId));
        $templateVars['pdfInvoiceCustom'] = $this->customFunction;

        return $templateVars;
    }

    /**
     * Return payment info block as html
     *
     * @param Order $order
     * @param $storeId
     *
     * @return string
     * @throws Exception
     */
    protected function getPaymentHtml(Order $order, $storeId)
    {
        return $this->paymentHelper->getInfoBlockHtml(
            $order->getPayment(),
            $storeId
        );
    }

    /**
     * Get format shipping address
     *
     * @param Order $order
     *
     * @return string|null
     */
    protected function getFormattedShippingAddress($order)
    {
        return $order->getIsVirtual()
            ? null
            : $this->addressRenderer->format($order->getShippingAddress(), 'html');
    }

    /**
     * Get format Billing address
     *
     * @param Order $order
     *
     * @return string|null
     */
    protected function getFormattedBillingAddress($order)
    {
        return $this->addressRenderer->format($order->getBillingAddress(), 'html');
    }

    /**
     * Get data preview
     *
     * @param string $type
     * @param null $id
     *
     * @return CreditmemoInterface|InvoiceInterface|OrderInterface|ShipmentInterface
     */
    public function getDataOrder($type = Type::INVOICE, $id = null)
    {
        switch ($type) {
            case Type::CREDIT_MEMO:
                if (empty($id)) {
                    $id = $this->creditmemo->getCollection()->getFirstItem()->getId();
                }

                $this->setComment($this->creditmemo->load($id));
                $model = $this->creditmemoRepository->get($id);

                break;
            case Type::ORDER:
                if (empty($id)) {
                    $id = $this->order->getCollection()->getFirstItem()->getId();
                }
                $model = $this->orderRepository->get($id);
                break;
            case Type::SHIPMENT:
                if (empty($id)) {
                    $id = $this->shipment->getCollection()->getFirstItem()->getId();
                }
                $this->setComment($this->shipment->load($id));
                $model = $this->shipmentRepository->get($id);
                break;
            default:
                if (empty($id)) {
                    $id = $this->invoice->getCollection()->getFirstItem()->getId();
                }

                $this->setComment($this->invoice->load($id));
                $model = $this->invoiceRepository->get($id);
        }
        $this->fileName = $type . $model->getIncrementId();

        return $model;
    }

    /**
     * Set comment
     *
     * @param $model
     */
    public function setComment($model)
    {
        $this->comment = $model->getCustomerNoteNotify() ? $model->getCustomerNote() : '';
    }

    /**
     * Get Comment
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param null $storeId
     * @param string $type
     *
     * @return string
     */
    public function getLogoUrl($storeId = null, $type = 'black')
    {
        $logoUrl = '';
        $logoPdf = $type == 'white' ? $this->helperData->getWhiteLogoPDF($storeId) : $this->helperData->getLogoPDF($storeId);
        if (!empty($logoPdf)) {
            $logoUrl = $this->_urlBuilder->getBaseUrl(['_type' => 'media']) . 'mageplaza/pdfinvoice/' . $logoPdf;
        }

        return $logoUrl;
    }

    /**
     * Get business information
     *
     * @param $storeId
     *
     * @return DataObject|mixed
     */
    public function getBusinessInformation($storeId)
    {
        $data = [];
        if (is_array($this->helperData->getBusinessInformationConfig('', $storeId))) {
            $data = $this->helperData->getBusinessInformationConfig('', $storeId);
        }

        if ($data['logo_width'] === null) {
            $data['logo_width'] = 180;
        }

        if ($data['logo_height'] === null) {
            $data['logo_height'] = 30;
        }

        return new DataObject($data);
    }

    /**
     * @param $type
     * @param null $id
     *
     * @return mixed
     * @throws Exception
     * @throws LocalizedException
     */
    public function getDataProcess($type, $id = null)
    {
        $dataOrder = $this->getDataOrder($type, $id);
        $typeTitle = $type;
        if ($type == 'order') {
            $order = $dataOrder;

            $data = [
                'order' => $dataOrder,
            ];
        } else {
            $order = $dataOrder->getOrder();
            $data = [
                'order'   => $order,
                'comment' => $this->getComment(),
                $type     => $dataOrder,
            ];
        }

        $storeId = $this->checkStoreId($order->getStore()->getId());
        $data['billing'] = $order->getBillingAddress();
        $data['payment_html'] = $this->getPaymentHtml($order, $storeId);
        $data['payment_title'] = $order->getPayment()->getMethodInstance()->getTitle();
        $data['store'] = $order->getStore();
        $data['formattedShippingAddress'] = $this->getFormattedShippingAddress($order);
        $data['formattedBillingAddress'] = $this->getFormattedBillingAddress($order);
        $data[$typeTitle . 'Note'] = $this->helperData->getPdfNote($type, $storeId);

        return $this->addCustomTemplateVars($data, $storeId);
    }

    /**
     * @param $type
     * @param $id
     *
     * @return string
     * @throws Exception
     * @throws LocalizedException
     */
    public function printPdf($type, $id)
    {
        $data = $this->getDataProcess($type, $id);
        $store = $data['store'];
        $storeId = $this->checkStoreId($store->getId());
        switch ($type) {
            case Type::CREDIT_MEMO:
                $templateId = $this->helperData->getPdfTemplate(Type::CREDIT_MEMO, $storeId);
                break;
            case Type::ORDER:
                $templateId = $this->helperData->getPdfTemplate(Type::ORDER, $storeId);
                break;
            case Type::SHIPMENT:
                $templateId = $this->helperData->getPdfTemplate(Type::SHIPMENT, $storeId);
                break;
            default:
                $templateId = $this->helperData->getPdfTemplate(Type::INVOICE, $storeId);
        }
        $templateHtml = $this->getTemplateHtml($templateId, $type);

        return $this->getPDFContent($templateHtml, $data, 'D', $storeId);
    }

    /**
     * Get font directory
     * @return string
     */
    public function getFontDirectory()
    {
        return $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, 'Mageplaza_PdfInvoice') . '/Fonts';
    }

    /**
     * Get base template path
     * @return string
     */
    public function getBaseTemplatePath()
    {
        // Get directory of Data.php
        $currentDir = __DIR__;

        // Get root directory(path of magento's project folder)
        $rootPath = $this->directoryList->getRoot();

        $currentDirArr = explode('\\', $currentDir);
        if (count($currentDirArr) == 1) {
            $currentDirArr = explode('/', $currentDir);
        }

        $rootPathArr = explode('/', $rootPath);
        if (count($rootPathArr) == 1) {
            $rootPathArr = explode('\\', $rootPath);
        }

        $basePath = '';
        for ($i = count($rootPathArr); $i < count($currentDirArr) - 1; $i++) {
            $basePath .= $currentDirArr[$i] . '/';
        }

        return $basePath . 'view/base/templates/default/';
    }
}
