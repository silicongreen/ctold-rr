<?php $arCustomNews = getFormatedContentAll($news, 55); ?>

<?php if ( $from == "main" ) : ?>
<li style="<?php echo $style; ?>" id="post-<?php echo $news->post_id; ?>" class="post-<?php echo $news->post_id; ?> <?php echo $s_post_class; ?> type-post post-content-showed status-publish format-image has-post-thumbnail hentry category-post-format tag-description tag-image tag-people tag-text <?php echo $li_class_name; ?> <?php echo ($i < $count_show) ? "shown" : ""; ?> <?php echo (!is_null($news->short_title) && strlen(trim($news->short_title)) > 0) ? " format-quote  tag-quote " : ""; ?> post-boxes ">
<?php else : ?>
<li style="position: relative;" id="post-<?php echo $news->post_id; ?>" class="post-<?php echo $news->post_id; ?> post post-content-showed type-post status-publish format-image has-post-thumbnail hentry category-post-format tag-description tag-image tag-people tag-text post ajax-hide <?php echo $li_class_name; ?> <?php echo ($i < 3) ? "shown" : ""; ?>  <?php echo (!is_null($news->short_title) && strlen(trim($news->short_title)) > 0) ? " format-quote   tag-quote " : ""; ?>  ">    
<?php endif; ?> 
    <?php
        $widget = new Widget;
        $widget->run('seenassessment', $news);
    ?>
    <div class="post-content clearfix" style="width:100%;<?php echo ( $news->post_type == 4 || $news->post_type == 3) ? 'background: transparent;' : ''; ?><?php echo ($is_exclusive_found) ? 'height:278px;' : '' ?>">
        <div style="width:100%" class="intro-post <?php if($is_exclusive_found===true): ?>col-xs-6<?php endif; ?>">
            <div class="post-thumb " style="position: relative; width: 100%;">
                <?php if (!is_null($news->lead_material) && strlen(trim($news->lead_material)) > 0) : ?>
                    <?php if ($news->post_type == 2) : ?>   
                        <a class="add-link" title="<?php echo $news->lead_caption; ?>" href="<?php echo $news->lead_link; ?>"   <?php if($news->ad_target!=2): ?> target="_blank"<?php endif; ?>>       
                        <?php endif; ?>     

                            <img class="no_toolbar" src="<?php echo $arCustomNews['lead_material']; ?>" class="attachment-post-thumbnail wp-post-image <?php echo ( $news->post_type == 2 ) ? 'no_toolbar' : ''; ?>" alt="<?php echo $news->headline; ?>" <?php if($is_exclusive_found===true): ?>style="width:475px;height:265px; "<?php endif; ?>>
                        <?php if ($news->post_type == 2) : ?>   
                        </a>
                    <?php endif; ?>
                <?php elseif (strlen(trim($arCustomNews['image'])) > 0) : ?>
                    <?php if ($news->post_type == 2) : ?>   
                        <a class="add-link"   title="<?php echo $arCustomNews['all_image_title'][0]; ?>"  href="<?php echo $arCustomNews['all_image_url'][0]; ?>"   <?php if($news->ad_target!=2): ?> target="_blank"<?php endif; ?>>       
                    <?php endif; ?> 
                        <img class="no_toolbar" src="<?php echo $arCustomNews['image']; ?>" class="attachment-post-thumbnail wp-post-image" alt="<?php echo $news->headline; ?>" style="clip: rect(0px,60px,200px,0px);"<?php if($is_exclusive_found===true): ?>style="width:475px;height:265px; "<?php endif; ?>>
                    <?php if ($news->post_type == 2) : ?>   
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
                <div style="width: 50%; background: #fff; height: 100%; position: absolute; top: 0; left: 0; opacity: 0.80;">
                    <div style="display: inline-table; vertical-align: bottom; position: absolute; bottom: 0px; margin-left: 10px;">
                        <div class="akmanda-excerpt" style="color:black;"  <?php if($is_exclusive_found===true): ?>style="margin: 20px 30px;"<?php endif; ?>> 
                            <?php show_summary($arCustomNews['content'],$news); ?>
                        </div>
                    </div>
                </div>
                <div style="width: 50%; height: 100%; position: absolute; top: 0; right: 0;">
                    <div style="position: absolute; top: 10px; ">
                        <div class="post-title" style="margin: 0; padding: 5px 0;">
                            <h2 class="f5" style="font-size: 25px; font-weight: bold; color: #fff; letter-spacing: 0.04em; ">
                                <!--<a class="word-of-the-day-swf word-of-the-day-sound-link" href="<?php //echo $swf_external_url . 'swf/spellbee/'; ?>" title="<?php //echo $news->headline; ?>">-->
                                <a class="word-of-the-day-sound-link" href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" title="<?php echo $news->headline; ?>" style="color: #fff;">
                                    <?php echo $news->headline; ?>
                                </a>
                                <p style="color: #fff; font-size: 15px;"><?php echo $news->title; ?></p>
                            </h2>
                        </div>
                    </div>
                </div>
                <?php if ($target == "index") : ?>
                <div style="width: 57px; height: 100%; position: absolute; top: 45%; left: 42%;">
                    <img width="57"  src="<?php echo $news->icon ?>">
                </div>
                <?php endif; ?>
            </div><!-- post thumb -->
            
        </div> 

        
        </div><!-- post-content -->  
        
        
       <?php
            $widget = new Widget;
            $widget->run('actionbox', $news,$target,$category_id);
        ?>
        <div style="clear:both; height: 5px;"></div>
        <!-- <div class="box-shadow"></div> --> 
</li>