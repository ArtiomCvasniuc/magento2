<?php
 
namespace Themevast\SlideBanner\Model\Resource\Slide;

use Magento\Framework\DB\Select;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	/**
     * @var string
     */
    protected $_idFieldName = 'slide_id';
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'Themevast\SlideBanner\Model\Slide','Themevast\SlideBanner\Model\Resource\Slide'
        );
    }
	public function joinSlider()
	{
		$this->getSelect()->joinLeft(['slider'=>$this->getTable('themevast_slider')], "main_table.slider_id = slider.slider_id", ['slider_title']);
		return $this;
	}
	
}