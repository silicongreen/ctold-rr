<?php
$widget = new Widget;    
$i = 0;
if ($obj_post_news)
    foreach ($obj_post_news as $news) :
        ?>		
        <?php $arCustomNews = getFormatedContentAll($news, 125); ?>
        <?php 
            $li_class_name = 'col-md-6';
            $li_class_name = ( $news->is_breaking && date("Y-m-d H:i:s") < $news->breaking_expire ) ? 'col-sm-8' : ( ( $news->is_exclusive && date("Y-m-d H:i:s") < $news->exclusive_expired ) ? 'col-sm-12' : 'col-md-6' );
        ?>
        <?php $style = "position: relative;"; ?>
         <?php if ( in_array($news->post_layout, array(1,2,3)) ) : ?>   
                <?php $widget->run('post_type_' . $news->post_layout, $news, $style, $s_post_class, $li_class_name, $i, $count_show, $is_exclusive_found, $target,'from-inside'); ?>         
          <?php elseif (!is_null($news->short_title) && strlen(trim($news->short_title)) > 0 && in_array($news->sort_title_type, array(1,2,3,4,5)) ) : ?>
              <?php $widget->run('short_title_block' . $news->sort_title_type, $news, $style, $s_post_class, $li_class_name, $i, $count_show, $is_exclusive_found, $target,'from-inside'); ?>
          <?php else : ?>
        <li style="position: relative;" id="post-<?php echo $news->post_id; ?>" class="post-<?php echo $news->post_id; ?> post post-content-showed type-post status-publish format-image has-post-thumbnail hentry category-post-format tag-description tag-image tag-people tag-text post ajax-hide <?php echo $li_class_name; ?> <?php echo ($i < 3) ? "shown" : ""; ?>  <?php echo (!is_null($news->short_title) && strlen(trim($news->short_title)) > 0) ? " format-quote   tag-quote " : ""; ?>  ">
            <div class="post-content clearfix">
                <div class="intro-post">	
                    <?php if ($news->post_type != 2) : ?>   
                        <a href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" title="<?php echo $news->headline; ?>">   
                        <?php else : ?>

                        <?php endif; ?>
                        <div class="post-thumb">
                                            <?php if (strlen(trim($news->embedded)) > 0) : ?>
                                                <?php echo $news->embedded; ?>
                                            <?php elseif (!is_null($news->lead_material) && strlen(trim($news->lead_material)) > 0) : ?>
                                                <?php if ($news->post_type == 2) : ?>   
                                                    <a class="add-link" title="<?php echo $news->lead_caption; ?>" href="<?php echo $news->lead_link; ?>" target="_blank">       
                                                    <?php endif; ?>     

                                                        <img class="ad" src="<?php echo $arCustomNews['lead_material']; ?>" class="attachment-post-thumbnail wp-post-image <?php echo ( $news->post_type == 2 ) ? 'ad' : ''; ?>" alt="<?php echo $news->headline; ?>" height="576" width="1024">
                                                    <?php if ($news->post_type == 2) : ?>   
                                                    </a>
                                                <?php endif; ?>
                                            <?php elseif (strlen(trim($arCustomNews['image'])) > 0) : ?>
                                                <?php if (count($arCustomNews['all_image']) == 1) : ?>
                                                    <?php if ($news->post_type == 2) : ?>   
                                                        <a class="add-link"   title="<?php echo $arCustomNews['all_image_title'][0]; ?>"  href="<?php echo $arCustomNews['all_image_url'][0]; ?>" target="_blank">       
                                                        <?php endif; ?> 
                                                        <img class="ad" src="<?php echo $arCustomNews['image']; ?>" class="attachment-post-thumbnail wp-post-image" alt="<?php echo $news->headline; ?>" height="576" width="1024">
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
                                                                            <a class="add-link"  title="<?php echo $arCustomNews['all_image_title'][$key]; ?>" href="<?php echo $arCustomNews['all_image_url'][$key]; ?>" target="_blank">       
                                                                            <?php endif; ?>        
                                                                            <img class="ad"    src="<?php echo $image; ?>" alt="<?php echo $news->headline; ?>" />
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
                <?php if ($news->post_type != 2) : ?>    
                    <div class="post-entry">
                        <?php
                        $showed = false;
                        if (!is_null($news->short_title) && strlen(trim($news->short_title)) > 0) :
                            ?>
                            <div class="quote-wrap">

                                <blockquote> 
                                    <i class="icon icon-fontawesome-webfont"></i>
                                    <p><?php echo $news->short_title; ?></p>
                                </blockquote>

                            </div>
                            <?php
                            $showed = true;
                        endif;
                        ?>
                        <div class="post-title">
                            <?php if ($target == "index") : ?>
                                <?php if ($showed): ?>
                                    <!--                                                don nothing-->
                                <?php elseif ($news->show_byline_image) : ?>
                                    <?php if (!is_null($news->author_image) && strlen(trim($news->author_image)) > 0) : ?>
                                        <div class="akmanda_author_img"><img src="<?php echo base_url($news->author_image); ?>"></div>
                                    <?php else : ?>
                                        <p style="margin-top: -67px;position: relative;text-align: center;width: 100%;">
                                            <img width="57" src="<?php echo $news->icon ?>">
                                        </p>
                                    <?php endif; ?>
                                <?php else : ?>
                                    <p style="margin-top: -67px;position: relative;text-align: center;width: 100%;">
                                        <img width="57" src="<?php echo $news->icon ?>">
                                    </p>
                                <?php endif; ?>

                            <?php endif; ?>
                            <h2 class="f2">
                                <a href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" title="<?php echo $news->headline; ?>">
                                    <?php echo $news->headline; ?>
                                </a>
                            </h2>
                            <?php if ($target == "inner") : ?>
                                <span class="brown-subtitle">
                                    <?php echo $news->name; ?>  |   <?php echo date("d M", strtotime($news->published_date)); ?>
                                </span>
                            <?php elseif ($target == "index") : ?>
                                <span class="brown-subtitle">
                                    <?php echo get_post_time($news->published_date); ?>
                                </span>
                            <?php endif; ?>
                        </div><!-- post-title --> 

                        <div class="akmanda-excerpt"> <?php echo $arCustomNews['content']; ?></div>
                    </div>
                </div><!-- post-content -->    

                <?php
                   $widget = new Widget;
                   $widget->run('actionbox', $news);
                ?>

                <!-- <div class="box-shadow"></div> --> 

            <?php endif; ?>
        </li>
        <?php endif; ?>
        <?php
        $i++;
    endforeach;   
?>
