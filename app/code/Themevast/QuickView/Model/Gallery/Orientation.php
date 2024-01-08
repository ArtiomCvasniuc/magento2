<?php
namespace Themevast\QuickView\Model\Gallery;

class Orientation implements \Magento\Framework\Option\ArrayInterface
{
    
    public function toOptionArray()
    {
        return [['value' => 'horizontal', 'label' => __('Horizontal')], ['value' => 'vertical', 'label' => __('Vertical')]];
    }

    
    public function toArray()
    {
        return ['horizontal' => __('Horizontal'), 'vertical' => __('Vertical')];
    }
}
