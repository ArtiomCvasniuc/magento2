<?php
namespace Themevast\ProductTab\Model\Config\Source;
class  Type implements \Magento\Framework\Option\ArrayInterface
{
	const LATEST = 'latest';
    const LATEST_LABEL = 'Latest';
	const NEWARRIVAL = 'new_arrival';
    const NEWARRIVAL_LABEL = 'New Arrivals';
	const SPECIAL = 'special';
    const SPECIAL_LABEL = 'On sale';
	const MOSTPOPULAR = 'most_popular';
    const MOSTPOPULAR_LABEL = 'Most Popular';
	const BESTSELLER = 'best_seller';
    const BESTSELLER_LABEL = 'Best Seller';
	const TOPRATED = 'top_rated';
    const TOPRATED_LABEL = 'Top Rated';
	const RANDOM = 'random';
    const RANDOM_LABEL = 'Random';
	const FEATURED = 'featured';
    const FEATURED_LABEL = 'Featured';
	const DEALS = 'deals';
    const DEALS_LABEL = 'Deals';
    public function toOptionArray()
    {
        $sources = [];
        $sources[] = ['value' => self::LATEST, 'label' => __(self::LATEST_LABEL)];
        $sources[] = ['value' => self::NEWARRIVAL, 'label' => __(self::NEWARRIVAL_LABEL)];
        $sources[] = ['value' => self::SPECIAL, 'label' => __(self::SPECIAL_LABEL)];
        $sources[] = ['value' => self::MOSTPOPULAR, 'label' => __(self::MOSTPOPULAR_LABEL)];
        $sources[] = ['value' => self::BESTSELLER, 'label' => __(self::BESTSELLER_LABEL)];
        $sources[] = ['value' => self::TOPRATED, 'label' => __(self::TOPRATED_LABEL)];
        $sources[] = ['value' => self::RANDOM, 'label' => __(self::RANDOM_LABEL)];
        $sources[] = ['value' => self::FEATURED, 'label' => __(self::FEATURED_LABEL)];
        $sources[] = ['value' => self::DEALS, 'label' => __(self::DEALS_LABEL)];
        return $sources;
    }
}
