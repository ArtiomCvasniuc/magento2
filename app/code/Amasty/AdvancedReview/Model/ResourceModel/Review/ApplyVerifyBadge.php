<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


declare(strict_types=1);

namespace Amasty\AdvancedReview\Model\ResourceModel\Review;

use Amasty\AdvancedReview\Api\Data\CommentInterface;
use Magento\Framework\Config\ConfigOptionsListConstants as Constants;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Model\Order\Item;
use Magento\Framework\DB\Adapter\AdapterInterface;

class ApplyVerifyBadge extends \Magento\Review\Model\ResourceModel\Review\Collection
{
    /**
     * @param ModuleDataSetupInterface $setup
     */
    public function execute(ModuleDataSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $salesConnection = $setup->getConnection('sales');

        if ($connection->getConfig()[Constants::KEY_NAME] == $salesConnection->getConfig()[Constants::KEY_NAME]) {
            $this->getSingleDbData($connection);
        } else {
            $this->getSplitDbData($connection, $salesConnection);
        }
    }

    /**
     * @param AdapterInterface $connection
     */
    private function getSingleDbData(AdapterInterface $connection)
    {
        //do not work for grouped products
        $select = $this->getSelect()
            ->reset(\Magento\Framework\DB\Select::COLUMNS)
            ->columns(['main_table.review_id'])
            ->join(
                ['sales_order_item' => $this->getTable('sales_order_item')],
                'main_table.entity_pk_value = sales_order_item.product_id',
                []
            )->join(
                ['sales_order' => $this->getTable('sales_order')],
                'sales_order.entity_id = sales_order_item.order_id'
                . ' AND detail.customer_id=sales_order.customer_id AND sales_order.created_at < main_table.created_at',
                []
            )->group('main_table.review_id');

        $data = $connection->fetchAll($select);
        if (!empty($data)) {
            foreach ($data as &$item) {
                $item['verified_buyer'] = 1;
            }

            $connection->insertOnDuplicate(
                $this->getMainTable(),
                $data,
                ['review_id', 'verified_buyer']
            );
        }
    }

    /**
     * @param AdapterInterface $connection
     * @param AdapterInterface $salesConnection
     */
    private function getSplitDbData(AdapterInterface $connection, AdapterInterface $salesConnection)
    {
        $insertData = [];

        $selectOrderItems = $salesConnection->select()->from(
            ['sales_order_item' => $this->getTable('sales_order_item')],
            ['sales_order_item.product_id']
        )->join(
            ['sales_order' => $this->getTable('sales_order')],
            'sales_order.entity_id = sales_order_item.order_id',
            ['sales_order.customer_id', 'sales_order.created_at']
        );
        $salesOrderItems = $this->convertOrderData($salesConnection->fetchAll($selectOrderItems));

        foreach (array_keys($salesOrderItems) as $customerId) {
            $productIds = array_column($salesOrderItems[$customerId], Item::PRODUCT_ID);
            //do not work for grouped products
            $select = $this->getSelect()
                ->reset(\Magento\Framework\DB\Select::COLUMNS)
                ->columns(
                    [
                        'main_table.entity_pk_value',
                        'main_table.review_id',
                        'detail.customer_id',
                        'main_table.created_at'
                    ]
                )->where('main_table.entity_pk_value IN (?)', $productIds)
                ->where('detail.customer_id = (?)', $customerId)
                ->group('main_table.review_id');

            $data = $connection->fetchAll($select);
            if (!empty($data)) {
                foreach ($data as $item) {
                    $item['verified_buyer'] = $this->verifyBuyer($salesOrderItems[$customerId], $item);
                    unset($item[CommentInterface::CUSTOMER_ID]);
                    unset($item[Item::CREATED_AT]);
                    $insertData[] = $item;
                }
            }
        }

        if (!empty($insertData)) {
            $connection->insertOnDuplicate(
                $this->getMainTable(),
                $insertData,
                ['review_id', 'verified_buyer']
            );
        }
    }

    /**
     * @param array $orderData
     * @return array
     */
    private function convertOrderData(array $orderData): array
    {
        foreach ($orderData as $orderItem) {
            $customerId = $orderItem['customer_id'];
            unset($orderItem['customer_id']);
            $convertedData[$customerId][] = $orderItem;
        }

        return $convertedData ?? [];
    }

    /**
     * @param array $salesOrderItems
     * @param array $item
     * @return int
     */
    private function verifyBuyer(array $salesOrderItems, array $item): int
    {
        foreach ($salesOrderItems as $salesOrderItem) {
            if ($salesOrderItem[Item::PRODUCT_ID] == $item['entity_pk_value']
                && $salesOrderItem[Item::CREATED_AT] < $item[Item::CREATED_AT]
            ) {
                return 1;
            }
        }

        return 0;
    }
}
