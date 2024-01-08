<?php

namespace Themevast\Blog\Model\ResourceModel\Author;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Themevast\Blog\Model\Author', 'Themevast\Blog\Model\ResourceModel\Author');
    }
}
