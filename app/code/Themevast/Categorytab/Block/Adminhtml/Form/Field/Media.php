<?php 
/*
mageducdq
ducdevphp@gmail.com
*/
?>
<?php
namespace Themevast\Categorytab\Block\Adminhtml\Form\Field;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Widget\Button;
use \Magento\Framework\Data\Form\Element\AbstractElement as Element;
use \Magento\Backend\Block\Template\Context as TemplateContext;
use \Magento\Framework\Data\Form\Element\Factory as FormElementFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\Text;

class Media extends \Magento\Backend\Block\Template
{
    
    // protected $_elementFactory;

   
    // public function __construct(
        // \Magento\Backend\Block\Template\Context $context,
        // \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        // array $data = []
    // ) {
        // $this->_elementFactory = $elementFactory;
        // parent::__construct($context, $data);
    // }

    // public function prepareElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    // {
        // $config = $this->_getData('config');
        // $sourceUrl = $this->getUrl('cms/wysiwyg_images/index',
            // ['target_element_id' => $element->getId(), 'type' => 'file']);
        // $chooser = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            // ->setType('button')
            // ->setClass('btn-chooser')
            // ->setLabel($config['button']['open'])
          // /*   ->setOnClick('MediabrowserUtility.openDialog(\''. $sourceUrl .'\')') */
		    // ->setOnClick("MediabrowserUtility.openDialog('$sourceUrl',null, null,'".$this->escapeQuote(__('Choose Image...'),true)."',(opt = new Object(), opt.closed = false, opt))")
            // ->setDisabled($element->getReadonly());

        // $input = $this->_elementFactory->create("text", ['data' => $element->getData()]);
        // $input->setId($element->getId());
        // $input->setForm($element->getForm());
        // $input->setClass("widget-option input-text admin__control-text");
        // if ($element->getRequired()) {
            // $input->addClass('required-entry');
        // }

        // $element->setData('after_element_html', $input->getElementHtml() . $chooser->toHtml());
        // return $element;
    // }
	protected $_elementFactory;

    public function __construct(TemplateContext $context, FormElementFactory $elementFactory, $data = [])
    {
        $this->_elementFactory = $elementFactory;
        parent::__construct($context, $data);
    }

   
    public function prepareElementHtml(Element $element)
    {
        $config = $this->_getData('config');
        $sourceUrl = $this->getUrl('cms/wysiwyg_images/index',
            ['target_element_id' => $element->getId(), 'type' => 'file']);

       
        $chooser = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
                        ->setType('button')
                        ->setClass('btn-chooser')
                        ->setLabel($config['button']['open'])
                        /* ->setOnClick('MediabrowserUtility.openDialog(\'' . $sourceUrl . '\', 0, 0, "MediaBrowser", {})') */
						->setOnClick("MediabrowserUtility.openDialog('$sourceUrl',null, null,'".$this->escapeQuote(__('Choose Image...'),true)."',(opt = new Object(), opt.closed = false, opt))")
                        ->setDisabled($element->getReadonly());

       
        $input = $this->_elementFactory->create("text", ['data' => $element->getData()]);
        $input->setId($element->getId());
        $input->setForm($element->getForm());
        $input->setClass("widget-option input-text admin__control-text");
        if ($element->getRequired()) {
            $input->addClass('required-entry');
        }

        $element->setData('after_element_html', $input->getElementHtml()
            . $chooser->toHtml() . "<script>require(['mage/adminhtml/browser']);</script>");

        return $element;
    }
}