<?php
namespace Themevast\Testimonials\Model\ResourceModel\Testimonials;
use \Themevast\Testimonials\Model\ResourceModel\AbstractCollection;
class Collection extends AbstractCollection {
	
    protected $_idFieldName = 'testimonial_id';
	
	protected function _construct() {
		$this->_init('Themevast\Testimonials\Model\Testimonials', 'Themevast\Testimonials\Model\ResourceModel\Testimonials');
		/* $this->_map['fields']['testimonial_id'] = 'main_table.testimonial_id'; */
		$this->_map['fields']['store'] = 'store_table.store_id';
	}
	
	 public function setFirstStoreFlag($flag = false)
    {
        $this->_previewFlag = $flag;
        return $this;
    }

    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
        }
        return $this;
    }

 
    protected function _afterLoad()
    {
	//	var_dump('dasdasdasdd');die();
        $this->performAfterLoad('tv_testimonials_store', 'testimonial_id');
        //$this->_previewFlag = false;

        return parent::_afterLoad();
    }

    /**
     * Perform operations before rendering filters
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $this->joinStoreRelationTable('tv_testimonials_store', 'testimonial_id');
    }
	public function toOptionArray()
    {
        return $this->_toOptionArray('testimonial_id', 'name');
    }
	 public function addActiveFilter()
    {
        return $this
            ->addFieldToFilter('is_active', \Themevast\Testimonials\Model\Config\Source\Status::APPROVE);
    }
	 public function getTabByConfig($store,$cf)
    {
      if ($store instanceof \Magento\Store\Api\Data\StoreInterface) {
            $store = array($store->getId());
        }

        $this->getSelect()->join(
            array('store_table' => $this->getTable('tv_testimonials_store')),
            'main_table.testimonial_id = store_table.testimonial_id',
            array()
        )
        ->where('store_table.store_id in (?)', array(0, $store))
		->where('main_table.testimonial_id in (?)',explode(',',$cf))
		/* ->where('tab_status',\Mageducdq\Producttab\Model\Config\Source\Status::STATUS_ENABLED) */
		->order('position','ASC');
        return $this;
    }
}
