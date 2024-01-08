<?php
namespace Themevast\Blog\Observer;

use Magento\Framework\Event\ObserverInterface;

class PredispathAdminActionControllerObserver implements ObserverInterface
{
    protected $_feedFactory;

    protected $_backendAuthSession;

    
    public function __construct(
        \Themevast\Blog\Model\AdminNotificationFeedFactory $feedFactory,
        \Magento\Backend\Model\Auth\Session $backendAuthSession
    ) {
        $this->_feedFactory = $feedFactory;
        $this->_backendAuthSession = $backendAuthSession;
    }

    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_backendAuthSession->isLoggedIn()) {
            $feedModel = $this->_feedFactory->create();
            
            $feedModel->checkUpdate();
        }
    }
}
