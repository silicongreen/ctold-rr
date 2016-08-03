<div style="width: 100%;" class="grid effect-6 posts-<?php echo $current_page; ?>" >
    <div class="extra_news_headline f2" style="margin:0; width:100%; margin-bottom:10px;">
        <?php echo $extra_column_name; ?>
    </div> 
    <div style="margin-bottom:5px;" id="post-<?php echo $news->post_id;?>" class="post-<?php echo $news->post_id;?>  type-post status-publish format-image has-post-thumbnail hentry category-post-format tag-description tag-image tag-people tag-text post col-md-12 animate <?php echo ($i < 3) ? "shown" : ""; ?> post-boxes">
        <?php $i=0; if($ar_3rd_column_extra_data) foreach($ar_3rd_column_extra_data as $news) : ?>
        <?php $arCustomNews = getFormatedContentAll($news, 125); ?>
        <div class="post-content clearfix" style="padding: 10px;">
            <div style="float: left; width: 50%; margin-right: 2%;">
                <a href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" title="<?php echo $news->headline; ?>">   
                    <div class="post-thumb">
                        <?php if ( ! is_null($news->embedded) || strlen($news->embedded) > 0 ) : ?>
                        <?php echo $news->embedded; ?>
                        <?php elseif ( ! is_null($news->lead_material) ) : ?>
                        <img src="<?php echo $arCustomNews['lead_material']; ?>" class="attachment-post-thumbnail wp-post-image" alt="<?php echo $news->headline; ?>" height="576" width="1024">
                        <?php elseif ( strlen(trim($arCustomNews['image'])) > 0 ) : ?>
                        <img src="<?php echo $arCustomNews['image'];?>" class="attachment-post-thumbnail wp-post-image" alt="<?php echo $news->headline; ?>" height="576" width="1024">
                        <?php endif; ?>
                    </div><!-- post thumb -->
                </a>
            </div>
            <div class="video-desc" style="float: left; width: 47%;">
                <a class="video-title" href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" title="<?php echo $news->headline; ?>">
                    <?php echo $news->headline; ?>
                </a>
                <br />By <?php echo $news->title; ?>
                <?php if ( $news->user_view_count > 0 ) : ?>
                <br /><?php echo $news->user_view_count; ?> views
                <?php endif; ?>
            </div>
            

        </div><!-- post-content -->   
        <?php $i++; endforeach; ?>
        <!-- <div class="box-shadow"></div> --> 
    </div>
</div>    