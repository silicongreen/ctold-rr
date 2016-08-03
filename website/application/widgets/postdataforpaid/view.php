<div id="fb-root"></div>
<!--<script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id))
            return;
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=210972602426467&version=v2.0";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>-->

<?php $widget = new Widget; ?>
<?php if ($featured == 1) : ?>
    <?php if (count($obj_post_news) > 0) : ?>
        <?php if ($target == "index") : ?>
            <?php $widget->run('featurednews', $obj_post_news); ?>
        <?php elseif ($is_game == 1) : ?>
            <?php $widget->run('featurednewsgame', $obj_post_news); ?>
        <?php else: ?>
            <?php $widget->run('featurednewsinner', $obj_post_news); ?>
        <?php endif; ?>
    <?php endif; ?>
<?php else : ?>
    <?php //if ( $position_div ) : ?> 

<style>
    #masonry-ordered div
    {
        border:1px solid black;
        width:33%;
    }
    .word-of-the-day {
        text-align: center;
    }
    .word-of-the-day-sound {
        height: 75px;
        margin: -5px auto auto 105px;
        width: 75px;
    }
    .word-of-the-day-sound button {
        background: url("styles/layouts/tdsfront/image/Words-ofthe-day-logo-sound.png") no-repeat scroll 0 0 / 55px auto;
        border: medium none;
        color: #de3427;
        margin: 0;
        padding: 30px;
        position: absolute;
    }
    .word-of-the-day-sound button:hover {
        background: url("styles/layouts/tdsfront/image/Words-ofthe-day-logo-sound-hover.png") no-repeat scroll 0 0 / 55px auto;
    }
    
</style>    





    <div style="width: 84.1%; float:left; margin-left:100px;">
        <?php //endif; ?>   
        <?php $j = -1; ?>    
        <?php $news_to_show = count($obj_post_news); ?>   
        <?php //if ( $layout_type == 2 ) : ?>
        <?php //$widget->run('featuredtwocolumn', $obj_post_news, $li_class_name, -1); ?>
        <?php //$j = 1; ?>    
        <?php //$news_to_show = count($obj_post_news) - 3; ?>  
        <?php //endif; ?> 
        <?php //if ( $layout_type == 3 ) : ?>
        <?php //$widget->run('featuredtwocolumn', $obj_post_news, $li_class_name, -1); ?>
        <?php //$j = 1; ?>    
        <?php //endif; ?>     
        <?php //$j = -1; ?> 

        <ul style="position: relative; width:100%; " class="grid effect-6 posts-<?php echo $current_page; ?>" id="grid">
            <?php $is_breaking_found = false; $count_show = 20; ?>
            <?php $found_slider = 0; ?>
            <?php $ar_slider_amount = array(); ?>
            <?php $i = 0; $ka = 0; if ($obj_post_news) foreach ($obj_post_news as $news) :?>
            <?php 
                $is_exclusive_found = false;
                
                if ( $news->is_exclusive && date("Y-m-d H:i:s") < $news->exclusive_expired && $i==0 )
                {
                    $count_show = 4;
                }
                
                if ( $news->is_exclusive && date("Y-m-d H:i:s") < $news->exclusive_expired )
                {
                    
                    $is_exclusive_found = true;
                    if ( $obj_post_news[$i + 1]->is_exclusive && date("Y-m-d H:i:s") < $obj_post_news[$i + 1]->exclusive_expired )
                    {
                        if ( ! $is_breaking_found )
                        {
                            $count_show++;
                            $ka++;
                            print '<li   style="padding:0px"  class="post col-sm-12 type-post status-publish format-image has-post-thumbnail hentry category-post-format tag-description tag-image tag-people tag-text shown post-boxes ">
                                    <div class="flex-wrapper_news">
                                        <div id="slider" class="flexslider_news" style="margin-bottom:10px;">
                                        <ul class="slides_news" style="padding:14px; margin:0px;">';
                        }
                        $is_breaking_found = true;
                    }
                }
                else if ( $news->is_breaking && date("Y-m-d H:i:s") < $news->breaking_expire )
                {
                    
                    //$is_exclusive_found = true;
                    if ( $obj_post_news[$i + 1]->is_breaking && date("Y-m-d H:i:s") < $obj_post_news[$i + 1]->breaking_expire )
                    {
                        if ( ! $is_breaking_found )
                        {
                            $count_show++;
                            $ka++;
                            print '<li  class="post col-sm-8 type-post status-publish format-image has-post-thumbnail hentry category-post-format tag-description tag-image tag-people tag-text shown post-boxes ">
                                    <div class="flex-wrapper_news">
                                        <div id="slider" class="flexslider_news" style="margin-bottom:10px;">
                                        <ul class="slides_news" style="padding: 0px; margin: 0px;">';
                        }
                        $is_breaking_found = true;
                    }
                }
            ?>
                    <?php if ($layout_type == 2 || $layout_type == 3) : ?>
                        <?php $li_class_name = ( $i == 0 || $i == $news_to_show - 1 ) ? 'col-sm-8' : 'col-md-6'; ?>
                        <?php $style = ( $i == 0 || $i == $news_to_show - 1 ) ? 'list-style: none;' : 'list-style: none; '; ?>
                    <?php endif; ?>

                    <?php if ($news->post_type == 4 || $news->post_type == 3) : ?>
                        <?php $style .= 'background: transparent;'; ?>
                    <?php endif; ?>

                    <?php if ($i > $j) : ?>
                        <?php if($is_exclusive_found===true): ?>
                        <?php $arCustomNews = getFormatedContentAll($news, 210); ?>
                        <?php elseif($news->post_layout==4): ?>
                         <?php $arCustomNews = getFormatedContentAll($news, 210); ?>
                        <?php else: ?>
                        <?php $arCustomNews = getFormatedContentAll($news, 125); ?>
                        <?php endif; ?>
                        <?php if ($i == 2): ?>
                            <?php if ($has_3rd_column && count($ar_3rd_column_extra_data) > 0 && $ar_extra_config['type'] == "news") : ?>
                                <li class="post shown col-md-6 ">
                                    <?php $widget->run('thirdcolumninnernews', $ar_3rd_column_extra_data, $extra_column_name, $ar_extra_config); ?>    
                                </li>
                            <?php elseif ($has_3rd_column && count($ar_3rd_column_extra_data) > 0 && $ar_extra_config['type'] == "list"): ?>
                                <li class="post shown col-md-6 ">
                                    <?php $widget->run('thirdcolumninnerlist', $ar_3rd_column_extra_data, $extra_column_name, $ar_extra_config); ?> 
                                </li>
                            <?php endif; ?> 
                        <?php endif; ?>
                        
                        <?php 
                            $li_class_name = 'col-md-6';
                            $li_class_name = ( $news->is_breaking && date("Y-m-d H:i:s") < $news->breaking_expire ) ? 'col-sm-8' : ( ( $news->is_exclusive && date("Y-m-d H:i:s") < $news->exclusive_expired ) ? 'col-sm-12' : 'col-md-6' );
                        ?>
                        
                        <?php $s_post_class = ( $is_breaking_found ) ? "news_slides" : "post" ; ?>  
                        <?php if ( in_array($news->post_layout, array(1,2,3)) ) : ?>   
                              <?php $widget->run('post_type_' . $news->post_layout, $news, $style, $s_post_class, $li_class_name, $i, $count_show, $is_exclusive_found, $target,"main",$category); ?>         
                        <?php elseif (!is_null($news->short_title) && strlen(trim($news->short_title)) > 0 && in_array($news->sort_title_type, array(1,2,3,4,5)) ) : ?>
                            <?php $widget->run('short_title_block' . $news->sort_title_type, $news, $style, $s_post_class, $li_class_name, $i, $count_show, $is_exclusive_found, $target,"main",$category); ?>
                        <?php else : ?>
                        <li style="<?php echo $style; ?>" id="post-<?php echo $news->post_id; ?>" class="post-<?php echo $news->post_id; ?> <?php echo $s_post_class; ?> type-post post-content-showed status-publish format-image has-post-thumbnail hentry category-post-format tag-description tag-image tag-people tag-text <?php echo $li_class_name; ?> <?php echo ($i < $count_show) ? "shown" : ""; ?> <?php echo (!is_null($news->short_title) && strlen(trim($news->short_title)) > 0) ? " format-quote  tag-quote " : ""; ?> post-boxes ">
                            <div class="post-content clearfix" style="<?php echo ( $news->post_type == 4 || $news->post_type == 3) ? 'background: transparent;' : ''; ?><?php echo ($is_exclusive_found) ? 'height:278px;' : '' ?>">
                                <div class="intro-post <?php if($is_exclusive_found===true): ?>col-xs-6<?php endif; ?>">	
                                    <?php if ($news->post_type != 2) : ?>   
                                        <a href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" title="<?php echo $news->headline; ?>">   
                                        <?php else : ?>

                                        <?php endif; ?>
                                        <div class="post-thumb ">
                                            <?php if (strlen(trim($news->embedded)) > 0) : ?>
                                                <?php echo $news->embedded; ?>
                                            <?php elseif (!is_null($news->lead_material) && strlen(trim($news->lead_material)) > 0) : ?>
                                                <?php if ($news->post_type == 2) : ?>   
                                                    <a class="add-link" title="<?php echo $news->lead_caption; ?>" href="<?php echo $news->lead_link; ?>"   <?php if($news->ad_target!=2): ?> target="_blank"<?php endif; ?>>       
                                                    <?php endif; ?>     

                                                        <img class="no_toolbar" src="<?php echo $arCustomNews['lead_material']; ?>" class="attachment-post-thumbnail wp-post-image <?php echo ( $news->post_type == 2 ) ? 'no_toolbar' : ''; ?>" alt="<?php echo $news->headline; ?>" <?php if($is_exclusive_found===true): ?>style="width:475px;height:265px; "<?php endif; ?>>
                                                    <?php if ($news->post_type == 2) : ?>   
                                                    </a>
                                                <?php endif; ?>
                                            <?php elseif (strlen(trim($arCustomNews['image'])) > 0) : ?>
                                                <?php if (count($arCustomNews['all_image']) == 1): ?>
                                                    <?php if ($news->post_type == 2) : ?>   
                                                        <a class="add-link"   title="<?php echo $arCustomNews['all_image_title'][0]; ?>"  href="<?php echo $arCustomNews['all_image_url'][0]; ?>"   <?php if($news->ad_target!=2): ?> target="_blank"<?php endif; ?>>       
                                                        <?php endif; ?> 
                                                        <img class="no_toolbar" src="<?php echo $arCustomNews['image']; ?>" class="attachment-post-thumbnail wp-post-image" alt="<?php echo $news->headline; ?>" <?php if($is_exclusive_found===true): ?>style="width:475px;height:265px; "<?php endif; ?>>
                                                        <?php if ($news->post_type == 2) : ?>   
                                                        </a>
                                                    <?php endif; ?>
                                                <?php else : ?>
                                                    <div class="flex-wrapper">
                                                        <div id="slider" class="flexslider" style="border: 1px solid #Fff;">
                                                            <ul class="slides">
                                                                <?php foreach ($arCustomNews['all_image'] as $key => $image): ?>
                                                                    <li style="padding:0px; margin:0px;">
                                                                        <?php if ($news->post_type == 2) : ?>   
                                                                            <a class="add-link"  title="<?php echo $arCustomNews['all_image_title'][$key]; ?>" href="<?php echo $arCustomNews['all_image_url'][$key]; ?>"   <?php if($news->ad_target!=2): ?> target="_blank"<?php endif; ?>>       
                                                                            <?php endif; ?>        
                                                                            <img class="no_toolbar"    src="<?php echo $image; ?>" alt="<?php echo $news->headline; ?>" <?php if($is_exclusive_found===true): ?>style="width:475px;height:265px; "<?php endif; ?> />
                                                                            <?php if ($news->post_type == 2) : ?>   
                                                                            </a>
                                                                        <?php endif; ?>
                                                                    </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div><!-- post thumb -->
                                        <?php if ($news->post_type != 2) : ?>   
                                        </a>
                                    <?php endif; ?>
                                </div> 
                                
                                <?php if ($news->post_type == 4) : ?>    
                                    <div class="post-entry">     
                                        <div class="post-title">
                                            <h2 class="f2" style="font-size: 32px;">
                                                <a  href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" title="<?php echo $news->headline; ?>">
                                                    <?php echo $news->headline; ?>
                                                </a>
                                            </h2>
                                        </div><!-- post-title --> 

                                        <div class="akmanda-excerpt"> 
                                            <?php $s_gk_answers = $arCustomNews['content']; ?>
                                            <?php $a_gk_answers = explode(",", $s_gk_answers); ?>
                                            <?php
                                            $b = 1;
                                            foreach ($a_gk_answers as $answer) :
                                                ?>
                                                <?php $a_answer = explode("|", $answer); ?>
                                                <?php $a_color_layout = explode("|", $news->layout_color); ?>
                                                <div class="gk_layout_<?php echo $news->layout; ?>" style="background: <?php echo ($news->layout == 1) ? (( $b == 1 || $b == 4 ) ? $a_color_layout[1] : $a_color_layout[0]) : (( $b % 2 == 0 ) ? $a_color_layout[1] : $a_color_layout[0]); ?>; ">
                                                    <?php echo $a_answer[0]; ?>
                                                </div>
                                                <?php
                                                $b++;
                                            endforeach;
                                            ?>
                                        </div>

                                    </div>    
                                <?php elseif ($news->post_type == 3) : ?>
                                    
                                    <div class="post-entry">     
                                        
                                        <a class="word-of-the-day-swf" href="<?php echo $swf_external_url . 'swf/spellbee/'; ?>">
                                            <div class="word-of-the-day">
                                                <img width="170px" src="<?php echo base_url('styles/layouts/tdsfront/image/Words-ofthe-day-logo.png'); ?>" />
                                            </div>
                                        </a>
                                        
                                        <div class="post-title" style="margin: 0; padding: 5px 0;">
                                            <h2 class="f5" style="font-size: 35px; letter-spacing: 0.04em; margin-left: -30px;">
                                                <!--<a class="word-of-the-day-swf word-of-the-day-sound-link" href="<?php //echo $swf_external_url . 'swf/spellbee/'; ?>" title="<?php //echo $news->headline; ?>">-->
                                                <a class="word-of-the-day-sound-link" href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" title="<?php echo $news->headline; ?>">
                                                    <?php echo $news->headline; ?>
                                                </a>
                                            </h2>
                                        </div><!-- post-title -->
                                        
                                        
                                        <div class="word-of-the-day-sound">
                                            <button class="word-of-the-day-sound-btn"></button>
                                        </div>

                                    </div>
                                
                                <?php elseif ($news->post_type != 2) : ?>        
                                    <div class="post-entry <?php if($is_exclusive_found===true): ?>col-xs-6<?php endif; ?>" > 
                                        <?php $showed = false;
                                        if (!is_null($news->short_title) && strlen(trim($news->short_title)) > 0 && $is_exclusive_found===false) : ?>
                                            <div class="quote-wrap">

                                                <blockquote> 
                                                    <i class="icon icon-fontawesome-webfont"></i>
                                                    <p><?php echo $news->short_title; ?></p>
                                                </blockquote>

                                            </div>
                                                <?php $showed = true;
                                            endif; ?>
                                        <div class="post-title" <?php if($is_exclusive_found===true): ?>style="margin: 24px 0px;"<?php endif; ?>>
                                            <?php if ($target == "index" && $news->post_layout!=4) : ?>
                                                <?php if ($showed): ?>
                                                    <!--                                                don nothing-->
                                                <?php elseif ($news->show_byline_image) : ?>
                                                    <?php if (!is_null($news->author_image) && strlen(trim($news->author_image)) > 0) : ?>
                                                        <div class="akmanda_author_img"><img src="<?php echo base_url($news->author_image); ?>"></div>
                            <?php else : ?>
                                                        <p style="<?php if($is_exclusive_found===true):?>top:104px;position:absolute;<?php else: ?>margin-top: -67px;position: relative;<?php endif;?>text-align: center;width: 100%;">
                                                            <img width="57" <?php if($is_exclusive_found===true):?>style="margin-left:-101%;"<?php endif; ?> src="<?php echo $news->icon ?>">
                                                        </p>
                            <?php endif; ?>
                        <?php else : ?>
                                                    <p style="<?php if($is_exclusive_found===true):?>top:104px;position:absolute;<?php else: ?>margin-top: -67px;position: relative;<?php endif;?> text-align: center;width: 100%;">
                                                        <img width="57" <?php if($is_exclusive_found===true):?>style="margin-left:-101%;"<?php endif; ?> src="<?php echo $news->icon ?>">
                                                    </p>
                                                <?php endif; ?>

                                            <?php endif; ?>
                                            <h2 class="f2" <?php if($news->post_layout==4): ?> style="text-align:left !important;" <?php endif; ?>>
                                                <a <?php if($news->post_layout==4): ?> style="margin-left:17px;" <?php endif; ?> href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" title="<?php echo $news->headline; ?>">
                                                    <?php echo $news->headline; ?>
                                                </a>
                                            </h2>
                                                <?php if ($target == "inner" && $news->post_layout!=4) : ?>
                                                <span class="brown-subtitle">
                                                <?php //echo $news->name . ' | '; ?><?php echo date("d M", strtotime($news->published_date)); ?>
                                                </span>
                                                <?php elseif ($target == "index" && $news->post_layout!=4) : ?>
                                                <span class="brown-subtitle">
                                                <?php echo get_post_time($news->published_date); ?>
                                                </span>
                                                <?php endif; ?>
                                        </div><!-- post-title --> 

                                        <div class="akmanda-excerpt" <?php if($is_exclusive_found===true): ?>style="margin: 20px 30px;"<?php endif; ?>> 
                                           <?php if($news->sub_head): ?>
                                               <p><?php echo $news->sub_head; ?></p>
                                            <?php endif; ?>
                                            <?php show_summary($arCustomNews['content'],$news); ?>
                                        
                                        </div>
                                    </div>
                                </div><!-- post-content -->    

                               <?php
                                    $widget = new Widget;
                                    $widget->run('actionbox', $news);
                                ?>

                        <?php endif; ?>
                        </li>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php 
                        if ( $news->is_exclusive && date("Y-m-d H:i:s") < $news->exclusive_expired )
                        {
                            if ( $obj_post_news[$i + 1]->is_exclusive && date("Y-m-d H:i:s") < $obj_post_news[$i + 1]->exclusive_expired )
                            {
                                
                            }
                            else if ( $is_breaking_found )
                            {
                                array_push($ar_slider_amount, $ka);
                                $found_slider++;
                                $ka = 0;
                                print "</ul></div></div></li>";
                                $is_breaking_found = false;
                            }
                        }
                        else if ( $news->is_breaking && date("Y-m-d H:i:s") < $news->breaking_expire )
                        {
                            if ( $obj_post_news[$i + 1]->is_breaking && date("Y-m-d H:i:s") < $obj_post_news[$i + 1]->breaking_expire )
                            {
                                
                            }
                            else if ( $is_breaking_found )
                            {
                                array_push($ar_slider_amount, $ka);
                                $found_slider++;
                                $ka = 0;
                                print "</ul></div></div></li>";
                                $is_breaking_found = false;
                            }
                        }
                    ?>    
                        
                    <?php
                    $i++;
                endforeach;
            ?>

        </ul>
        

        <style>
            .flex-wrapper_news .flex-control-nav {
                    bottom: 33;
                    height: 36px;
            } 
            .flex-wrapper_news ul > li.news_slides
            {
                display:none;
            }
            .flex-wrapper_news ul > li.news_slides:first-child
            {
                display:block;
            }
            
            
            
            
            
.scrollgeneric {
line-height: 1px;
font-size: 1px;
position: absolute;
top: 0; left: 0;
}

.vscrollerbase {
width: 10px;
background-color: white;
}
.vscrollerbar {
width: 10px;
background-color: #C72329;
}


#mycustomscroll {
/* Typical fixed height and fixed width example */
height: 350px;
overflow: auto;
/* IE overflow fix, position must be relative or absolute*/
position: relative;
background-color: #fff;
padding: 0px;
}

.fixedwidth {
/* a wide div is used for creating horizontal scrollbars for demonstration purposes */
width: 550px;
height: auto;
position: relative;
color: black;
padding: 1px;
}
.champs21_feed_title {
            background-color: #DC3434;
            color: #fff;
            font-size: 25px;
            font-weight: bold;
            height: 47px;
            padding: 10px 0 10px 20px;
            text-align: left;
            width: 100%;
          }
        </style>
        
        <?php //if ( $layout_type == 2 ) :  ?>
        <?php //$widget->run('featuredtwocolumn', $obj_post_news, $li_class_name, 4);    ?>
        <?php //endif; ?>        

    <?php //if ( $position_div ) :    ?>     
    </div>    
    <?php //endif;    ?>    
    <?php if ($total_data > $page_size) : ?>
        <div class="loading-box" style="">  
            <div class="loading"></div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php if ($featured !== 1) : ?>
    <input type="hidden" name="target" id="target" value="<?php echo $target; ?>" autocomplete="off" />
    <input type="hidden" name="page" id="page" value="<?php echo $page; ?>" autocomplete="off" />
    <input type="hidden" name="s" id="q" value="<?php echo $q; ?>" autocomplete="off" />
    <input type="hidden" name="category" id="category" value="<?php echo $category; ?>" autocomplete="off" />


    <input type="hidden" name="page-limit" id="page-limit" value="<?php echo $page_size; ?>" autocomplete="off" />
    <input type="hidden" name="current-page" id="current-page" value="<?php echo $current_page; ?>"  autocomplete="off" />
    <input type="hidden" name="page-size" id="page-size" value="<?php echo $page_size; ?>" autocomplete="off" />
    <input type="hidden" name="total_data" id="total_data" value="<?php echo $total_data; ?>" autocomplete="off" />
<?php endif; ?>

    <script>
      jQuery(function($) {
          $("a").attr("target","_blank");
      });  
    </script>    