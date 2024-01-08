<?php
/*
 * custom themevast
 * customer ducdevphp@gmail.com 
 */ 
?>
<?php
namespace Themevast\Blog\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
   
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $setup->startSetup();

        $version = $context->getVersion();
        $connection = $setup->getConnection();

        if (version_compare($version, '2.0.1') < 0) {
			
            $connection->addColumn(
                $setup->getTable('themevast_blog_post'),
                'author_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Author ID',

                ]
            );

            $connection->addIndex(
                $setup->getTable('themevast_blog_post'),
                $setup->getIdxName($setup->getTable('themevast_blog_post'), ['author_id']),
                ['author_id']
            );

        }
		
        $setup->endSetup();
    }
}
