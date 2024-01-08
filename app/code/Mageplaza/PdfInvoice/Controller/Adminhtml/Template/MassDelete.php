<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_PdfInvoice
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\PdfInvoice\Controller\Adminhtml\Template;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\PdfInvoice\Controller\Adminhtml\Template;
use Mageplaza\PdfInvoice\Helper\Data as HelperData;
use Mageplaza\PdfInvoice\Model\TemplateFactory;

/**
 * Class MassDelete
 * @package Mageplaza\PdfInvoice\Controller\Adminhtml\Template
 */
class MassDelete extends Template
{
    /**
     * Massactions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * MassDelete constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param TemplateFactory $templateFactory
     * @param Filter $filter
     * @param HelperData $helperData
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        TemplateFactory $templateFactory,
        Filter $filter,
        HelperData $helperData
    ) {
        $this->filter = $filter;

        parent::__construct($context, $resultPageFactory, $templateFactory, $helperData);
    }

    /**
     * @return $this|ResponseInterface|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $collection = $this->filter->getCollection($this->_getTemplateCollection());
        $deleted = 0;
        foreach ($collection->getItems() as $template) {
            if ($this->checkTemplate($template->getId())) {
                $this->messageManager->addError(__('Cannot delete template #%1! The Template is using in config', $template->getId()));

                continue;
            }
            $template->delete();
            $deleted++;
        }
        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', $deleted)
        );

        return $resultRedirect->setPath('pdfinvoice/*/');
    }
}
