<?php
namespace Themevast\ThemevastUp\Model\Category;

class DataProvider extends \Magento\Catalog\Model\Category\DataProvider
{
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $category = $this->getCurrentCategory();
        if ($category) {
            $categoryData = $category->getData();
            $categoryData = $this->addUseDefaultSettings($category, $categoryData);
            $categoryData = $this->addUseConfigSettings($categoryData);
            $categoryData = $this->filterFields($categoryData);
            if (isset($categoryData['image'])) {
                unset($categoryData['image']);
                $categoryData['image'][0]['name'] = $category->getData('image');
                $categoryData['image'][0]['url'] = $category->getImageUrlUp('image');
            }
			if (isset($categoryData['tv_menu_icon_img'])) {
                unset($categoryData['tv_menu_icon_img']);
                $categoryData['tv_menu_icon_img'][0]['name'] = $category->getData('tv_menu_icon_img');
                $categoryData['tv_menu_icon_img'][0]['url'] = $category->getImageUrlUp('tv_menu_icon_img');
            }
			 if (isset($categoryData['vc_menu_icon_img'])) {
                unset($categoryData['vc_menu_icon_img']);
                $categoryData['vc_menu_icon_img'][0]['name'] = $category->getData('vc_menu_icon_img');
                $categoryData['vc_menu_icon_img'][0]['url'] = $category->getImageUrlUp('vc_menu_icon_img');
            }
            $this->loadedData[$category->getId()] = $categoryData;
        }
        return $this->loadedData;
    }

    protected function getFieldsMap()
    {
        return [
            'general' =>
                [
                    'parent',
                    'path',
                    'is_active',
                    'include_in_menu',
                    'name',
                ],
            'content' =>
                [
                    'image',
                    'description',
                    'landing_page',
                ],
            'display_settings' =>
                [
                    'display_mode',
                    'is_anchor',
                    'available_sort_by',
                    'use_config.available_sort_by',
                    'default_sort_by',
                    'use_config.default_sort_by',
                    'filter_price_range',
                    'use_config.filter_price_range',
                ],
            'search_engine_optimization' =>
                [
                    'url_key',
                    'url_key_create_redirect',
                    'use_default.url_key',
                    'url_key_group',
                    'meta_title',
                    'meta_keywords',
                    'meta_description',
                ],
            'assign_products' =>
                [
                ],
            'design' =>
                [
                    'custom_use_parent_settings',
                    'custom_apply_to_products',
                    'custom_design',
                    'page_layout',
                    'custom_layout_update',
                ],
            'schedule_design_update' =>
                [
                    'custom_design_from',
                    'custom_design_to',
                ],
            'category_view_optimization' =>
                [
                ],
            'category_permissions' =>
                [
                ],
			'custom-menu' =>
                [
				'tv_menu_hide_item',
				'tv_menu_type',
				'tv_menu_static_width',
				'tv_menu_cat_columns',
				'tv_menu_float_type',
				'tv_menu_cat_label',
				'tv_menu_icon_img',
				'tv_menu_font_icon',
				'tv_menu_block_top_content',
				'tv_menu_block_left_width',
				'tv_menu_block_right_width',
				'tv_menu_block_left_content',
				'tv_menu_block_right_content',
				'tv_menu_block_bottom_content',
                ],
			'vertical-menu' =>
                [
				'vc_menu_hide_item',
				'vc_menu_type',
				'vc_menu_static_width',
				'vc_menu_cat_columns',
				'vc_menu_float_type',
				'vc_menu_cat_label',
				'vc_menu_icon_img',
				'vc_menu_font_icon',
				'vc_menu_block_top_content',
				'vc_menu_block_left_width',
				'vc_menu_block_right_width',
				'vc_menu_block_left_content',
				'vc_menu_block_right_content',
				'vc_menu_block_bottom_content',
                ],
        ];
    }
}
