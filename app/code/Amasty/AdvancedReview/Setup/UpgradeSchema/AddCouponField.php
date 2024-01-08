<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Setup\UpgradeSchema;

use Amasty\AdvancedReview\Model\ResourceModel\Reminder;
use Magento\Framework\Setup\SchemaSetupInterface;

class AddCouponField
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $name = $setup->getTable(Reminder::MAIN_TABLE);
        $setup->getConnection()->addColumn(
            $name,
            'coupon',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                'nullable' => true,
                'default' => 0,
                'comment' => 'is Coupon Sent'
            ]
        );
    }
}
