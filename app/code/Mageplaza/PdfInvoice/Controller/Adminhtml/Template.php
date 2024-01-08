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

namespace Mageplaza\PdfInvoice\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\PdfInvoice\Helper\Data as HelperData;
use Mageplaza\PdfInvoice\Model\TemplateFactory;

/**
 * Class Template
 * @package Mageplaza\PdfInvoice\Controller\Adminhtml
 */
abstract class Template extends Action
{
    /** Authorization level of a basic admin session */
    const ADMIN_RESOURCE = 'Mageplaza_PdfInvoice::template';

    /** @type PageFactory */
    protected $resultPageFactory;

    /** @type  TemplateFactory */
    protected $_templateFactory;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * Template constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param TemplateFactory $templateFactory
     * @param HelperData $helperData
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        TemplateFactory $templateFactory,
        HelperData $helperData
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_templateFactory = $templateFactory;
        $this->helperData = $helperData;

        parent::__construct($context);
    }

    /**
     * Init layout, menu and breadcrumb
     *
     * @return Page
     */
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE);
        $resultPage->addBreadcrumb(__('PDF Invoice'), __('PDF Invoice'));
        $resultPage->addBreadcrumb(__('Templates'), __('Templates'));

        return $resultPage;
    }

    /**
     * @return bool|\Mageplaza\PdfInvoice\Model\Template
     */
    protected function _initObject()
    {
        $id = (int)$this->getRequest()->getParam('id');

        $template = $this->_templateFactory->create();
        if ($id) {
            $template->load($id);
            if (!$template->getId()) {
                $this->messageManager->addErrorMessage(__('This template no longer exists.'));

                return false;
            }
        }

        return $template;
    }

    /**
     * @return AbstractCollection
     */
    protected function _getTemplateCollection()
    {
        return $this->_templateFactory->create()->getCollection();
    }

    /**
     * Check template
     *
     * @param $id
     *
     * @return bool
     */
    protected function checkTemplate($id)
    {
        return $this->helperData->checkTemplateInConfig($id);
    }
}
