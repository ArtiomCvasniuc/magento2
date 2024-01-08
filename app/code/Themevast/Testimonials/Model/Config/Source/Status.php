<?php
namespace Themevast\Testimonials\Model\Config\Source;

class Status implements \Magento\Framework\Option\ArrayInterface
{
	const STATUS_APPROVE = 'Approve';
	const STATUS_PENDING = 'Pending';
	const STATUS_DENY    ='Rejected';
	const APPROVE = 1;
	const PENDING = 2;
	const DENY=3;
	public static function getAvailableStatuses() {
		return [
			self::APPROVE => __('Approve'),
		    self::PENDING => __('Pending'),
		    self::DENY => __('Rejected'),
		];
	}
	public function toOptionArray()
	{
		return [
				['value'=>self::APPROVE,'label'=>__(self::STATUS_APPROVE)],
				['value'=>self::PENDING,'label'=>__(self::STATUS_PENDING)],
				['value'=>self::DENY,'label'=>__(self::STATUS_DENY)],
			];
	}
}
