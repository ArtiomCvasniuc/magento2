<?php
namespace Themevast\Brand\Model\Config\Source;

class Status implements \Magento\Framework\Option\ArrayInterface
{
	const STATUS_ENABLED = 1;
	const STATUS_DISABLED = 0;
	const YES=1;
	const NO=2;
	public static function getAvailableStatuses() {
		return [
		   self::STATUS_DISABLED => __('Disabled'),
		   self::STATUS_ENABLED => __('Enabled')
		];
	}
	public function toOptionArray()
	{
		return [
				['value'=>self::YES,'label'=>__('Yes')],
				['value'=>self::NO,'label'=>__('No')],
			];
	}
}
