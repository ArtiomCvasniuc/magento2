<?php
namespace Themevast\Themeoption\Observer;

use Magento\Framework\Event\ObserverInterface;

class Compileless implements ObserverInterface
{
	protected $_storeManager;

	protected $_design;
	protected $_scopeConfig;
	
    private $_lessc; 
	protected $localeResolver;
	
	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\Locale\ResolverInterface $localeResolver,
        array $data = []
    )
    {
		$this->_scopeConfig = $context->getScopeConfig();
		$this->_localeResolver = $localeResolver;
        $this->_storeManager = $context->getStoreManager();
		$this->_design = $context->getDesignPackage();
    }
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		if(!$this->_scopeConfig->getValue('themeoption_setting/less/auto_render_less'))
			return $this;
		$file_in = '';
		$file_out = '';
		$layout = $observer->getEvent()->getLayout();
		$url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK); 
		$static_url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_STATIC);
		
		$design =   $this->_design->getDesignTheme(); 
		$files = array(
				'style',
				'themes'
		);
	
		$web_css=  BP.'/app/design/'.$design->getFullPath().'/web/css/'; 	
		$locale = $this->_localeResolver->getLocale();
		$static_css = BP.'/pub/static/'.$design->getFullPath().'/'.$locale.'/css/';
		foreach($files as $file) {
			$file_in = $web_css.$file.'.less';
			if(!file_exists($file_in))
				continue;
			try{
				$file_out = $static_css.$file.'.css';
				$less = new \lessc;
				$less ->compileFile($file_in, $file_out); 	
			}catch(Exception $e)
			{
				
			}	
		}
		return $this;
	}
}
