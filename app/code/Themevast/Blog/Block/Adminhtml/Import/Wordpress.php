<?php
namespace Themevast\Blog\Block\Adminhtml\Import;

use Magento\Store\Model\ScopeInterface;

class Wordpress extends \Magento\Backend\Block\Widget\Form\Container
{

   
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Themevast_Blog';
        $this->_controller = 'adminhtml_import';
        $this->_mode = 'wordpress';

        parent::_construct();

        if (!$this->_isAllowedAction('Themevast_Blog::import')) {
            $this->buttonList->remove('save');
        } else {
          $this->updateButton(
              'save', 'label', __('Start Import')
          );
        }

        $this->buttonList->remove('delete');
    }

   
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/run', ['_current' => true]);
    }

}
