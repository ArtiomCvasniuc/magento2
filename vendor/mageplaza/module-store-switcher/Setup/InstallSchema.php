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
 * @package     Mageplaza_StoreSwitcher
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\StoreSwitcher\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class InstallSchema
 * @package Mageplaza\StoreSwitcher\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (!$installer->tableExists('mageplaza_storeswitcher_rules')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('mageplaza_storeswitcher_rules'))
                ->addColumn('rule_id', Table::TYPE_INTEGER, null, [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true
                ], 'Rule Id')
                ->addColumn('name', Table::TYPE_TEXT, 255, [], 'Rule Name')
                ->addColumn('status', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '0'], 'Status')
                ->addColumn(
                    'priority',
                    Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                    'Priority'
                )
                ->addColumn('countries', Table::TYPE_TEXT, '64k', [], 'Countries')
                ->addColumn('page_type', Table::TYPE_TEXT, 255, [], 'Page Type')
                ->addColumn('include_path', Table::TYPE_TEXT, '64k', [], 'Include Url')
                ->addColumn('exclude_path', Table::TYPE_TEXT, '64k', [], 'Exclude Url')
                ->addColumn('exclude_ips', Table::TYPE_TEXT, '64k', [], 'Exclude Ips')
                ->addColumn('redirect_type', Table::TYPE_TEXT, 255, [], 'Redirect Type')
                ->addColumn('change_type', Table::TYPE_TEXT, 255, [], 'Change Story View Type')
                ->addColumn('store_redirected', Table::TYPE_TEXT, 255, [], 'Redirect Store')
                ->addColumn('currency', Table::TYPE_TEXT, 255, [], 'Currency')
                ->addColumn('redirect_url', Table::TYPE_TEXT, 255, [], 'Redirect Url')
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Creation Time'
                )->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                    'Update Time'
                )->setComment('StoreSwitcher Rules');

            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
