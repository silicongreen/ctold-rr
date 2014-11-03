<?php $arCustomNews = getFormatedContentAll($news, 125); ?>

<?php if ( $from == "main" ) : ?>
<li style="<?php echo $style; ?>" id="post-<?php echo $news->post_id; ?>" class="post-<?php echo $news->post_id; ?> <?php echo $s_post_class; ?> type-post post-content-showed status-publish format-image has-post-thumbnail hentry category-post-format tag-description tag-image tag-people tag-text <?php echo $li_class_name; ?> <?php echo ($i < $count_show) ? "shown" : ""; ?> <?php echo (!is_null($news->short_title) && strlen(trim($news->short_title)) > 0) ? " format-quote  tag-quote " : ""; ?> post-boxes ">
<?php else : ?>
<li style="position: relative;" id="post-<?php echo $news->post_id; ?>" class="post-<?php echo $news->post_id; ?> post post-content-showed type-post status-publish format-image has-post-thumbnail hentry category-post-format tag-description tag-image tag-people tag-text post ajax-hide <?php echo $li_class_name; ?> <?php echo ($i < 3) ? "shown" : ""; ?>  <?php echo (!is_null($news->short_title) && strlen(trim($news->short_title)) > 0) ? " format-quote   tag-quote " : ""; ?>  ">    
<?php endif; ?>    
    <div class="post-content clearfix" style="<?php echo ( $news->post_type == 4 || $news->post_type == 3) ? 'background: transparent;' : ''; ?><?php echo ($is_exclusive_found) ? 'height:278px;' : '' ?>">
        <div class="intro-post <?php if($is_exclusive_found===true): ?>col-xs-6<?php endif; ?>">
            <div class="post-thumb ">
                <?php if (strlen(trim($news->embedded)) > 0) : ?>
                    <?php echo $news->embedded; ?>
                <?php elseif (!is_null($news->lead_material) && strlen(trim($news->lead_material)) > 0) : ?>
                    <?php if ($news->post_type == 2) : ?>   
                        <a class="add-link" title="<?php echo $news->lead_caption; ?>" href="<?php echo $news->lead_link; ?>" target="_blank">       
                        <?php endif; ?>     

                            <img class="ad" src="<?php echo $arCustomNews['lead_material']; ?>" class="attachment-post-thumbnail wp-post-image <?php echo ( $news->post_type == 2 ) ? 'ad' : ''; ?>" alt="<?php echo $news->headline; ?>" <?php if($is_exclusive_found===true): ?>style="width:475px;height:265px; "<?php endif; ?>>
                        <?php if ($news->post_type == 2) : ?>   
                        </a>
                    <?php endif; ?>
                <?php elseif (strlen(trim($arCustomNews['image'])) > 0) : ?>
                    <?php if (count($arCustomNews['all_image']) == 1): ?>
                        <?php if ($news->post_type == 2) : ?>   
                            <a class="add-link"   title="<?php echo $arCustomNews['all_image_title'][0]; ?>"  href="<?php echo $arCustomNews['all_image_url'][0]; ?>" target="_blank">       
                            <?php endif; ?> 
                            <img class="ad" src="<?php echo $arCustomNews['image']; ?>" class="attachment-post-thumbnail wp-post-image" alt="<?php echo $news->headline; ?>" <?php if($is_exclusive_found===true): ?>style="width:475px;height:265px; "<?php endif; ?>>
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
                                                <img class="ad"    src="<?php echo $image; ?>" alt="<?php echo $news->headline; ?>" <?php if($is_exclusive_found===true): ?>style="width:475px;height:265px; "<?php endif; ?> />
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
        </div> 
        
        <?php if ($news->post_type == 4) : ?>    
            <div class="post-entry">     
                <div class="post-title">
                    <h2 class="f2" style="font-size: 32px;">
                        <a href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" title="<?php echo $news->headline; ?>">
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
                        <p><?php echo $news->title; ?></p>
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
                                            <?php if ($target == "index") : ?>
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
                                            <h2 class="f2">
                                                <a href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" title="<?php echo $news->headline; ?>">
                                            <?php echo $news->headline; ?>
                                                </a>
                                            </h2>
                                                <?php if ($target == "inner") : ?>
                                                <span class="brown-subtitle">
                                                <?php //echo $news->name . ' | '; ?><?php echo date("d M", strtotime($news->published_date)); ?>
                                                </span>
                                                <?php elseif ($target == "index") : ?>
                                                <span class="brown-subtitle">
                                                <?php echo get_post_time($news->published_date); ?>
                                                </span>
                    <?php endif; ?>
                                        </div><!-- post-title --> 

                                        <div class="akmanda-excerpt" <?php if($is_exclusive_found===true): ?>style="margin: 20px 30px;"<?php endif; ?>> <?php echo $arCustomNews['content']; ?></div>
                                        <div class="post-entry" style="margin-left: 10px;">     
                                            <?php $l=0;foreach( $images as $image ) :?>
                                            <div style="float: left; width: 33%;">
                                                <img width="90%" style="height:70px;"  src="<?php echo $image->image; ?>">
                                            </div>
                                            <?php if ( $l == 2 ) break; ?>
                                            <?php $l++; endforeach;?>
                                        </div>    
                                        <div class="clearfix" style="clear: both; height:5px;"></div>
                                    </div>
        </div><!-- post-content -->    

        <!-- <div class="box-shadow"></div> --> 
        
        <div class="action-box">
<!--                                    <div class="fb">
                                        <iframe src="//www.facebook.com/plugins/like.php?href=<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>&amp;width&amp;layout=button&amp;action=like&amp;show_faces=false&amp;share=true&amp;height=35&amp;appId=210972602426467" scrolling="no" frameborder="0" style="border:none; overflow:hidden; padding: 8px 8px; height:35px; " allowTransparency="true"></iframe>
                                    </div>-->
									<div style="width:25%;float:left;padding:0 10px;margin-left:10px;margin-top:8px;">
										<div class="seen-image col-lg-6"><img src="<?php echo base_url("styles/layouts/tdsfront/images/social/seen.png"); ?>" /></div>
										<div class="seen col-lg-6"><span style="font-size:12px;margin-left:5px;margin-top:6px;color:#666"><?php echo $news->user_view_count; ?></span></div>
									</div>
                                    <div data="read_later" id="read_later_<?php echo $news->post_id; ?>" class="read_later <?php echo ( free_user_logged_in() ) ? "" : "before-login-user"; ?>">Read Later</div>
                                </div>

<?php endif; ?>
</li>