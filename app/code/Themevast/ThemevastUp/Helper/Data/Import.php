<?php
namespace Themevast\ThemevastUp\Helper\Data;
use Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\Module\Dir;

class Import extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $_tvlData;

	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\App\ResourceConnection $resource,
		\Themevast\ThemevastUp\Helper\Data $tvlData
		) {
		parent::__construct($context);
		$this->_resource = $resource;
		$this->_tvlData = $tvlData;
	}

	public function buildQueryImportData($data = array(), $table_name = "", $override = true, $store_id = 0, $where = '') {
		$query = false;
		$binds = array();
		if($data) {
			$table_name = $this->_resource->getTableName($table_name);
			if($override) {
				$query = "REPLACE INTO `".$table_name."` ";
			} else {
				$query = "INSERT IGNORE INTO `".$table_name."` ";
			}
			$stores = $this->_tvlData->getAllStores();
			$fields = $values = array();
			foreach($data as $key=>$val) {
				if($val) {
					if($key == "store_id" && !in_array($val, $stores)){
						$val = $store_id;
					}
					$fields[] = "`".$key."`";
					$values[] = ":".strtolower($key);
					$binds[strtolower($key)] = $val;
				}
			}
			$query .= " (".implode(",", $fields).") VALUES (".implode(",", $values).")";
		}
		return array($query, $binds);
	}
}