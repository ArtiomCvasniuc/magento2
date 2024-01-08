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

namespace Mageplaza\PdfInvoice\Controller\Adminhtml\MassAction;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory as CreditmemoCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory as InvoiceCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\PdfInvoice\Helper\Data;
use Mageplaza\PdfInvoice\Helper\PrintProcess as HelperData;
use Mageplaza\PdfInvoice\Model\Source\Type;

/**
 * Class Printpdf
 * @package Mageplaza\PdfInvoice\Controller\Adminhtml\MassAction
 */
class Printpdf extends Action
{
    /**
     * @var string
     */
    protected $redirectUrl = 'sales/order/index';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var InvoiceCollectionFactory
     */
    protected $invoiceCollectionFactory;

    /**
     * @var ShipmentCollectionFactory
     */
    protected $shipmentCollectionFactory;

    /**
     * @var CreditmemoCollectionFactory
     */
    protected $creditmemoCollectionFactory;

    /**
     * @var $collectionFactory
     */
    protected $collectionFactory;

    /**
     * Printpdf constructor.
     *
     * @param Context $context
     * @param Filter $filter
     * @param HelperData $helperData
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param InvoiceCollectionFactory $invoiceCollectionFactory
     * @param ShipmentCollectionFactory $shipmentCollectionFactory
     * @param CreditmemoCollectionFactory $creditmemoCollectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        HelperData $helperData,
        OrderCollectionFactory $orderCollectionFactory,
        InvoiceCollectionFactory $invoiceCollectionFactory,
        ShipmentCollectionFactory $shipmentCollectionFactory,
        CreditmemoCollectionFactory $creditmemoCollectionFactory
    ) {
        parent::__construct($context);

        $this->filter = $filter;
        $this->helperData = $helperData;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->invoiceCollectionFactory = $invoiceCollectionFactory;
        $this->shipmentCollectionFactory = $shipmentCollectionFactory;
        $this->creditmemoCollectionFactory = $creditmemoCollectionFactory;
    }

    /**
     * Execute action
     *
     * @return $this|ResponseInterface|ResultInterface
     */
    public function execute()
    {
        try {
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

            $ids = [];
            $storeId = '';
            $type = $this->getRequest()->getParam('type');
            $subtype = $this->getRequest()->getParam('subType');
            $this->setTypeCollection($type);
            $collection = $this->filter->getCollection($this->collectionFactory);

            if ($type == Type::ORDER) {
                switch ($subtype) {
                    case "invoice":
                        foreach ($collection as $order) {
                            $currentStoreId = $order->getStoreId();
                            if ($order->hasInvoices()) {
                                foreach ($order->getInvoiceCollection() as $invoice) {
                                    $ids[$currentStoreId][] = $invoice->getId();
                                }
                            }
                        }
                        $type = Type::INVOICE;
                        break;
                    case "shipment":
                        foreach ($collection as $order) {
                            $currentStoreId = $order->getStoreId();
                            if ($order->hasShipments()) {
                                foreach ($order->getShipmentsCollection() as $shipment) {
                                    $ids[$currentStoreId][] = $shipment->getId();
                                }
                            }
                        }
                        $type = Type::SHIPMENT;
                        break;
                    case "creditmemo":
                        foreach ($collection as $order) {
                            $currentStoreId = $order->getStoreId();
                            if ($order->hasCreditmemos()) {
                                foreach ($order->getCreditmemosCollection() as $creditMemo) {
                                    $ids[$currentStoreId][] = $creditMemo->getId();
                                }
                            }
                        }
                        $type = Type::CREDIT_MEMO;
                        break;
                    default:
                        /**Print selected orders*/
                        foreach ($collection as $data) {
                            $currentStoreId = $data->getStoreId();
                            $ids[$currentStoreId][] = $data->getId();
                        }
                        break;
                }
            } else {
                foreach ($collection as $data) {
                    $currentStoreId = $data->getStoreId();
                    $ids[$currentStoreId][] = $data->getId();
                }
            }

            /** If $ids is null, go back*/
            if (!$ids) {
                $this->messageManager->addError(__('There are no printable documents related to selected orders.'));

                return $resultRedirect->setPath($this->redirectUrl);
            }

            $this->helperData->printAllPdf($type, $ids, $storeId);
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());

            return $resultRedirect->setPath($this->redirectUrl);
        }
    }

    /**
     * Set type collection
     *
     * @param $type
     *
     * @return Collection
     */
    public function setTypeCollection($type)
    {
        if (!$this->collectionFactory) {
            switch ($type) {
                case Type::CREDIT_MEMO:
                    $collection = $this->creditmemoCollectionFactory;
                    $this->redirectUrl = 'sales/creditmemo/index';
                    break;
                case Type::ORDER:
                    $collection = $this->orderCollectionFactory;
                    break;
                case Type::SHIPMENT:
                    $collection = $this->shipmentCollectionFactory;
                    $this->redirectUrl = 'sales/shipment/index';
                    break;
                default:
                    $collection = $this->invoiceCollectionFactory;
                    $this->redirectUrl = 'sales/invoice/index';
            }

            $this->collectionFactory = $collection->create();
        }

        return $this->collectionFactory;
    }
}
