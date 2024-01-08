<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_AdvancedReview
 */


namespace Amasty\AdvancedReview\Plugin\Review\Block\Adminhtml\Edit;

use Amasty\AdvancedReview\Helper\BlockHelper as BlockHelper;
use Amasty\AdvancedReview\Model\Sources\Recommend;
use Amasty\AdvancedReview\Model\Sources\UseDefaultConfig;
use Amasty\AdvancedReview\Model\Repository\VoteRepository;
use Magento\Backend\Block\Widget\Form\Generic as MagentoForm;

class Form
{
    /**
     * @var \Amasty\AdvancedReview\Helper\Config
     */
    private $configHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var VoteRepository
     */
    private $voteRepository;

    /**
     * @var BlockHelper
     */
    private $blockHelper;

    /**
     * @var Recommend
     */
    private $recommendModel;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var UseDefaultConfig
     */
    private $useDefaultConfig;

    public function __construct(
        \Amasty\AdvancedReview\Helper\Config $configHelper,
        BlockHelper $blockHelper,
        \Magento\Framework\Registry $registry,
        VoteRepository $voteRepository,
        Recommend $recommendModel,
        UseDefaultConfig $useDefaultConfig,
        \Magento\Framework\DataObjectFactory $dataObjectFactory
    ) {
        $this->configHelper = $configHelper;
        $this->registry = $registry;
        $this->voteRepository = $voteRepository;
        $this->blockHelper = $blockHelper;
        $this->recommendModel = $recommendModel;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->useDefaultConfig = $useDefaultConfig;
    }

    /**
     * @param MagentoForm $subject
     * @param \Magento\Framework\Data\Form $form
     * @return array
     */
    public function beforeSetForm(
        MagentoForm $subject,
        \Magento\Framework\Data\Form $form
    ) {
        $fieldset = $form->getElement('review_details') ?: $form->getElement('add_review_form');
        $review = $this->registry->registry('review_data') ?: $this->dataObjectFactory->create();

        $guestEmail = $review->getData('guest_email');
        if ($guestEmail) {
            $text = $form->getElement('customer')->getText();
            // @codingStandardsIgnoreLine
            $text .= sprintf(' (<a href="mailto:%1$s">%1$s</a>)', $guestEmail);
            $form->getElement('customer')->setText($text);
        }

        if ($this->configHelper->isAllowAnswer()) {
            $fieldsetAnswer = $form->addFieldset(
                'review_answer',
                ['legend' => __('Admin Answer Section'), 'class' => 'fieldset-wide', 'collapsble' => true]
            );

            $field = $fieldsetAnswer->addField(
                'answer',
                'textarea',
                ['label' => __('Answer'), 'required' => false, 'name' => 'answer', 'style' => 'height:24em;']
            );

            $field->setValue($review->getAnswer());

            if ($review->getCustomerId() || $guestEmail) {
                $fieldsetAnswer->addField(
                    'is_need_send_notification',
                    'checkbox',
                    [
                        'label' => __('Send to Customer'),
                        'required' => false,
                        'name' => 'is_need_send_notification',
                        'value' => 1
                    ]
                );
            }

            $fieldsetAnswer->addField(
                BlockHelper::ADMIN_ANSWER_ACCOUNT_ONLY,
                'select',
                [
                    'label' =>__('Show Admin\'s Comment in Customer Account Only'),
                    'required' => false,
                    'values' => $this->useDefaultConfig->toOptionArray(),
                    'name' =>  BlockHelper::ADMIN_ANSWER_ACCOUNT_ONLY,
                    'value' => $review->getData(BlockHelper::ADMIN_ANSWER_ACCOUNT_ONLY)
                ]
            );
        }

        if ($this->configHelper->isRecommendFieldEnabled()) {
            if ($fieldset) {
                $field = $fieldset->addField(
                    'is_recommended',
                    'select',
                    [
                        'label' =>__('I recommend this product'),
                        'required' => false,
                        'values' => $this->recommendModel->toOptionArray(),
                        'name' => 'is_recommended',
                        'value' => (int)$review->getData('is_recommended')
                    ]
                );
            }
        }

        if ($this->configHelper->isAllowImages()) {
            $form->setData('enctype', 'multipart/form-data');
            if ($fieldset) {
                $imageHtml = trim($this->blockHelper->getReviewImagesHtml($review->getId()));
                $addImageTitle = __('Customer Images');
                if ($imageHtml) {
                    $fieldset->addField(
                        'added_images',
                        'note',
                        [
                            'label'              => __('Customer Images'),
                            'required'           => false,
                            'name'               => 'added_images',
                            'after_element_html' => $imageHtml
                        ]
                    );
                    $addImageTitle = '';
                }

                $fieldset->addField(
                    'review_images',
                    \Amasty\AdvancedReview\Data\Form\Element\Image::class,
                    [
                        'label' => $addImageTitle,
                        'name' => 'review_images[]',
                        'before_element_html' =>  __('Add new images')
                    ]
                );
            }
        }

        if ($fieldset) {
            $fieldset->addField(
                'verified_buyer',
                'note',
                [
                    'label'    => __('Verified Buyer'),
                    'required' => false,
                    'name'     => 'verified_buyer',
                    'note'     => __('A client who already purchased the product.'),
                    'text'     => $review->getVerifiedBuyer() ? __('Yes') : __('No'),
                ]
            );

            if ($this->configHelper->isAllowHelpful()) {
                $fieldset->addField(
                    'helpfulness',
                    'note',
                    [
                        'label'    => __('Helpfulness'),
                        'required' => false,
                        'name'     => 'Helpfulness',
                        'text'     => $this->getReviewHelpfulness($review)
                    ]
                );
            }

            if ($this->configHelper->isProsConsEnabled()) {
                $fieldset->addField(
                    'like_about',
                    'textarea',
                    [
                        'label'    => __('Advantages'),
                        'required' => false,
                        'name'     => __('like_about'),
                        'value'    => $review->getData('like_about')
                    ]
                );

                $fieldset->addField(
                    'not_like_about',
                    'textarea',
                    [
                        'label'    => __('Disadvantages'),
                        'required' => false,
                        'name'     => __('not_like_about'),
                        'value'    => $review->getData('not_like_about')
                    ]
                );
            }
        }

        return [$form];
    }

    /**
     * @param $review
     * @return \Magento\Framework\Phrase
     */
    private function getReviewHelpfulness($review)
    {
        $vote = $this->voteRepository->getVotesCount($review->getId());
        return __('%1 people found this helpful; %2 found this unhelpful.', $vote['plus'], $vote['minus']);
    }
}
