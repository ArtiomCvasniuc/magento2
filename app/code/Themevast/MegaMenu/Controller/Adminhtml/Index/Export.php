<?php
namespace Themevast\MegaMenu\Controller\Adminhtml\Index;
class Export extends \Magento\Backend\App\Action
{
	protected $resultForwardFactory;
	protected $menuFactory;
	protected $csv;
	protected $fixtureManager;
	
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\Setup\SampleData\FixtureManager $fixtureManager,
		\Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
		\Magento\Framework\File\Csv	$csv,
		\Themevast\MegaMenu\Model\MegamenuFactory $menuFactory
	)
	{
		$this->resultForwardFactory = $resultForwardFactory;
		$this->menuFactory = $menuFactory;
		$this->csv = $csv;
		$this->fixtureManager = $fixtureManager;
		parent::__construct($context);
	}
	/**
     * Is the user allowed to view the menu grid.
     *
     * @return bool
     */
	protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Themevast_MegaMenu::save');
    }
	public function execute()
    {
		$menu = $this->menuFactory->create();
		$collection = $menu->getCollection();
		$data = [];
		$message = [];
		foreach($collection->getItems() as $item){
			$item->unsetData('menu_id');
			$data[] = $item->getData();
			$message[] = '<p>'.$item->getData('identifier').'</p>';
		}
		
		$file = $this->fixtureManager->getFixture('Themevast_MegaMenu::fixtures/themevast_megamenu.csv');
		$this->csv->saveData($file, $data);
		$this->getReponse()->setBody('<p>Export successfully:</p>'.implode('',$message));
    }
}