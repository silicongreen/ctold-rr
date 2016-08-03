<ul id="featured_1" style="<?php echo $s_width; ?>; height: 530px; margin-top:10px;">
    <?php $i=0; if($obj_post_news) foreach($obj_post_news as $news) : ?>
        <?php if ( $i > $news_to_show ) : ?>
        <?php 
            if ( $i == $news_to_show + 1 )
            {
                if ( $news_to_show == -1 )
                {
                    $li_class_name = "col-md-7";
                    $style = "list-style: none; width: 61.333%;";
                }
                else
                {
                    $li_class_name = "col-md-6";
                    $style = "list-style: none;";
                }
            }
            else
            {
                if ( $news_to_show == -1 )
                {
                    $li_class_name = "col-md-6";
                    $style = "margin-left: 50px; list-style: none;";
                }
                else
                {
                    $li_class_name = "col-md-7";
                    $style = "margin-left: 50px; list-style: none; width: 61.333%;";
                }
                
            }
        ?>    
        <?php $arCustomNews = getFormatedContentAll($news, 125); ?>
        <li style="<?php echo $style; ?>" id="post-<?php echo $news->post_id;?>" class="post-<?php echo $news->post_id;?> post type-post status-publish format-image has-post-thumbnail hentry category-post-format tag-description tag-image tag-people tag-text post animate <?php echo $li_class_name; ?> <?php echo ($i < 3) ? "shown" : ""; ?> post-boxes">
            <div class="post-content clearfix">
            <div class="intro-post">	
                <a href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" title="<?php echo $news->headline; ?>">   
                    <div class="post-thumb">
                        <?php if ( strlen(trim($news->embedded)) > 0 ) : ?>
                        <?php echo $news->embedded; ?>
                        <?php elseif ( !is_null($news->lead_material) && strlen(trim($news->lead_material)) > 0 ) : ?>
                        <img src="<?php echo base_url($news->lead_material);?>" class="attachment-post-thumbnail wp-post-image" alt="<?php echo $news->headline; ?>" height="576" width="1024">
                        <?php elseif ( strlen(trim($arCustomNews['image'])) > 0 ) : ?>
                        <img src="<?php echo $arCustomNews['image'];?>" class="attachment-post-thumbnail wp-post-image" alt="<?php echo $news->headline; ?>" height="576" width="1024">
                        
                        <?php endif; ?>
                    </div><!-- post thumb -->
                </a>
            </div> 

            <div class="post-entry">                                                             
                <div class="post-title">
                    <?php if ( $target == "index" ) : ?>
                    <p style="margin-top: -67px;position: absolute;text-align: center;width: 80%;">
                        <img width="57" src="<?php echo $news->icon?>">
                    </p>
                    <?php endif; ?>
                    <h2 class="f2">
                        <a href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" title="<?php echo $news->headline; ?>">
                            <?php echo $news->headline; ?>
                        </a>
                    </h2>
                    <?php if ( $target == "inner" ) : ?>
                    <span class="brown-subtitle">
                        <?php echo $news->name; ?>  |   <?php echo date("d M", strtotime($news->published_date)); ?>
                    </span>
                    <?php endif; ?>
                </div><!-- post-title --> 

                <div class="akmanda-excerpt"> <?php show_summary($arCustomNews['content'],$news); ?></div>
            </div>

        </div><!-- post-content -->    
        <div class="action-box">
            <div class="fb">
                <iframe src="//www.facebook.com/plugins/like.php?href=<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>&amp;width&amp;layout=button&amp;action=like&amp;show_faces=false&amp;share=true&amp;height=35&amp;appId=210972602426467" scrolling="no" frameborder="0" style="border:none; overflow:hidden; padding: 8px 8px; height:35px; " allowTransparency="true"></iframe>
            </div>
            <div id="read_later_<?php echo $news->post_id; ?>" class="read_later <?php echo ( free_user_logged_in() ) ? "" : "login-user" ; ?>">Read Later</div>
        </div>
        <!-- <div class="box-shadow"></div> --> 
    </li>
    <?php endif; ?>
    <?php if ( $i == $news_to_show + 2 ) : ?>
    <?php break; ?>
    <?php endif; ?>
    <?php $i++; endforeach; ?>
</ul>