<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$config[ 'news_in_index' ] = array(
    'show_old_news'         => TRUE,
    'days_to_retrieve_news' => "-30 days"
);

$config['world_cup_quize_link']   = '/cricaddict/day-2-2-13/1';
$config['api_url']   = 'http://api.champs21.com/api/freeuser';
$config['paid_auth_api_url']   = 'http://api.champs21.com/api/user/auth';

$config['android_app_dl_popup_show']   = TRUE;
$config['android_app_dl_url']   = 'https://play.google.com/store/apps/details?id=com.champs21.schoolapp';

$config['education_changes_life'] = TRUE;

$config[ 'from_api' ] = TRUE;
$config[ 'go_to_assessment' ] = TRUE;

$config[ 'normal_view_count_add' ] = 1;

$config[ 'wow_login' ] = false;

$config[ 'api_index' ] = array('index','inner','inner-popular');

$config[ 'accept_without_cookie' ] = array('school_feed_for_paid');

$config[ 'not_loggable' ] = array('styles', 'upload');

$config[ 'assessment' ] = array(
    'types' => array(
        1 => 'Assessment',
        2 => 'ICC Quiz',
    ),
    'levels' => array(
        0 => 'Common',
        1 => 'Level 1',
        2 => 'Level 2',
        3 => 'Level 3',
    ),
    'auto_next' => array(
        'assessment'    => FALSE,
        'quiz'          => TRUE,
    ),
    'url_prefix' => array(
        1 => 'quiz/',
        2 => 'cricaddict/',
    ),
    'update_played' => array(
        'before_start' => TRUE,
        'after_finish' => FALSE,
    )
);

$config[ 'school_candle_category_id' ] = 58;

$config['education-changes-life'] = array(
    'ecl_ids' => array(59, 60, 61, 62),
    "li-class-name" => 'col-sm-5',
    "3rd-column" => array(
        "category_id" =>   59,
        "type"          => "news",
        "count"         => 1,
        "force_limit"   => TRUE,
        "char_count"    => 1
    )
);

$config['single_post_cover'] = array(
    'post' => array(
        824 => array(
            'banner' => '<p style="float:left; width:100%; clear:both; margin-top:10px;"><a href="#"><img id="candlepopup" class="ads ads-image candlepopup check_login" style="width: 98%; float: left; margin-left: 1%; margin-right: 1%; cursor: pointer;" src="/styles/layouts/tdsfront/images/ads/final/banner-candle.png" /></a></p>',
            'visible' => TRUE,
            'position' => 'bottom'
        ),
    ),
    'category' => array(
        63 => array(
            'banner' => '<p style="float:left; width:100%; clear:both; margin-top:10px;"><a href="#"><img id="candlepopup" class="ads ads-image candlepopup check_login" style="width: 98%; float: left; margin-left: 1%; margin-right: 1%; cursor: pointer;" src="/styles/layouts/tdsfront/images/ads/final/candle-bangla-wide-banner.png" /></a></p>',
            'visible' => TRUE,
            'position' => 'bottom'
        ),
        64 => array(
            'banner' => '<p style="float:left; width:100%; clear:both; margin-top:10px;"><a href="#"><img id="candlepopup" class="ads ads-image candlepopup check_login" style="width: 98%; float: left; margin-left: 1%; margin-right: 1%; cursor: pointer;" src="/styles/layouts/tdsfront/images/ads/final/candle-bangla-wide-banner.png" /></a></p>',
            'visible' => TRUE,
            'position' => 'bottom'
        )
    )
);


$config['opinion'] = array(
    'op_ids' => array(63, 64),
    'candle_category_id' => 64,
//    "li-class-name" => 'col-sm-5',
//    "3rd-column" => array(
//        "category_id" =>   59,
//        "type"          => "news",
//        "count"         => 1,
//        "force_limit"   => TRUE,
//        "char_count"    => 1
//    )
);

$config[ 'eca' ] = array(
        "width" => '70%',
        "li-class-name" => 'col-sm-5',
        "exclude_category"  => 12,
        "hide_category"  => 12,
//        "3rd-column"    => array(
//            "category_id"   =>   12,
//            "type"          => "news",
//            "count"         => 1,
//            "char_count"    => 200
//        )
);
$config[ 'games' ] = array(
        "3rd-column"    => array(
            "category_id" =>   23,
            "type"          => "box",
            "count"         => 5,
            "char_count"    => 125
        )
);
$config[ 'fitness-and-health' ] = array(
        "width" => '70%',
        "li-class-name" => 'col-sm-5',
        "exclude_category"  => 16,
        "hide_category"  => 16,
//        "3rd-column"    => array(
//            "category_id" =>   16,
//            "type"          => "list",
//            "count"         => 16,
//            "char_count"    => 125
//        )
);

$config[ 'food-and-nutrutions' ] = array(
        "width" => '70%',
        "li-class-name" => 'col-sm-5',
        "exclude_category"  => "18,19",
        "hide_category"  => "18,19",
//        "3rd-column"    => array(
//            "category_id" =>   18,
//            "type"          => "news",
//            "count"         => 1,
//            "char_count"    => 400
//        )
);

$config['home_layout'] = array(
    "3-block-with-featured", "3-block-default", "3-block-with-featured-in-two-block", "3-block-with-featured-with-two-block"
);


$config['cover'] = array(
    "index"                 => FALSE,
    "error_page"            => FALSE,
    "post"                  => FALSE,
    "game"                  => FALSE,
    "fitness-and-health"    => FALSE,
    "entertainment"         => FALSE,
    "food-and-nutrutions"   => FALSE,
    "eca."                  => FALSE,
    "resource-center"       => FALSE
);

$config['hide-top-breadcrumb'] = array(
    "opinion"                 => TRUE
);

$config['cover-image'] = array(
    "index"                 => "",
    "error_page"            => "",
    "post"                  => "",
    "game"                  => "styles/layouts/tdsfront/images/cover/game.jpg",
    //"fitness-and-health"    => "styles/layouts/tdsfront/images/cover/fitness-and-health.jpg",
    "fitness-and-health"    => "",
    "entertainment"         => "styles/layouts/tdsfront/images/cover/entertainment.jpg",
    //"food-and-nutrutions"   => "styles/layouts/tdsfront/images/cover/food-and-nutrutions.jpg",
    "food-and-nutrutions"   => "",
    "eca."                  => "styles/layouts/tdsfront/images/cover/eca1.jpg",
    "resource-center"       => "styles/layouts/tdsfront/images/cover/resource-center.jpg"
);

$config['LOGO'] = array(
    "allpage"               =>TRUE,
    "index"                 => TRUE,
    "error_page"            => TRUE,
    "post"                  => TRUE,
    "extra-curricular"      => TRUE,
    "fitness-and-health"    => TRUE,
    "food-and-nutrutions"   => TRUE,
    
);

$config['swf'] = array(
    "external_url" => 'http://www.champs21.com/',
);

$config['css_champs21'] = Array(
        'all.css' => Array( // when loading example.com/css/example1.css you will load stylesheet1.css and stylesheet2.css into one single file
                    'merapi/style/bootstrap.css',
                    'merapi/style/plugin.css',
                    'merapi/style/style.css',
                    'merapi/style/font.css',
                    'merapi/css.css',
                    'merapi/style/styles.css',
                    'styles/layouts/tdsfront/css/fonts.css',
                    'styles/layouts/tdsfront/css/freefeed.css',
                    'Profiler/sidebar.css',
                    'Profiler/theme.css',
                    'Profiler/template.css',
                    'Profiler/megamenu.css',
                    'Profiler/megamenu-theme.css',
                    'Profiler/custom_theme.css',
                    'styles/plugins/fancybox/fancybox.css',
                    'merapi/style/header-icon-animation.css'
        ),
        'champs.css' => Array( // when loading example.com/css/example1.css you will load stylesheet1.css and stylesheet2.css into one single file
                    'merapi/style/bootstrap.css',
                    'merapi/style/plugin.css',
                    'merapi/style/style.css',
                    'merapi/style/font.css',
                    'merapi/css.css'
        ),
        'styles.css' => Array( // when loading example.com/css/example1.css you will load stylesheet1.css and stylesheet2.css into one single file
                    'merapi/style/styles.css',
                    'styles/layouts/tdsfront/css/fonts.css',
                    'styles/layouts/tdsfront/css/freefeed.css',
        ),
        'sidebar.css' => Array( // when loading example.com/css/example1.css you will load stylesheet1.css and stylesheet2.css into one single file
                    'Profiler/sidebar.css',
                    'Profiler/theme.css',
                    'Profiler/template.css',
                    'Profiler/megamenu.css',
                    'Profiler/megamenu-theme.css',
                    'Profiler/custom_theme.css',
                    'styles/plugins/fancybox/fancybox.css',
                    'merapi/style/header-icon-animation.css'
        )
);

$config['post-ads'] = array(
                            "add" => array(
                                            "styles/layouts/tdsfront/images/ads/final/candle.png",
                                            "styles/layouts/tdsfront/images/ads/final/entertainment.png",
                                            "styles/layouts/tdsfront/images/ads/final/extra_curricular_activities.png",
                                            "styles/layouts/tdsfront/images/ads/final/games.png",
                                            "styles/layouts/tdsfront/images/ads/final/good_read.png",
                                            "styles/layouts/tdsfront/images/ads/final/literature.png",
                                            "styles/layouts/tdsfront/images/ads/final/news_and_articles.png",
                                            "styles/layouts/tdsfront/images/ads/final/nutrition.png",
                                            "styles/layouts/tdsfront/images/ads/final/personality.png",
                                            "styles/layouts/tdsfront/images/ads/final/resource_centre.png",
                                            "styles/layouts/tdsfront/images/ads/final/schools.png",
                                            "styles/layouts/tdsfront/images/ads/final/sports.png",
                                            "styles/layouts/tdsfront/images/ads/final/Travel.png"
                                        ),
                               "link" => array("#","entertainment","extra-curricular","games","good-read","literature",
                                   "news-and-articles","nutrition","personality","resource-center","schools","sports","travel"),
                               "check_login"=> array("1","entertainment","extra-curricular","games","1","literature",
                                   "news-and-articles","nutrition","personality","resource-center","schools","sports","travel"),
    
                               "class"=> array("candlepopup","","","","","","","","","","","",""),
                               "id"   => array("","","","","good_read","","","","","","","","")
                            );

$config['school_templates'] = array(
    'FT001' => array(
        'id' => 1,
        'name' => 'FT001',
        'price' => 0,
        'cover_url' => '/styles/layouts/tdsfront/school_templates/FT001/cover.png',
        'price_tag_url' => '/styles/layouts/tdsfront/school_templates/FT001/price.png',
        'demo_url' => 'http://schoolpage.champs21.com/free-template/template2.html',
    ),
    'FT002' => array(
        'id' => 2,
        'name' => 'FT002',
        'price' => 0,
        'cover_url' => '/styles/layouts/tdsfront/school_templates/FT002/cover.png',
        'price_tag_url' => '/styles/layouts/tdsfront/school_templates/FT002/price.png',
        'demo_url' => 'http://schoolpage.champs21.com/free-template/template3.html',
    ),
    'FT003' => array(
        'id' => 3,
        'name' => 'FT003',
        'price' => 0,
        'cover_url' => '/styles/layouts/tdsfront/school_templates/FT003/cover.png',
        'price_tag_url' => '/styles/layouts/tdsfront/school_templates/FT003/price.png',
        'demo_url' => 'http://schoolpage.champs21.com/free-template/template4.html',
    ),
    'FT004' => array(
        'id' => 4,
        'name' => 'FT004',
        'price' => 0,
        'cover_url' => '/styles/layouts/tdsfront/school_templates/FT004/cover.png',
        'price_tag_url' => '/styles/layouts/tdsfront/school_templates/FT004/price.png',
        'demo_url' => 'http://schoolpage.champs21.com/free-template/template1.html',
    )
);