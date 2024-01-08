<?php
namespace Themevast\Blog\Model\Config\Source;

class CommetType implements \Magento\Framework\Option\ArrayInterface
{
    
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Disabled')],
            ['value' => 'facebook', 'label' => __('Use Facebook Comments')],
            ['value' => 'disqus', 'label' => __('Use Disqus Comments')],
        ];
    }

    public function toArray()
    {
        $array = [];
        foreach($this->toOptionArray() as $item) {
            $array[$item['value']] = $item['label'];
        }
        return $array;
    }
}
