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
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\PdfInvoice\Controller\Adminhtml\Template;
use Mageplaza\PdfInvoice\Helper\Data as HelperData;
use Mageplaza\PdfInvoice\Model\TemplateFactory;

/**
 * Class Edit
 * @package Mageplaza\PdfInvoice\Controller\Adminhtml\Template
 */
class Edit extends Template
{
    /** @var Registry */
    protected $registry;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param TemplateFactory $templateFactory
     * @param Registry $registry
     * @param HelperData $helperData
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        TemplateFactory $templateFactory,
        Registry $registry,
        HelperData $helperData
    ) {
        $this->registry = $registry;

        parent::__construct($context, $resultPageFactory, $templateFactory, $helperData);
    }

    /**
     * @return Page
     */
    public function execute()
    {
        $template = $this->_initObject();
        if ($template) {
            $this->registry->register('current_template', $template);

            /** @var Page $resultPage */
            $resultPage = $this->_initAction();
            $resultPage->getConfig()->getTitle()->prepend($template->getId() ? __("Edit Template '%1'", $template->getName()) : __('Create New Template'));

            return $resultPage;
        }

        $this->_redirect('*/*/');
    }
}
