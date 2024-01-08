<?php
namespace Themevast\SlideBanner\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Setup\EavSetup;

class InstallData implements InstallDataInterface{
	
    private $eavSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $installer = $setup;

        $installer->startSetup();
        /**
         * Install eav entity types to the eav/entity_type table
         */
        $installer->getConnection()->dropTable($installer->getTable('themevast_slider'));
        $installer->getConnection()->dropTable($installer->getTable('themevast_slide'));
        $table = $installer->getConnection()
            ->newTable($installer->getTable('themevast_slider'))
            ->addColumn(
                'slider_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Timer ID'
            )
            ->addColumn(
                'slider_identifier',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Identifier'
            )
            ->addColumn(
                'slider_title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Slider title'
            )->addColumn(
                'slider_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                6,
                ['nullable' => false, 'default'=>1],
                'Status'
            )->addColumn(
                'slider_setting',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Setting slider'
            )->addColumn(
                'storeids',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => true],
                'Stores Ids'
            )->addColumn(
                'slider_styles',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Styles'
            )->addColumn(
                'slider_script',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Styles'
            )->addColumn(
                'slider_template',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Template'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Creation Time'
            )
			->addIndex(
				'slider_identifier',
				['slider_identifier']
			)
            ->setComment('SlideBanner');
        $installer->getConnection()
            ->createTable($table);
		$table = $installer->getConnection()
            ->newTable($installer->getTable('themevast_slide'))
            ->addColumn(
                'slide_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Slide ID'
            )
			->addColumn(
                'slider_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['nullable' => false],
                'Slider ID'
            )
			->addColumn(
                'slide_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                6,
                ['nullable' => false, 'default'=>1],
                'Slide Type'
            )
            ->addColumn(
                'slide_text',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Slide Text'
            )->addColumn(
                'slide_image',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'File Name'
            )->addColumn(
                'slide_link',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Link Slide'
            )->addColumn(
                'slide_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                6,
                ['nullable' => false, 'default' => '1'],
                'Timer status'
            )
			->addColumn(
                'slide_position',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['nullable' => false],
                'slide position'
            )
			->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Creation Time'
            )
            ->setComment('Slide item');
        $installer->getConnection()
            ->createTable($table);

        $installer->endSetup();
    }
}
