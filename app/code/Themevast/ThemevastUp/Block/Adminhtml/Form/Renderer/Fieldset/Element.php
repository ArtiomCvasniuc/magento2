<?php
namespace Themevast\ThemevastUp\Block\Adminhtml\Form\Renderer\Fieldset;

class Element extends \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element implements
    \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    
    protected $_element;

    protected $_template = 'field.phtml';

    public function getElement()
    {
        return $this->_element;
    }

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->_element = $element;
        return $this->toHtml();
    }

    public function getHintHtml()
    {
        $storeSwitcher = $this->_layout->getBlockSingleton('Magento\Backend\Block\Store\Switcher');
        return $storeSwitcher->getHintHtml();
    }
}
