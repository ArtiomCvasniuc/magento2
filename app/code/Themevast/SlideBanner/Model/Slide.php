<?php
 
namespace Themevast\SlideBanner\Model;

class Slide extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Define resource model
     */
	const BASE_MEDIA_PATH = 'slidebanner';
    protected function _construct()
    {
        $this->_init('Themevast\SlideBanner\Model\Resource\Slide');
    }
}