<?php
namespace Themevast\ThemevastUp\Helper\Data;
use Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\Module\Dir;

class Export extends \Magento\Framework\App\Helper\AbstractHelper
{
	
	protected $parser;
	
	protected $_storeManager;

	protected $_moduleDir;
	
	protected $_tvTable;

	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\App\ResourceConnection $resource,
		\Themevast\ThemevastUp\Model\Modules\Tv\Table $tvTable,
		Dir $moduleDir
		) {
		parent::__construct($context);
		$this->_moduleDir = $moduleDir;
		$this->parser = new \Magento\Framework\Xml\Parser();
		$this->_storeManager = $storeManager;
		$this->_resource = $resource;
		$this->_tvTable = $tvTable;
	}

	public function exportModules($data){
		$themevastTables = $this->_tvTable->getTvTables();
		$configs = [];
		if(!empty($data['modules'])){
			$store = $this->_storeManager->getStore($data['store_id']);
			foreach ($data['modules'] as $k => $v) {
				if(isset($themevastTables[$v])){
					$tables = $themevastTables[$v];
				}
				$systemFileDir = $this->_moduleDir->getDir($v,Dir::MODULE_ETC_DIR). DIRECTORY_SEPARATOR . 'adminhtml' . DIRECTORY_SEPARATOR . 'system.xml';

				if(file_exists($systemFileDir)){

					$systemConfigs = $this->parser->load($systemFileDir)->xmlToArray();
					if($systemConfigs['config']['_value']['system']['section']){
						foreach ($systemConfigs['config']['_value']['system']['section'] as $_section) {
							$groups = [];
							if(isset($_section['_value']['group'])){
								$groups = $_section['_value']['group'];
							}elseif(isset($_section['group'])){
								$groups = $_section['group'];
							}

							$_sectionId = '';
							if(isset($_section['_attribute']['id'])){
								$_sectionId = $_section['_attribute']['id'];
							}elseif(isset($systemConfigs['config']['_value']['system']['section']['_attribute']['id'])){
								$_sectionId = $systemConfigs['config']['_value']['system']['section']['_attribute']['id'];
							}

							if(empty($groups)) continue;
							foreach ($groups as $_group) {
								if(!isset($_group['_value']['field'])) continue;
								foreach ($_group['_value']['field'] as $_field) {
									if(isset($_sectionId) && isset($_group['_attribute']['id']) && isset($_field['_attribute']['id'])){
										$key = $_sectionId . '/' . $_group['_attribute']['id'] . '/' . $_field['_attribute']['id'];
										$result = $this->scopeConfig->getValue(
											$key,
											\Magento\Store\Model\ScopeInterface::SCOPE_STORE,
											$store);
										if($result=='') continue;
										$configs[$v]['system_configs'][] = [
										'key' => $key,
										'value' => $result
										];
									}
								}
							}
						}
					}
				}
				if(isset($themevastTables[$v]) && is_array($themevastTables[$v])){
					foreach ($themevastTables[$v] as $key => $tableName) {
						$connection = $this->_resource->getConnection();
						$select = 'SELECT * FROM ' . $this->_resource->getTableName($tableName);
						$rows = $connection->fetchAll($select);
						$configs[$v]['tables'][$tableName] = $rows;
					}
				}
			}
		}
		return $configs;
	}

	public function exportCmsPages($data){
		$configs = [];
		if(!empty($data['cmspages'])){
			$pageIds = implode(',', $data['cmspages']);
			$themevastTables = $this->_tvTable->getTvTables();
			if(isset($themevastTables["Magento_Cms_Page"])){
				foreach ($themevastTables["Magento_Cms_Page"] as $k => $tableName) {
					$connection = $this->_resource->getConnection();
					$select = 'SELECT * FROM ' . $this->_resource->getTableName($tableName) . ' WHERE page_id IN (' . $pageIds . ') ';
					$rows = $connection->fetchAll($select);
					$configs['Magento_Cms_Page']['tables'][$tableName] = $rows;
				}
			}
		}
		return $configs;
	}

	public function exportStaticBlocks($data){
		$configs = [];
		if(!empty($data['cmsblocks'])){
			$blockIds = implode(',', $data['cmsblocks']);
			$themevastTables = $this->_tvTable->getTvTables();
			if(isset($themevastTables["Magento_Cms_Block"])){
				foreach ($themevastTables["Magento_Cms_Block"] as $k => $tableName) {
					$connection = $this->_resource->getConnection();
					$select = 'SELECT * FROM ' . $this->_resource->getTableName($tableName) . ' WHERE block_id IN (' . $blockIds . ') ';
					$rows = $connection->fetchAll($select);
					$configs['Magento_Cms_Block']['tables'][$tableName] = $rows;
				}
			}
		}
		return $configs;
	}

	public function exportWidgets($data){
		$configs = [];
		if(!empty($data['widgets'])){
			$themevastTables = $this->_tvTable->getTvTables();
			if(isset($themevastTables["Magento_Widget"])){
				
				$connection = $this->_resource->getConnection();
				$select = 'SELECT * FROM ' . $this->_resource->getTableName('widget_instance') . ' WHERE instance_id IN (' .  implode(',', $data['widgets']) . ') ';
				$rows = '';
				$configs['Magento_Widget']['tables']['widget_instance'] = $connection->fetchAll($select);
				$widgetIds = [];
				foreach ($configs['Magento_Widget']['tables']['widget_instance'] as $k => $v) {
					$widgetIds[] = $v['instance_id'];
				}

				if(!empty($widgetIds)){
					$connection = $this->_resource->getConnection();
					$select = 'SELECT * FROM ' . $this->_resource->getTableName('widget_instance_page') . ' WHERE instance_id IN (' .  implode(',', $widgetIds) . ') ';
					$rows = '';
					$configs['Magento_Widget']['tables']['widget_instance_page'] = $connection->fetchAll($select);
					$widgetPageIds = [];
					foreach ($configs['Magento_Widget']['tables']['widget_instance_page'] as $k => $v) {
						$widgetPageIds[] = $v['page_id'];
					}
				}

				$widgetInstancePageLayoutIds = [];
				if(!empty($widgetPageIds)){
					$connection = $this->_resource->getConnection();
					$select = 'SELECT * FROM ' . $this->_resource->getTableName('widget_instance_page_layout') . ' WHERE page_id IN (' . implode(',', $widgetPageIds) . ') ';
					$rows = '';
					$configs['Magento_Widget']['tables']['widget_instance_page_layout'] = $connection->fetchAll($select);
					foreach ($configs['Magento_Widget']['tables']['widget_instance_page_layout'] as $k => $v) {
						$widgetInstancePageLayoutIds[] = $v['layout_update_id'];
					}
				}

				$widgetLayoutUpdateId = [];
				if(!empty($widgetInstancePageLayoutIds)){
					$connection = $this->_resource->getConnection();
					$select = 'SELECT * FROM ' . $this->_resource->getTableName('layout_link') . ' WHERE layout_link_id IN (' .  implode(',', $widgetInstancePageLayoutIds) . ') ';
					$rows = '';
					$configs['Magento_Widget']['tables']['layout_link'] = $connection->fetchAll($select);
					$widgetInstancePageLayoutIds = [];
					foreach ($configs['Magento_Widget']['tables']['layout_link'] as $k => $v) {
						$widgetLayoutUpdateId[] = $v['layout_update_id'];
					}
				}

				if(!empty($widgetLayoutUpdateId)){
					$connection = $this->_resource->getConnection();
					$select = 'SELECT * FROM ' . $this->_resource->getTableName('layout_update') . ' WHERE layout_update_id IN (' .  implode(',', $widgetLayoutUpdateId) . ') ';
					$configs['Magento_Widget']['tables']['layout_update'] = $connection->fetchAll($select);
				}
			}
		}
		return $configs;
	}

	
}