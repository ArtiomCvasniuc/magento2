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
 * @package     Mageplaza_SpecialPromotions
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\SpecialPromotions\Model\Rule\Condition\Order;

use Magento\Framework\Model\AbstractModel;
use Magento\Rule\Model\Condition\Context;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

/**
 * Subselect conditions for order.
 *
 * Class Subselect
 * @package Mageplaza\SpecialPromotions\Model\Rule\Condition\Order
 */
class Subselect extends Combine
{
    /**
     * @var CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * Subselect constructor.
     *
     * @param Context $context
     * @param CollectionFactory $orderCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $orderCollectionFactory,
        array $data = []
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;

        parent::__construct($context, $data);

        $this->setType(__CLASS__)->setValue(null);
    }

    /**
     * Load array
     *
     * @param array $arr
     * @param string $key
     *
     * @return $this
     */
    public function loadArray($arr, $key = 'conditions')
    {
        $this->setAttribute($arr['attribute']);
        $this->setOperator($arr['operator']);
        parent::loadArray($arr, $key);

        return $this;
    }

    /**
     * Return as xml
     *
     * @param string $containerKey
     * @param string $itemKey
     *
     * @return string
     */
    public function asXml($containerKey = 'conditions', $itemKey = 'condition')
    {
        $xml = '<attribute>' .
               $this->getAttribute() .
               '</attribute>' .
               '<operator>' .
               $this->getOperator() .
               '</operator>' .
               parent::asXml(
                   $containerKey,
                   $itemKey
               );

        return $xml;
    }

    /**
     * Load attribute options
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption([
            'qty'                 => __('total quantity'),
            'base_grand_total'    => __('total amount'),
            'base_total_paid'     => __('total paid amount'),
            'base_total_refunded' => __('total refunded amount'),
            'average'             => __('average amount')
        ]);

        return $this;
    }

    /**
     * Load value options
     *
     * @return $this
     */
    public function loadValueOptions()
    {
        return $this;
    }

    /**
     * Load operator options
     *
     * @return $this
     */
    public function loadOperatorOptions()
    {
        $this->setOperatorOption(
            [
                '=='  => __('is'),
                '!='  => __('is not'),
                '>='  => __('equals or greater than'),
                '<='  => __('equals or less than'),
                '>'   => __('greater than'),
                '<'   => __('less than'),
                '()'  => __('is one of'),
                '!()' => __('is not one of'),
            ]
        );

        return $this;
    }

    /**
     * Get value element type
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * Return as html
     *
     * @return string
     */
    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml() . __(
            'If %1 %2 %3 for a subselection of orders matching %4 of these conditions (leave blank for all orders):',
            $this->getAttributeElement()->getHtml(),
            $this->getOperatorElement()->getHtml(),
            $this->getValueElement()->getHtml(),
            $this->getAggregatorElement()->getHtml()
        );
        if ($this->getId() != '1') {
            $html .= $this->getRemoveLinkHtml();
        }

        return $html;
    }

    /**
     * Validate
     *
     * @param AbstractModel $model
     *
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function validate(AbstractModel $model)
    {
        $attr = $this->getAttribute();
        $total = 0;
        $totalQty = 0;
        foreach ($this->getAllOrders($model) as $order) {
            if (parent::validate($order)) {
                $totalQty++;
                $total += $order->getData(($attr === 'average') ? 'base_grand_total' : $attr);
            }
        }

        if ($attr === 'qty') {
            $total = $totalQty;
        } elseif ($totalQty && $attr === 'average') {
            $total /= $totalQty;
        }

        return $this->validateAttribute($total);
    }

    /**
     * @param $address
     *
     * @return array|Collection
     */
    protected function getAllOrders($address)
    {
        $customerId = $address->getCustomerId();
        if (!$customerId) {
            return [];
        }

        return $this->orderCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customerId);
    }
}
