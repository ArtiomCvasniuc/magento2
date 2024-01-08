<?php
/**
 * @Author: zerokool - Do Van Tuan
 * @Email: tien.uet.qh2011@gmail.com
 * @File Name: Data.php
 * @File Path: 
 * @Date:   2015-04-07 19:26:42
 * @Last Modified by:   zero
 * @Last Modified time: 2015-07-28 08:35:17
 */
namespace Themevast\BestsellerProduct\Helper;

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
