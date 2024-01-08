<?php
namespace Themevast\ThemevastUp\Controller\Adminhtml\Category;

class Save extends \Magento\Catalog\Controller\Adminhtml\Category\Save
{
	private $storeManager;
	
    protected function _filterCategoryPostData(array $rawData)
    {
        $data = $rawData;
        if (isset($data['image']) && is_array($data['image'])) {
            if (!empty($data['image']['delete'])) {
                $data['image'] = null;
            } else {
                if (isset($data['image'][0]['name']) && isset($data['image'][0]['tmp_name'])) {
                    $data['image'] = $data['image'][0]['name'];
                } else {
                    unset($data['image']);
                }
            }
        }
		
		if (isset($data['tv_menu_icon_img']) && is_array($data['tv_menu_icon_img'])) {
            if (!empty($data['tv_menu_icon_img']['delete'])) {
                $data['tv_menu_icon_img'] = null;
            } else {
                if (isset($data['tv_menu_icon_img'][0]['name']) && isset($data['tv_menu_icon_img'][0]['tmp_name'])) {
                    $data['tv_menu_icon_img'] = $data['tv_menu_icon_img'][0]['name'];
                } else {
                    unset($data['tv_menu_icon_img']);
                }
            }
        }
		
		if (isset($data['vc_menu_icon_img']) && is_array($data['vc_menu_icon_img'])) {
            if (!empty($data['vc_menu_icon_img']['delete'])) {
                $data['vc_menu_icon_img'] = null;
            } else {
                if (isset($data['vc_menu_icon_img'][0]['name']) && isset($data['vc_menu_icon_img'][0]['tmp_name'])) {
                    $data['vc_menu_icon_img'] = $data['vc_menu_icon_img'][0]['name'];
                } else {
                    unset($data['vc_menu_icon_img']);
                }
            }
        }
		
        return $data;
    }

  //   public function imagePreprocessing($data)
  //   {
  //       if (empty($data['image'])) {
  //           unset($data['image']);
  //           $data['image']['delete'] = true;
  //       }
		
		// if (empty($data['tv_menu_icon_img'])) {
  //           unset($data['tv_menu_icon_img']);
  //           $data['tv_menu_icon_img']['delete'] = true;
  //       }
		
  //       if (empty($data['vc_menu_icon_img'])) {
  //           unset($data['vc_menu_icon_img']);
  //           $data['vc_menu_icon_img']['delete'] = true;
  //       }
  //       return $data;
  //   }
}
