<?php
/**
 * JReviews - Reviews Extension
 * Copyright (C) 2010-2012 ClickFWD LLC
 * This is not free software, do not distribute it.
 * For licencing information visit http://www.reviewsforjoomla.com
 * or contact sales@reviewsforjoomla.com
**/

defined( 'MVC_FRAMEWORK') or die( 'Direct Access to this location is not allowed.' );

class ConfigComponent extends S2Component 
{
	var $version = null;
//general tab
    var $libraries_jquery = 0;
    var $libraries_jqueryui = 0;
	var $name_choice = "realname";
	var $template = "default";
    var $mobile_theme = "default";
    var $fallback_theme = "default";
	var $template_path = "/components/com_jreviews";
    var $ie6pngfix = 0;
	var $display_list_limit = 0;
	var $url_param_joomla = 1;
	var $paginator_mid_range = 6;
// breadcrumb    
    var $dir_show_breadcrumb = "1"; // JReviews 
    var $breadcrumb_detail_override = "0"; 
    var $breadcrumb_detail_directory = "0"; 
    var $breadcrumb_detail_section = "1"; 
    var $breadcrumb_detail_category = "1"; 
// 3rd party integration
    var $joomfish_plugin = false;   
// Community
    var $community = null;
    var $social_sharing_detail = 0;
    // Jomsocial integration
    var $jomsocial_tnmode =  'crop';
    var $jomsocial_tnsize =  65;
    var $jomsocial_listings =  1;
    var $jomsocial_reviews =  1;
    var $jomsocial_discussions =  1;
    var $jomsocial_favorites =  1;
    var $jomsocial_votes =  1;
    //Twitter integration
    var $twitter_enable = 0;
    var $twitter_oauth = ''; 
    var $twitter_listings = 0;
    var $twitter_reviews = 0;
    var $twitter_discussions = 0;
    var $bitly_user = '';
    var $bitly_key = '';
    var $bitly_history = 0;
    // Facebook
    var $facebook_enable = 0;
    var $facebook_send = 0;
    var $facebook_optout = 0;
    var $facebook_appid;
    var $facebook_admins;
    var $facebook_opengraph = 1;
    var $facebook_secret;
    var $facebook_listings = 0;
    var $facebook_reviews = 0;
    var $facebook_votes = 0; 
    var $facebook_posts_trim = '';   
    //access tab
	var $security_image = "1,2";
	var $moderation_item = "1,2";
	var $moderation_item_edit = "0";
	var $moderation_reviews = "1,2";
	var $moderation_editor_reviews = "0";
	var $editaccess = "7,8";
    var $listing_publish_access = "7,8";
    var $listing_delete_access = "7,8";
	var $editaccess_reviews = "7,8";
	var $moderation_review_edit = "0";
	var $moderation_editor_review_edit = "0";
	var $addnewaccess = "7,8";
	var $addnewaccess_reviews = "2,3,4,5,6,7,8";
	var $addnewwysiwyg = "7,8";
	var $addnewmeta = "7,8";
	var $user_vote_public = "1,2,3,4,5,6,7,8";
	var $user_owner_disable	= "0";	    
    var $addnewaccess_posts = "2,3,4,5,6,7,8";
    var $moderation_posts = "1,2";
    var $post_edit_access = "2,3,4,5,6,7,8";
    var $post_delete_access = "2,3,4,5,6,7,8";    
    var $moderation_owner_replies = "2"; 
//directory tab
	var $dir_show_alphaindex = "1";
	var $dir_columns = "2";
	var $dir_cat_num_entries = "1";
	var $dir_cat_images = 'None';
    var $dir_cat_format = "1";
	var $dir_section_order = "1";
	var $dir_category_order = "1";
	var $dir_category_limit = "0";
	var $dir_category_hide_empty = "0";
    var $dir_category_levels = "2";
//item list tab
	var $list_display_type = "1";
	var $list_display_type_joomla = "blogjoomla_simple";
	var $list_show_addnew	= "1";
    var $list_addnew_menuid = "1"; // Keep current itemid in url
	var $list_show_sectionlist = "1";
	var $list_show_searchbox = "1";
	var $list_show_orderselect = "1";
	var $list_order_default	= "alpha";
    var $list_order_field   = "";
	var $list_show_categories = "1"; // category list
	var $list_show_categories_section = "1"; // section list
    var $list_show_child_listings = "1"; // J16 only
	var $list_show_date = "1";
	var $list_show_author = "1";
	var $list_show_user_rating = "1";
	var $list_show_hits = "1";
	var $list_show_readmore = "1";
	var $list_show_readreviews = "1";
	var $list_show_newreview = "1";
	var $list_show_image = "1";
    var $list_thumb_mode = 'scale';
	var $list_image_resize = "150";
	var $list_category_image = "0";
	var $list_noimage_image = "0";
	var $list_noimage_filename = "noimage.png";
	var $list_show_abstract = "1";
	var $list_abstract_trim = "30";
	var $list_new = "1";
	var $list_new_days = "10";
	var $list_hot = "1";
	var $list_hot_hits = "1000";
	var $list_featured = "1";
	var $cat_columns = "3";
	var $list_limit = "10";
    // listing comparison
    var $list_compare = 0;
    var $list_compare_columns = 3;
    var $list_compare_user_ratings = 0;
    var $list_compare_editor_ratings = 0;    
    // listings tab
    var $favorites_enable = 1;
    var $claims_enable = 0;    
    var $claims_enable_userids = '42';
    var $inquiry_recipient = 'owner';
    var $inquiry_enable = 0;    
    var $inquiry_field = '';
    var $inquiry_bcc = '';        
	//reviews tab
	var $location = "0";
	var $location_places = "";
	var $show_criteria_rating_count = "2";
    var $owner_replies = "1";
    var $review_discussions = "1";
    var $user_multiple_reviews  = "1";
    var $review_ipcheck_disable = 0;
    var $vote_ipcheck_disable = 0;
    var $user_review_order = 'rdate';
    var $ranks_rebuild_interval = 3;
    var $ranks_rebuild_last = 0; 
        //=> ratings
    var $rating_scale = "5";
    var $rating_increment = "1";
    var $rating_selector = "stars"; // stars
    var $rating_graph = "1";  
    var $rating_default_na = "1";
    var $rating_hide_na = "0";     
		//=> author reviews
	var $author_review = "0";
	var $authorids = "42";
	var $author_vote = "1";
	var $author_report = "1";
	var $author_forum = "";
	var $author_ratings = "1"; // detailed ratings box
	var $author_rank_link = "1";
	var $author_myreviews_link = "1";
    var $editor_rank_exclude = "0";
	var $editor_limit = "5";
		// => user reviews
	var $user_reviews = "1";
	var $user_vote = "1";
	var $user_report = "1";
	var $user_forum = "";
	var $user_ratings = "1"; // detailed ratings box
	var $user_rank_link = "1";
	var $user_myreviews_link = "1";
	var $user_limit = "5";
	//images tab
	var $content_images = "4";
	var $content_images_edit = "1";
	var $content_images_total_limit = "0";
	var $content_max_imgsize = "300";
	var $content_max_imgwidth = "0";
	var $content_thumb_size = "65";
	var $content_intro_img_size = "230";
	var $content_intro_img = "1";
    var $content_gallery = "1";
	var $content_default_image = "0";
	//standard fields tab
	var $content_title_duplicates = 'category';
    var $content_name = "required";
    var $content_email = "required";
	var $content_title = "1";
	var $content_summary = "required";
	var $content_description = "optional";
	var $content_pathway = "1";
	var $content_show_reviewform = "authors";
    var $reviewform_title = "required";    
    var $reviewform_name = "required";
    var $reviewform_email = "required";
    var $reviewform_comment = "required";
    var $reviewform_optional = "1";    
    var $discussform_name = "required";
    var $discussform_email = "required";
	//search tab
	var $search_itemid = "0";
	var $search_itemid_hc = "";
	var $search_display_type = "1";
    var $search_return_all = "0";
	var $search_tmpl_suffix = "";      
	var $search_item_author = "0";
	var $search_field_conversion = "0";
    var $search_archived = "0";
    var $search_one_result = "1";  
    var $search_cat_filter = "0";  
	//notification tab
	var $notify_review = "0";
	var $notify_content = "0";
	var $notify_report = "0";
	var $notify_review_emails;
	var $notify_content_emails;
    var $notify_report_emails;
    var $notify_user_listing = "0";
    var $notify_user_listing_emails;
    var $notify_owner_review = "0";
    var $notify_owner_review_emails;
    var $notify_user_review = "0";
    var $notify_user_review_emails;
    var $notify_review_post = "0";
    var $notify_review_post_emails;
    var $notify_owner_reply = "0";
    var $notify_owner_reply_emails; 
    var $notify_claim = "0";
    var $notify_claim_emails;           
	//rss tab
	var $rss_enable = "0";
	var $rss_limit = "10";
	var $rss_title;
	var $rss_image;
	var $rss_description;
	var $rss_item_images= "0";
	var $rss_item_image_align = "right";
	//seo manager
	var $seo_title = "0";
	var $seo_description = "0";
	//seo manager
	var $cache_disable = "0";
	var $cache_query = "0";
	var $cache_expires = "3600";
	var $cache_cleanup = "7200";
    var $cache_view = "0";
    var $cache_session = "1";
    // Remote updater
    var $updater_betas = "0"; 
 
	function startup(&$controller = null)
	{        
        # Use different default values for J15 access settings
        if(getCmsVersion() == CMS_JOOMLA15)
        {
            $this->security_image = "0,18";
            $this->moderation_item = "0,18";
            $this->moderation_reviews = "0,18";
            $this->editaccess = "24,25";
            $this->listing_publish_access = "24,25";
            $this->listing_delete_access = "24,25";
            $this->editaccess_reviews = "24,25";
            $this->addnewaccess = "24,25";
            $this->addnewaccess_reviews = "18,19,20,21,23,24,25";
            $this->addnewwysiwyg = "24,25";
            $this->addnewmeta = "24,25";
            $this->user_vote_public = "0,18,19,20,21,23,24,25";
            $this->addnewaccess_posts = "18,19,20,21,23,24,25";
            $this->moderation_posts = "0,18";
            $this->post_edit_access = "18,19,20,21,23,24,25";
            $this->post_delete_access = "18,19,20,21,23,24,25";
            $this->moderation_owner_replies = "18";    
            $this->claims_enable_userids = "62";   
            $this->authorids = "62";          
        }
        
		if($Config = Configure::read('JreviewsSystem.Config'))
		{
			$this->merge($Config);
		} else
        {
			$cache_file = 'jreviews_config_'.md5(cmsFramework::getConfig('secret'));
			
			$Config = S2Cache::read($cache_file);

			if(false == $Config || empty($Config)) {
				$Config = $this->load();
				S2Cache::write($cache_file,$Config);
			}
			$this->merge($Config);				
			Configure::write('JreviewsSystem.Config',$Config);
		}
		
		($this->url_param_joomla == '1' && !defined('URL_PARAM_JOOMLA_STYLE')) and define('URL_PARAM_JOOMLA_STYLE',1);

		Configure::write('System.version',strip_tags($this->version));
		Configure::write('Theme.name',$this->template);
		Configure::write('Community.extension', $this->community);
		Configure::write('Cache.enable',true);
		Configure::write('Cache.disable',false);
		Configure::write('Cache.expires',$this->cache_expires);
		Configure::write('Cache.query',(bool)$this->cache_query);
		Configure::write('Cache.view',(bool)$this->cache_view);
        Configure::write('Cache.session',!defined('MVC_FRAMEWORK_ADMIN') && (bool)$this->cache_session);
        Configure::write('Jreviews.editor_rank_exclude',(bool)$this->editor_rank_exclude);
	}
	
	function load() 
    {
		$Model = new MyModel();
		
		$Config = new stdClass();
		
		$Model->_db->setQuery("SELECT id, value FROM #__jreviews_config");
		
		$rows = $Model->_db->loadObjectList();

		if ($rows)
		{
			foreach ($rows as $row)
			{
				$prop = $row->id;
                $length = strlen($row->value)-1;   
                if(substr($row->value,0,1)=='[' && substr($row->value,$length,1)==']')
                {
                    $row->value = json_decode($row->value);
                } else {
                    $row->value = stripcslashes($row->value);
                }
				$Config->$prop = $row->value;
			}
		}		
		
        $Config->rss_title = @$Config->rss_title != '' ? $Config->rss_title : $Model->makeSafe(cmsFramework::getConfig('sitename'));
        $Config->rss_description = @$Config->rss_description != '' ? $Config->rss_description : $Model->makeSafe(cmsFramework::getConfig('MetaDesc'));		

		# Get current version number                               
        include_once(PATH_ROOT . 'components' . DS . 'com_jreviews' . DS . 'jreviews.info.php' );
        if(isset($package_info))
        {
            $version = $package_info['jreviews']['version'];
            $Config->version = $version;
        }
        return $Config;	
	}
	
	function merge(&$Config) 
    {
		foreach($Config AS $key=>$value) {
			$this->{$key} = $value;
		}
	}

	function store($arr = null)
	{                 
		$db = cmsFramework::getDB();
		
        if(is_null($arr))
        {
            $arr = get_object_vars($this);            
        }
	
		while (list($prop, $val) = each($arr)) 
		{
			if($prop != 'c') 
			{    
                if(is_array($val)){
                    $val = json_encode($val);
                }
                else
                {
                    // Fixes an issue where an Array string is added to some values
                    $val = str_replace(',Array','',$val);
                }
                                   
				$db->setQuery("
                    UPDATE 
                        #__jreviews_config
					SET 
                        value= '" . $db->getEscaped($val) . "'
					WHERE 
                        id = '" . $db->getEscaped($prop) . "'"
				);
				
                $db->query();
				
				$db->setQuery(
					"SELECT 
                        count(*) 
                    FROM 
                        #__jreviews_config 
                    WHERE 
                        id = '" . $db->getEscaped($prop) . "'"
				);
				
				$saved = $db->loadResult();
				
				if (!$saved) 
				{           
					$db->setQuery("
                        INSERT INTO
                            #__jreviews_config (id, value) 
						VALUES
                            ('" . $db->getEscaped($prop) . "', '" . $db->getEscaped($val) . "')
                    ");
					
					if (!$db->query()) {
						echo "<br/>".$db->getErrorMsg();
						exit;
					}				
				}
            }
		}

        if(defined('MVC_FRAMEWORK_ADMIN')) {
            // Forces clear cache when config settings are modified in the administration
            clearCache('','views');
            clearCache('','__data');
        }
        else {
            // Push updates to the cached file
            $cache_file = 'jreviews_config_'.md5(cmsFramework::getConfig('secret'));
            $Config = $this->load();
            S2Cache::write($cache_file,$Config);                
        }
    }

	function bindRequest($request)
	{
		$arr = get_object_vars($this);
		while (list($prop, $val) = each($arr))
			$this->$prop = Sanitize::getVar($request, $prop, $val);
	} // bindRequest
    
    function override($config_overrides)
    {   
        if(empty($config_overrides)) return;
        if(!is_array($config_overrides)) $config_overrides = json_decode($config_overrides,true);
        $Config = Configure::read('JreviewsSystem.Config');    
        $override_ban = Configure::read('JreviewsSystem.OverrideBan',array());
        foreach($config_overrides AS $key=>$value)
        {
            if(!in_array($key,$override_ban) && $value != -1 and $value != '')
            {
                $this->{$key} = $Config->{$key} = $value; 
            }   
        }
        Configure::write('JreviewsSystem.Config',$Config);
    }
    
    function getOverride($var,$config)
    {         
        $override = Sanitize::getVar($config,$var,-1);
        return $override != -1 && $override != '' ? $override : $this->{$var};
    }
}
