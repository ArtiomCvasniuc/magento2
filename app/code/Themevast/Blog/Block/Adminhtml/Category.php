<?php
 /*
 * custom ducdevphp@gmail.com
 */
?>
<?php
namespace Themevast\Blog\Block\Adminhtml;

class Category extends \Magento\Backend\Block\Widget\Grid\Container
{
   
    protected function _construct()
    {
        $this->_controller = 'adminhtml';
        $this->_blockGroup = 'Themevast_Blog';
        $this->_headerText = __('Category');
        $this->_addButtonLabel = __('Add New Category');
        parent::_construct();
    }
}
