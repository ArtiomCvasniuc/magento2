<?php
 
namespace Themevast\SlideBanner\Block\Adminhtml\Slide;
 
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
 
    
    protected function _construct()
    {
        parent::_construct();
        $this->setId('gridGrid');
        $this->setDefaultSort('slide_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('grid_record');
    }
 
    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
		$collection = $this->_objectManager->create('Themevast\SlideBanner\Model\Slide', [])->getCollection()->joinSlider();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }
 
   
    protected function _prepareColumns()
    {
        $this->addColumn(
            'slide_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'slide_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
		$this->addColumn(
            'slider_id',
            [
                'header' => __('Slider'),
                'type' => 'options',
                'index' => 'slider_id',
                'filter_index' => 'main_table.slider_id',
				'options'=> $this->_getSliderOptions(),
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
		$this->addColumn(
            'slide_status',
            [
                'header' => __('Status'),
                'type' => 'options',
                'index' => 'slide_status',
				'options'=> [1=>__('Enable'), 2=>__('Disable')],
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
		$this->addColumn(
            'slide_image',
            [
                'header' => __('Images'),
                'renderer' => 'Themevast\SlideBanner\Block\Adminhtml\Slide\Renderer\Image',
                'filter' => false,
                'order' => false,
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
	protected function _getSliderOptions()
	{
		$result = [];
		$collection = $this->_objectManager->create('Themevast\SlideBanner\Model\Slider', [])->getCollection();
		foreach($collection as $slider)
		{
			$result[$slider->getId()] = $slider->getSliderTitle();
		}
		return $result;
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
            ['slide_id' => $row->getId()]
        );
    }
}