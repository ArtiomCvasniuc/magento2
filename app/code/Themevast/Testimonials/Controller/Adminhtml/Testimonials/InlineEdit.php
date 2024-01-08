<?php
namespace Themevast\Testimonials\Controller\Adminhtml\Testimonials;

abstract class InlineEdit extends \Magento\Backend\App\Action
{
   
    protected $jsonFactory;

    protected $tabFactory;

    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Themevast\Testimonials\Model\TestimonialsFactory $tabFactory,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->jsonFactory = $jsonFactory;
        $this->tabFactory = $tabFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
        $tabItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($tabItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }
        foreach (array_keys($tabItems) as $tabId) {
            $tab = $this->tabFactory->create()->load($tabId);
            try {
                $tabData = $tabItems[$tabId];
                $tab->setData($tabData);
                $tab->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithTabId($tab, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithTabId($tab, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithTabId(
                    $tab,
                    __('Something went wrong while saving the Tab.')
                );
                $error = true;
            }
        }
        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    protected function getErrorWithTabId(\Themevast\Testimonials\Model\Testimonials $tab, $errorText)
    {
        return '[Tab ID: ' . $tab->getId() . '] ' . $errorText;
    }
}
