<?php
 
namespace Themevast\Brand\Model;

class Brand extends \Magento\Framework\Model\AbstractModel
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
	const BASE_MEDIA_PATH = 'themevast/brand';
	
    protected $_storeViewId = null;

	protected $_brandFactory;

	protected $_formFieldHtmlIdPrefix = 'page_';

	protected $_storeManager;
	protected function _construct()
    {
        $this->_init('Themevast\Brand\Model\ResourceModel\brand');
    }

	public function getFormFieldHtmlIdPrefix() {
		return $this->_formFieldHtmlIdPrefix;
	}

	 public function getAvailableStatuses()
    {
        return [self::STATUS_DISABLED => __('Disabled'), self::STATUS_ENABLED => __('Enabled')];
    }
}