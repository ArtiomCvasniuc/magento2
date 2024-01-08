<?php
namespace Themevast\Blog\Model;

use Magento\Framework\Config\ConfigOptionsListConstants;

class AdminNotificationFeed extends \Magento\AdminNotification\Model\Feed
{
    
    protected $_backendAuthSession;

    
    protected $_moduleList;

    
    protected $_moduleManager;

  
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\ConfigInterface $backendConfig,
        \Magento\AdminNotification\Model\InboxFactory $inboxFactory,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory,
        \Magento\Framework\App\DeploymentConfig $deploymentConfig,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $backendConfig, $inboxFactory, $curlFactory, $deploymentConfig, $productMetadata, $urlBuilder, $resource, $resourceCollection, $data);
        $this->_backendAuthSession  = $backendAuthSession;
        $this->_moduleList = $moduleList;
        $this->_moduleManager = $moduleManager;
    }

    
    public function getFeedUrl()
    {
        if (is_null($this->_feedUrl)) {
            $this->_feedUrl = 'http://mage'.'fan'
            .'.c'.'om/community/notifications'.'/'.'feed/';
        }

        $urlInfo = parse_url($this->urlBuilder->getBaseUrl());
        $domain = isset($urlInfo['host']) ? $urlInfo['host'] : '';

        $url = $this->_feedUrl . 'domain/' . urlencode($domain);

        $modulesParams = array();
        foreach($this->getThemevastModules() as $key => $module) {
            $key = str_replace('Themevast_', '', $key);
            $modulesParams[] = $key . ',' . $module['setup_version'];
        }

        if (count($modulesParams)) {
            $url .= '/modules/'.base64_encode(implode(';', $modulesParams));
        }

        return $url;
    }

    
    protected function getThemevastModules()
    {
        $modules = array();
        foreach($this->_moduleList->getAll() as $moduleName => $module) {
            if ( strpos($moduleName, 'Themevast_') !== false && $this->_moduleManager->isEnabled($moduleName) ) {
                $modules[$moduleName] = $module;
            }
        }

        return $modules;
    }

    
    public function checkUpdate()
    {
        $session = $this->_backendAuthSession;
        $time = time();
        $frequency = $this->getFrequency();
        if (($frequency + $session->getMfNoticeLastUpdate() > $time)
            || ($frequency + $this->getLastUpdate() > $time)
        ) {
            return $this;
        }

        $session->setMfNoticeLastUpdate($time);
        return parent::checkUpdate();
    }

    
    public function getFrequency()
    {
        return 86400;
    }

    
    public function getLastUpdate()
    {
        return $this->_cacheManager->load('Themevast_admin_notifications_lastcheck');
    }

    
    public function setLastUpdate()
    {
        $this->_cacheManager->save(time(), 'themevast_admin_notifications_lastcheck');
        return $this;
    }

}
