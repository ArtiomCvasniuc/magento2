<?php
 
namespace Themevast\SlideBanner\Model;
 
class Slider extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Themevast\SlideBanner\Model\Resource\Slider');
    }
	public function getSliderSetting()
	{
		if(!$this->getData('slider_setting'))
			return $defaultSetting = array('items'=>1, 'itemsDesktop'=>'1', 'itemsDesktopSmall' => '1', 'itemsTablet' => '1', 'itemsMobile' => '1', 'slideSpeed' => 500, 'paginationSpeed' => 500, 'rewindSpeed'=>500);
		$data = $this->getData('slider_setting');
		$data = json_decode($data, true);
		return $data;
	}
	public function getSetting()
	{
		$data = $this->getData('slider_setting');
		return $data;
	}
}