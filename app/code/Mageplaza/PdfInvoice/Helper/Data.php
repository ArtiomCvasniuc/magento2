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

use Magento\Config\Model\ResourceModel\Config as ResourceConfig;
use Magento\Email\Model\AbstractTemplate;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Information;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData;
use Mageplaza\PdfInvoice\Model\Source\Type;
use Mageplaza\PdfInvoice\Model\TemplateFactory;

/**
 * Class Data
 * @package Mageplaza\PdfInvoice\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH = 'pdfinvoice';
    const BUSINESS_INFORMATION_CONFIGURATION = 'pdfinvoice/general/business_information';
    /**
     * Recipient email config path
     */
    const XML_PATH_EMAIL_RECIPIENT = 'contact/email/recipient_email';

    /**
     * @var TemplateFactory
     */
    protected $templateFactory;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Information
     */
    protected $storeInformation;

    /**
     * @var string
     */
    protected $note;

    /**
     * @var ResourceConfig
     */
    protected $resourceConfig;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param TemplateFactory $templateFactory
     * @param Filesystem $filesystem
     * @param StoreManagerInterface $storeManager
     * @param Information $storeInformation
     * @param ObjectManagerInterface $objectManager
     * @param ResourceConfig $resourceConfig
     */
    public function __construct(
        Context $context,
        TemplateFactory $templateFactory,
        Filesystem $filesystem,
        StoreManagerInterface $storeManager,
        Information $storeInformation,
        ObjectManagerInterface $objectManager,
        ResourceConfig $resourceConfig
    ) {
        $this->templateFactory = $templateFactory;
        $this->filesystem = $filesystem;
        $this->storeManager = $storeManager;
        $this->storeInformation = $storeInformation;
        $this->resourceConfig = $resourceConfig;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * Is enable pdf template for each type
     *
     * @param $type
     * @param null $storeId
     *
     * @return mixed
     */
    public function isEnableAttachment($type, $storeId = null)
    {
        return $this->getConfigValue(self::CONFIG_MODULE_PATH . '/' . $type . '/enable', $storeId);
    }

    /**
     * Get pdf template for each type
     *
     * @param $type
     * @param null $storeId
     *
     * @return mixed
     */
    public function getPdfTemplate($type, $storeId = null)
    {
        return $this->getConfigValue(self::CONFIG_MODULE_PATH . '/' . $type . '/template', $storeId);
    }

    /**
     * Can show custom print button for each type
     *
     * @param $type
     * @param null $storeId
     *
     * @return bool|mixed
     */
    public function canShowCustomPrint($type, $storeId = null)
    {
        if (!$this->isEnabled($storeId)) {
            return false;
        }

        return $this->getConfigValue(self::CONFIG_MODULE_PATH . '/' . $type . '/print', $storeId);
    }

    /**
     * Get pdf invoice note
     *
     * @param $type
     * @param null $storeId
     *
     * @return mixed
     */
    public function getPdfNote($type, $storeId = null)
    {
        return $this->getConfigValue(self::CONFIG_MODULE_PATH . '/' . $type . '/note', $storeId);
    }

    /**
     * Get business information
     *
     * @param $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getBusinessInformationConfig($code, $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigGeneral('business_information' . $code, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getLogoPDF($storeId = null)
    {
        return $this->getBusinessInformationConfig('logo', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getWhiteLogoPDF($storeId = null)
    {
        return $this->getBusinessInformationConfig('white_logo', $storeId);
    }

    /**
     * Get pdf invoice design
     *
     * @param $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getPdfDesign($code, $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(self::CONFIG_MODULE_PATH . '/design' . $code, $storeId);
    }

    /**
     * Is enable page number in pdf
     *
     * @param null $storeId
     *
     * @return mixed
     */
    public function isEnablePageNumber($storeId = null)
    {
        return $this->getPdfDesign('page_number', $storeId);
    }

    /**
     * Get page size
     *
     * @param null $storeId
     *
     * @return mixed
     */
    public function getPageSize($storeId = null)
    {
        return $this->getPdfDesign('page_size', $storeId);
    }

    /**
     * Set Business Information
     */
    public function setBusinessInformation()
    {
        $this->saveConfig(self::BUSINESS_INFORMATION_CONFIGURATION . '/company', $this->getConfigValue(Information::XML_PATH_STORE_INFO_NAME, 0), 0);
        $this->saveConfig(self::BUSINESS_INFORMATION_CONFIGURATION . '/phone', $this->getConfigValue(Information::XML_PATH_STORE_INFO_PHONE, 0), 0);
        $this->saveConfig(self::BUSINESS_INFORMATION_CONFIGURATION . '/address', $this->getConfigValue(Information::XML_PATH_STORE_INFO_STREET_LINE1, 0), 0);
        $this->saveConfig(self::BUSINESS_INFORMATION_CONFIGURATION . '/vat_number', $this->getConfigValue(Information::XML_PATH_STORE_INFO_VAT_NUMBER, 0), 0);
        $this->saveConfig(self::BUSINESS_INFORMATION_CONFIGURATION . '/contact', $this->getConfigValue(self::XML_PATH_EMAIL_RECIPIENT, 0), 0);

        /** @var Store $store */
        foreach ($this->storeManager->getStores() as $store) {
            $storeId = $store->getId();
            $storeInfo = $this->storeInformation->getStoreInformationObject($store);
            $this->saveConfig(self::BUSINESS_INFORMATION_CONFIGURATION . '/company', $storeInfo->getName());
            $this->saveConfig(self::BUSINESS_INFORMATION_CONFIGURATION . '/phone', $storeInfo->getPhone());
            $this->saveConfig(self::BUSINESS_INFORMATION_CONFIGURATION . '/contact', $this->getConfigValue(self::XML_PATH_EMAIL_RECIPIENT, $storeId));
            $this->saveConfig(self::BUSINESS_INFORMATION_CONFIGURATION . '/logo_width', $this->getConfigValue(AbstractTemplate::XML_PATH_DESIGN_EMAIL_LOGO_WIDTH, $storeId));
            $this->saveConfig(self::BUSINESS_INFORMATION_CONFIGURATION . '/logo_height', $this->getConfigValue(AbstractTemplate::XML_PATH_DESIGN_EMAIL_LOGO_HEIGHT, $storeId));
        }
    }

    /**
     * Save config
     *
     * @param $field
     * @param $value
     * @param $storeId
     */
    public function saveConfig($field, $value)
    {
        if (!empty($value)) {
            $this->resourceConfig->saveConfig($field, $value, 'default', 0);
        }
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getTemplatesConfig($storeId = null)
    {
        return $this->getModuleConfig('template', $storeId);
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function toOptionArray()
    {
        $result = [];

        foreach ($this->getTemplatesConfig() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    /**
     * Get Templates
     *
     * @param $type
     *
     * @return array
     */
    public function getTemplates($type)
    {
        $result = [];
        $invoiceCollection = $this->templateFactory->create()
            ->getCollection()
            ->addFieldToFilter('type', $type);

        foreach ($invoiceCollection as $invoice) {
            $result[] = ['value' => $invoice->getId(), 'label' => $invoice->getName()];
        }
        $result = array_merge($result, $this->toOptionArray());

        return $result;
    }

    /**
     * Check template is using in config
     *
     * @param $id
     *
     * @return bool
     */
    public function checkTemplateInConfig($id)
    {
        $flag = false;
        foreach ($this->getStores() as $store) {
            $storeId = $store->getId();
            $invoice = $this->getPdfTemplate(Type::INVOICE, $storeId);
            $order = $this->getPdfTemplate(Type::ORDER, $storeId);
            $shipment = $this->getPdfTemplate(Type::SHIPMENT, $storeId);
            $creditmemo = $this->getPdfTemplate(Type::CREDIT_MEMO, $storeId);
            if ($id == $invoice || $id == $order || $id == $shipment || $id == $creditmemo) {
                $flag = true;
            }
        }

        return $flag;
    }

    /**
     * Get stores
     * @return StoreInterface[]
     */
    public function getStores()
    {
        return $this->storeManager->getStores();
    }

    /**
     * display print button on top of action list in order grid
     *
     * @param null $storeId
     *
     * @return mixed
     */
    public function isTopButton($storeId = null)
    {
        return $this->getConfigValue(self::CONFIG_MODULE_PATH . '/' . 'order' . '/button_top', $storeId);
    }

    /**
     * display print invoice button in action list from order grid
     *
     * @param null $storeId
     *
     * @return mixed
     */
    public function isInvoiceInOrderGrid($storeId = null)
    {
        return $this->getConfigValue(self::CONFIG_MODULE_PATH . '/' . 'invoice' . '/orderGrid_button', $storeId);
    }

    /**
     * display print shipment button in action list from order grid
     *
     * @param null $storeId
     *
     * @return mixed
     */
    public function isShipmentInOrderGrid($storeId = null)
    {
        return $this->getConfigValue(self::CONFIG_MODULE_PATH . '/' . 'shipment' . '/orderGrid_button', $storeId);
    }

    /**
     * display print creditmemo button in action list from order grid
     *
     * @param null $storeId
     *
     * @return mixed
     */
    public function isCreditmemoInOrderGrid($storeId = null)
    {
        return $this->getConfigValue(self::CONFIG_MODULE_PATH . '/' . 'creditmemo' . '/orderGrid_button', $storeId);
    }
}
