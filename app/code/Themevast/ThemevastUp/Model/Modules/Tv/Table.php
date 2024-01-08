<?php 
namespace Themevast\ThemevastUp\Model\Modules\Tv;
class Table
{
	public function getTvTables() {
		$tv_tables = [
		"Themevast_Blog" => ["themevast_blog_category","themevast_blog_category_store","themevast_blog_post","themevast_blog_post_category","themevast_blog_post_relatedpost","themevast_blog_post_relatedproduct","themevast_blog_post_store"],
		"Themevast_Brand" => ["tv_brand","tv_brand_store"],
		"Themevast_SlideBanner" => ["themevast_slide", "themevast_slider"],
		"Themevast_Testimonials" => ["tv_testimonials", "tv_testimonials_store"],
		"Themevast_MegaMenu" => ["themevast_megamenu"],
		"Themevast_PriceCountdown"=>["themevast_pricecountdown"],
		"Themevast_Faqs" => ["tv_faq", "tv_faq_store","tv_faq_category","tv_faq_category_store","tv_faq_category_id"],
		"Magento_Cms_Page" => ["cms_page", "cms_page_store"],
		"Magento_Cms_Block" => ["cms_block", "cms_block_store"],
		"Magento_Widget" => ["widget", "widget_instance", "widget_instance_page", "widget_instance_page_layout", "core_layout_link", "core_layout_update"]
		];
		return $tv_tables;
	}	
}