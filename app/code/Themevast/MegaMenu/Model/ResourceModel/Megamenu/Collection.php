<?php
namespace Themevast\MegaMenu\Model\ResourceModel\Megamenu;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected function _construct()
	{
		$this->_init('Themevast\MegaMenu\Model\Megamenu','Themevast\MegaMenu\Model\ResourceModel\Megamenu');
	}
	protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
		
	}
	
	
}
