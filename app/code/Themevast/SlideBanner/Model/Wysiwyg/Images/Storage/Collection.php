<?php
namespace Magento\Cms\Model\Wysiwyg\Images\Storage;

use Magento\Framework\App\Filesystem\DirectoryList;


class Collection extends \Magento\Framework\Data\Collection\Filesystem
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    
    public function __construct(\Magento\Framework\Data\Collection\EntityFactory $entityFactory, \Magento\Framework\Filesystem $filesystem)
    {
        $this->_filesystem = $filesystem;
        parent::__construct($entityFactory);
    }

    protected function _generateRow($filename)
    {
        $filename = preg_replace('~[/\\\]+~', '/', $filename);
        $path = $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        return [
            'filename' => $filename,
            'basename' => basename($filename),
            'mtime' => $path->stat($path->getRelativePath($filename))['mtime']
        ];
    }
}
