<?php
namespace Themevast\QuickView\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

	public function __construct(
		\Magento\Framework\App\Helper\Context $context
	) {
		parent::__construct($context);
	}
	public function getConfigData($path)
	{
		$value = $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		return $value;
	}
}
