<?php
 
namespace Themevast\SlideBanner\Block\Adminhtml\Slider\Edit;
use Magento\Backend\Block\Widget\Tabs as WidgetTabs;
 
class Tabs extends WidgetTabs
{
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('slide_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Slider Information'));
    }
 
    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'slider_info',
            [
                'label' => __('General'),
                'title' => __('General'),
                'content' => $this->getLayout()->createBlock(
                    'Themevast\SlideBanner\Block\Adminhtml\Slider\Edit\Tab\Info'
                )->toHtml(),
                'active' => true
            ]
        );
        $this->addTab(
            'slider_setting',
            [
                'label' => __('Setting Slider'),
                'title' => __('Setting Slider'),
                'content' => $this->getLayout()->createBlock(
                    'Themevast\SlideBanner\Block\Adminhtml\Slider\Edit\Tab\Setting'
                )->toHtml(),
                'active' => false
            ]
        );
        $this->addTab(
            'slide_info',
            [
                'label' => __('Banner List'),
                'title' => __('Banner List'),
                'content' => $this->getLayout()->createBlock(
                    'Themevast\SlideBanner\Block\Adminhtml\Slider\Edit\Tab\Banner'
                )->toHtml(),
                'active' => false
            ]
        );
 
        return parent::_beforeToHtml();
    }
}