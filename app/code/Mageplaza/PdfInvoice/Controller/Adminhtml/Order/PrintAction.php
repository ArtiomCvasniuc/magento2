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

namespace Mageplaza\PdfInvoice\Controller\Adminhtml\Order;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Mageplaza\PdfInvoice\Helper\Data;
use Mageplaza\PdfInvoice\Helper\PrintProcess as HelperData;
use Mageplaza\PdfInvoice\Model\Source\Type;

/**
 * Class PrintAction
 * @package Mageplaza\PdfInvoice\Controller\Adminhtml\Order
 */
class PrintAction extends Action
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * PrintAction constructor.
     *
     * @param Context $context
     * @param HelperData $helperData
     */
    public function __construct(
        Context $context,
        HelperData $helperData
    ) {
        $this->helperData = $helperData;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('order_id');
        try {
            $this->helperData->printPdf(Type::ORDER, $id);
        } catch (Exception $e) {
            $this->messageManager->addError(__('Cannot print PDF!'));
        }

        return $this->_redirect('sales/order/view', ['order_id' => $id]);
    }
}
