<?php

    if ( !defined( 'BASEPATH' ) )
        exit( 'No direct script access allowed' );

    /*
      |--------------------------------------------------------------------------
      | Base Site URL
      |--------------------------------------------------------------------------
      |
      | URL to your CodeIgniter root. Typically this will be your base URL,
      | WITH a trailing slash:
      |
      |	http://example.com/
      |
      | If this is not set then CodeIgniter will guess the protocol, domain and
      | path to your installation.
      |
     */
    
    $config['post_show_publish_date'] = FALSE;
    $config['post_show_updated_date'] = FALSE;
    $config['has_outbrain'] = FALSE;
    $config['has_disqus'] = FALSE;
    $config['country_filter'] = TRUE;
    
    $config['user_modified_view_count'] = TRUE;
    $config['user_modified_view_min'] = 11;
    $config['user_modified_view_max'] = 19;
    
    $config[ 'medium' ] = array(
        0 => 'Bangla',
        1 => 'English'

    );
    
    $config[ 'layout' ] = array(
        0 => '3-block-with-featured',
        1 => '3-block-custom'

    );
    
    $config[ 'free_user_types' ] = array(
        1 => 'Visitor',
        2 => 'Student',
        3 => 'Parents',
        4 => 'Teacher'
    );
    
    $config['replace_url'] = TRUE;
    $config['JS_CSS_V'] = "4.1.8";

    $config['special_char_for_related'] = array(
		"’"	=>  "&rsquo;",
		"“"	=>  "&ldquo;",
		"”"	=>  "&rdquo;"
	
    );

    $config['special_category']       = false;
    
    $config['special_category_id']       = 276;
     $config['special_category_title'] = "Budget 2014-15";
     $config['special_category_banner_image'] = false;
     $config['special_category_font_color'] = "#000";
    $config['special_category_background_color'] = "#ccc";
     $config['special_category_theme'] = "black";
    
	
	
    $config['otherboxtop']    = true;
     $config['otherboxtop_category_id'] = 278;
      $config['otherboxtop_limit']       = 5;
     $config['otherboxtop_title'] = "FIFA WORLD CUP 2014 BRAZIL";
     $config['otherboxtop_banner_image'] = false;
     $config['otherboxtop_link'] = "http://www.thedailystar.net/fifa-world-cup-2014-brazil";
 

    $config['staronlinevideos']              = true;
    
    $config['staronlinevideos_category_id']  = 266;
    $config['staronlinevideos_limit']        = 5;

    $config['issuedate_enable'] = FALSE;
    $config['tricker_enable']   = TRUE;

# Menu position array. This configaration determines whether the menu will be loaded
# at the header or the footer.
    $config[ 'menu_position' ] = array(null => 'Select', 1 => 'Header', 2 => 'Footer');

# Menu Types array. This configaration determines available menu types in the system.
    $config[ 'menu_types' ] = array(
        NULL => 'Select',
        1 => 'Category',
        2 => 'Text',
        3 => 'Icon',
        4 => 'News',
    );
    
# Menu Types array for Menu CRUD. This configaration determines available menu types in the system.
 $config[ 'menu_types_for_crud' ] = array(
     5 => 'Only Text',
     2 => 'Text',
     3 => 'Icon',
    
 );
###############################################################
#	MENU CONFIG FOR FOOTER GROUP
#
#
    $config[ 'footer_group' ] = array(
        0 => 'Newspaper',
        1 => 'OP_ED',
        2 => 'Magazine',
        3 => 'Sections',
        4 => 'Advertisement',
        5 => 'Other Pages '
    );
//$config['front_layout']	= 'layouts/main.php';
    $config[ 'front_layout' ] = 'views/layout/tdsfront/main.php';
    $config[ 'exclusive_news_count' ] = 1;

    
    
    $config['newspaper_menu'] = array(79,53,1,2,71,116,72,3,4,5);
    $config['newsarchive_menu'] = array(79,53,1,2,71,116,72,3,4,5);
    $config['supplements_menu'] = array(209,211,201);
##############################################################
#	GALLERY TYPE CONFIG
#
#
    $config[ 'gallery_type' ] = array(
        1 => 'Image',
        2 => 'Video',
        3 => 'Docs',
        4 => 'PDF',
        5 => 'Cartoon',
        6 => 'Podcast'
    );
    
    $config[ 'download_magazine_option' ] = "download"; //"download" Or "show"
    $config[ 'zero_comment_show' ] = TRUE;
    $config[ 'show_overlay' ] = TRUE;
    $config[ 'show_more' ] = FALSE;
    $config[ 'show_placeholder_for_no_images'] = FALSE;
    $config[ 'placeholder_path'] = "styles/layouts/tdsfront/images/noimage.jpg";
    
    $config['twitter_default_account'] = array(
            "name"      => "HuffasAbdullah",
            "widget_id" => "398368082709585921"
    );
    
    $config['index_gallery'] = array(
            "target"    => "othernews",
            "position"  => "bottom"
    );
    
    $config['business_gallery'] = array(
            "target"    => "middle",
            "position"  => "bottom"
    );
    
    $config['sports_gallery'] = array(
            "target"    => "middle",
            "position"  => "top"
    );

    $config['city_gallery'] = array(
            "target"    => "common",
            "position"  => "bottom"
    );

    $config['world_gallery'] = array(
            "target"    => "common",
            "position"  => "bottom"
    );	
    
    $config['op-ed_gallery'] = array(
            "target"    => "middle",
            "position"  => "top"
    );
    
    $config['entertainment_gallery'] = array(
            "target"    => "middle",
            "position"  => "top"
    );
    
    $config['lifestyle_gallery'] = array(
            "target"    => "middle",
            "position"  => "top"
    );
    
    $config['shout_gallery'] = array(
            "target"    => "middle",
            "position"  => "top"
    );
    
    $config['the-star_gallery'] = array(
            "target"    => "middle",
            "position"  => "top"
    );
    
    $config['showbiz_gallery'] = array(
            "target"    => "middle",
            "position"  => "top"
    );
    
    $config['supplements_gallery'] = array(
            "target"    => "middle",
            "position"  => "top"
    );
    
    $config['show_old_magazine_cover'] = TRUE;
    
    $config['news_images'] = array(
        'exclusive'  => array(
            'image_liquid'                  => true,
            'width'                         => 500,
            'width_prefix'                  => 'px',
            'height'                        => 250,
            'height_prefix'                 => 'px',
            'show_image_gallery'            => true,
            'show_video_gallery'            => true,
            'show_pdf_gallery'              => true,
            'show_caption'                  => true,
            'main_image'                    => 'lead',
            'lead_image'                    => true,
            'if_no_lead_show_news_images'   => true,
            'has_link'                      => true,
            'extra_styles'                  => "cursor: pointer;position: relative;",
            'extra_image_styles'            => ""
        ),
		'carrosel'  => array(
            'image_liquid'                  => true,
            'width'                         => 460,
            'width_prefix'                  => 'px',
            'height'                        => 230,
            'height_prefix'                 => 'px',
            'show_image_gallery'            => true,
            'show_video_gallery'            => true,
            'show_pdf_gallery'              => true,
            'show_caption'                  => true,
            'main_image'                    => 'lead',
            'lead_image'                    => true,
            'if_no_lead_show_news_images'   => true,
            'has_link'                      => true,
            'extra_styles'                  => "cursor: pointer;position: relative;",
            'extra_image_styles'            => ""
        ),
        'main_news_overlay'  => array(
            'image_liquid'                  => true,
            'width'                         => 80,
            'width_prefix'                  => 'px',
            'height'                        => 40,
            'height_prefix'                 => 'px',
            'show_image_gallery'            => false,
            'show_video_gallery'            => false,
            'show_pdf_gallery'              => false,
            'show_caption'                  => false,
            'main_image'                    => 'post',
            'lead_image'                    => true,
            'if_no_lead_show_news_images'   => true,
            'has_link'                      => false,
            'extra_styles'                  => "float: left; margin-right: 5px;",
            'extra_image_styles'            => ""
        ),
        'other_news_image_float'  => array(
            'image_liquid'                  => false,
            'width'                         => '163',
            'width_prefix'                  => 'px',
            'height'                        => 0,
            'height_prefix'                 => '',
            'show_image_gallery'            => false,
            'show_video_gallery'            => false,
            'show_pdf_gallery'              => false,
            'show_caption'                  => false,
            'main_image'                    => 'post',
            'lead_image'                    => true,
            'if_no_lead_show_news_images'   => true,
            'has_link'                      => false,
            'extra_styles'                  => "",
            'extra_image_styles'            => ""
        ),
        'other_news_image'  => array(
            'image_liquid'                  => false,
            'width'                         => '218',
            'width_prefix'                  => 'px',
            'height'                        => '',
            'height_prefix'                 => '',
            'show_image_gallery'            => false,
            'show_video_gallery'            => false,
            'show_pdf_gallery'              => false,
            'show_caption'                  => false,
            'main_image'                    => 'post',
            'lead_image'                    => true,
            'if_no_lead_show_news_images'   => true,
            'has_link'                      => false,
            'extra_styles'                  => "",
            'extra_image_styles'            => "margin-right: 15px;"
        ),
        'inner_top_common'  => array(
            'image_liquid'                  => false,
            'width'                         => '100%',
            'width_prefix'                  => '%',
            'height'                        => 0,
            'height_prefix'                 => '',
            'show_image_gallery'            => false,
            'show_video_gallery'            => false,
            'show_pdf_gallery'              => false,
            'show_caption'                  => false,
            'main_image'                    => 'lead',
            'lead_image'                    => true,
            'if_no_lead_show_news_images'   => true,
            'has_link'                      => false,
            'extra_styles'                  => "",
            'extra_image_styles'            => "margin: 10px 0;"
        ),
        'inner_main_story'  => array(
            'image_liquid'                  => true,
            'width'                         => 440,
            'width_prefix'                  => 'px',
            'height'                        => 230,
            'height_prefix'                 => 'px',
            'show_image_gallery'            => true,
            'show_video_gallery'            => true,
            'show_pdf_gallery'              => true,
            'show_caption'                  => true,
            'main_image'                    => 'lead',
            'lead_image'                    => true,
            'if_no_lead_show_news_images'   => true,
            'has_link'                      => true,
            'extra_styles'                  => "cursor: pointer;position: relative; margin:2.4px 0;",
            'extra_image_styles'            => ""
        ),
        'inner_main_topics'  => array(
            'image_liquid'                  => false,
            'width'                         => '60',
            'width_prefix'                  => 'px',
            'height'                        => 0,
            'height_prefix'                 => '',
            'show_image_gallery'            => false,
            'show_video_gallery'            => false,
            'show_pdf_gallery'              => false,
            'show_caption'                  => false,
            'main_image'                    => 'post',
            'lead_image'                    => true,
            'if_no_lead_show_news_images'   => true,
            'has_link'                      => false,
            'extra_styles'                  => "",
            'extra_image_styles'            => ""
        ),
        'inner_bottom'  => array(
            'image_liquid'                  => false,
            'width'                         => '178',
            'width_prefix'                  => 'px',
            'height'                        => 0,
            'height_prefix'                 => '',
            'show_image_gallery'            => false,
            'show_video_gallery'            => false,
            'show_pdf_gallery'              => false,
            'show_caption'                  => false,
            'main_image'                    => 'post',
            'lead_image'                    => true,
            'if_no_lead_show_news_images'   => true,
            'has_link'                      => false,
            'extra_styles'                  => "",
            'extra_image_styles'            => ""
        ),
        'weekly'  => array(
            'image_liquid'                  => false,
            'width'                         => '200',
            'width_prefix'                  => 'px',
            'height'                        => 0,
            'height_prefix'                 => '',
            'show_image_gallery'            => false,
            'show_video_gallery'            => false,
            'show_pdf_gallery'              => false,
            'show_caption'                  => false,
            'main_image'                    => 'lead',
            'lead_image'                    => true,
            'if_no_lead_show_news_images'   => true,
            'cover'                         => true,
            'has_link'                      => false,
            'extra_styles'                  => "",
            'extra_image_styles'            => ""
        ),
        'inner_common'  => array(
            'image_liquid'                  => false,
            'width'                         => '150',
            'width_prefix'                  => 'px',
            'height'                        => 0,
            'height_prefix'                 => '',
            'show_image_gallery'            => false,
            'show_video_gallery'            => false,
            'show_pdf_gallery'              => false,
            'show_caption'                  => false,
            'main_image'                    => 'post',
            'lead_image'                    => true,
            'if_no_lead_show_news_images'   => true,
            'has_link'                      => false,
            'extra_styles'                  => "",
            'extra_image_styles'            => ""
        ),
        'inner_common_first'  => array(
            'image_liquid'                  => false,
            'width'                         => '250',
            'width_prefix'                  => 'px',
            'height'                        => 0,
            'height_prefix'                 => '',
            'show_image_gallery'            => false,
            'show_video_gallery'            => false,
            'show_pdf_gallery'              => false,
            'show_caption'                  => false,
            'main_image'                    => 'post',
            'lead_image'                    => true,
            'if_no_lead_show_news_images'   => true,
            'has_link'                      => false,
            'extra_styles'                  => "",
            'extra_image_styles'            => ""
        ),
        'inner_other_topic_main'  => array(
            'image_liquid'                  => true,
            'width'                         => '200',
            'width_prefix'                  => 'px',
            'height'                        => '100',
            'height_prefix'                 => 'px',
            'show_image_gallery'            => false,
            'show_video_gallery'            => false,
            'show_pdf_gallery'              => false,
            'show_caption'                  => false,
            'main_image'                    => 'post',
            'lead_image'                    => true,
            'if_no_lead_show_news_images'   => true,
            'has_link'                      => false,
            'extra_styles'                  => "float: left; margin-right: 10px;",
            'extra_image_styles'            => ""
        ),
        'first_category_image'  => array(
            'image_liquid'                  => false,
            'width'                         => '152',
            'width_prefix'                  => 'px',
            'height'                        => 0,
            'height_prefix'                 => '',
            'show_image_gallery'            => false,
            'show_video_gallery'            => false,
            'show_pdf_gallery'              => false,
            'show_caption'                  => false,
            'main_image'                    => 'post',
            'lead_image'                    => true,
            'if_no_lead_show_news_images'   => true,
            'has_link'                      => false,
            'extra_styles'                  => "",
            'extra_image_styles'            => ""
        ),
        'second_category_image'  => array(
            'image_liquid'                  => false,
            'width'                         => '152',
            'width_prefix'                  => 'px',
            'height'                        => 0,
            'height_prefix'                 => '',
            'show_image_gallery'            => false,
            'show_video_gallery'            => false,
            'show_pdf_gallery'              => false,
            'show_caption'                  => false,
            'main_image'                    => 'post',
            'lead_image'                    => true,
            'if_no_lead_show_news_images'   => true,
            'has_link'                      => false,
            'extra_styles'                  => "",
            'extra_image_styles'            => ""
        ),
        'six_category_image'  => array(
            'image_liquid'                  => true,
            'width'                         => 222,
            'width_prefix'                  => 'px',
            'height'                        => 105,
            'height_prefix'                 => 'px',
            'show_image_gallery'            => false,
            'show_video_gallery'            => false,
            'show_pdf_gallery'              => false,
            'show_caption'                  => false,
            'main_image'                    => 'lead',
            'lead_image'                    => true,
            'if_no_lead_show_news_images'   => true,
            'has_link'                      => false,
            'extra_styles'                  => "",
            'extra_image_styles'            => ""
        ),
        'menu_image'  => array(
            'image_liquid'                  => false,
            'width'                         => 150,
            'width_prefix'                  => 'px',
            'height'                        => 0,
            'height_prefix'                 => 'px',
            'show_image_gallery'            => false,
            'show_video_gallery'            => false,
            'show_pdf_gallery'              => false,
            'show_caption'                  => false,
            'main_image'                    => 'lead',
            'lead_image'                    => true,
            'if_no_lead_show_news_images'   => true,
            'has_link'                      => false,
            'extra_styles'                  => "",
            'extra_image_styles'            => ""
        ),
    );
    
    $config[ 'carrosel_news_count' ] = 1;
    $config[ 'main_news_count' ] = 4;
    $config[ 'other_box_news_count' ] = 6;
    $config[ 'more_news_count' ] = 50;
    $config[ 'categories_news_count' ] = 8;

    $config[ 'outbrain_url' ] = "http://www.thedailystar.net/beta2/";

    $config[ 'disqus_short_name' ] = "thedailystar001";
  


    $config[ 'sports' ] = array(
        "total_news"    => 15,
        "top_news" => array(
            "carrosel"   => false,
            "news_count" => 1,
            "show_lead_image" => true,
            "img_position"  => array("none"),
            "sub_category" => 0,
            "has_more"  => false
        ),
        "main_news" => array(
            "news_count" => 3,
            "image_link" => "",
            "columnist" => false
        ),
        "middle_left" => array(
            "category_ids_with_news" => array(),
            "news_count" => 5,
            "has_image"  => array(true, true, true, true, true),
            "image_position"  => array('left','right','left','right','left')
        ),
        "middel_right" => array(
            "news_from"  => "category_type_id",
            "category_type_id"  => 9,
            "catgory_count"     => 2,
            "news_count" => 1,
            "show_image" => true,
            "show_content" => false,
            "box-height" => 270,
            "not_show_dot_dot" => true
        ),
        "middel_right_widget" =>array('innermiddlenewsblock'),
        "footer_widget" =>array('whattowatch')
    );


    $config[ 'business' ] = array(
        "total_news"    => 15,
        "top_news" => array(
            "news_count" => 1,
            "show_lead_image" => true,
            "img_position"  => array("none"),
            "sub_category" => 0,
            "has_more"  => false
        ),
        "main_news" => array(
            "news_count" => 3,
            "image_link" => "",
            "columnist" => false
        ),
        "middle_left" => array(
            "category_ids_with_news" => array(),
            "news_count" => 5,
            "has_image"  => array(true, true, true, true, true),
            "image_position"  => array('left','right','left','right','left')
        ),
        "middel_right" => array(
            "news_from"  => "category_type_id",
            "category_type_id"  => 9,
            "catgory_count"     => 1,
            "news_count" => 1,
            "show_image" => true,
            "show_content" => true,
            "box-height" => 500,
            "not_show_dot_dot" => true,
            "show_byline" => true
        ),
        "middel_right_widget" =>array('innermiddlenewsblock')
    );

   /*$config[ 'bytes' ] = array(
        "total_news"    => 15,
        "top_news" => array(
            "news_count" => 1,
            "show_lead_image" => true,
            "img_position"  => array("none"),
            "sub_category" => 0,
            "has_more"  => false
        ),
        "main_news" => array(
            "news_count" => 3,
            "image_link" => "",
            "columnist" => false
        ),
        "middle_left" => array(
            "category_ids_with_news" => array(),
            "news_count" => 5,
            "has_image"  => array(true, true, true, true, true),
            "image_position"  => array('left','right','left','right','left')
        ),
        "middel_right" => array(
            "news_from"  => "category_type_id",
            "category_type_id"  => 9,
            "catgory_count"     => 2,
            "news_count" => 1,
            "show_image" => true,
            "show_content" => true,
            "box-height" => 360,
            "not_show_dot_dot" => false,
            "show_byline" => false
        ),
        "middel_right_widget" =>array('innermiddlenewsblock')
    );*/

    $config[ 'entertainment' ] = array(
        "total_news"    => 15,
        "top_news" => array(
		 "carrosel"   => false,
            "news_count" => 1,
            "show_lead_image" => true,
            "img_position"  => array("none"),
            "sub_category" => 0,
            "has_more"  => false
        ),
        "main_news" => array(
            "news_count" => 3,
            "image_link" => "",
            "columnist" => false
        ),
        "middle_left" => array(
            "category_ids_with_news" => array(),
            "news_count" => 7,
            "has_image"  => array(true, true, true, true, true, true, true),
            "image_position"  => array('left','right','left','right','left','right','left')
        ),
        "middel_right" => array(
            "news_from"  => "category_id",
            0 => array(
                "category_id" => 179,
                "news_count" => 2,
                "show_image" => true,
                "show_content" => false,
                "box-height" => 270,
                "not_show_dot_dot" => true
            ),
            1 => array(
                "category_id" => 180,
                "news_count" => 1,
                "show_image" => true,
                "show_content" => false,
                "box-height" => 270,
                "not_show_dot_dot" => true
            )
        ),
        "middel_right_widget" =>array('innermiddlenewsblock','whatson')
    );
    $config[ 'op-ed' ] = array(
        "total_news"    => 15,
        "top_news" => array(
	    "carrosel"   => true,
            "news_count" => 2,
            "show_lead_image" => true,
            "img_position"  => array("left","right"),
            "sub_category" => 71,
            "has_more"  => false
        ),
        "main_news" => array(
            "news_count" => 0,
            "image_link" => "",
            "columnist" => true
        ),
        "middle_left" => array(
            "sub_category" => 173,
            "news_count" => 5,
            "columnist" => false,
            "has_image"  => array(true, true, true, true, true),
            "image_position"  => array('left','right','left','right','left')
        ),
        "middel_right" => array(
            "news_from"  => "quotes",
            0 => array(
                "tables"    => "quotes",
                "news_count" => 1,
                "box-height" => 270,
                "not_show_dot_dot" => true,
                "layout" => "quotes"
            ),
            "layout" => "quotes"
        ),
        "bottom_news" => array(
            "news_count" => 100,
            "sub_category" => 84,
            "columnist" => false
        ),
        "middel_right_widget" =>array('innermiddlenewsblock')
        
    );

   $config[ 'icc-world-t20-bangladesh-2014' ] = array(
        "total_news"    => 250
    );

   $config[ 'apollo hospitals' ] = array(
        "total_news"    => 250
    );


   
   $config[ 'recipes' ] = array(
        "total_news"    => 15
    );
    
    $config[ 'newspaper' ] = array(
        "total_news"    => 20,
        "type"          => "true"
    );
    
    $config[ 'newsarchive' ] = array(
        "total_news"    => 20,
        "type"          => "true"
    );

    $config[ 'supplements' ] = array(
        "total_news"    => 10,
        "type"          => "true"
    );
 		
	    
    $config[ 'common' ] = array();
    
    //$config[ 'title' ] = array(
//        'common' => 'getCommonTitle',
//        'custom' => 'getCustomTitle',
//    );
    
    $config[ 'title' ] = array(
        'custom' => true,
    );

    # Menu Types array for Menu CRUD. This configaration determines available menu types in the system.
    $config[ 'archive' ] = array(
        0 => array("last_date"  => "2013-12-01", "server"   => "current"),
        1 => array("last_date"  => "2013-03-05", "server"   => "http://www.thedailystar.net/beta2/newspaper/?date="),
        2 => array("last_date"  => "2007-08-15", "server"   => "http://archive.thedailystar.net/newDesign/archive.php?date="),
        3 => array("last_date"  => "1980-01-01", "server"   => "http://archive.thedailystar.net/")
    );
    
    $config["shout_widget"] =array(
        array("id"=>231,"name"=>"buletin_news","field"=>array("headline"),"number_of_news"=>6,"top_image"=>"buletin"),
        array("id"=>232,"name"=>"comic_strip","field"=>array("shoulder","image"),"number_of_news"=>1,"image_size"=>304,'image_class'=>''),
        array("id"=>233,"name"=>"untranslatable","field"=>array("shoulder","image","headline","news"),"number_of_news"=>1,"image_size"=>304,"image_class"=>'')
        
    );
    $config["the-star_widget"] =array(
        array("id"=>227,"name"=>"letters", "field"=>array("headline"), "number_of_news"=>6,"top_image"=>"letter",),
        array("id"=>228,"name"=>"chintito","field"=>array("headline","image","news"),"number_of_news"=>1,"image_size"=>304,'image_class'=>'',"top_image"=>"chintito"),
        array("id"=>230,"name"=>"the_wall","field"=>array("image"),"number_of_news"=>1,"image_size"=>304,'image_class'=>'',"top_image"=>"the-wall"),
        array("id"=>229,"name"=>"home_and_abroad_news","field"=>array("image","headline"),"number_of_news"=>6,"top_image"=>"home-abroad","image_size"=>60,'image_class'=>'floatLeft'),
    );
    $config["showbiz_widget"] =array(
        array("id"=>234,"name"=>"wannabiz","field"=>array("shoulder","image","headline"),"number_of_news"=>1,"image_size"=>304,'image_class'=>'')
    );
    $config['extra_category_the-star'] = array(
        array('id'=>array(242, 241), "name"=>"1_minute","field"=>array("image","headline"),"number_of_news"=>2,),
        array('id'=>243, "name"=>"science_news","field"=>array("image","headline"),"number_of_news"=>4,),
    );
    $config['magazin_extra_gallery_shout'] = array("id"=>236,"name"=>"kaleidoscope", "field"=>array("shoulder","all_image","headline","news"));
    $config['magazin_extra_gallery_showbiz'] = array("id"=>237,"name"=>"film_review", "field"=>array("shoulder","all_image","headline","news"));
    
	$config[ 'rss_category' ] = array(79,53,71,187,116,1,3,2,72,92,5,4,84,167,132,85,83,109,137,142,120,55,207,76);
    $config[ 'rss_category_women' ] = array(219, 221,222,223,220,73,225,61);