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

namespace Mageplaza\PdfInvoice\Controller;

use Exception;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Config;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Shipment;
use Mageplaza\PdfInvoice\Helper\PrintProcess;
use Mageplaza\PdfInvoice\Model\Source\Type;

/**
 * Class PrintAction
 * @package Mageplaza\PdfInvoice\Controller\Invoice
 */
abstract class AbstractPrint extends Action
{
    /**
     * @var PrintProcess
     */
    protected $helperData;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @var Creditmemo
     */
    protected $creditmemo;

    /**
     * @var Shipment
     */
    protected $shipment;

    /**
     * @var Invoice
     */
    protected $invoice;

    /**
     * @var Config
     */
    protected $orderConfig;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * AbstractPrint constructor.
     *
     * @param Context $context
     * @param Order $order
     * @param Invoice $invoice
     * @param Shipment $shipment
     * @param Creditmemo $creditmemo
     * @param PrintProcess $helperData
     * @param Session $customerSession
     * @param Config $orderConfig
     */
    public function __construct(
        Context $context,
        Order $order,
        Invoice $invoice,
        Shipment $shipment,
        Creditmemo $creditmemo,
        PrintProcess $helperData,
        Session $customerSession,
        Config $orderConfig
    ) {
        $this->helperData = $helperData;
        $this->order = $order;
        $this->invoice = $invoice;
        $this->shipment = $shipment;
        $this->creditmemo = $creditmemo;
        $this->customerSession = $customerSession;
        $this->orderConfig = $orderConfig;

        return parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        if ($this->customerSession->isLoggedIn()) {
            $type = $this->getType();
            $orderId = $this->getRequest()->getParam('order_id', false);
            if (!is_numeric($orderId)) {
                $orderId = false;
            }
            if ($type != Type::ORDER) {
                $id = $this->getRequest()->getParam($type . '_id', false);
                if (!is_numeric($id)) {
                    $id = false;
                }
                $printAll = $this->getRequest()->getParam('print', false);
            } else {
                $id = $orderId;
                $printAll = false;
            }

            $order = $this->getOrder($orderId, $id, $type);

            // echo 'Order ID: ' . $orderId . '<br/>';
            // echo 'ID: ' . $id . '<br/>';
            // echo 'Type: ' . $type;
            // exit;

            if ($order && (($type == Type::ORDER && $orderId) || ($printAll && $orderId) || $id)) {
                // exit('1');
                if ($order && $this->canPrint($order)) {
                    // exit('1');
                    try {
                        if ($printAll) {
                            // exit('1');
                            $ids = $this->getIds($type, $orderId);
                            $this->helperData->printAllPdf($type, $ids);
                        } else {
                            // exit('2');
                            $this->helperData->printPdf($type, $id);
                        }
                    } catch (Exception $e) {
                        $this->messageManager->addError(__('Can not print PDF!'));
                    }
                    exit('OK');

                    if ($type != Type::ORDER) {
                        return $this->_redirect('sales/order/' . $type, ['order_id' => $orderId]);
                    } else {
                        return $this->_redirect('sales/order/view', ['order_id' => $orderId]);
                    }
                }
                // exit('2');

                $this->messageManager->addError(__('You don\'t have permission to print this ' . $type . '.'));
            } else {
                $this->messageManager->addError(__('Invalid ' . $type . ' ID.'));
            }

            return $this->_redirect('sales/order/history');
        }

        return $this->_redirect('customer/account/login');
    }

    /**
     * @return string
     */
    protected function getType()
    {
        return Type::INVOICE;
    }

    /**
     * @param $orderId
     * @param $id
     * @param $type
     *
     * @return $this|bool|Order
     */
    public function getOrder($orderId, $id, $type)
    {
        // echo 'ID: ' . $id . '<br/>';
        // echo 'Type: ' . $type;
        // exit;
        
        if ($id && $type != Type::ORDER) {
            // exit('1');
            switch ($type) {
                case Type::INVOICE:
                    $varType = $this->invoice;
                    break;
                case Type::SHIPMENT:
                    $varType = $this->shipment;
                    break;
                case Type::CREDIT_MEMO:
                    $varType = $this->creditmemo;
                    break;
            }
            $pdfInvoice = $varType->load($id);
            if (!$pdfInvoice->isEmpty()) {
                return $pdfInvoice->getOrder();
            }
        } elseif ($orderId) {
            // exit('2');
            $order = $this->order->load($orderId);
            if (!$order->isEmpty()) {
                return $order;
            }
        }

        return false;
    }

    /**
     * Get the [Invoice ore Shipment or Creditmemo] Id array from order id
     *
     * @param $type
     * @param $orderId
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getIds($type, $orderId)
    {
        switch ($type) {
            case Type::INVOICE:
                $ids = $this->helperData->getInvoiceIds($orderId);
                break;
            case Type::SHIPMENT:
                $ids = $this->helperData->getShipmentIds($orderId);
                break;
            case Type::CREDIT_MEMO:
                $ids = $this->helperData->getCreditmemoIds($orderId);
                break;
        }

        return $ids;
    }

    /**
     * Check if order can print
     *
     * @param $order
     *
     * @return bool
     */
    public function canPrint($order)
    {
        $customerId = $this->customerSession->getCustomerId();
        // echo 'Customer ID: ' . $customerId;
        // exit;
        $availableStatuses = $this->orderConfig->getVisibleOnFrontStatuses();
        array_push($availableStatuses, 'cancelled');
        // echo 'Available Statuses: ';
        // echo '<pre>';
        // print_r($availableStatuses);
        // echo '</pre>';
        // exit;

        // echo '1: ' . $order->getId() . '<br/>';
        // echo '2: ' . $order->getCustomerId() . '<br/>';
        // echo '3: ' . $customerId . '<br/>';
        // echo '4: ' . $order->getStatus() . '<br/>';
        // exit;

        if ($order->getId()
            && $order->getCustomerId()
            && $order->getCustomerId() == $customerId
            && in_array($order->getStatus(), $availableStatuses, true)
        ) {
            // exit('OK');
            return true;
        }

        // return false;
    }
}
