<?php

namespace Themevast\Blog\Model\ResourceModel;

class Author extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
  
    protected function _construct()
    {
        $this->_init('admin_user', 'user_id');
    }

}
