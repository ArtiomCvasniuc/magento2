<?php
namespace Themevast\Blog\Block\Sidebar;
class Archive extends \Themevast\Blog\Block\Post\PostList\AbstractList
{
    use Widget;

    protected $_widgetKey = 'archive';

    protected $_months;

    protected function _preparePostCollection()
    {
        parent::_preparePostCollection();
        $this->_postCollection->getSelect()->group(
            'MONTH(main_table.publish_time)',
            'DESC'
        );
    }

    public function getMonths()
    {
        if (is_null($this->_months)) {
            $this->_months = array();
            $this->_preparePostCollection();
            foreach($this->_postCollection as $post) {
                $time = strtotime($post->getData('publish_time'));
                $this->_months[date('Y-m', $time)] = $time;
            }
        }


        return $this->_months;
    }

    public function getYear($time)
    {
        return date('Y', $time);
    }

    public function getMonth($time)
    {
        return __(date('F', $time));
    }

    public function getTimeUrl($time)
    {
        return $this->getUrl('blog/archive/'.date('Y-m', $time));
    }
	
	public function getIdentities()
    {
        return [\Magento\Cms\Model\Block::CACHE_TAG . '_blog_archive_widget'  ];
    }

}
