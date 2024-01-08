<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoReports\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $this->changeMetaTitleColumnToNullable($setup);
        }

        $setup->endSetup();
    }

    /**
     * Сhange the meta_title column to nullable
     *
     * @param SchemaSetupInterface $setup
     * @return void
     */
    private function changeMetaTitleColumnToNullable(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $connection->modifyColumn(
            $setup->getTable('mageworx_seoreports_product'),
            'meta_title',
            [
                'type'     => Table::TYPE_TEXT,
                'length'   => 1024,
                'nullable' => true,
                'comment'  => 'Product Meta Title'
            ]
        );
    }
}
