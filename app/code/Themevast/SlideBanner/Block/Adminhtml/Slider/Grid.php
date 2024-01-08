<?php
 
namespace Themevast\SlideBanner\Block\Adminhtml\Slider;
 
use Magento\Backend\Block\Widget\Grid as WidgetGrid;
 
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    
    protected $moduleManager;
 
    protected $_collection;
 
    
    protected $_status;
    protected $_objectManager;
 
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
		\Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
		$this->_objectManager = $objectManager;
        parent::__construct($context, $backendHelper, $data);
    }
 
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sliderGrid');
        $this->setDefaultSort('slider_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('slider_record');
    }
 
    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
		$collection = $this->_objectManager->create('Themevast\SlideBanner\Model\Slider', [])->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }
 
   
    protected function _prepareColumns()
    {
        $this->addColumn(
            'slider_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'slider_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
		$this->addColumn(
            'slider_identifier',
            [
                'header' => __('Identifier'),
                'type' => 'text',
                'index' => 'slider_identifier',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
		$this->addColumn(
            'slider_title',
            [
                'header' => __('Title'),
                'type' => 'text',
                'index' => 'slider_title',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
		$this->addColumn(
            'slider_status',
            [
                'header' => __('Status'),
                'type' => 'options',
                'index' => 'slider_status',
				'options'=> [1=>__('Enable'), 2=>__('Disable')],
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
		$block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }
        return parent::_prepareColumns();
    }
 
    /**
     * @return $this
     */
    // 
 
    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }
 
    
    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/edit',
            ['slider_id' => $row->getId()]
        );
    }
}