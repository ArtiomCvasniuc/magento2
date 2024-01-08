<?php
namespace Themevast\Testimonials\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface {
	
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$installer = $setup;

		$installer->startSetup();

		$installer->getConnection()->dropTable($installer->getTable('tv_testimonials'));
	    $installer->getConnection()->dropTable($installer->getTable('tv_testimonials_store'));
		$table = $installer->getConnection()->newTable(
			$installer->getTable('tv_testimonials')
		)->addColumn('testimonial_id',\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,null,['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],'Testimonials ID')
		->addColumn('name',\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,null,['nullable' => false, 'default' => null],'Name')
		->addColumn('job', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Testimonial Job')
		->addColumn('email', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => false], 'Testimonial Email')
		->addColumn('avatar', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable' => true], 'Testimonial Avatar')
		->addColumn('website', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Testimonial Website')
		->addColumn('company', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Testimonial Company')
		->addColumn('address', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [], 'Testimonial Address')
		->addColumn('testimonial', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', [], 'Testimonial Testimonial')
		->addColumn('rating', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, [], 'Testimonial Rating')
		->addColumn('created_time', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default'=> \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT], 'Testimonial Creation Time')
		->addColumn('updated_time', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default'   => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE], 'Testimonial Update Time')
		->addColumn('is_active', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '2'], 'Is Testimonial Pending?')
		->addColumn('position',\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,null,['nullable' => true, 'default' => '0'],'Position')
		->setComment('Testimonials');
		
		$installer->getConnection()->createTable($table);
		
		$table = $installer->getConnection()->newTable(
                $installer->getTable('tv_testimonials_store')
            )
            ->addColumn(
                'testimonial_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
				['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Testimonial Id'
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
                'tv_testimonials_store',
                'testimonial_id',
                'tv_testimonials',
                'testimonial_id'
            ),
            'testimonial_id',
            $installer->getTable('tv_testimonials'),
            'testimonial_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
			)
			->addForeignKey(
            $installer->getFkName(
                'tv_testimonials_store',
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
			'Testimonial Store'
		);
		$installer->getConnection()->createTable($table);

		$installer->endSetup();

	}
}
