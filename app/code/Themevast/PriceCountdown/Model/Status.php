<?php
namespace Themevast\PriceCountdown\Model;

class Status {
	const STATUS_ENABLED = 1;
	const STATUS_DISABLED = 2;

	
	public static function getAvailableStatuses() {
		return [
			self::STATUS_ENABLED => __('Enabled')
			, self::STATUS_DISABLED => __('Disabled'),
		];
	}
}
