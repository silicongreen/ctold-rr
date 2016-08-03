<div class="col-md-12" >
    <div class="extra_news_headline f2" style="font-size:26px;margin-left:19px;background: #ADACB0;
         border-bottom: 4px solid #727273;padding:0px; width:94%; margin-bottom:10px;">
        Related Videos
    </div>
    <?php foreach($obj_post_news as $news): ?>
        <?php 
        if($post_id == $news->id)
        {
            continue;
        }
        ?>
        <?php $arCustomNews = getFormatedContentAll($news, 125); ?>
        <div class="col-md-12" style="margin-top:15px;width:94%;margin-left:19px; float:left; clear:both;" >
            <div class="col-md-4" >
                <a  href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" title="<?php echo $news->headline; ?>">   
                   
                        
                        <?php if ( ! is_null($news->lead_material) ) : ?>
                        <img src="<?php echo $arCustomNews['lead_material']; ?>" style="width:100%;" class="attachment-post-thumbnail wp-post-image" alt="<?php echo $news->headline; ?>">
                        <?php elseif ( strlen(trim($arCustomNews['image'])) > 0 ) : ?>
                        <img src="<?php echo $arCustomNews['image'];?>"  style="width:100%;"  class="attachment-post-thumbnail wp-post-image" alt="<?php echo $news->headline; ?>" >
                        <?php endif; ?>
                   
                </a>
            </div>
            <div class="col-md-7" style="margin-left:8px;" >
                <a class="video-title" style="font-size:13px;" href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" title="<?php echo $news->headline; ?>">
                   <?php echo $news->headline; ?></b
                </a>
                <div class="col-md-12" style="font-size:11px;float:left; font-size:11px; width:100%; clear:both; color:#818087" >
                        <?php if ( $news->title!="") : ?>  
                                <p>By <?php echo $news->title; ?></p>
                        <?php endif; ?>      
                        <?php if ( $news->user_view_count > 0 ) : ?>
                                <p><?php echo $news->user_view_count; ?> views</p>
                        <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    
</div>  