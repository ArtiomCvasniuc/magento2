<?php
namespace Themevast\Categorytab\Helper;

class Image extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $_imageHelper;

	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Catalog\Helper\Image $imageHelper
		){
		$this->_imageHelper = $imageHelper;
		parent::__construct($context);
	}
	
	public function getImgRize($product, $w=570, $h, $imgVersion='image', $file=NULL)
	{
		if (!$h || (int)$h == 0){
			$image = $this->_imageHelper
			->init($product, $imgVersion)
			->constrainOnly(true)
			->keepAspectRatio(true)
			->keepFrame(false);
			if($file){
				$image->setImageFile($file);
			}
			$image->resize($w);
			return $image;
		}else{
			$image = $this->_imageHelper
			->init($product, $imgVersion);
			if($file){
				$image->setImageFile($file);
			}
			$image->resize($w, $h);
			return $image;
		}
	}

	public function getAltImgHtmlResize($product, $w, $h, $imgVersion='small_image', $column = 'position', $value = 1)
	{
		$product->load('media_gallery');
		if ($images = $product->getMediaGalleryImages())
		{
			$image = $images->getItemByColumnValue($column, $value);
			if(isset($image) && $image->getUrl()){
				$imgAlt = $this->getImg($product, $w, $h, $imgVersion , $image->getFile());
				if(!$imgAlt) return '';
				return $imgAlt;
			}else{
				return '';
			}
		}
		return '';
	}
}