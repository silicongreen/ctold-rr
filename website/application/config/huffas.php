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

$config[ 'from_api' ] = TRUE;

$config[ 'api_index' ] = array('index','inner','inner-popular');

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