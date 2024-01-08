<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Controller\Adminhtml\Review;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;
use Magento\Review\Model\ReviewFactory;

class MassAllApprove extends Action
{
    /**
     * @var ReviewFactory
     */
    private $reviewFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        Action\Context $context,
        ReviewFactory $reviewFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->reviewFactory = $reviewFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $reviewsIds = $this->getRequest()->getParam('reviews');

        if (!is_array($reviewsIds)) {
            $this->messageManager->addErrorMessage(__('Please select review(s).'));
        } else {
            try {
                $allStores = $this->getAllStoresIds();

                foreach ($reviewsIds as $reviewId) {
                    $model = $this->reviewFactory->create()->load($reviewId);
                    $model->setStatusId(\Magento\Review\Model\Review::STATUS_APPROVED)
                        ->setData('stores', $allStores);
                    $model->save()->aggregate();
                }

                $this->messageManager->addSuccessMessage(
                    __('A total of %1 record(s) have been approved for All store views.', count($reviewsIds))
                );
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while updating these review(s).')
                );
            }
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('review/product/' . $this->getRequest()->getParam('ret', 'index'));
        return $resultRedirect;
    }

    /**
     * @return array
     */
    protected function getAllStoresIds()
    {
        $stores = $this->storeManager->getStores();
        $ids = [];

        foreach ($stores as $store) {
            $ids[] = $store->getId();
        }

        return $ids;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getParam('ret')) {
            case 'pending':
                $result = $this->_authorization->isAllowed('Magento_Review::pending');
                break;
            default:
                $result = $this->_authorization->isAllowed('Magento_Review::reviews_all');
                break;
        }

        return $result;
    }
}
