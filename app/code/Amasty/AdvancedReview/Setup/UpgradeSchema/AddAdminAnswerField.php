<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Setup\UpgradeSchema;

use Magento\Framework\Setup\SchemaSetupInterface;

class AddAdminAnswerField
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $name = $setup->getTable('review');
        $setup->getConnection()->addColumn(
            $name,
            \Amasty\AdvancedReview\Helper\BlockHelper::ADMIN_ANSWER_ACCOUNT_ONLY,
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => true,
                'comment' => 'is admin answer visible on account area only'
            ]
        );
    }
}
