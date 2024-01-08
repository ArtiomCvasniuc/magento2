<?php
/**
 * Themevast
 */
namespace Themevast\Themeoption\Model\Cssgen;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Directory\Helper\Data;

class GeneratorS{

	
	protected $_storeManager;

	private $messageManager;

	protected $_coreRegistry = null;

	protected $_blockFactory;

	protected $_themeModel;

	protected $_scopeConfig;

	protected $_tvHelper;

	public function __construct(
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\View\Element\BlockFactory $blockFactory,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Theme\Model\Theme $themeModel,
		\Themevast\Themeoption\Helper\Data $tvHelper
		)
	{
		$this->_storeManager = $storeManager;
		$this->_blockFactory = $blockFactory;
		$this->_coreRegistry = $registry;
		$this->_filesystem = $filesystem;
		$this->messageManager = $messageManager;
		$this->_themeModel = $themeModel;
		$this->_tvHelper = $tvHelper;
		$this->_scopeConfig = $scopeConfig;
	}

	public function generateCss($websiteCode, $storeCode){
		if($websiteCode){
			$website = $this->_storeManager->getWebsite($websiteCode);
			$this->_generateWebsiteCss($website);
		}
		if($storeCode){
			$this->_generateStoreCss($storeCode);
		}
		if(!$websiteCode && !$storeCode){
			$websites = $this->_storeManager->getWebsites();
			foreach ($websites as $website) {
				$this->_generateWebsiteCss($website); 
			}
		}
	}

	protected function _generateWebsiteCss($website){
		foreach ($website->getStoreCodes() as $storeCode){
			$this->_generateStoreCss($storeCode);
		}
	}

	protected function _generateStoreCss($storeCode){
		$store = $this->_storeManager->getStore($storeCode);
		$storeId = $store->getId();
			$this->_coreRegistry->register('tv_setting_cssgen_store', $store);
			$cssTvHtml = $this->_blockFactory->createBlock('Themevast\Themeoption\Block\ThemeSetting')->setData('area','frontend')->setTemplate("Themevast_Themeoption::themevast/setting.phtml")->toHtml();
			$cssTvHtml = $this->_tvHelper->convertCssCode($cssTvHtml);
			$themeId =  $this->_scopeConfig->getValue(
				\Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID,
				\Magento\Store\Model\ScopeInterface::SCOPE_STORE,
				$store);
			$theme = $this->_themeModel->load($themeId);
			try{
				if (empty($cssTvHtml)) {
					throw new \Magento\Framework\Exception\LocalizedException( __("The system has an issue when create less file") ); 
				}
				$localeCode = $this->_scopeConfig->getValue(
					Data::XML_PATH_DEFAULT_LOCALE,
					\Magento\Store\Model\ScopeInterface::SCOPE_STORE,
					$store
					);

				$dir = $this->_filesystem->getDirectoryWrite(DirectoryList::STATIC_VIEW);
				$fileName = $theme->getFullPath() . DIRECTORY_SEPARATOR . $localeCode . DIRECTORY_SEPARATOR . 'css/themeless/_tv_setting.less';
				$dir->writeFile($fileName, $cssTvHtml);
				$this->messageManager->addSuccess(__('The updated successfully.'));
				$themeDir = $this->_filesystem->getDirectoryWrite(DirectoryList::APP);
				$fileName = 'design' . DIRECTORY_SEPARATOR . $theme->getFullPath() . DIRECTORY_SEPARATOR . '/web/css/themeless/_tv_setting.less';
				$themeDir->writeFile($fileName, $cssTvHtml);
				$files = array(
					'themes'
				);
				$web_css=  BP.'/app/design/'.$theme->getFullPath().'/web/css/'; 	
				$static_css = BP.'/pub/static/'.$theme->getFullPath().'/'.$localeCode.'/css/';
				foreach($files as $file) {
					$file_in = $web_css.$file.'.less';
					$file_out = $static_css.$file.'.css';
					$less = new \lessc;
					$less ->compileFile($file_in, $file_out); 	
				}

			}catch (\Exception $e){
				$this->messageManager->addError(__('The system has an issue when create css file'). '<br/>Message: ' . $e->getMessage());
			}
			$this->_coreRegistry->unregister('tv_setting_cssgen_store');
	}
}