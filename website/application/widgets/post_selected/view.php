<li class="post col-md-6 opinion-selected type-post status-publish format-image has-post-thumbnail hentry category-post-format tag-description tag-image tag-people tag-text shown post-boxes ">
    <div class="flex-wrapper_news">
        <div id="slider" class="flexslider_news">
            
            <ul class="slides_news" style="padding: 0px; margin: 0px;">
                <?php
                if ($obj_post_news)
                    foreach ($obj_post_news as $news) :
                        ?>
                        <?php $arCustomNews = getFormatedContentAll($news, 125); ?>
                        <li style="<?php echo $style; ?>" id="post-<?php echo $news->post_id; ?>" class="news_slides post-<?php echo $news->post_id; ?> type-post post-content-showed status-publish format-image has-post-thumbnail hentry category-post-format tag-description tag-image tag-people tag-text col-md-12 shown  post-boxes ">

                            <?php
                            if (!$ecl) {
                                $widget = new Widget;
                                $widget->run('seenassessment', $news);
                            }
                            ?>

                            <div class="post-content clearfix" >
                                <div class="intro-post">
                                    
                                    <div class="selected_header">
                                        <img src="/styles/layouts/tdsfront/image/nirbachito.png">
                                    </div>

                                    <a href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" title="<?php echo $news->headline; ?>">   

                                        <div class="post-thumb selected_thumb">
                                            <?php if (strlen(trim($news->embedded)) > 0) : ?>
                                                <?php echo $news->embedded; ?>
                                            <?php elseif (!is_null($news->lead_material) && strlen(trim($news->lead_material)) > 0) : ?>
                                                <?php if ($news->post_type == 2) : ?>   
                                                    <a class="add-link" title="<?php echo $news->lead_caption; ?>" href="<?php echo $news->lead_link; ?>"   <?php if($news->ad_target!=2): ?> target="_blank"<?php endif; ?>>       
                                                    <?php endif; ?>
                                                    <img class="no_toolbar" src="<?php echo $arCustomNews['lead_material']; ?>" class="attachment-post-thumbnail wp-post-image <?php echo ( $news->post_type == 2 ) ? 'no_toolbar' : ''; ?>" alt="<?php echo $news->headline; ?>" <?php if ($is_exclusive_found === true): ?>style="width:475px;height:265px; "<?php endif; ?>>
                                                    <?php if ($news->post_type == 2) : ?>   
                                                    </a>
                                                <?php endif; ?>
                                            <?php elseif (strlen(trim($arCustomNews['image'])) > 0) : ?>
                                                <?php if (count($arCustomNews['all_image']) == 1): ?>
                                                    <?php if ($news->post_type == 2) : ?>   
                                                        <a class="add-link" title="<?php echo $arCustomNews['all_image_title'][0]; ?>"  href="<?php echo $arCustomNews['all_image_url'][0]; ?>"   <?php if($news->ad_target!=2): ?> target="_blank"<?php endif; ?>>       
                                                        <?php endif; ?>

                                                        <img class="no_toolbar" src="<?php echo ($ecl && !empty($news->author_image)) ? $news->author_image : $arCustomNews['image']; ?>" class="attachment-post-thumbnail wp-post-image" alt="<?php echo $news->headline; ?>" <?php if ($is_exclusive_found === true): ?>style="width:475px;height:265px; "<?php endif; ?>>
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
                                                                            <a class="add-link"  title="<?php echo $arCustomNews['all_image_title'][$key]; ?>" href="<?php echo $arCustomNews['all_image_url'][$key]; ?>"   <?php if($news->ad_target!=2): ?> target="_blank"<?php endif; ?>>       
                                                                            <?php endif; ?>        
                                                                            <img <?php echo ($ecl) ? 'style="display: inline;"' : ''; ?> class="no_toolbar <?php echo $img_class; ?>" src="<?php echo $image; ?>" alt="<?php echo $news->headline; ?>" <?php if ($is_exclusive_found === true): ?>style="width:475px;height:265px; "<?php endif; ?> />
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

                                    </a>

                                </div> 


                                <div class="post-entry" > 
                                    <?php $showed = false ?>
                                    
                                    <?php
                                        if ($ecl || $opinion) { ?>

                                            <div class="f2 ecl-auther-name">
                                                <?php echo $news->title; ?>
                                            </div>

                                            <div class="clearfix"></div>

                                            <?php if (!empty($news->designation)) { ?>
                                                <div class="f2 ecl-auther-designation">
                                                    <?php echo $news->designation; ?>
                                                </div>
                                            <?php } ?>

                                    <?php } ?>
                                    
                                    <div class="post-title" >

                                        <h2 class="f2" <?php if ($news->post_layout == 4): ?> style="text-align:center !important;" <?php endif; ?>>
                                            <a <?php if ($news->post_layout == 4): ?> style="margin-left:17px;" <?php endif; ?> href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" title="<?php echo $news->headline; ?>">

                                                <?php echo $news->headline; ?>


                                            </a>
                                        </h2>

                                    </div><!-- post-title --> 

                                    <div class="akmanda-excerpt" <?php if ($is_exclusive_found === true): ?>style="margin: 20px 30px;"<?php endif; ?>> 
                                        <?php if ($news->sub_head): ?>
                                            <p><?php echo $news->sub_head; ?></p>
                                        <?php endif; ?>
                                        <?php show_summary($arCustomNews['content'], $news); ?>
                                    </div>


                                </div><!-- post-content -->    

                                <?php
                                $widget = new Widget;
                                $widget->run('actionbox', $news);
                                ?>                                   
                        </li>
                        
                        <?php
                    endforeach;
                ?>
            </ul>
        </div>
    </div>
</li>        

<style type="text/css">
    .opinion-selected .ecl-auther-name {
        text-align: left;
        padding-top: 20px;
    }
    .opinion-selected .ecl-auther-designation {
        text-align: left;
        padding-left: 0;
    }
    .opinion-selected .post-title {
        margin: 15px 0 3px;
        padding-left: 18px;
        text-align: left;
    }
    .opinion-selected .selected_header{
        border-bottom: 1px solid #ccc;
        padding: 15px;
        width: 100%;
    }
    .opinion-selected .selected_header img{
        width: 25%;
    }
    .opinion-selected .selected_thumb{
        padding: 12px;
    }
    .opinion-selected .flex-control-nav{
        top: 32px;
        right: 20px;
        text-align: right;
    }
    
    .opinion-selected .flex-control-nav li{
        list-style: square;
    }
    
    .opinion-selected .flex-control-paging li a{
        border-radius: 0px;
        color: #4C4C4C;
        font-size: 0;
    }
    .opinion-selected .flex-control-paging li a.flex-active{
        color: #191919;
        font-size: 0;
    }
    
</style>