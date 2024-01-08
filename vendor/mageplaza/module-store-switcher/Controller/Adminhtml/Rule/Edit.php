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
 * @package     Mageplaza_StoreSwitcher
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\StoreSwitcher\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\StoreSwitcher\Controller\Adminhtml\Rule;
use Mageplaza\StoreSwitcher\Model\RuleFactory;

/**
 * Class Edit
 * @package Mageplaza\StoreSwitcher\Controller\Adminhtml\Rule
 */
class Edit extends Rule
{
    /**
     * Page factory
     *
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param RuleFactory $ruleFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        RuleFactory $ruleFactory,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($ruleFactory, $registry, $context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|ResponseInterface|Redirect|ResultInterface|Page
     */
    public function execute()
    {
        /** @var \Mageplaza\StoreSwitcher\Model\Rule $rule */
        $rule = $this->initRule();
        if (!$rule) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*');

            return $resultRedirect;
        }

        $data = $this->_session->getData('mageplaza_storeswitcher_rule_data', true);
        if (!empty($data)) {
            $rule->setData($data);
        }

        $this->coreRegistry->register('mageplaza_storeswitcher_rule', $rule);

        /** @var \Magento\Backend\Model\View\Result\Page|Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Mageplaza_StoreSwitcher::rule');
        $resultPage->getConfig()->getTitle()->set(__('Manage Rules'));

        $title = $rule->getId() ? $rule->getName() : __('Create New Rule');
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
