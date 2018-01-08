<div style="width: 100%;" class="grid effect-6 posts-<?php echo $current_page; ?>" >
    <div class="extra_news_headline f2" style="margin:0;padding: 10px 26px; font-size:25px; width:100%;text-align:left; margin-bottom:8px;">
        <?php echo $extra_column_name; ?>
    </div> 

    <?php $i = 0;
    if ($ar_3rd_column_extra_data)
        foreach ($ar_3rd_column_extra_data as $news) :
            ?>
        <?php $arCustomNews = getFormatedContentAll($news, 125);
        ?>
            <div style="margin-bottom:22px;" id="post-<?php echo $news->post_id; ?>" class="post-<?php echo $news->post_id; ?>  type-post status-publish format-image has-post-thumbnail hentry category-post-format tag-description tag-image tag-people tag-text post col-md-12 animate <?php echo ($i < 3) ? "shown" : ""; ?> post-boxes">
                <div class="post-content clearfix" style="padding:0px;">
                    <div style="float: left; width: 100%;">
                        <a href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" title="<?php echo $news->headline; ?>">   
                            <div class="post-thumb">

                                <?php if (!is_null($news->embedded) && strlen(trim($news->embedded)) > 0) : ?>
                                    <?php echo $news->embedded; ?>
                                <?php elseif (!is_null($news->lead_material) && strlen(trim($news->lead_material)) > 0) : ?>
                                  
                                    <img src="<?php echo $arCustomNews['lead_material']; ?>" class="attachment-post-thumbnail wp-post-image" alt="<?php echo $news->headline; ?>" >
                                <?php elseif (strlen(trim($arCustomNews['image'])) > 0) : ?>
                                    <?php if ( count($arCustomNews['all_image']) == 1 ) : ?>
                        <img src="<?php echo $arCustomNews['image'];?>" class="attachment-post-thumbnail wp-post-image" alt="<?php echo $news->headline; ?>" height="576" width="1024">
                        <?php else : ?>
                        <div class="flex-wrapper">
                            <div id="slider" class="flexslider" style="border: 1px solid #Fff;">
                                <ul class="slides">
                                    <?php foreach( $arCustomNews['all_image'] as $image ): ?>
                                    <li>
                                        <img src="<?php echo $image;?>" alt="<?php echo $news->headline; ?>" />
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                        <?php endif; ?>
                                <?php endif; ?>
                            </div><!-- post thumb -->
                        </a>
                    </div>
                    <div class="video-desc" style="float: left; clear:both; width: 100%;">
                        <div style="width:90%; margin: 20px auto;">
                            <a class="video-title f5" style="font-size:20px;" href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" title="<?php echo $news->headline; ?>">
                            <?php echo $news->headline; ?>
                            </a>
                            <?php if ($news->user_view_count > 0) : ?>
                                <div><?php echo $news->user_view_count; ?> views</div>
                            <?php endif; ?>
                            <?php if ($news->title): ?>
                                <div>Developed By <font color="red" style="font-family:arial;"><?php echo $news->title; ?></font></div>
        <?php endif; ?>
                            <div class="akmanda-excerpt" style="margin:5px 0px !important;"> <?php show_summary($arCustomNews['content'],$news); ?></div>
                            <div class="button-read-more"  style="margin:5px 0px">
                                <a class="f5" style="color: #000;"  href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" title="<?php echo $news->headline; ?>">
                                    Read More
                                </a>
                            </div>
                        </div>


                    </div><!-- post-content -->   
                </div> 
             </div>   
        <?php $i++;
    endforeach;
?>
        <!-- <div class="box-shadow"></div> --> 
</div>
    
    <style>
        .button-read-more {
            -moz-box-shadow:inset 0px -1px 0px 0px #ffffff;
            -webkit-box-shadow:inset 0px -1px 0px 0px #ffffff;
            box-shadow:inset 0px -1px 0px 0px #ffffff;
            background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #ffffff), color-stop(1, #ffffff) );
            background:-moz-linear-gradient( center top, #ffffff 5%, #ffffff 100% );
            filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#ffffff');
            background-color:#ffffff;
            -webkit-border-top-left-radius:3px;
            -moz-border-radius-topleft:3px;
            border-top-left-radius:3px;
            -webkit-border-top-right-radius:3px;
            -moz-border-radius-topright:3px;
            border-top-right-radius:3px;
            -webkit-border-bottom-right-radius:3px;
            -moz-border-radius-bottomright:3px;
            border-bottom-right-radius:3px;
            -webkit-border-bottom-left-radius:3px;
            -moz-border-radius-bottomleft:3px;
            border-bottom-left-radius:3px;
            text-indent:0;
            border:1px solid #dcdcdc;
            display:inline-block;
            color:#666666;
            font-family:Arial;
            font-size:15px;
            font-weight:bold;
            font-style:normal;
            height:30px;
            line-height:25px;
            padding: 1px 5px 0px 7px !important;

            text-decoration:none;
            text-align:center;
            text-shadow:1px 1px 0px #ffffff;
        }
        .button-read-more:hover {
            background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #e9e9e9), color-stop(1, #f9f9f9) );
            background:-moz-linear-gradient( center top, #e9e9e9 5%, #f9f9f9 100% );
            filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#e9e9e9', endColorstr='#f9f9f9');
            background-color:#e9e9e9;
        }.button-read-more:active {
            position:relative;
            top:1px;
        }
    </style>    