<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Model\ResourceModel\Catalog\Product;

use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Store\Model\Store;
use MageWorx\SeoBase\Model\Source\CanonicalType;

/**
 * SEO Base resource product hreflang URLs
 */
class Hreflangs extends \MageWorx\SeoBase\Model\ResourceModel\Catalog\Product
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \MageWorx\SeoBase\Helper\StoreUrl
     */
    protected $helperStoreUrl;

    /**
     * @var \MageWorx\SeoBase\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\SeoBase\Model\ResourceModel\Catalog\Product\FlexibleCanonical
     */
    protected $flexibleCanonicalResource;

    /**
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \MageWorx\SeoBase\Helper\StoreUrl $helperStoreUrl
     * @param \MageWorx\SeoBase\Helper\Data $helperData
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageWorx\SeoBase\Helper\StoreUrl $helperStoreUrl,
        \MageWorx\SeoBase\Helper\Data $helperData,
        \MageWorx\SeoAll\Helper\LinkFieldResolver $linkFieldResolver,
        \MageWorx\SeoBase\Model\ResourceModel\Catalog\Product\FlexibleCanonical $flexibleCanonicalResource,
        $resourcePrefix = null
    ) {
        $this->storeManager              = $storeManager;
        $this->helperStoreUrl            = $helperStoreUrl;
        $this->helperData                = $helperData;
        $this->flexibleCanonicalResource = $flexibleCanonicalResource;
        parent::__construct($context, $productResource, $linkFieldResolver, $resourcePrefix);
    }

    /**
     * Retrieve array hreflang URLs:
     * [
     *      (int)itemId => [
     *          'identifier'   => (string)item URL identifier (URL key),
     *          'hreflangUrls' => [
     *              (int)storeId => (string)item store URL
     *          ]
     *       ]
     * ]
     *
     * @param array $storeIds
     * @param int|null $productId
     * @param int|null $categoryId
     * @return array
     */
    public function getHreflangsData($storeIds, $productId = null, $categoryId = null)
    {
        $linkField       = $this->linkFieldResolver->getLinkField(ProductInterface::class, 'entity_id');
        $attributeStatus = $this->getAttribute('status');

        $adapter = $this->getConnection();

        $this->select = $adapter->select()->from(
            ['e' => $this->getMainTable()],
            [$this->getIdFieldName()]
        )->joinInner(
            ['w' => $this->getTable('catalog_product_website')],
            'e.entity_id = w.product_id',
            []
        )->joinInner(
            ['store' => $this->getTable('store')],
            'w.website_id = store.website_id AND store.is_active = 1',
            []
        )->joinLeft(
            ['status_tab' => $attributeStatus['table']],
            $adapter->quoteInto('status_tab.attribute_id = ?', $attributeStatus['attribute_id'])
            . " AND e.$linkField = status_tab.$linkField AND store.store_id = status_tab.store_id",
            []
        )->joinLeft(
            ['status_tab_2' => $attributeStatus['table']],
            $adapter->quoteInto('status_tab_2.attribute_id = ?', $attributeStatus['attribute_id'])
            . " AND e.$linkField = status_tab_2.$linkField"
            . $adapter->quoteInto(' AND status_tab_2.store_id = ?', Store::DEFAULT_STORE_ID),
            []
        )->joinInner(
            ['url_rewrite' => $this->getTable('url_rewrite')],
            'e.entity_id = url_rewrite.entity_id AND url_rewrite.is_autogenerated = 1'
            . $adapter->quoteInto(' AND url_rewrite.store_id IN(?)', $storeIds)
            . $adapter->quoteInto(' AND url_rewrite.entity_type = ?', ProductUrlRewriteGenerator::ENTITY_TYPE)
            . ' AND store.store_id = url_rewrite.store_id',
            ['store_id', 'request_path']
        )->joinLeft(
            ['catalog_url_rewrite' => $this->getTable('catalog_url_rewrite_product_category')],
            'url_rewrite.url_rewrite_id = catalog_url_rewrite.url_rewrite_id',
            []
        )->where(
            'e.entity_id = ?',
            $productId
        )->where(
            'CASE WHEN status_tab.value IS NULL THEN status_tab_2.value = ? ELSE status_tab.value = ? END',
            Status::STATUS_ENABLED
        );

        foreach ($this->getStoreIdsGroupedByUrlTypes($storeIds) as $urlType => $urlTypeStoreIds) {
            $subQueryCondition = $this->getUrlRewriteSubQueryCondition($urlType, $urlTypeStoreIds, $productId);
            $subQuery          = $this->getUrlRewriteSubQuery($urlType, $urlTypeStoreIds, $productId);
            if ($subQueryCondition) {
                if (!isset($orConditionFlag)) {
                    $this->select->where($subQueryCondition, $subQuery);
                } else {
                    $this->select->orWhere($subQueryCondition, $subQuery);
                }
            }
            $orConditionFlag = true;
        }

        $query = $adapter->query($this->select);
        $rows  = $query->fetchAll();

        if (!is_array($rows)) {
            return false;
        }

        $data = [];
        foreach ($rows as $row) {
            if (array_key_exists($row['entity_id'], $data)) {
                $hreflangUrls = $data[$row['entity_id']]['hreflangUrls'];
            } else {
                $data[$row['entity_id']] = [];
                $hreflangUrls            = [];
            }

            $url = $this->helperStoreUrl->getUrl($row['request_path'], $row['store_id'], true);
            if (!in_array($url, $hreflangUrls)) {
                $hreflangUrls[$row['store_id']] = $url;
            }
            $data[$row['entity_id']] = ['requestPath' => $row['request_path'], 'hreflangUrls' => $hreflangUrls];
        }

        return $data;
    }


    /**
     * @param array $storeIds
     * @return array
     */
    protected function getStoreIdsGroupedByUrlTypes($storeIds)
    {
        $result = [];

        foreach ($storeIds as $storeId) {
            $urlType            = $this->helperData->getProductCanonicalUrlType($storeId);
            $result[$urlType][] = $storeId;
        }

        return $result;
    }

    /**
     * @param string $urlType
     * @param array $storeIds
     * @param string $productId
     * @return null|\Zend_Db_Expr
     */
    protected function getUrlRewriteSubQueryCondition($urlType, $storeIds, $productId)
    {
        if ($urlType !== CanonicalType::URL_TYPE_NO_CATEGORIES) {
            return new \Zend_Db_Expr(
                '`url_rewrite`.`store_id` IN (' . implode(',', $storeIds) . ') AND `url_rewrite`.`request_path` = ?'
            );
        }

        return new \Zend_Db_Expr(
            '`url_rewrite`.`entity_id` = ' . (int)$productId .
            ' AND `url_rewrite`.`store_id` IN (' . implode(',', $storeIds) . ') AND `url_rewrite`.`metadata` IS NULL'
        );
    }

    /**
     * @param string $urlType
     * @param array $storeIds
     * @param int $productId
     * @return \Magento\Framework\DB\Select|null
     */
    protected function getUrlRewriteSubQuery($urlType, $storeIds, $productId)
    {
        if (CanonicalType::URL_TYPE_NO_CATEGORIES === $urlType) {
            return null;
        }

        $select = $this->getConnection()
                       ->select()
                       ->from(
                           ['url_b' => $this->getTable('url_rewrite')],
                           ['url_b.request_path']
                       )
                       ->where('url_b.entity_type = ?', ProductUrlRewriteGenerator::ENTITY_TYPE)
                       ->where('url_b.entity_id = ?', $productId)
                       ->where('url_b.store_id = url_rewrite.store_id')
                       ->where('url_b.is_autogenerated = 1');

        $this->flexibleCanonicalResource->addFlexibleConditions($select, $storeIds, 'url_b');

        return $select;
    }
}
