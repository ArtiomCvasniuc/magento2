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

namespace Mageplaza\PdfInvoice\Block;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Block\Order\Creditmemo\Items as SalesCreditmemoItems;
use Magento\Sales\Block\Order\Invoice\Items;
use Magento\Sales\Model\Order;
use Magento\Shipping\Block\Order\Shipment;
use Mageplaza\PdfInvoice\Helper\Data;
use Mageplaza\PdfInvoice\Model\Source\Type;

/**
 * Class CustomPrint
 * @package Mageplaza\PdfInvoice\Block
 */
class CustomPrint extends Template
{
    /**
     * @var Data
     */
    protected $helperConfig;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var Items
     */
    protected $invoiceItems;

    /**
     * @var SalesCreditmemoItems
     */
    protected $creditmemoItems;

    /**
     * @var Shipment
     */
    protected $shipment;

    /**
     * CustomPrint constructor.
     *
     * @param Context $context
     * @param Data $helperConfig
     * @param Registry $registry
     * @param Items $invoiceItems
     * @param SalesCreditmemoItems $creditmemoItems
     * @param Shipment $shipment
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helperConfig,
        Registry $registry,
        Items $invoiceItems,
        SalesCreditmemoItems $creditmemoItems,
        Shipment $shipment,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->helperConfig = $helperConfig;
        $this->invoiceItems = $invoiceItems;
        $this->creditmemoItems = $creditmemoItems;
        $this->shipment = $shipment;

        parent::__construct($context, $data);
    }

    /**
     * Get full action name
     * @return mixed
     */
    public function getFullActionName()
    {
        return $this->getRequest()->getFullActionName();
    }

    /**
     * Retrieve current order model instance
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    /**
     *
     * Get invoice urls
     * @return string
     */
    public function getInvoiceUrls()
    {
        $data = [];
        foreach ($this->getOrder()->getInvoiceCollection() as $invoice) {
            $url = $this->getPrintUrl('invoice', ['invoice_id' => $invoice->getId()]);
            $data[$url] = $this->invoiceItems->getPrintInvoiceUrl($invoice);
        }

        return Data::jsonEncode($data);
    }

    /**
     * Get Shipment Urls
     * @return string
     */
    public function getShipmentUrls()
    {
        $data = [];
        foreach ($this->getOrder()->getShipmentsCollection() as $shipment) {
            $url = $this->getPrintUrl('shipment', ['shipment_id' => $shipment->getId()]);
            $data[$url] = $this->shipment->getPrintShipmentUrl($shipment);
        }

        return Data::jsonEncode($data);
    }

    /**
     * Get order Url
     * @return string
     */
    public function getOrderUrl()
    {
        return $this->getPrintUrl('order', ['order_id' => $this->getOrder()->getId()]);
    }

    /**
     * Get print url
     *
     * @param $type
     * @param $param
     *
     * @return string
     */
    public function getPrintUrl($type, $param)
    {
        $param['order_id'] = $this->getOrder()->getId();

        return $this->getUrl('pdfinvoice/' . $type . '/print', $param);
    }

    /**
     * Get print all url for Invoice
     * @return mixed
     */
    public function getPrintAllForInvoice()
    {
        return $this->getPrintUrl(Type::INVOICE, ['print' => 'all']);
    }

    /**
     * Get print all url for Shipment
     * @return mixed
     */
    public function getPrintAllForShipment()
    {
        return $this->getPrintUrl(Type::SHIPMENT, ['print' => 'all']);
    }

    /**
     * Get print all for Creditmemo
     * @return mixed
     */
    public function getPrintAllForCreditmemo()
    {
        return $this->getPrintUrl(Type::CREDIT_MEMO, ['print' => 'all']);
    }

    /**
     * Get Shipment Urls
     * @return string
     */
    public function getCreditmemoUrls()
    {
        $data = [];
        foreach ($this->getOrder()->getCreditmemosCollection() as $creditmemo) {
            $url = $this->getPrintUrl('creditmemo', ['creditmemo_id' => $creditmemo->getId()]);
            $data[$url] = $this->creditmemoItems->getPrintCreditmemoUrl($creditmemo);
        }

        return Data::jsonEncode($data);
    }

    /**
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isEnable()
    {
        return $this->helperConfig->isEnabled($this->getCurrentStoreId());
    }

    /**
     * Get pdf invoice config
     * @return Data
     */
    public function getHelperConfig()
    {
        return $this->helperConfig;
    }

    /**
     * Get current store id
     * @return int
     * @throws NoSuchEntityException
     */
    public function getCurrentStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
}
