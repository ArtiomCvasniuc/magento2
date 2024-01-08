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

namespace Mageplaza\PdfInvoice\Ui\Component\Listing;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Mageplaza\PdfInvoice\Helper\Data;
use Mageplaza\PdfInvoice\Model\Source\PrintButton;
use Mageplaza\PdfInvoice\Model\Source\Type;

/**
 * Class MassAction
 * @package Mageplaza\PdfInvoice\Ui\Component\Listing
 */
class MassAction extends \Magento\Ui\Component\MassAction
{
    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var Data
     */
    protected $helperConfig;

    /**
     * MassAction constructor.
     *
     * @param ContextInterface $context
     * @param UrlInterface $urlBuilder
     * @param RequestInterface $request
     * @param Data $helperConfig
     * @param UiComponentInterface[] $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UrlInterface $urlBuilder,
        RequestInterface $request,
        Data $helperConfig,
        array $components = [],
        array $data = []
    ) {
        $this->_urlBuilder = $urlBuilder;
        $this->_request = $request;
        $this->helperConfig = $helperConfig;
        parent::__construct($context, $components, $data);
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        parent::prepare();

        switch ($this->_request->getFullActionName()) {
            case 'sales_order_index':
                // With sales_order_index Magento2 did not have print PDF for order , We will replace config PrintButton::CUSTOM_PDF equal PrintButton::BOTH
                $this->addMassAction($this->helperConfig->canShowCustomPrint(Type::ORDER, 0), Type::ORDER, 'Orders', 'pdfinvoices_order', '');
                if ($this->helperConfig->isInvoiceInOrderGrid()) {
                    // For: Print invoices of selected order function
                    $this->addMassAction($this->helperConfig->canShowCustomPrint(Type::INVOICE, 0), Type::ORDER, 'Invoices', 'pdfinvoices_order', Type::INVOICE);
                }
                if ($this->helperConfig->isshipmentInOrderGrid()) {
                    // For: Print shipments of selected order function
                    $this->addMassAction($this->helperConfig->canShowCustomPrint(Type::SHIPMENT, 0), Type::ORDER, 'Shipments', 'pdfinvoices_order', Type::SHIPMENT);
                }
                if ($this->helperConfig->isCreditmemoInOrderGrid()) {
                    // For: Print credit memos of selected order function
                    $this->addMassAction($this->helperConfig->canShowCustomPrint(Type::CREDIT_MEMO, 0), Type::ORDER, 'Credit Memos', 'pdfinvoices_order', Type::CREDIT_MEMO);
                }
                break;

            case 'sales_invoice_index':
                $this->addMassAction($this->helperConfig->canShowCustomPrint(Type::INVOICE, 0), Type::INVOICE, 'Invoices', 'pdfinvoices_order', '');
                break;

            case 'sales_shipment_index':
                $this->addMassAction($this->helperConfig->canShowCustomPrint(Type::SHIPMENT, 0), Type::SHIPMENT, 'Shipments', 'pdfshipments_order', '');
                break;

            case 'sales_creditmemo_index':
                $this->addMassAction($this->helperConfig->canShowCustomPrint(Type::CREDIT_MEMO, 0), Type::CREDIT_MEMO, 'Credit Memos', 'pdfcreditmemos_order', '');
                break;

            case 'sales_order_view':
                $jsConfig = $this->getJsConfig($this);
                if ($jsConfig['extends'] == 'sales_order_view_invoice_grid') {
                    $this->addOrderViewMassAction($this->helperConfig->canShowCustomPrint(Type::INVOICE, 0), Type::INVOICE, 'Invoices', 'pdfinvoices_order');
                } elseif ($jsConfig['extends'] == 'sales_order_view_shipment_grid') {
                    $this->addOrderViewMassAction($this->helperConfig->canShowCustomPrint(Type::SHIPMENT, 0), Type::SHIPMENT, 'Shipments', 'pdfshipments_order');
                } elseif ($jsConfig['extends'] == 'sales_order_view_creditmemo_grid') {
                    $this->addOrderViewMassAction($this->helperConfig->canShowCustomPrint(Type::CREDIT_MEMO, 0), Type::CREDIT_MEMO, 'Credit Memos', 'pdfcreditmemos_order');
                }
                break;
        }
    }

    /**
     * Add mass action
     *
     * @param $configPrint
     * @param $type
     * @param $label
     * @param $typeAction
     * @param string $subType
     */
    public function addMassAction($configPrint, $type, $label, $typeAction, $subType = 'none')
    {
        if ($configPrint) {
            $config = $this->getConfiguration();
            $url = $this->_urlBuilder->getUrl('pdfinvoice/massaction/printpdf', ['type' => $type, 'subType' => $subType]);

            if ($configPrint == PrintButton::CUSTOM_PDF) {
                foreach ($config['actions'] as $key => $action) {
                    if ($action['type'] == $typeAction) {
                        $config['actions'][$key]['url'] = $url;
                        break;
                    }
                }
            } else {
                $massPrintActions = [
                    'component' => 'uiComponent',
                    'type'      => 'mp_' . $type . '_' . $subType,
                    'label'     => __('Print PDF %1', $label),
                    'url'       => $url
                ];

                if ($this->helperConfig->isTopButton()) {
                    array_unshift($config['actions'], $massPrintActions);
                } else {
                    array_push($config['actions'], $massPrintActions);
                }
            }

            $this->setData('config', (array)$config);
        }
    }

    /**
     * Add mass action for order view grid
     *
     * @param $configPrint
     * @param $type
     * @param $label
     * @param $typeAction
     * @param string $subType
     */
    public function addOrderViewMassAction($configPrint, $type, $label, $typeAction, $subType = 'none')
    {
        if ($configPrint) {
            $config = $this->getConfiguration();
            $context = $this->getContext();
            $order_id = $context->getRequestParam('order_id');
            $url = $this->_urlBuilder->getUrl('pdfinvoice/massaction/printFromOrder', ['type' => $type, 'order_id' => $order_id]);

            if ($configPrint == PrintButton::CUSTOM_PDF) {
                foreach ($config['actions'] as $key => $action) {
                    if ($action['type'] == $typeAction) {
                        $config['actions'][$key]['url'] = $url;
                        break;
                    }
                }
            } else {
                $massPrintActions = [
                    'component' => 'uiComponent',
                    'type'      => 'mp_' . $type . '_' . $subType,
                    'label'     => __('Print PDF %1', $label),
                    'url'       => $url
                ];
                array_push($config['actions'], $massPrintActions);
            }

            $this->setData('config', (array)$config);
        }
    }
}
