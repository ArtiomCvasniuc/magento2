<?php

namespace Themevast\Blog\Block\Adminhtml\Category\Edit;


class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    
    protected function _construct()
    {
        parent::_construct();
        $this->setId('category_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Category Information'));
    }
}
