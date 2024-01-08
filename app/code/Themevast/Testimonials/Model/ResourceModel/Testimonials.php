<?php
//ducdevphp@gmail.com
namespace Themevast\Testimonials\Model\ResourceModel;

class Testimonials extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	 protected $_storeManager;
	 public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connectionName = null
        ) {
        parent::__construct($context, $connectionName);
        $this->_storeManager = $storeManager;
    }
	
    protected function _construct(){
    	 $this->_init('tv_testimonials','testimonial_id');
    }
	 protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $storeIds = [\Magento\Store\Model\Store::DEFAULT_STORE_ID, (int)$object->getStoreId()];
            $select->join(
                ['tv_testimonials_store' => $this->getTable('tv_testimonials_store')],
                $this->getMainTable() . '.testimonial_id = tv_testimonials_store.testimonial_id',
                []
                )->where(
                'is_active = ?',
                \Mageducdq\Producttab\Model\Config\Source\Status::APPROVE
                )->where(
                'tv_testimonials_store.store_id IN (?)',
                $storeIds
                )->order(
                'tv_testimonials_store.store_id DESC'
                )->limit(
                1
                );
            }

            return $select;
        }
	public function lookupStoreIds($brandId)
    {
        $adapter = $this->getConnection();

        $select = $adapter->select()->from(
            $this->getTable('tv_testimonials_store'),
            'store_id'
        )->where(
            'testimonial_id = ?',
            (int)$brandId
        );

        return $adapter->fetchCol($select);
    }
 	/* protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        var_dump($object->getData());die();
    }   */
	 protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['testimonial_id = ?' => (int)$object->getId()];

        $this->getConnection()->delete($this->getTable('tv_testimonials_store'), $condition);
        $this->getConnection()->delete($this->getTable('tv_testimonials'), $condition);

        return parent::_beforeDelete($object);
    }

	
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
       /*  $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();

        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }

        $table = $this->getTable('tv_testimonials_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = ['testimonial_id = ?' => (int)$object->getId(), 'store_id IN (?)' => $delete];

            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];

            foreach ($insert as $storeId) {
                $data[] = ['testimonial_id' => (int)$object->getId(), 'store_id' => (int)$storeId];
            }

            $this->getConnection()->insertMultiple($table, $data);
        } 
		 */
		 if($stores = $object->getStores()){
			$table = $this->getTable('tv_testimonials_store');
			$where = ['testimonial_id = ?' => (int)$object->getId()];
			$this->getConnection()->delete($table, $where);
			if ($stores) {
				$data = [];
				foreach ($stores as $storeId) {
					$data[] = ['testimonial_id' => (int)$object->getId(), 'store_id' => (int)$storeId];
				}
				try{
					$this->getConnection()->insertMultiple($table, $data);
				}catch(\Exception $e){
					die($e->getMessage());
				}
			}
		} 
        return parent::_afterSave($object);
    }
	
	 protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
			$object->setData('stores', $stores);
        }

        return parent::_afterLoad($object);
    }
	
	public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null)
    {
        if (!is_numeric($value) && $field === null) {
            $field = 'identifier';
        }

        return parent::load($object, $value, $field);
    }
}