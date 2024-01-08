<?php
 
namespace Themevast\SlideBanner\Model\Resource;
 
class Slider extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('themevast_slider', 'slider_id');
    }
	public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null)
    {
        if (!is_numeric($value) && $field === null) {
            $field = 'slider_identifier';
        }
        return parent::load($object, $value, $field);
    }
}