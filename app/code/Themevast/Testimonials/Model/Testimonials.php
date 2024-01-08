<?php 
/**
  *ducdevphp@gmail.com
*/
?>
<?php
namespace Themevast\Testimonials\Model;
 
class Testimonials extends \Magento\Framework\Model\AbstractModel
{

	const BASE_MEDIA_PATH = 'themevast/testimonials/images';
	
	protected $_monolog;
	
	protected $_messageManager;
	
	
	protected $_itemFactory;
	
	protected $_resourceConnection;
	
    protected function _construct()
    {
        $this->_init('Themevast\Testimonials\Model\ResourceModel\Testimonials');
    }	
	public function __construct(
		\Magento\Framework\Model\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Themevast\Testimonials\Model\TestimonialsFactory $testimonialsFactory,
		\Themevast\Testimonials\Model\ResourceModel\Testimonials $resource,
		\Themevast\Testimonials\Model\ResourceModel\Testimonials\Collection $resourceCollection,
		\Magento\Framework\App\ResourceConnection $resourceConnection,
		\Magento\Framework\Logger\Monolog $monolog
	) {
		parent::__construct(
			$context,
			$registry,
			$resource,
			$resourceCollection
		);
		$this->_messageManager = $messageManager;
		$this->_monolog = $monolog;
		$this->_itemFactory = $testimonialsFactory;
		$this->_resourceConnection = $resourceConnection;
	}
	
}
 
?>