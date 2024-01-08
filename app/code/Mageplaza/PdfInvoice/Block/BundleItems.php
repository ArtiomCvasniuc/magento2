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

use Magento\Backend\Block\Template\Context;
use Magento\Bundle\Block\Adminhtml\Sales\Order\Items\Renderer;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Registry;
use Magento\Tax\Helper\Data;
use Mageplaza\PdfInvoice\Model\Source\Type;

/**
 * Class BundleItems
 * @package Mageplaza\PdfInvoice\Block
 */
class BundleItems extends Renderer
{
    /**
     * @var Data
     */
    protected $taxHelper;

    /**
     * @var $storeId
     */
    protected $storeId;

    /**
     * BundleItems constructor.
     *
     * @param Context $context
     * @param StockRegistryInterface $stockRegistry
     * @param StockConfigurationInterface $stockConfiguration
     * @param Registry $registry
     * @param Data $taxHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        StockRegistryInterface $stockRegistry,
        StockConfigurationInterface $stockConfiguration,
        Registry $registry,
        Data $taxHelper,
        array $data = []
    ) {
        $this->taxHelper = $taxHelper;
        $this->stockRegistry = $stockRegistry;
        $this->stockConfiguration = $stockConfiguration;
        $this->_coreRegistry = $registry;

        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $data);
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
     * Set store id
     *
     * @param $id
     */
    public function setStoreId($id)
    {
        $this->storeId = $id;
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
     * @param $type
     *
     * @return bool
     */
    public function isTypeOrder($type)
    {
        return $type == Type::ORDER;
    }
}
