<div class='featured-full col-lg-12'>
    <ul  style="margin: 0; list-style: none; ">
        <?php $i=0; if($obj_post_news) foreach($obj_post_news as $news) : ?>	
            <?php $arCustomNews = getFormatedContentAll($news, 300); ?>
        
            <li id="carrosel_<?php echo $news->post_id; ?>" class="carrosel_news-data rounded" style="list-style: none; margin-bottom: 20px; background: #fff; border: 1px solid #ccc; border-bottom: 3px solid #ccc;  ">
                <div class='carrosel-images' style="height: 140px; width: 100%;">
                   <?php if ( strlen($news->lead_material) > 0 ) : ?>
                    <div class="post-thumb1">
                            <a href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" title="<?php echo $news->headline; ?>">
                                <img src="<?php echo $arCustomNews['lead_material']; ?>" class="attachment-post-thumbnail wp-post-image" alt="<?php echo $news->headline; ?>" style="height: 146px; width: 100%;">
                            </a>
                        </div>
                    <?php else : ?>
                        <div class="post-thumb1">
                            <a href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" title="<?php echo $news->headline; ?>">
                                <img src="<?php echo $arCustomNews['image'];?>" class="attachment-post-thumbnail wp-post-image" alt="<?php echo $news->headline; ?>" height="276" width="100">
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <p style="margin-top: -35px; position: absolute;width: 100%; left: 417px;">
                    <img src="<?php echo $news->icon?>">
                </p>
                
                <div class='carrosel-news-content' style="padding: 10px; width: 100%">
                        <div class="post-title">
                            <h2 class="f2">
                                <a href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" title="<?php echo $news->headline; ?>">
                                    <?php if ( $news->name != $news->headline ) : ?>
                                    <span style="color: #FB3D2D;"><?php echo $news->name; ?></span> | <?php echo $news->headline; ?>
                                    <?php else : ?>
                                    <?php echo $news->headline; ?>
                                    <?php endif; ?>
                                </a>
                            </h2>
                        </div><!-- post-title --> 
                        <div class="akmanda-excerpt" style="width: 90%; "> <?php echo $arCustomNews['content']; ?></div>
                </div>
            </li>               
        <?php $i++; endforeach; ?>
    </ul>    
    <div class="clearfix"></div>
    <div class="pagination" id="pager"></div>
    <div id="timer1" class="timer"></div>
</div>
<div style='clear: both;'></div>