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

namespace Mageplaza\PdfInvoice\Plugin;

use Magento\Backend\Block\Widget\Button\ButtonList;
use Magento\Backend\Block\Widget\Button\Toolbar;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\AbstractBlock;
use Mageplaza\PdfInvoice\Helper\Data;
use Mageplaza\PdfInvoice\Model\Source\PrintButton;
use Mageplaza\PdfInvoice\Model\Source\Type;

/**
 * Class PdfInvoiceButtonToolbar
 * @package Mageplaza\PdfInvoice\Plugin
 */
class PdfInvoiceButtonToolbar
{
    /**
     * @param Toolbar $subject
     * @param AbstractBlock $context
     * @param ButtonList $buttonList
     */
    public function beforePushButtons(
        Toolbar $subject,
        AbstractBlock $context,
        ButtonList $buttonList
    ) {
        $request = $context->getRequest();
        switch ($request->getFullActionName()) {
            case 'sales_order_view':
                $url = $context->getUrl('pdfinvoice/order/print', ['order_id' => $request->getParam('order_id')]);
                $this->addCustomButton($buttonList, $url, Type::ORDER);
                break;
            case 'sales_order_invoice_view':
                $url = $context->getUrl('pdfinvoice/invoice/print', ['invoice_id' => $request->getParam('invoice_id')]);
                $this->addCustomButton($buttonList, $url, Type::INVOICE);
                break;
            case 'adminhtml_order_shipment_view':
                $url = $context->getUrl('pdfinvoice/shipment/print', ['shipment_id' => $request->getParam('shipment_id')]);
                $this->addCustomButton($buttonList, $url, Type::SHIPMENT);
                break;
            case 'sales_order_creditmemo_view':
                $url = $context->getUrl('pdfinvoice/creditmemo/print', ['creditmemo_id' => $request->getParam('creditmemo_id')]);
                $this->addCustomButton($buttonList, $url, Type::CREDIT_MEMO);
                break;
            default:
        }
    }

    /**
     * @param $buttonList
     * @param $url
     * @param $type
     */
    public function addCustomButton($buttonList, $url, $type)
    {
        $canShowPrint = $this->getHelperConfig()->canShowCustomPrint($type, 0);
        if ($canShowPrint) {
            if ($canShowPrint == PrintButton::CUSTOM_PDF) {
                $id = "print";
                $label = __('Print');
            } else {
                $typeOption = Type::getOptionArray();

                $id = "print_custom_pdf";
                $label = __('Print PDF ' . $typeOption[$type]);
            }

            $buttonList->add($id, [
                'label'   => __($label),
                'onclick' => 'setLocation(\'' . $url . '\')', 'class' => 'reset'
            ]);
        }
    }

    /**
     * Get pdf invoice config
     * @return mixed
     */
    public function getHelperConfig()
    {
        $objectManager = ObjectManager::getInstance();
        $configHelper = $objectManager->get(Data::class);

        return $configHelper;
    }
}
