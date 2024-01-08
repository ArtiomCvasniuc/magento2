<?php
namespace Themevast\CustomMenu\Model\Attribute;

class Categorylabel extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    protected $_helper;
    
    public function __construct(
        \Themevast\CustomMenu\Helper\Data $helper
    ) {
        $this->_helper = $helper;
    }
    public function getAllOptions()
    {
        $label1 = $this->_helper->getConfig('custommenu/cat_labels/label1');
        $label2 = $this->_helper->getConfig('custommenu/cat_labels/label2');
        $label3 = $this->_helper->getConfig('custommenu/cat_labels/label3');
        
        if (!$this->_options) {
            $this->_options = [
                ['value' => '', 'label' => __('No Label')],
                ['value' => 'label1', 'label' => $label1],
                ['value' => 'label2', 'label' => $label2],
                ['value' => 'label3', 'label' => $label3]
            ];
        }
        
        return $this->_options;
    }
}