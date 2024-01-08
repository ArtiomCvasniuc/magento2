<?php
namespace Themevast\Brand\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface {
	
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$installer = $setup;

		$installer->startSetup();

		$installer->getConnection()->dropTable($installer->getTable('tv_brand'));
	    $installer->getConnection()->dropTable($installer->getTable('tv_brand_store'));
		$table = $installer->getConnection()->newTable(
			$installer->getTable('tv_brand')
		)->addColumn(
			'brand_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			null,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Brand ID'
		)->addColumn(
			'title',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			null,
			['nullable' => false, 'default' => ''],
			'Title'
		)->addColumn(
			'store_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => false, 'default' => ''],
			'Store Id'
		)->addColumn(
			'status',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			10,
			['nullable' => false, 'default' => '1'],
			'Brand Status'
		)->addColumn(
			'link',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			null,
			['nullable' => true, 'default' => ''],
			'Link'
		)->addColumn(
			'image',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			null,
			['nullable' => true],
			'Brand image'
		)->addColumn(
			'description',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			null,
			['nullable' => true],
			'Description'
		)->addColumn(
			'position',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			null,
			['unsigned' => true, 'nullable' => false, 'default' => '0'],
			'Position'
		);
		
		$installer->getConnection()->createTable($table);
		
		$table = $installer->getConnection()->newTable(
                $installer->getTable('tv_brand_store')
            )
            ->addColumn(
                'brand_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
				['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Brand Id'
            )
             ->addColumn(
                'store_id',
               \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
               ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
             )
			->addForeignKey(
            $installer->getFkName(
                'tv_brand_store',
                'brand_id',
                'tv_brand',
                'brand_id'
            ),
            'brand_id',
            $installer->getTable('tv_brand'),
            'brand_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
			)
			->addForeignKey(
            $installer->getFkName(
                'tv_brand_store',
                'store_id',
                'store',
                'store_id'
            ),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
			)
		->setComment(
			'Brand Store'
		);
		$installer->getConnection()->createTable($table);

		$installer->endSetup();

	}
}
