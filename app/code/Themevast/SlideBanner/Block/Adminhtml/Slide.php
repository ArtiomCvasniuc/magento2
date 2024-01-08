<?php
 
namespace Themevast\SlideBanner\Block\Adminhtml;
 
use Magento\Backend\Block\Widget\Grid\Container;
 
class Slide extends Container
{
   /**
     * Constructor
     *
     * @return void
     */
   protected function _construct()
    {
        $this->_controller = 'adminhtml_slide';
        $this->_blockGroup = 'Themevast_SlideBanner';
        $this->_headerText = __('Manage Banner');
        $this->_addButtonLabel = __('Add Banner');
        parent::_construct();
    }
}
 