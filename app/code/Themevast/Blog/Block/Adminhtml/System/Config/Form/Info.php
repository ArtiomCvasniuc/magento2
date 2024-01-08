<?php
namespace Themevast\Blog\Block\Adminhtml\System\Config\Form;

use Magento\Store\Model\ScopeInterface;

class Info extends \Magento\Config\Block\System\Config\Form\Field
{
    
    protected $moduleList;

    
    public function __construct(
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->moduleList       = $moduleList;
    }

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $m = $this->moduleList->getOne($this->getModuleName());
        $html = '<div style="padding:10px;background-color:#f8f8f8;border:1px solid #ddd;margin-bottom:7px;">
            Blog Extension v' . $m['setup_version'] . ' was developed by <a href="http://www.themevast.com/" target="_blank">Themevast</a>.
        </div>';

        return $html;
    }

}
