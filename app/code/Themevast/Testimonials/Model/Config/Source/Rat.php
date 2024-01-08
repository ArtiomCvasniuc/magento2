<?php
namespace Themevast\Testimonials\Model\Config\Source;

class Rat implements \Magento\Framework\Option\ArrayInterface
{
	const STAR1 = 1;
	const STAR1_LABEL ="1 Star";
	const STAR2 = 2;
	const STAR2_LABEL ="2 Star";
	const STAR3 = 3;
	const STAR3_LABEL ="3 Star";
	const STAR4 = 4;
	const STAR4_LABEL ="4 Star";
	const STAR5 = 5;
	const STAR5_LABEL ="5 Star";	
	public function toOptionArray()
	{
		return [
			['value' => self::STAR1, 'label' => __(self::STAR1_LABEL)],
			['value' => self::STAR2, 'label' => __(self::STAR2_LABEL)],
			['value' => self::STAR3, 'label' => __(self::STAR3_LABEL)],
			['value' => self::STAR4, 'label' => __(self::STAR4_LABEL)],
			['value' => self::STAR5, 'label' => __(self::STAR5_LABEL)],
		];
	}
	public function getStarArray()
	{
		return [
			 self::STAR5 => __(self::STAR5_LABEL),
			 self::STAR4 => __(self::STAR4_LABEL),
			 self::STAR3 => __(self::STAR3_LABEL),
			 self::STAR2 => __(self::STAR2_LABEL),
			 self::STAR1 => __(self::STAR1_LABEL)
		];
	}
}