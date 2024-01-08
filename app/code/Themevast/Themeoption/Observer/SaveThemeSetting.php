<?php
namespace Themevast\Themeoption\Observer;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class SaveThemeSetting implements ObserverInterface
{
	protected $_generator;

	public function __construct(\Themevast\Themeoption\Model\Cssgen\GeneratorS $generator)
	{
		$this->_generator = $generator;
	}

	public function execute(EventObserver $observer)
	{
		$website = $observer->getEvent()->getWebsite();
		$store = $observer->getEvent()->getStore();
		$this->_generator->generateCss($website, $store);
	}
}