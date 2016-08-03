<?php if ($obj_post_news): ?> 
    <ul style="position: relative; width:96%; margin:0px; " class="grid effect-6 posts" id="grid">
        <?php
        $i = 0;
        foreach ($obj_post_news as $news) :
            ?>	

            <?php $arCustomNews = getFormatedContentAll($news, 125); ?>
            <?php
            $link_array = get_post_link_url($news);
            ?>
            <li style="<?php echo $style; ?>" data-order="isotop_<?php echo $i; ?>" id="post-<?php echo $news->post_id; ?>" class="post-<?php echo $news->post_id; ?> post type-post status-publish format-image has-post-thumbnail hentry category-post-format tag-description tag-image tag-people tag-text post col-md-6 post-boxes shown">


                <div class="post-content clearfix content-div"  style="<?php echo ( $news->post_type == 4 ) ? 'background: transparent' : ''; ?>;">
                    <div class="intro-post">


                        <a href="<?php echo $link_array['url']; ?>" target="<?php echo $link_array['target'] ?>" title="<?php echo $news->headline; ?>">   

                            <div class="post-thumb">
                                <?php echo get_post_image_type_content($news, $arCustomNews, "width:100%; height:184px;"); ?>

                            </div>
                        </a>
                    </div> 

                    <?php if ($news->post_type != 2) : ?>        
                        <div class="post-entry">                                                             
                            <div class="post-title">

                                <h2 class="f5">
                                    <a href="<?php echo $link_array['url']; ?>" target="<?php echo $link_array['target'] ?>" title="<?php echo $news->headline; ?>">   
                                        <?php echo $news->headline; ?>
                                    </a>
                                </h2>

                            </div><!-- post-title --> 

                        </div>


                        <!-- <div class="box-shadow"></div> --> 

                    <?php endif; ?>
                </div><!-- post-content --> 
            </li>

            <?php
            $i++;
        endforeach;
        ?>
    </ul>
<?php else: ?>
    <ul style="position: relative; width:96%; margin:0px; " class="grid effect-6 posts" id="grid">
        <li><h2 class="f2">NO DATA FOUND</h2></li>
    </ul>
<?php endif; ?>
<?php if ($game_type == 1): ?>
    <?php
    $has_next_web = 0;
    $has_prev_web = 0;
    $current_page_web = $current_page;
    if ((($current_page + 1) * $page_size) < $total_data)
    {
        $has_next_web = 1;
    }
    if ($current_page > 0)
    {
        $has_prev_web = 1;
    }
    ?>
    <input type="hidden" id="has_next_web" name="has_next_web" value="<?php echo $has_next_web; ?>" />
    <input type="hidden" id="has_prev_web"  name="has_prev_web" value="<?php echo $has_prev_web; ?>" />
    <input type="hidden" id="current_page_web"  name="current_page_web" value="<?php echo $current_page_web; ?>" />
<?php endif; ?>
<?php if ($game_type == 2): ?>
    <?php
    $has_next_mobile = 0;
    $has_prev_mobile = 0;
    $current_page_mobile = $current_page;
    if ((($current_page + 1) * $page_size) < $total_data)
    {
        $has_next_mobile = 1;
    }
    if ($current_page > 0)
    {
        $has_prev_mobile = 1;
    }
    ?>
    <input type="hidden" id="has_next_mobile" name="has_next_mobile" value="<?php echo $has_next_mobile; ?>" />
    <input type="hidden" id="has_prev_mobile" name="has_prev_mobile" value="<?php echo $has_prev_mobile; ?>" />
    <input type="hidden" id="current_page_mobile" name="current_page_mobile" value="<?php echo $current_page_mobile; ?>" />

<?php endif; ?>

