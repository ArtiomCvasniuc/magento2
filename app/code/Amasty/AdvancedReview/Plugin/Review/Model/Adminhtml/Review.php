<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Plugin\Review\Model\Adminhtml;

use Amasty\AdvancedReview\Helper\BlockHelper;
use Amasty\AdvancedReview\Helper\Config;
use Amasty\AdvancedReview\Model\EmailSender;
use Amasty\AdvancedReview\Model\ResourceModel\Reminder\ReminderDataFactory;
use Amasty\AdvancedReview\Model\Sources\UseDefaultConfig;
use Magento\Review\Model\Review as MagentoReview;

class Review
{
    /**
     * @var Config
     */
    private $configHelper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Amasty\AdvancedReview\Model\Email\Coupon
     */
    private $coupon;

    /**
     * @var ReminderDataFactory
     */
    private $reminderDataFactory;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var EmailSender
     */
    private $emailSender;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        Config $configHelper,
        \Magento\Framework\App\RequestInterface $request,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Amasty\AdvancedReview\Model\Email\Coupon $coupon,
        ReminderDataFactory $reminderDataFactory,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        EmailSender $emailSender,
        \Magento\Framework\Url $urlBuilder
    ) {
        $this->configHelper = $configHelper;
        $this->logger = $logger;
        $this->customerRepository = $customerRepository;
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->request = $request;
        $this->coupon = $coupon;
        $this->reminderDataFactory = $reminderDataFactory;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->emailSender = $emailSender;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param MagentoReview $subject
     * @param $result
     *
     * @return mixed
     */
    public function afterAggregate(
        MagentoReview $subject,
        $result
    ) {
        $this->sendAdminReplyToCustomer($subject);
        $this->sendCoupon($subject);

        return $result;
    }

    /**
     * @param MagentoReview $subject
     */
    private function sendAdminReplyToCustomer(MagentoReview $subject)
    {
        $isNeedSendNotification = (int)$this->request->getParam('is_need_send_notification');
        if ($isNeedSendNotification && $subject->getAnswer()) {
            $customerData = $this->getCustomerData($subject);
            $emailTo = $customerData->getData('emailTo');

            if (!$emailTo) {
                return;
            }

            $sender = $this->configHelper->getModuleConfig('customer_notify/sender');
            $template = $this->configHelper->getModuleConfig('customer_notify/template');

            try {
                $store = $this->storeManager->getStore($subject->getStoreId());

                $data =  [
                    'website_name'  => $store->getWebsite()->getName(),
                    'group_name'    => $store->getGroup()->getName(),
                    'store_name'    => $store->getName(),
                    'review_title'  => $subject->getData('title'),
                    'review_detail' => $subject->getData('detail'),
                    'link'          => $this->getReviewLink($subject),
                    'admin_answer'  => $subject->getAnswer(),
                    'customer_name' => $customerData->getData('customerName'),
                ];

                $this->sendMessage($template, $store, $data, $sender, $emailTo);
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }
    }

    /**
     * @param MagentoReview $review
     *
     * @return array|string
     */
    protected function getReviewLink(MagentoReview $review)
    {
        if (!$review->getCustomerId()) {
            if ($review->getStatusId() == MagentoReview::STATUS_APPROVED
                && !$this->configHelper->isAdminAnswerAvailableOnAccountOnly($review)
            ) {
                $reviewLink = $review->getProductUrl($review->getEntityPkValue(), $review->getStoreId());
                $reviewLink = explode('?', $reviewLink);
                $reviewLink = array_shift($reviewLink) . '#reviews'; //remove backend params( SID for example)
            }
        } else {
            $reviewLink = $this->urlBuilder->getUrl(
                'review/customer/view',
                ['id' => $review->getReviewId(), '_nosid' => true]
            );
        }

        return $reviewLink ?? '';
    }

    /**
     * @param MagentoReview $subject
     */
    private function sendCoupon(MagentoReview $subject)
    {
        if ($subject->isApproved()
            && $subject->dataHasChangedFor('status_id')
            && $this->configHelper->isAllowCoupons()
        ) {
            $customerData = $this->getCustomerData($subject);
            $emailTo = $customerData->getData('emailTo');
            if (!$emailTo) {
                return;
            }

            $reminderDataObject = $this->reminderDataFactory->create();
            $reminderData = $reminderDataObject->getReminderData($emailTo);
            $ids = explode(',', $reminderData['ids']);
            if (!in_array($subject->getEntityPkValue(), $ids)) {
                return;
            }

            $sender = $this->configHelper->getModuleConfig('coupons/sender');
            $template = $this->configHelper->getModuleConfig('coupons/template');
            $days = (int)$this->configHelper->getModuleConfig('coupons/coupon_days');
            try {
                $store = $this->storeManager->getStore($subject->getStoreId());
                $data =  [
                    'coupon_days_message'  => $this->coupon->getDaysMessage($days),
                    'coupon_code' => $this->coupon->generateCoupon($emailTo),
                    'customer_name' => $customerData->getData('customerName'),
                ];

                $this->sendMessage($template, $store, $data, $sender, $emailTo);
                $this->emailSender->updateCouponStatus($reminderData['entity_id']);
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }
    }

    /**
     * @param MagentoReview $subject
     * @return \Magento\Framework\DataObject
     */
    private function getCustomerData($subject)
    {
        $customerName = '';
        $customerId = $subject->getCustomerId();

        try {
            $customer = $this->customerRepository->getById($customerId);
            $emailTo = $customer->getEmail();
            $customerName = $customer->getFirstname();
        } catch (\Exception $ex) {
            $emailTo = null;
        }

        $guestEmail = $subject->getData('guest_email');
        if (!$emailTo && $guestEmail) {
            $emailTo = $guestEmail;
        }

        return $this->dataObjectFactory->create(
            [
                'data' => [
                    'customerName' => $customerName,
                    'emailTo' => $emailTo
                ]
            ]
        );
    }

    /**
     * @param string $template
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @param array $data
     * @param array|string $sender
     * @param array|string $emailTo
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    private function sendMessage($template, $store, $data, $sender, $emailTo)
    {
        $transport = $this->transportBuilder->setTemplateIdentifier(
            $template
        )->setTemplateOptions(
            ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $store->getId()]
        )->setTemplateVars(
            $data
        )->setFrom(
            $sender
        )->addTo(
            $emailTo
        )->getTransport();

        $transport->sendMessage();
    }

    /**
     * @param MagentoReview $subject
     */
    public function beforeAfterSave(MagentoReview $subject)
    {
        if ($this->configHelper->isProsConsEnabled()) {
            $connection = $subject->getResource()->getConnection();
            $reviewDetailTable = $subject->getResource()->getTable('review_detail');

            /* save details */
            $select = $connection->select()->from($reviewDetailTable, 'detail_id')
                ->where('review_id = :review_id');
            $detailId = $connection->fetchOne($select, [':review_id' => $subject->getId()]);

            if ($detailId) {
                $detail = [
                    'like_about'     => $subject->getLikeAbout(),
                    'not_like_about' => $subject->getNotLikeAbout()
                ];

                $condition = ["detail_id = ?" => $detailId];
                $connection->update($reviewDetailTable, $detail, $condition);
            }
        }
    }
}
