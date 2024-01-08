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
 * @package     Mageplaza_PdfInvoice
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\PdfInvoice\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class InstallSchema
 * @package Mageplaza\PdfInvoice\Setup
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
        $connection = $installer->getConnection();

        if (!$installer->tableExists('mageplaza_pdfinvoice_template')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('mageplaza_pdfinvoice_template'))
                ->addColumn('template_id', Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true], 'Template Id')
                ->addColumn('name', Table::TYPE_TEXT, 255, ['nullable' => false], 'Template Name')
                ->addColumn('type', Table::TYPE_TEXT, 64, ['nullable' => false], 'Type')
                ->addColumn('store_ids', Table::TYPE_TEXT, 64, ['nullable' => false], 'Stores')
                ->addColumn('template_html', Table::TYPE_TEXT, '2M', [], 'Template Html')
                ->addColumn('template_styles', Table::TYPE_TEXT, '2M', [], 'Template Styles')
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT], 'Created At')
                ->addColumn('updated_at', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT], 'Update At')
                ->setComment('PDF Invoice template');

            $connection->createTable($table);
        }
        $installer->endSetup();
    }
}
