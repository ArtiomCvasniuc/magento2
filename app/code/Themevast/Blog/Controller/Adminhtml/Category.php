<?php

namespace Themevast\Blog\Controller\Adminhtml;

class Category extends Actions
{
	
    protected $_formSessionKey  = 'blog_category_form_data';

    
    protected $_allowedKey      = 'Themevast_Blog::category';

    
    protected $_modelClass      = 'Themevast\Blog\Model\Category';

   
    protected $_activeMenu      = 'Themevast_Blog::category';


    protected $_statusField     = 'is_active';
}