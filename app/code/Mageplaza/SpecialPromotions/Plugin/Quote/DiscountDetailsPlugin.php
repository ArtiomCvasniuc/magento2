<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_SpecialPromotions
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\SpecialPromotions\Plugin\Quote;

use Magento\Quote\Api\Data\TotalSegmentExtensionFactory;
use Magento\Quote\Model\Cart\TotalsConverter;
use Magento\Tax\Api\Data\GrandTotalDetailsInterfaceFactory;
use Mageplaza\SpecialPromotions\Api\Data\DiscountDetailsInterfaceFactory;
use Mageplaza\SpecialPromotions\Api\Data\DiscountDetailsItemInterfaceFactory;

/**
 * Class DiscountDetailsPlugin
 * @package Mageplaza\SpecialPromotions\Plugin\Quote
 */
class DiscountDetailsPlugin
{
    /**
     * @var GrandTotalDetailsInterfaceFactory
     */
    private $detailsFactory;

    /**
     * @var DiscountDetailsItemInterfaceFactory
     */
    private $itemDetailsFactory;

    /**
     * @var TotalSegmentExtensionFactory
     */
    private $totalSegmentExtensionFactory;

    /**
     * @var string
     */
    private $code;

    /**
     * DiscountDetailsPlugin constructor.
     *
     * @param DiscountDetailsInterfaceFactory $detailsFactory
     * @param DiscountDetailsItemInterfaceFactory $itemDetailsFactory
     * @param TotalSegmentExtensionFactory $totalSegmentExtensionFactory
     */
    public function __construct(
        DiscountDetailsInterfaceFactory $detailsFactory,
        DiscountDetailsItemInterfaceFactory $itemDetailsFactory,
        TotalSegmentExtensionFactory $totalSegmentExtensionFactory
    ) {
        $this->detailsFactory = $detailsFactory;
        $this->itemDetailsFactory = $itemDetailsFactory;
        $this->totalSegmentExtensionFactory = $totalSegmentExtensionFactory;
        $this->code = TotalsReader::COLLECTOR_TYPE_CODE;
    }

    /**
     * @param TotalsConverter $subject
     * @param array $totalSegments
     * @param array $addressTotals
     *
     * @return array
     */
    public function afterProcess(
        TotalsConverter $subject,
        array $totalSegments,
        array $addressTotals = []
    ) {
        if (!array_key_exists($this->code, $addressTotals)) {
            return $totalSegments;
        }

        $fullInfo = $addressTotals[$this->code]->getFullInfo();
        if (!$fullInfo) {
            return $totalSegments;
        }

        $finalData = [];
        foreach ($fullInfo as $info) {
            $discountDetails = $this->detailsFactory->create([]);
            $discountDetails->setAmount($info['discount'] ?? 0);
            $discountDetails->setTitle($info['label'] ?? __('Undefine rule'));
            $discountDetails->setRuleId($info['rule_id'] ?? 0);

            $discountItems = [];
            $items = $info['items'] ?? [];
            foreach ($items as $item) {
                if (!isset($item['item_id'])) {
                    continue;
                }
                $discountItemDetails = $this->itemDetailsFactory->create([]);
                $discountItemDetails->setItemId($item['item_id']);
                $discountItemDetails->setAmount($item['discount'] ?? 0);
                $discountItemDetails->setQty((int) ($item['qty'] ?? 0));

                $discountItems[] = $discountItemDetails;
            }
            $discountDetails->setItems($discountItems);

            $finalData[] = $discountDetails;
        }

        $attributes = $totalSegments[$this->code]->getExtensionAttributes();
        if ($attributes === null) {
            $attributes = $this->totalSegmentExtensionFactory->create();
        }
        $attributes->setDiscountDetails($finalData);
        $totalSegments[$this->code]->setExtensionAttributes($attributes);

        return $totalSegments;
    }
}
