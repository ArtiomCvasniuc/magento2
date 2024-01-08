<?php
namespace Themevast\Themevast\Helper;

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
	public function changeString($text, $length = 100, $finishtext = '...', $is_striped = true) {
    	$text = ($is_striped == true) ? strip_tags($text) : $text;
    	if (strlen($text) <= $length) {
    		return $text;
    	}
    	$text = substr($text, 0, $length);
    	$_space = strrpos($text, ' ');
    	return substr($text, 0, $_space) . $finishtext;
    }
}
