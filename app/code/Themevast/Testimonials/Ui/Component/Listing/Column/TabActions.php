<?php
namespace Themevast\Testimonials\Ui\Component\Listing\Column;

class TabActions extends \Magento\Ui\Component\Listing\Columns\Column
{
	
    const URL_PATH_EDIT = 'testimonials/testimonials/edit';

    const URL_PATH_DELETE = 'testimonials/testimonials/delete';
	
    protected $urlBuilder;

    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    )
    {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }


    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['testimonial_id'])) {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_EDIT,
                                [
                                    'testimonial_id' => $item['testimonial_id']
                                ]
                            ),
                            'label' => __('Edit')
                        ],
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_DELETE,
                                [
                                    'testimonial_id' => $item['testimonial_id']
                                ]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete "${ $.$data.name }"'),
                                'message' => __('Are you sure you wan\'t to delete the testimonial "${ $.$data.name }" ?')
                            ]
                        ]
                    ];
                }
            }
        }
        return $dataSource;
    }
}
