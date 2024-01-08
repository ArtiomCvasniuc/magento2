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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Block\Items\AbstractItems;
use Magento\Tax\Helper\Data;
use Mageplaza\PdfInvoice\Helper\Data as HelperData;
use Mageplaza\PdfInvoice\Model\Source\Type;

/**
 * Class PdfItems
 * @package Mageplaza\PdfInvoice\Block
 */
abstract class PdfItems extends AbstractItems
{
    const BUNDLE_BLOCK = 'Mageplaza\PdfInvoice\Block\BundleItems';
    const DEFAULT_BUNDLE_TEMPLATE = 'Mageplaza_PdfInvoice::handle/';
    const ORDER_BUNDLE_TEMPLATE = 'Mageplaza_PdfInvoice::handle/order/';
    const SHIPMENT_BUNDLE_TEMPLATE = 'Mageplaza_PdfInvoice::handle/shipment/';
    const BUNDLE_ITEM = 'bundle';

    /**
     * @var HelperData
     */
    protected $helperConfig;

    /**
     * @var Data
     */
    protected $taxHelper;

    /**
     * @var $storeId
     */
    protected $storeId;

    /**
     * @var $item
     */
    protected $item;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * PdfItems constructor.
     *
     * @param Context $context
     * @param Data $taxHelper
     * @param HelperData $helperdata
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $taxHelper,
        HelperData $helperdata,
        array $data = []
    ) {
        $this->taxHelper = $taxHelper;
        $this->helperData = $helperdata;

        parent::__construct($context, $data);
    }

    /**
     * Get item
     * @return mixed
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set Item
     *
     * @param $item
     */
    public function setItem($item)
    {
        $this->item = $item;
        $this->storeId = $item->getStoreId();
    }

    /**
     * Get type item
     * @return string
     */
    public function getTypeItem()
    {
        return $this->_getItemType($this->getItem());
    }

    /**
     * Is bundle item
     * @return bool
     */
    public function isBundleItem()
    {
        return $this->getTypeItem() == self::BUNDLE_ITEM ? true : false;
    }

    /**
     * @param $item
     * @param $order
     * @param $type
     * @param $indexKey
     * @param int $isBarcode
     * @param null $bundleFile
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function renderBundleItem($item, $order, $type, $indexKey, $isBarcode = 0, $bundleFile = null)
    {
        if ($type == Type::ORDER) {
            $template = self::ORDER_BUNDLE_TEMPLATE . $bundleFile;
        } elseif ($type == Type::SHIPMENT) {
            $template = self::SHIPMENT_BUNDLE_TEMPLATE . $bundleFile;
        } else {
            $template = self::DEFAULT_BUNDLE_TEMPLATE . $bundleFile;
        }

        return $this->getLayout()
            ->createBlock(self::BUNDLE_BLOCK)
            ->setItem($item)
            ->setOrder($order)
            ->setType($type)
            ->setIndexKey($indexKey)
            ->setIsBarcode($isBarcode)
            ->setPageSize($this->getPageSize())
            ->setTemplate($template)
            ->toHtml();
    }

    /**
     * @return array
     */
    public function getItemOptions()
    {
        $result = [];
        if ($options = $this->getItem()->getOrderItem()->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }

        return $result;
    }

    /**
     * @param string|array $value
     *
     * @return string
     */
    public function getValueHtml($value)
    {
        if (is_array($value)) {
            return sprintf(
                '%d',
                $value['qty']
            ) . ' x ' . $this->escapeHtml(
                $value['title']
            ) . " " . $this->getItem()->getOrder()->formatPrice(
                $value['price']
            );
        }

        return $this->escapeHtml($value);
    }

    /**
     * @param mixed $item
     *
     * @return mixed
     */
    public function getSku($item)
    {
        if ($item->getOrderItem()->getProductOptionByCode('simple_sku')) {
            return $item->getOrderItem()->getProductOptionByCode('simple_sku');
        }

        return $item->getSku();
    }

    /**
     * @return bool|BlockInterface
     * @throws LocalizedException
     */
    public function getProductAdditionalInformationBlock()
    {
        return $this->getLayout()->getBlock('additional.product.info');
    }

    /**
     * @param $item
     *
     * @return string
     * @throws LocalizedException
     */
    public function getItemPrice($item)
    {
        $block = $this->getLayout()->getBlock('item_price');
        $block->setItem($item);

        return $block->toHtml();
    }

    /**
     * Return whether display setting is to display price including tax
     *
     * @return bool
     */
    public function displayPriceInclTax()
    {
        return $this->taxHelper->displaySalesPriceInclTax($this->storeId);
    }

    /**
     * Return whether display setting is to display price excluding tax
     *
     * @return bool
     */
    public function displayPriceExclTax()
    {
        return $this->taxHelper->displaySalesPriceExclTax($this->storeId);
    }

    /**
     * Return whether display setting is to display both price including tax and price excluding tax
     *
     * @return bool
     */
    public function displayBothPrices()
    {
        return $this->taxHelper->displaySalesBothPrices($this->storeId);
    }

    /**
     * @return mixed
     */
    public function getPageSize()
    {
        return $this->helperData->getPageSize();
    }
}
