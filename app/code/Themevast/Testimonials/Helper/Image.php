<?php
//ducdevphp@gmail.com
namespace Themevast\Testimonials\Helper;

class Image extends \Magento\Framework\App\Helper\AbstractHelper
{
	
		protected $_filesystem ;
		protected $_imageFactory;
		protected $_storeManager;
	public function __construct(            
        \Magento\Framework\Filesystem $filesystem,         
        \Magento\Framework\Image\AdapterFactory $imageFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManagerInterface
        ) {         
        $this->_filesystem = $filesystem;               
        $this->_imageFactory = $imageFactory;
			$this->_storeManager = $storeManagerInterface;
        }

    public function resize($image, $width = null, $height = null)
    {
        $absolutePath = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('').$image;
		$_directory = $this->_filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
		 $realPath = $_directory->getRelativePath($absolutePath);
		 if (!$_directory->isFile($realPath) || !$_directory->isExist($realPath)) {
            return false;
        }
        $imageResized = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('resized/'.$width.'/').$image;         
        $imageResize = $this->_imageFactory->create();         
        $imageResize->open($absolutePath);
        $imageResize->constrainOnly(TRUE);         
        $imageResize->keepTransparency(TRUE);         
        $imageResize->keepFrame(FALSE);         
        $imageResize->keepAspectRatio(FALSE);         
        $imageResize->resize($width,$height);               
        $destination = $imageResized ;        
        $imageResize->save($destination);         
        $resizedURL = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'resized/'.$width.'/'.$image;
        return $resizedURL;
  } 
}
