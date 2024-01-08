<?php
namespace Themevast\ThemevastUp\Model;

class Category extends \Magento\Catalog\Model\Category
{
    public function getImageUrlUp($image_type)
    {
        $url = false;
		if($image_type == 'image')
			$image = $this->getImage();
		if($image_type == 'tv_menu_icon_img')
			$image = $this->getData('tv_menu_icon_img');
		if($image_type == 'vc_menu_icon_img')
			$image = $this->getData('vc_menu_icon_img');
        if ($image) {
            if (is_string($image)) {
                $url = $this->_storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . 'catalog/category/' . $image;
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Something went wrong while getting the image url.')
                );
            }
        }
        return $url;
    }
    
}
