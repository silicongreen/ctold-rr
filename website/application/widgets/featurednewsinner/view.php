<div class='featured col-lg-12'>
    <div class="close-carrosel"></div>
    <ul id="carrosel-news" class="col-lg-12">
        <?php
        $i = 0;
        if ($obj_post_news)
            foreach ($obj_post_news as $news) :
                ?>	
                <?php $arCustomNews = getFormatedContentAll($news, 300); ?>
                <li id="carrosel_<?php echo $news->post_id; ?>" class="carrosel_news">
                    <div class="carrosel-news">
                        <div class='carrosel-images'>
                            <?php if (strlen(trim($news->embedded)) > 0) : ?>
                                <?php echo $news->embedded; ?>
                            <?php elseif (strlen($news->lead_material) > 0) : ?>
                                <div class="post-thumb">
                                    <a href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" title="<?php echo $news->headline; ?>">
                                        <img src="<?php echo $arCustomNews['lead_material']; ?>" class="attachment-post-thumbnail wp-post-image" alt="<?php echo $news->headline; ?>">
                                    </a>
                                </div>
                            <?php elseif (strlen(trim($arCustomNews['image'])) > 0) : ?>
                                <div class="post-thumb">
                                    <a href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" title="<?php echo $news->headline; ?>">
                                        
                                       <img src="<?php echo $arCustomNews['image']; ?>" class="attachment-post-thumbnail wp-post-image" alt="<?php echo $news->headline; ?>" height="576" width="1024">
                                       
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class='carrosel-news-content'>
                            <div class="post-title">
                                <h2 class="f2" style="font-size:40px;">
                                    <a href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" title="<?php echo $news->headline; ?>">
                                        <?php if ($news->name != $news->headline) : ?>
                                            <span style="color: #FB3D2D;"><?php echo $news->name; ?></span> | <?php echo $news->headline; ?>
                                        <?php else : ?>
                                            <?php echo $news->headline; ?>
                                        <?php endif; ?>
                                    </a>
                                </h2>
                            </div><!-- post-title --> 
                            <div class="akmanda-excerpt"> <?php show_summary($arCustomNews['content'],$news); ?></div>
                        </div>
                    </div>                    
                </li>               
                <?php
                $i++;
            endforeach;
        ?>
    </ul>    
    <div class="clearfix"></div>
    <div class="pagination" id="pager"></div>
    <div id="timer1" class="timer"></div>
</div>
<div style='clear: both;'></div>