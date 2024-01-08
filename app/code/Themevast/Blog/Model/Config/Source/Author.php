<?php
   /*
	* ducdevphp@gmail.com
   */
?>
<?php
namespace Themevast\Blog\Model\Config\Source;

class Author implements \Magento\Framework\Option\ArrayInterface
{
   
    protected $authorCollectionFactory;


    protected $options;

   
    public function __construct(
        \Magento\User\Model\ResourceModel\User\CollectionFactory $authorCollectionFactory
    ) {
        $this->authorCollectionFactory = $authorCollectionFactory;
    }

   
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [['label' => __('Please select'), 'value' => 0]];
            $collection = $this->authorCollectionFactory->create();

            foreach ($collection as $item) {
                $this->options[] = [
                    'label' => $item->getName(),
                    'value' => $item->getId(),
                ];
            }
        }

        return $this->options;
    }

    
    public function toArray()
    {
        $array = [];
        foreach ($this->toOptionArray() as $item) {
            $array[$item['value']] = $item['label'];
        }
        return $array;
    }
	
}
