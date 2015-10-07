<?php
    $s_ci_key = (isset($ci_key)) ? $ci_key : NULL; 
    
?>
<?php
//    $b_checked_cache = FALSE;
//    if ( ( list($i_type_id_cache, $i_category_id_cache, $s_category_name_cache) = get_category_type(sanitize($ci_key)) ) ) 
//    {
//       $b_checked_cache = TRUE; 
//    }
//    
//    if ((isset($_GET['archive']) &&  strlen($_GET['archive']) != "0") || (isset($_GET['date']) &&  strlen($_GET['date']) != "0" && $_GET['date']!=date("Y-m-d")))
//    {
//        $b_checked_cache = FALSE;
//    }
//    $CI = & get_instance();
//    $CI->load->driver('cache',array('adapter' => 'file'));
//    $cache_name = "INNER_CONTENT_CACHE_".$i_category_id_cache."_". str_replace(":", "-",  str_replace(".", "-", str_replace("/", "-", base_url()))) . date("Y_m_d");
//    
//    ob_start();

//    if (isset($_COOKIE['local'])) {
//        unset($_COOKIE['local']);
//        setcookie('local', null, -1, '/');
//    }

    $widget = new Widget;
    $lang = get_language_cookie();

?>
<?php $s_ci_key = (isset($ci_key)) ? $ci_key : NULL; ?>
<div class="container" style="width: 77%;min-height:250px;">
    
    <!-- Top Menu of category and sub-category -->
    <?php if(!$hide_top_breadcrumb) { ?>
    
        <div class="sports-inner-news yesPrint" style="padding: 0px 22px 0 35px;">    
            <div style="float:left;">
                <a href="<?php echo create_link_url(sanitize($name)); ?>">
                    <h1 class="title noPrint f2" style="color:#333333;">
                        <?php
                        if (isset($display_name) && $display_name != "") {
                            echo $display_name;
                        } else {
                            echo $name;
                        }
                        ?>

                    </h1>
                </a>
            </div>

    <!--        <div style="float:right;">
                <center>		
                     News Detials headline above 
                    <?php  /* ?>
                    <?php if (sanitize($name) == "eca.") { ?>
                        <img src="<?php echo base_url('/upload/ads/banners/eca.jpg'); ?>" width="350"  >
                    <?php } else { ?>
                        <img src="<?php echo base_url('/upload/ads/banners/' . sanitize($name) . '.jpg'); ?>" width="350"  >

                    <?php } ?>
                    <?php
                    //$adplace_helper = new Adplace;
                    //$adplace_helper->printAds( 27, null, FALSE,"0",'details' );
                    ?>
                    <?php */ ?>
                </center>
            </div>-->

            <div style="clear:both;"></div>
            <?php $parent_category = $name; ?>
            <?php if ( $has_categories ) : ?>
            <div class="sub-categories f5 col-md-9">
                <ul style="margin: 0px; padding: 0px;">
                    <li class="<?php echo ( empty($a_category_ids) ) ? "selected" : ""; ?>">
                        <a href="<?php echo base_url(sanitize($parent_category));?>" class="<?php echo (  empty($a_category_ids) ) ? "selected" : ""; ?>" style="<?php echo (  empty($a_category_ids) ) ? "" : "color: #93989C;"; ?>">
                        All
                        </a>
                    </li>
                    <?php foreach ( $obj_child_categories as $categories ) :?>
                    <li>
                        <a href="<?php echo base_url(sanitize($parent_category) . "/" . sanitize($categories->name));?>" class="<?php echo ( in_array($categories->id, $a_category_ids) ) ? "selected" : ""; ?>" style="<?php echo ( in_array($categories->id, $a_category_ids) ) ? "" : "color: #93989C;"; ?>">
                            <?php 
                                    if(isset($categories->display_name) && $categories->display_name!="")
                                    {
                                       echo $categories->display_name; 
                                    }
                                    else
                                    {
                                        echo $categories->name;
                                    }
                             ?>
                        </a>
                    </li>
                    <?php if ( in_array($categories->id, $a_category_ids) ) : ?>
                    <?php $name = $categories->name; ?>
                    <?php endif; ?>
                    <?php endforeach;?>
                </ul>
            </div>

            <div class="inner-news col-md-3">

                <div id="article-title" style="display: none; width: 40%;">
                    <h1 class="title noPrint f2">Articles for all</h1>
                </div>

                <div class="next-previous">

                    <div class="previous <?php echo ( ! $popular ) ? 'button-selected' : ''; ?>">
                        <span><a href="<?php echo ( strtolower($parent_category) ==  strtolower($name)) ? base_url(sanitize(strtolower($parent_category))) : base_url(sanitize(strtolower($parent_category)) ."/" . sanitize(strtolower($name))); ?>">Recent</a></span>
                    </div>

                    <div class="next <?php echo ( $popular ) ? 'button-selected' : ''; ?>">
                        <span><a href="<?php echo ( strtolower($parent_category) ==  strtolower($name)) ? base_url(sanitize(strtolower($parent_category)) . "/popular") : base_url(sanitize(strtolower($parent_category)) ."/" . sanitize(strtolower($name)) . "/popular"); ?>">Popular</a></span>
                    </div>

                </div>

            </div>
            <?php endif; ?>
        </div>
    
    <?php } ?>
    <!-- Top Menu of category and sub-category -->
    
    <?php //$widget->run('postdata', "featured", $s_category_ids, 'inner', true, 1); ?>
    
    <div style="clear: both;"></div>
    
    <?php
        if($s_category_ids == 59) {
            $mix_category = (isset($display_name) && $display_name != "") ? $display_name : $name;
        } else {
            $mix_category = $obj_child_categories;
        }
//    var_dump($mix_category);exit; ?>
    
    <?php $widget->run('postdata', $category_name, $s_category_ids, $ci_key, FALSE, 0, "index", 0, 9, 0, '', $mix_category, false, 0, array(), $lang); ?>
    <?php //$widget->run('postdata', "featured", $s_category_ids, 'inner', true, 2); ?>
    <?php $widget->run('mediagallery', $ci_key, "inner", 21, date("Y-m-d"), false); ; ?>
    <?php //$widget->run('postdata', "featured", $s_category_ids, 'inner', true, 3); ?>
    
</div>

<style type="text/css">
    .next-previous {
        float: right;
        letter-spacing: 0.08em;
        width: 75%;
    }
    .next-previous a {
        color: #ffffff;
    }
    .next {
        background-color: #bfc3c6;
        border-radius: 5px;
         -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        -ms-border-radius: 5px;
        -o-border-radius: 5px;
        color: #ffffff;
        cursor: pointer;
        float: right;
        padding: 2px 12px;
    }
    .previous {
        background-color: #bfc3c6;
        border-radius: 5px;
         -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        -ms-border-radius: 5px;
        -o-border-radius: 5px;
        color: #ffffff;
        cursor: pointer;
        float: left;
        padding: 2px 12px;
    }
    .next:hover, .previous:hover {
        background-color: #373737;
        -webkit-transition: background-color 0.5s ease;
        -moz-transition: background-color 0.5s ease;
        -o-transition: background-color 0.5s ease;
        -ms-transition: background-color 0.5s ease;
        transition: background-color 0.5s ease;
    }
    .button-selected {
        background-color: #373737;
    }
    #grid {
        margin: 0 auto 0 20px;
    }
</style>