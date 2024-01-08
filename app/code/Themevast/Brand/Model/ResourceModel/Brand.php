<?php
//ducdevphp@gmail.com
namespace Themevast\Brand\Model\ResourceModel;

class Brand extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct(){
    	 $this->_init('tv_brand','brand_id');
    }
	
	public function lookupStoreIds($brandId)
    {
        $adapter = $this->getConnection();

        $select = $adapter->select()->from(
            $this->getTable('tv_brand_store'),
            'store_id'
        )->where(
            'brand_id = ?',
            (int)$brandId
        );

        return $adapter->fetchCol($select);
    }
	
	 protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['brand_id = ?' => (int)$object->getId()];

        $this->getConnection()->delete($this->getTable('tv_brand_store'), $condition);
        $this->getConnection()->delete($this->getTable('tv_brand'), $condition);

        return parent::_beforeDelete($object);
    }

	
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();

        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }

        $table = $this->getTable('tv_brand_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = ['brand_id = ?' => (int)$object->getId(), 'store_id IN (?)' => $delete];

            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];

            foreach ($insert as $storeId) {
                $data[] = ['brand_id' => (int)$object->getId(), 'store_id' => (int)$storeId];
            }

            $this->getConnection()->insertMultiple($table, $data);
        }

        return parent::_afterSave($object);
    }
	
	 protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
        }

        return parent::_afterLoad($object);
    }
}