<?php
 
namespace Themevast\SlideBanner\Model\Resource;
 
class Slide extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('themevast_slide', 'slide_id');
    }
}