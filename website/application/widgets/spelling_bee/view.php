<?php $arCustomNews = getFormatedContentAll($news, 125); ?>


<li  id="post-<?php echo $news->post_id; ?>" class="post-<?php echo $news->post_id; ?> <?php echo $s_post_class; ?> type-post post-content-showed status-publish format-image has-post-thumbnail hentry category-post-format tag-description tag-image tag-people tag-text col-sm-8 <?php echo ($i < $count_show) ? "shown" : ""; ?>  post-boxes ">
 
    <?php
        $widget = new Widget;
        $widget->run('seenassessment', $news);
    ?>
    <div class="post-content clearfix">
        <div class="intro-post">
            <div class="post-thumb " id="post-image" style="position: relative;">
                <?php if ($target == "index") : ?>
                <div class="post-thumb " style="width: 57px; position: absolute; left: 43%; top: 49px;">
                    <p style="<?php if($is_exclusive_found===true):?>top:104px;position:absolute;<?php else: ?>margin-top: -67px;position: relative;<?php endif;?>text-align: center;width: 100%;">
                        <img width="57" <?php if($is_exclusive_found===true):?>style="margin-left:-101%;"<?php endif; ?> src="<?php echo $news->icon ?>">
                    </p>
                </div>
                <?php endif; ?>
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
                <div id="triangle-bottomright" style="position: absolute; bottom: 0;"></div>
                <div class="post-thumb " style="width: 50%; position: absolute; left: 25%; bottom: -30px;">
                    <img class="no_toolbar" src="<?php echo $news->inside_image; ?>" class="attachment-post-thumbnail wp-post-image" alt="<?php echo $news->headline; ?>" style="border: 8px solid #fff; box-shadow: 0px 0px 20px #999;">
                </div><!-- post thumb -->
            </div><!-- post thumb -->
            
        </div> 

       
</li>
<script>
    $(document).ready(function(){
        
        $("#triangle-bottomright").css("border-left-width", $("#post-image").width() + "px");
       
    });
</script>