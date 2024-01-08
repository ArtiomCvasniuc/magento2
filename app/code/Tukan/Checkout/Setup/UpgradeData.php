<?php
namespace Tukan\Checkout\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

class UpgradeData implements UpgradeDataInterface
{

    private $eavSetupFactory;
    private $eavConfig;
    private $attributeSetFactory;

    public function __construct(
        Config $eavConfig,
        EavSetupFactory $eavSetupFactory,
        AttributeSetFactory $attributeSetFactory
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig            = $eavConfig;
        $this->attributeSetFactory  = $attributeSetFactory;
    }

    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();
        $this->addTaxNumberFieldToAddress($setup);
        $this->addCompanyRegistrationNumber($setup);
        $this->addCompanyBankName($setup);
        $this->addCompanyBankAccount($setup);
        $setup->endSetup();

    }

    protected function addTaxNumberFieldToAddress($setup) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute('customer_address', 'tax_number', [
            'default' => '',
            'type' => 'int',
            'input' => 'text',
            'label' => 'Tax Number',
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'system'=> false,
            'group'=> 'General',
            'sort_order' => 71,
            'global' => true,
            'visible_on_front' => true,
        ]);

        $customAttribute = $this->eavConfig->getAttribute('customer_address', 'tax_number');

        $customAttribute->setData(
            'used_in_forms',
            ['adminhtml_customer_address','customer_address_edit','customer_register_address'] 
        );
        $customAttribute->save();

        $installer = $setup;

        $installer->getConnection()->addColumn(
            $installer->getTable('quote_address'),
            'tax_number',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'comment' => 'Tax Number'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_address'),
            'tax_number',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'comment' => 'Tax Number'
            ]
        );
    }

    protected function addCompanyRegistrationNumber($setup) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute('customer_address', 'company_registration_number', [
            'default' => '',
            'type' => 'varchar',
            'input' => 'text',
            'label' => 'Company Registration Number',
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'system'=> false,
            'group'=> 'General',
            'sort_order' => 72,
            'global' => true,
            'visible_on_front' => true,
        ]);

        $customAttribute = $this->eavConfig->getAttribute('customer_address', 'company_registration_number');

        $customAttribute->setData(
            'used_in_forms',
            ['adminhtml_customer_address','customer_address_edit','customer_register_address'] 
        );
        $customAttribute->save();

        $installer = $setup;

        $installer->getConnection()->addColumn(
            $installer->getTable('quote_address'),
            'company_registration_number',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 
                'length' => 255,
                'comment' => 'Company Registration Number'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_address'),
            'company_registration_number',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 
                'length' => 255,
                'comment' => 'Company Registratio Number'
            ]
        );
    }

    protected function addCompanyBankName($setup) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute('customer_address', 'company_bank_name', [
            'default' => '',
            'type' => 'varchar',
            'input' => 'text',
            'label' => 'Company Bank Name',
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'system'=> false,
            'group'=> 'General',
            'sort_order' => 73,
            'global' => true,
            'visible_on_front' => true,
        ]);

        $customAttribute = $this->eavConfig->getAttribute('customer_address', 'company_bank_name');

        $customAttribute->setData(
            'used_in_forms',
            ['adminhtml_customer_address','customer_address_edit','customer_register_address'] 
        );
        $customAttribute->save();

        $installer = $setup;

        $installer->getConnection()->addColumn(
            $installer->getTable('quote_address'),
            'company_bank_name',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 
                'length' => 255,
                'comment' => 'Company Bank Name'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_address'),
            'company_bank_name',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 
                'length' => 255,
                'comment' => 'Company Bank Name'
            ]
        );
    }

    protected function addCompanyBankAccount($setup) {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute('customer_address', 'company_bank_account', [
            'default' => '',
            'type' => 'varchar',
            'input' => 'text',
            'label' => 'Company Bank Account',
            'visible' => true,
            'required' => false,
            'user_defined' => true,
            'system'=> false,
            'group'=> 'General',
            'sort_order' => 74,
            'global' => true,
            'visible_on_front' => true,
        ]);

        $customAttribute = $this->eavConfig->getAttribute('customer_address', 'company_bank_account');

        $customAttribute->setData(
            'used_in_forms',
            ['adminhtml_customer_address','customer_address_edit','customer_register_address'] 
        );
        $customAttribute->save();

        $installer = $setup;

        $installer->getConnection()->addColumn(
            $installer->getTable('quote_address'),
            'company_bank_account',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 
                'length' => 255,
                'comment' => 'Company Bank Account'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order_address'),
            'company_bank_account',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 
                'length' => 255,
                'comment' => 'Company Bank Account'
            ]
        );
    }
}
