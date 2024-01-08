<?php
 
namespace Themevast\SlideBanner\Model\Resource\Slider;

use Magento\Framework\DB\Select;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	/**
     * @var string
     */
    protected $_idFieldName = 'slider_id';
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'Themevast\SlideBanner\Model\Slider','Themevast\SlideBanner\Model\Resource\Slider'
        );
    }
}