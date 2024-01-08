<?php
namespace Magento\Cms\Model\Page\Source;

/**
 * Custom layout source
 */
class CustomLayout extends PageLayout
{
    
    public function toOptionArray()
    {
        return array_merge([['label' => 'Default', 'value' => '']], parent::toOptionArray());
    }
}
