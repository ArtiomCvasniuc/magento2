<?php

namespace Themevast\Blog\Controller\Adminhtml;

class Post extends Actions
{
	
    protected $_formSessionKey  = 'blog_post_form_data';

    
    protected $_allowedKey      = 'Themevast_Blog::post';

    
    protected $_modelClass      = 'Themevast\Blog\Model\Post';

   
    protected $_activeMenu      = 'Themevast_Blog::post';

    
    protected $_statusField     = 'is_active';

   
    protected $_paramsHolder 	= 'post';
}
