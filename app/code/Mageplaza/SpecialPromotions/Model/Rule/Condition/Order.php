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

namespace Mageplaza\SpecialPromotions\Model\Rule\Condition;

use Magento\Framework\Model\AbstractModel;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;
use Magento\Sales\Model\Config\Source\Order\Status;

/**
 * Order rule condition data model
 *
 * @package Mageplaza\SpecialPromotions\Model\Rule\Condition
 */
class Order extends AbstractCondition
{
    /**
     * @var Status
     */
    protected $_orderStatus;

    /**
     * Order constructor.
     *
     * @param Context $context
     * @param Status $orderStatus
     * @param array $data
     */
    public function __construct(
        Context $context,
        Status $orderStatus,
        array $data = []
    ) {
        $this->_orderStatus = $orderStatus;

        parent::__construct($context, $data);
    }

    /**
     * @return AbstractCondition
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);

        return $element;
    }

    /**
     * @return AbstractCondition
     */
    public function getValueElement()
    {
        $element = parent::getValueElement();
        if ($this->getAttribute() === 'created_at') {
            $element->setExplicitApply(true);
        }

        return $element;
    }

    /**
     * @inheritdoc
     */
    public function loadAttributeOptions()
    {
        $attributes = [
            'status'     => __('Order Status'),
            'created_at' => __('Order Created Date'),
            'period'     => __('Order Created Within (days)'),
        ];

        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * @return string
     */
    public function getInputType()
    {
        switch ($this->getAttribute()) {
            case 'status':
                return 'multiselect';
            case 'created_at':
                return 'date';
            case 'period':
                return 'numeric';
        }

        return 'string';
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {
        switch ($this->getAttribute()) {
            case 'status':
                return 'multiselect';
            case 'created_at':
                return 'date';
            case 'period':
                return 'text';
        }

        return 'text';
    }

    /**
     * @return array
     */
    public function getDefaultOperatorInputByType()
    {
        $operator = parent::getDefaultOperatorInputByType();
        $operator['numeric'] = ['==', '>=', '>', '<=', '<'];

        return $operator;
    }

    /**
     * @return array|mixed
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            $options = ($this->getAttribute() === 'status') ? $this->_orderStatus->toOptionArray() : [];
            $this->setData('value_select_options', $options);
        }

        return $this->getData('value_select_options');
    }

    /**
     * Validate Order Rule Condition
     *
     * @param AbstractModel $order
     *
     * @return bool
     * @throws \Exception
     */
    public function validate(AbstractModel $order)
    {
        /** @var \Magento\Sales\Model\Order $order */
        if ($this->getAttribute() === 'period') {
            $orderCreatedDate = new \DateTime($order->getCreatedAt());
            $today = new \DateTime();

            $order->setPeriod($today->diff($orderCreatedDate)->format('%d'));
        }

        return parent::validate($order);
    }
}
