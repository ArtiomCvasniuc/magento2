<?php
namespace Themevast\PriceCountdown\Model\Config\Source;

class Heading implements \Magento\Framework\Option\ArrayInterface
{
    
	public function toOptionArray()
    {
        return [['value' => 'showall', 'label'=> __('Show in catalog/products pages')],
            ['value' => 'listpage', 'label'=> __('Show in catalog page')],
            ['value' => 'viewpage', 'label'=> __('Show in product page')],
            ['value' => 'hideall', 'label'=> __('Hide in all pages')]];
    }

   
    public function toArray()
    {
        return ['showall' => __('Show in catalog/products pages'), 'listpage' => __('Show in catalog page'), 'viewpage' => __('Show in product page'), 'hideall' => __('Hide in all pages')];
    }
}