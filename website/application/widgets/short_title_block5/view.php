<style>
#parallelogram { width: 150px; height: 100px;  background: red; }
div.callout {
	float: left;
}

div.callout {
	background-color: #FFF;
	background-image: -moz-linear-gradient(top, #fff, #fff);
	color: #000;
	padding: 20px;
	border-radius: 13px;
        -moz-border-radius: 13px;
        -webkit-border-radius: 13px;
        -o-border-radius: 13px;
        box-shadow: 0px 0px 20px #999;
	min-height: 50px;
       
	/*box-shadow: 0 1px 0 rgba(255, 255, 255, 0.2) inset;*/
}
.bend{
     -webkit-transform: skew(-14deg); -moz-transform: skew(-14deg); -o-transform: skew(-14deg);
    opacity: .8;
}
.callout::before {
	content: "";
	width: 0px;
	height: 0px;
	border: 0.8em solid transparent;
	position: absolute;
}

.callout.top::before {
	left: 45%;
	bottom: -20px;
	border-top: 10px solid #444;
}

.callout.bottom::before {
	left: 45%;
	top: -20px;
	border-bottom: 10px solid #444;
}

.callout.left::before {
	right: -20px;
	top: 40%;
	border-left: 10px solid #444;
}

.callout.right::before {
	left: -20px;
	top: 0%;
	border-right: 10px solid #FFF;
}

.callout.top-left::before {
	left: 7px;
	bottom: -20px;
	border-top: 10px solid #444;
}

.callout.top-right::before {
	right: 7px;
	bottom: -20px;
	border-top: 10px solid #444;
}
</style>
<?php $arCustomNews = getFormatedContentAll($news, 125); ?>
<?php if ( $from == "main" ) : ?>
<li style="<?php echo $style; ?>" id="post-<?php echo $news->post_id; ?>" class="post-<?php echo $news->post_id; ?> <?php echo $s_post_class; ?> type-post post-content-showed status-publish format-image has-post-thumbnail hentry category-post-format tag-description tag-image tag-people tag-text <?php echo $li_class_name; ?> <?php echo ($i < $count_show) ? "shown" : ""; ?> <?php echo (!is_null($news->short_title) && strlen(trim($news->short_title)) > 0) ? " format-quote  tag-quote " : ""; ?> post-boxes ">
<?php else : ?>
<li style="position: relative;" id="post-<?php echo $news->post_id; ?>" class="post-<?php echo $news->post_id; ?> post post-content-showed type-post status-publish format-image has-post-thumbnail hentry category-post-format tag-description tag-image tag-people tag-text post ajax-hide <?php echo $li_class_name; ?> <?php echo ($i < 3) ? "shown" : ""; ?>  <?php echo (!is_null($news->short_title) && strlen(trim($news->short_title)) > 0) ? " format-quote   tag-quote " : ""; ?>  ">    
<?php endif; ?>    
    <div class="post-content clearfix" style="margin-top:0px;<?php echo ( $news->post_type == 4 || $news->post_type == 3) ? 'background: transparent;' : ''; ?><?php echo ($is_exclusive_found) ? 'height:278px;' : '' ?>">
        <div class="intro-post <?php if($is_exclusive_found===true): ?>col-xs-6<?php endif; ?>">
            <div class="post-thumb " style="position: relative;">
                <?php if (strlen(trim($news->embedded)) > 0) : ?>
                    <?php echo $news->embedded; ?>
                <?php elseif (!is_null($news->lead_material) && strlen(trim($news->lead_material)) > 0) : ?>
                    <?php if ($news->post_type == 2) : ?>   
                        <a class="add-link" title="<?php echo $news->lead_caption; ?>" href="<?php echo $news->lead_link; ?>" target="_blank">       
                        <?php endif; ?>     

                            <img class="ad" src="<?php echo $arCustomNews['lead_material']; ?>" class="attachment-post-thumbnail wp-post-image <?php echo ( $news->post_type == 2 ) ? 'ad' : ''; ?>" alt="<?php echo $news->headline; ?>" <?php if($is_exclusive_found===true): ?>style="width:475px;height:265px; "<?php endif; ?>>
                        <?php if ($news->post_type == 2) : ?>   
                        </a>
                    <?php endif; ?>
                <?php elseif (strlen(trim($arCustomNews['image'])) > 0) : ?>
                    <?php if (count($arCustomNews['all_image']) == 1): ?>
                        <?php if ($news->post_type == 2) : ?>   
                            <a class="add-link"   title="<?php echo $arCustomNews['all_image_title'][0]; ?>"  href="<?php echo $arCustomNews['all_image_url'][0]; ?>" target="_blank">       
                            <?php endif; ?> 
                            <img class="ad" src="<?php echo $arCustomNews['image']; ?>" class="attachment-post-thumbnail wp-post-image" alt="<?php echo $news->headline; ?>" <?php if($is_exclusive_found===true): ?>style="width:475px;height:265px; "<?php endif; ?>>
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
                                                <a class="add-link"  title="<?php echo $arCustomNews['all_image_title'][$key]; ?>" href="<?php echo $arCustomNews['all_image_url'][$key]; ?>" target="_blank">       
                                                <?php endif; ?>        
                                                <img class="ad"    src="<?php echo $image; ?>" alt="<?php echo $news->headline; ?>" <?php if($is_exclusive_found===true): ?>style="width:475px;height:265px; "<?php endif; ?> />
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
                <div class="post-thumb callout bend" style="width: 70%; left: 16%; margin: 0 auto; position: relative; bottom: 90px; border-radius: 33px;">
                    <div style="background: url('<?php echo base_url('styles/layouts/tdsfront/images/bg-quote-box.png'); ?>') no-repeat; position: absolute; top: 15px; left: 15px; width: 38px; height: 31px; "></div>
                    <div style="padding-top: 30px; padding-bottom: 25px; padding-left: 30px;">
                        <?php echo $news->short_title; ?>
                    </div>
                    <div style="background: url('<?php echo base_url('styles/layouts/tdsfront/images/bg-quote-box.png'); ?>') no-repeat; position: absolute; right: 20px; bottom: 10px; width: 38px; height: 31px; -moz-transform: scaleX(-1);-o-transform: scaleX(-1);-webkit-transform: scaleX(-1);transform: scaleX(-1);filter: FlipH;-ms-filter: 'FlipH';"></div>
                    <?php if ($target == "index") : ?>
                    <div class="post-thumb " style="width: 57px; position: absolute; bottom: -40px; left: 39%;">
                        <p style="<?php if($is_exclusive_found===true):?>top:104px;position:absolute;<?php else: ?>margin-top: -67px;position: relative;<?php endif;?>text-align: center;width: 100%;">
                            <img width="57" <?php if($is_exclusive_found===true):?>style="margin-left:-101%;"<?php endif; ?> src="<?php echo $news->icon ?>">
                        </p>
                    </div>
                    <?php endif; ?>
                </div><!-- post thumb -->
            </div><!-- post thumb -->
            
        </div> 
        
        <?php if ($news->post_type == 4) : ?>    
            <div class="post-entry">     
                <div class="post-title">
                    <h2 class="f2" style="font-size: 32px; border: 1px solid #000;">
                        <a href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" title="<?php echo $news->headline; ?>">
                            <?php echo $news->headline; ?>
                        </a>
                    </h2>
                </div><!-- post-title --> 

                <div class="akmanda-excerpt"> 
                    <?php $s_gk_answers = $arCustomNews['content']; ?>
                    <?php $a_gk_answers = explode(",", $s_gk_answers); ?>
                    <?php
                    $b = 1;
                    foreach ($a_gk_answers as $answer) :
                        ?>
                        <?php $a_answer = explode("|", $answer); ?>
                        <?php $a_color_layout = explode("|", $news->layout_color); ?>
                        <div class="gk_layout_<?php echo $news->layout; ?>" style="background: <?php echo ($news->layout == 1) ? (( $b == 1 || $b == 4 ) ? $a_color_layout[1] : $a_color_layout[0]) : (( $b % 2 == 0 ) ? $a_color_layout[1] : $a_color_layout[0]); ?>; ">
                            <?php echo $a_answer[0]; ?>
                        </div>
                        <?php
                        $b++;
                    endforeach;
                    ?>
                </div>

            </div>    
        <?php elseif ($news->post_type == 3) : ?>

            <div class="post-entry">     

                <a class="word-of-the-day-swf" href="<?php echo $swf_external_url . 'swf/spellbee/'; ?>">
                    <div class="word-of-the-day">
                        <img width="170px" src="<?php echo base_url('styles/layouts/tdsfront/image/Words-ofthe-day-logo.png'); ?>" />
                    </div>
                </a>

                <div class="post-title" style="margin: 0; padding: 5px 0; border: 1px solid #000;">
                    <h2 class="f5" style="font-size: 35px; letter-spacing: 0.04em; margin-left: -30px;">
                        <!--<a class="word-of-the-day-swf word-of-the-day-sound-link" href="<?php //echo $swf_external_url . 'swf/spellbee/'; ?>" title="<?php //echo $news->headline; ?>">-->
                        <a class="word-of-the-day-sound-link" href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" title="<?php echo $news->headline; ?>">
                            <?php echo $news->headline; ?>
                        </a>
                    </h2>
                </div><!-- post-title -->


                <div class="word-of-the-day-sound">
                    <button class="word-of-the-day-sound-btn"></button>
                </div>

            </div>

        <?php elseif ($news->post_type != 2) : ?>        
        <div class="post-entry <?php if($is_exclusive_found===true): ?>col-xs-6<?php endif; ?>" style="margin-top: 4px; clear: both; " > 
                
                <div class="post-title" <?php echo ($is_exclusive_found===true)? 'style="margin: 24px 0px;"' : 'style=" padding-top:5px; padding-bottom: 5px; margin-top: 80px;"'?>>
                    
                            
                        <h2 class="f2" style="text-align: center; padding: 5px; ">
                            <a href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" title="<?php echo $news->headline; ?>">
                                <?php echo $news->headline; ?>
                            </a>
                        </h2>
                    
                            
                        
                </div><!-- post-title --> 

                <div class="akmanda-excerpt" style="padding-top: 30px;"> 
                    <?php show_summary($arCustomNews['content'],$news); ?>
                </div>
            </div>
        </div><!-- post-content -->    
        <?php
            $widget = new Widget;
            $widget->run('actionbox', $news);
        ?>

<?php endif; ?>
</li>