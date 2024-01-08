<?php
namespace Themevast\Blog\Block\Adminhtml;

class Post extends \Magento\Backend\Block\Widget\Grid\Container
{
    
    protected function _construct()
    {
        $this->_controller = 'adminhtml';
        $this->_blockGroup = 'Themevast_Blog';
        $this->_headerText = __('Post');
        $this->_addButtonLabel = __('Add New Post');
        parent::_construct();
    }
}
