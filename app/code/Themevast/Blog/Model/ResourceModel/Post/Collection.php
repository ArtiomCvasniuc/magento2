<?php
namespace Themevast\Blog\Model\ResourceModel\Post;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    
    protected $_storeManager;

    
    protected $_date;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);

        $this->_date = $date;
        $this->_storeManager = $storeManager;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->_init('Themevast\Blog\Model\Post', 'Themevast\Blog\Model\ResourceModel\Post');
        $this->_map['fields']['post_id'] = 'main_table.post_id';
        $this->_map['fields']['store'] = 'store_table.store_id';
        $this->_map['fields']['category'] = 'category_table.category_id';
    }
	
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'store_id') {
            return $this->addStoreFilter($condition, false);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            if ($store instanceof \Magento\Store\Model\Store) {
                $store = [$store->getId()];
            }

            if (!is_array($store)) {
                $store = [$store];
            }

            if ($withAdmin) {
                $store[] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
            }

            $this->addFilter('store', ['in' => $store], 'public');
        }
        return $this;
    }

    
    public function addCategoryFilter($category)
    {
        if (!$this->getFlag('category_filter_added')) {
            if ($category instanceof \Themevast\Blog\Model\Category) {
                $category = [$category->getId()];
            }

            if (!is_array($category)) {
                $category = [$category];
            }

            $this->addFilter('category', ['in' => $category], 'public');
        }
        return $this;
    }

    
    public function addActiveFilter()
    {
        return $this
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('publish_time', array('lteq' => $this->_date->gmtDate()));
    }

    
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(\Magento\Framework\DB\Select::GROUP);

        return $countSelect;
    }

    
    protected function _afterLoad()
    {
        $items = $this->getColumnValues('post_id');
        if (count($items)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(['cps' => $this->getTable('themevast_blog_post_store')])
                ->where('cps.post_id IN (?)', $items);
            $result = $connection->fetchPairs($select);

            if ($result) {
                foreach ($this as $item) {
                    $postId = $item->getData('post_id');
                    if (!isset($result[$postId])) {
                        continue;
                    }
                    if ($result[$postId] == 0) {
                        $stores = $this->_storeManager->getStores(false, true);
                        $storeId = current($stores)->getId();
                        $storeCode = key($stores);
                    } else {
                        $storeId = $result[$item->getData('post_id')];
                        $storeCode = $this->_storeManager->getStore($storeId)->getCode();
                    }
                    $item->setData('_first_store_id', $storeId);
                    $item->setData('store_code', $storeCode);
                    $item->setData('store_id', [$result[$postId]]);
                }
            }

            $select = $connection->select()->from(['cps' => $this->getTable('themevast_blog_post_category')])
                ->where('cps.post_id IN (?)', $items);
            $result = $connection->fetchAll($select);

            if ($result) {
                $categories = [];
                foreach($result as $item) {
                    $categories[$item['post_id']][] = $item['category_id'];
                }

                foreach ($this as $item) {
                    $postId = $item->getData('post_id');
                    if (isset($categories[$postId])) {
                        $item->setData('categories', $categories[$postId]);
                    }
                }

            }
        }

        $this->_previewFlag = false;
        return parent::_afterLoad();
    }

    protected function _renderFiltersBefore()
    {
        foreach(array('store', 'category') as $key) {
            if ($this->getFilter($key)) {
                $this->getSelect()->join(
                    [$key.'_table' => $this->getTable('themevast_blog_post_'.$key)],
                    'main_table.post_id = '.$key.'_table.post_id',
                    []
                )->group(
                    'main_table.post_id'
                );
            }
        }
        parent::_renderFiltersBefore();
    }

}
