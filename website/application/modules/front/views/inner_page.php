<?php
    $s_ci_key = (isset($ci_key)) ? $ci_key : NULL; 
    
?>
<?php
    $b_checked_cache = FALSE;
    if ( ( list($i_type_id_cache, $i_category_id_cache, $s_category_name_cache) = get_category_type(sanitize($ci_key)) ) ) 
    {
       $b_checked_cache = TRUE; 
    }
    
    if ((isset($_GET['archive']) &&  strlen($_GET['archive']) != "0") || (isset($_GET['date']) &&  strlen($_GET['date']) != "0" && $_GET['date']!=date("Y-m-d")))
    {
        $b_checked_cache = FALSE;
    }
    $CI = & get_instance();
    $CI->load->driver('cache',array('adapter' => 'file'));
    $cache_name = "INNER_CONTENT_CACHE_".$i_category_id_cache."_". str_replace(":", "-",  str_replace(".", "-", str_replace("/", "-", base_url()))) . date("Y_m_d");
    
    ob_start();

    $widget = new Widget;
    $widget->run('champs21header');
?>
<div class="inner-news">    
        <div style="float:left;">
		<a href="<?php echo create_link_url(sanitize($ci_key));?>"><h1 class="<?php echo strtolower($name); ?> title noPrint f2" style="color:#93989C;"><?php echo $pagetitle; ?></h1></a>
		</div>
		<div style="float:right;">
        <center>		
            <!-- News Detials headline above -->
			<?php if(sanitize($name) == "eca."){?>
			<img src="<?php echo base_url('/upload/ads/banners/eca.jpg'); ?>"  width="350" >
			<?php }else {?>
			<img src="<?php echo base_url('/upload/ads/banners/'.sanitize($ci_key).'.jpg'); ?>" width="350" >
			
			<?php } ?>
            <?php
               //$adplace_helper = new Adplace;
               //$adplace_helper->printAds( 27, null, FALSE,"0",'details' );
            ?>

        </center>
		</div>
		<div style="clear:both;"></div>
        <?php if ( $has_categories ) : ?>
        <div class="sub-categories f2">
            <li class="<?php echo ( empty($a_category_ids) ) ? "selected" : ""; ?>">All</li>
            <?php foreach ( $obj_child_categories as $categories ) :?>
            <li class="<?php echo ( in_array($categories->id, $a_category_ids) ) ? "selected" : ""; ?>"><?php echo $categories->name; ?></li>
            <?php if ( in_array($categories->id, $a_category_ids) ) : ?>
            <?php $name = $categories->name; ?>
            <?php endif; ?>
            <?php endforeach;?>
        </div>
        <?php endif; ?>
</div>
<div class="row">
    <div class="inner-news">
        <div style="display: inline-block; width: 40%;">
            <a href="http://<?php echo create_link_url(sanitize($ci_key));?>"><h1 class="<?php echo strtolower($name); ?> title noPrint f2">Articles for all</h1></a>
        </div>
        <div style="display: inline-block;
    float: right;
    margin-right: 31px;
    margin-top: 36px;
    text-align: right;
    width: 55%;">
            <div class="button button-selected">
                Most Recent
            </div>
            <div class="button">
                Most Popular
            </div>
            <div class="button">
                Set Preference
            </div>
        </div>
    </div>
    <div style="clear: both;"></div>
    
    <ul style="position: relative; height: 2407.32px;" class="grid effect-6" id="grid">
        <?php $news_key = array('top_news', 'main_news','bottom_news','common'); ?>
        <?php if ( count($inner_posts) > 0 ) foreach( $inner_posts as $key => $inner_post ) : ?>
            <?php foreach($inner_post as $news) : ?>
                <li id="post-<?php echo $news->id; ?>" class="post-<?php echo $news->id; ?> post type-post status-publish format-gallery hentry category-post-format tag-gallery tag-image tag-people tag-pictures post col-md-6 animate shown">
                    <div class="post-content clearfix">
                        <div class="intro-post">
                            <div class="flex-wrapper">
                                <?php
                                    $size = getimagesize($news->image);
                                    $width = $size[0];
                                    $height = $size[1];
                                    $proportion = $height / $width;
                                    
                                    $div_width = 300;
                                    $div_height = round($div_width * $proportion);
                                ?>
                                <div style="height: <?php echo $div_height; ?>px;" id="slider" class="flexslider">
                                    <ul class="slides">
                                       <li class="flex-active-slide" style="width: 100%; float: left; margin-right: -100%; position: absolute; left: 0px; display: list-item;">
                                           <img src="<?php echo $news->image;?>" alt="<?php echo $news->headline;?>">
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="post-entry">
                            <div class="post-title">
                                <h2 class="f2">
                                    <a href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->id; ?>" title="<?php echo $news->headline;?>"><?php echo $news->headline;?></a>
                                </h2>
                            </div><!-- post-title --> 

                            <div class="akmanda-excerpt"> 
                                <p><?php echo ( strlen($news->content) >150 ) ? substr($news->content,0, 150) . "..." : $news->content; ?></p>
                                <div class="more-button" style="display: none;">
                                    <a href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->id; ?>" title="<?php echo $news->headline;?>" class="more">Continue</a>
                                </div>
                            </div>
                        </div>
                    </div><!-- post-content -->
                </li><!-- # -->
            <?php endforeach; ?>
        <?php endforeach; ?>
    </ul>
    <div class="pagination col-md-12 text-center" style="display: none;"><span class="current">1</span><a href="http://merapi.themesawesome.com/page/2/" class="inactive">2</a></div>
</div>
