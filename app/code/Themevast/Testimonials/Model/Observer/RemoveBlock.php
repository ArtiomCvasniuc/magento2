<?php
namespace Themevast\Testimonials\Model\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class RemoveBlock implements ObserverInterface
{
    protected $_scopeConfig;

    protected $_helper;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Themevast\Testimonials\Helper\Data $helper
    ) {
        $this->_helper = $helper;
        $this->_scopeConfig = $scopeConfig;
    }

    public function execute(Observer $observer)
    {
        $layout = $observer->getLayout();
        $block = $layout->getBlock('testimonials.top.link');

        if ($block) {
            if ($this->_helper->isEnabledInTopLink() == 0) {
                $layout->unsetElement('testimonials.top.link');
            }
        }

        $sidebar = $layout->getBlock('testimonials.footer.link');
        if($sidebar) {
            if($this->_helper->isEnabledInFooterLink() == 0) {
                $layout->unsetElement('testimonials.footer.link');
            }
        }
    }
}