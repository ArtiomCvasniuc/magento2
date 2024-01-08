<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


declare(strict_types=1);

namespace Amasty\AdvancedReview\Setup\Operation;

use Amasty\AdvancedReview\Model\ResourceModel\Reminder;
use Magento\Framework\Setup\SchemaSetupInterface;

class MoveTable
{
    /**
     * @param SchemaSetupInterface $setup
     *
     * @throws \Zend_Db_Statement_Exception
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $setup->startSetup();
        $tableName = $setup->getTable(Reminder::MAIN_TABLE);
        $salesConnection = $setup->getConnection('sales');

        if (!$salesConnection->isTableExists($tableName)) {
            $defaultConnection = $setup->getConnection();
            $salesConnection->query($defaultConnection->getCreateTable($tableName));
            $select = $defaultConnection->select()->from($tableName);
            $data = $defaultConnection->query($select)->fetchAll();

            if (count($data)) {
                $columns = array_keys($data[0]);
                $salesConnection->insertArray($tableName, $columns, $data);
            }

            $defaultConnection->dropTable($tableName);
        }

        $setup->endSetup();
    }
}
