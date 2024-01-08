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

namespace Mageplaza\SpecialPromotions\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $connection = $installer->getConnection();

        /** Update table 'salesrule' */
        $salesRuleTable = $installer->getTable('salesrule');
        if (!$connection->tableColumnExists($salesRuleTable, 'item_action')) {
            $connection->addColumn($salesRuleTable, 'item_action', [
                'type'     => Table::TYPE_TEXT,
                'length'   => 32,
                'nullable' => true,
                'comment'  => 'Additional Item Action'
            ]);
        }
        if (!$connection->tableColumnExists($salesRuleTable, 'maximum_discount_type')) {
            $connection->addColumn($salesRuleTable, 'maximum_discount_type', [
                'type'     => Table::TYPE_SMALLINT,
                'length'   => 5,
                'nullable' => true,
                'comment'  => 'Maximum Discount Type'
            ]);
        }
        if (!$connection->tableColumnExists($salesRuleTable, 'maximum_discount')) {
            $connection->addColumn($salesRuleTable, 'maximum_discount', [
                'type'     => Table::TYPE_DECIMAL,
                'length'   => '12,4',
                'nullable' => true,
                'comment'  => 'Maximum Discount Amount'
            ]);
        }
        if (!$connection->tableColumnExists($salesRuleTable, 'item_action_qty')) {
            $connection->addColumn($salesRuleTable, 'item_action_qty', [
                'type'     => Table::TYPE_INTEGER,
                'length'   => 10,
                'nullable' => true,
                'comment'  => 'Item Action Qty'
            ]);
        }
        if (!$connection->tableColumnExists($installer->getTable('quote_address'), 'discount_details')) {
            $connection->addColumn($installer->getTable('quote_address'), 'discount_details', [
                'type'     => Table::TYPE_TEXT,
                'length'   => '2M',
                'nullable' => true,
                'comment'  => 'Special Promotions Discount Details'
            ]);
        }

        $installer->endSetup();
    }
}
