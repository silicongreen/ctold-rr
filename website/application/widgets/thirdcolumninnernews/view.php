<div style="width: 100%;" class="grid effect-6" >

    <?php if (!$ecl) { ?>

        <div class="extra_news_headline f2" style="margin:0; width:100%; margin-bottom:10px;">
            <?php echo $extra_column_name; ?>
        </div> 
        <?php
        $i = 0;
        if ($ar_3rd_column_extra_data)
            foreach ($ar_3rd_column_extra_data as $news) :
                ?>		
                <?php $arCustomNews = getFormatedContentAll($news, $ar_extra_config['char_count']); ?>
                <div  style="margin-bottom:5px;"  id="post-<?php echo $news->post_id; ?>" class="post-<?php echo $news->post_id; ?>  type-post status-publish format-image has-post-thumbnail hentry category-post-format tag-description tag-image tag-people tag-text post col-md-12 animate <?php echo ($i < 3) ? "shown" : ""; ?> post-boxes">
                    <?php if ($ar_extra_config['type'] == "news") : ?>
                        <div class="post-content clearfix">
                            <div class="intro-post">	
                                <a href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" title="<?php echo $news->headline; ?>">   
                                    <div class="post-thumb">
                                        <?php if (!is_null($news->lead_material)) : ?>
                                            <img src="<?php echo $arCustomNews['lead_material']; ?>" class="attachment-post-thumbnail wp-post-image" alt="<?php echo $news->headline; ?>" height="576" width="1024">
                                        <?php else : ?>
                                            <img src="<?php echo $arCustomNews['image']; ?>" class="attachment-post-thumbnail wp-post-image" alt="<?php echo $news->headline; ?>" height="576" width="1024">
                                        <?php endif; ?>
                                    </div><!-- post thumb -->
                                </a>
                            </div> 

                            <div class="post-entry">                                                             
                                <div class="post-title">
                                    <?php if ($target == "index") : ?>
                                        <p style="margin-top: -67px;position: absolute;text-align: center;width: 80%;">
                                            <img width="57" src="<?php echo $news->icon ?>">
                                        </p>
                                    <?php endif; ?>
                                    <h2 class="f2">
                                        <a href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" title="<?php echo $news->headline; ?>">
                                            <?php echo $news->headline; ?>
                                        </a>
                                    </h2>

                                </div><!-- post-title --> 

                                <div class="akmanda-excerpt"> <?php show_summary($arCustomNews['content'], $news); ?></div>
                            </div>

                        </div><!-- post-content -->    
                        <div class="action-box">
                            <div class="fb">
                                <iframe src="//www.facebook.com/plugins/like.php?href=<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>&amp;width&amp;layout=button&amp;action=like&amp;show_faces=false&amp;share=true&amp;height=35&amp;appId=210972602426467" scrolling="no" frameborder="0" style="border:none; overflow:hidden; padding: 8px 8px; height:35px; " allowTransparency="true"></iframe>
                            </div>
                            <div id="read_later_<?php echo $news->post_id; ?>" class="read_later <?php echo ( free_user_logged_in() ) ? "" : "login-user"; ?>">Read Later</div>
                        </div>
                    <?php endif; ?> 
                    <!-- <div class="box-shadow"></div> --> 
                </div>
                <?php
                $i++;
            endforeach;
        ?>

    <?php } else { ?>

        <div class="candel-ecl-banner candlepopup">
            <img class="right-banner" src="<?php echo $ecl_banner; ?>" >

            <?php if (!empty($ecl_button)) { ?>
                <img class="right-banner-button" src="<?php echo $ecl_button; ?>" >
            <?php } ?>
        </div>

    <?php } ?>
</div>

<style type="text/css">
    .candel-ecl-banner {
        position: relative;
    }
    .candel-ecl-banner img.right-banner {
        width: 100%;
    }
    .candel-ecl-banner img.right-banner-button {
        bottom: 16%;
        position: absolute;
        right: 25%;
        width: 55%;
    }
</style>