<?php
namespace Themevast\Blog\Model\ResourceModel;

class Post extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    
    protected $_date;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        $resourcePrefix = null
    ) {
        $this->_date = $date;
        parent::__construct($context, $resourcePrefix);
    }

    
    protected function _construct()
    {
        $this->_init('themevast_blog_post', 'post_id');
    }

    
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['post_id = ?' => (int)$object->getId()];

        $this->getConnection()->delete($this->getTable('themevast_blog_post_store'), $condition);
        $this->getConnection()->delete($this->getTable('themevast_blog_post_category'), $condition);
        $this->getConnection()->delete($this->getTable('themevast_blog_post_relatedproduct'), $condition);

        $this->getConnection()->delete($this->getTable('themevast_blog_post_relatedpost'), $condition);
        $this->getConnection()->delete($this->getTable('themevast_blog_post_relatedpost'),
            ['related_id = ?' => (int)$object->getId()]
        );

        return parent::_beforeDelete($object);
    }

    
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$this->isValidPageIdentifier($object)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The post URL key contains capital letters or disallowed symbols.')
            );
        }

        if ($this->isNumericPageIdentifier($object)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The post URL key cannot be made of only numbers.')
            );
        }

        $gmtDate = $this->_date->gmtDate();

        if ($object->isObjectNew() && !$object->getCreationTime()) {
            $object->setCreationTime($gmtDate);
        }

        if (!$object->getPublishTime()) {
            $object->setPublishTime($object->getCreationTime());
        }

        $object->setUpdateTime($gmtDate);

        return parent::_beforeSave($object);
    }

    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $oldIds = $this->lookupStoreIds($object->getId());
        $newIds = (array)$object->getStores();
        if (empty($newIds)) {
            $newIds = (array)$object->getStoreId();
        }
        $this->_updateLinks($object, $newIds, $oldIds, 'themevast_blog_post_store', 'store_id');



        $newIds = (array)$object->getCategories();
        if (is_array($newIds)) {
            $oldIds = $this->lookupCategoryIds($object->getId());
            $this->_updateLinks($object, $newIds, $oldIds, 'themevast_blog_post_category', 'category_id');
        }


        $newIds = $object->getRelatedPostIds();
        if (is_array($newIds)) {
            $oldIds = $this->lookupRelatedPostIds($object->getId());
            $this->_updateLinks($object, $newIds, $oldIds, 'themevast_blog_post_relatedpost', 'related_id');
        }


        $newIds = $object->getRelatedProductIds();
        if (is_array($newIds)) {
            $oldIds = $this->lookupRelatedProductIds($object->getId());
            $this->_updateLinks($object, $newIds, $oldIds, 'themevast_blog_post_relatedproduct', 'related_id');
        }


        return parent::_afterSave($object);
    }

    
    protected function _updateLinks(
        \Magento\Framework\Model\AbstractModel $object,
        Array $newRelatedIds,
        Array $oldRelatedIds,
        $tableName,
        $field
    ) {
        $table = $this->getTable($tableName);

        $insert = array_diff($newRelatedIds, $oldRelatedIds);
        $delete = array_diff($oldRelatedIds, $newRelatedIds);

        if ($delete) {
            $where = ['post_id = ?' => (int)$object->getId(), $field.' IN (?)' => $delete];

            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];

            foreach ($insert as $storeId) {
                $data[] = ['post_id' => (int)$object->getId(), $field => (int)$storeId];
            }

            $this->getConnection()->insertMultiple($table, $data);
        }
    }

   
    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null)
    {
        if (!is_numeric($value) && is_null($field)) {
            $field = 'identifier';
        }

        return parent::load($object, $value, $field);
    }

    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);

            $categories = $this->lookupCategoryIds($object->getId());
            $object->setCategories($categories);

            $relatedPosts = $this->lookupRelatedPostIds($object->getId());
            $object->setRelatedPostIds($relatedPosts);

            $relatedProducts = $this->lookupRelatedProductIds($object->getId());
            $object->setRelatedProductIds($relatedProducts);

        }

        return parent::_afterLoad($object);
    }

    protected function _getLoadByIdentifierSelect($identifier, $store, $isActive = null)
    {
        $select = $this->getConnection()->select()->from(
            ['cp' => $this->getMainTable()]
        )->join(
            ['cps' => $this->getTable('themevast_blog_post_store')],
            'cp.post_id = cps.post_id',
            []
        )->where(
            'cp.identifier = ?',
            $identifier
        )->where(
            'cps.store_id IN (?)',
            $store
        );

        if (!is_null($isActive)) {
            $select->where('cp.is_active = ?', $isActive)
                ->where('cp.publish_time <= ?', $this->_date->gmtDate());
        }
        return $select;
    }

    protected function isNumericPageIdentifier(\Magento\Framework\Model\AbstractModel $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('identifier'));
    }

    
    protected function isValidPageIdentifier(\Magento\Framework\Model\AbstractModel $object)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('identifier'));
    }

    
    public function checkIdentifier($identifier, $storeId)
    {
        $stores = [\Magento\Store\Model\Store::DEFAULT_STORE_ID, $storeId];
        $select = $this->_getLoadByIdentifierSelect($identifier, $stores, 1);
        $select->reset(\Zend_Db_Select::COLUMNS)->columns('cp.post_id')->order('cps.store_id DESC')->limit(1);

        return $this->getConnection()->fetchOne($select);
    }

    
    public function lookupStoreIds($postId)
    {
        return $this->_lookupIds($postId, 'themevast_blog_post_store', 'store_id');
    }

    
    public function lookupCategoryIds($postId)
    {
        return $this->_lookupIds($postId, 'themevast_blog_post_category', 'category_id');
    }

    
    public function lookupRelatedPostIds($postId)
    {
        return $this->_lookupIds($postId, 'themevast_blog_post_relatedpost', 'related_id');
    }
  
    public function lookupRelatedProductIds($postId)
    {
        return $this->_lookupIds($postId, 'themevast_blog_post_relatedproduct', 'related_id');
    }

    protected function _lookupIds($postId, $tableName, $field)
    {
        $adapter = $this->getConnection();

        $select = $adapter->select()->from(
            $this->getTable($tableName),
            $field
        )->where(
            'post_id = ?',
            (int)$postId
        );

        return $adapter->fetchCol($select);
    }

}
