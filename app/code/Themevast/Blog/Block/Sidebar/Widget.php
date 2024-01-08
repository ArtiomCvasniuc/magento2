<?php
namespace Themevast\Blog\Block\Sidebar;

trait Widget
{
    
    public function getSortOrder()
    {
        if (!$this->hasData('sort_order')) {
            $this->setData('sort_order', $this->_scopeConfig->getValue(
                'tvblog/sidebar/'.$this->_widgetKey.'/sort_order', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ));
        }
        return (int) $this->getData('sort_order');
    }

    protected function _toHtml()
    {
        if ($this->_scopeConfig->getValue(
            'tvblog/sidebar/'.$this->_widgetKey.'/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )) {
            return parent::_toHtml();
        }

        return '';
    }
}
