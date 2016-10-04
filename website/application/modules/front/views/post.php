<?php

$CI = &get_instance();
$cache_name = "POST" . '_' . $post_id;

//ob_start();
?>
<script type="text/javascript" src="<?php echo base_url('scripts/jquery/jquery.als-1.6.min.js'); ?>"></script>

<?php $widget = new Widget; ?>

<div class="container" style="width: 77%;min-height:250px;">
    <input type="hidden" id="post_id_value" value="<?php echo $post_id; ?>" />
    <?php if (!$b_layout) : ?>
        <div class="noPrint" style="float: left;">
            <img src="<?php echo base_url('styles/layouts/tdsfront/images/printer1.png'); ?>" class="pinter_page noPrint" style="cursor: pointer;" onClick="print_page();" />
        <?php endif; ?>
        <?php if ($school_id == 0): ?>
            <div class="sports-inner-news yesPrint" style="padding: 5px 25px 0 25px;">    
                <?php if ($b_layout) : ?>
                    <div style="float:left;">
                        <a href="<?php echo create_link_url(sanitize($name)); ?>">
                            <h1 class="title noPrint f2" style="color:#333333;">
                                <?php
                                if (isset($display_name) && $display_name != "") {
                                    echo $display_name;
                                } else {
                                    echo $name;
                                }
                                ?>

                            </h1>
                        </a>
                    </div>
                    <input type="hidden" value="<?php echo $parent_category_id; ?>" name="main_p_category" id="main_p_category">
                    <div style="clear:both;"></div>

                    <?php $parent_category = $name; ?>
                    <?php if ($has_categories) : ?>
                        <div class="sub-categories f5">
                            <li class="layout_<?php echo $post_type; ?>">All</li>
                            <?php foreach ($obj_child_categories as $categories) : ?>
                                <li class="layout_<?php echo $post_type; ?> <?php echo ( in_array($categories->id, $a_category_ids) ) ? "selected" : ""; ?>">
                                <!--<li class="layout_<?php //echo $post_type;                                            ?> <?php //echo ( in_array($categories->id, $a_category_ids) ) ? "selected_" . $post_type : "";                                            ?>">-->
                                    <a href="<?php echo base_url(sanitize($parent_category) . "/" . sanitize($categories->name)); ?>" style="<?php echo ( in_array($categories->id, $a_category_ids) ) ? "" : "color: #93989C;"; ?>">

                                        <?php
                                        if (isset($categories->display_name) && $categories->display_name != "") {
                                            echo $categories->display_name;
                                        } else {
                                            echo $categories->name;
                                        }
                                        ?>
                                    </a>
                                </li>
                                <?php if (in_array($categories->id, $a_category_ids)) : ?>
                                    <?php $name = $categories->name; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            <?php endif; ?>    

            <?php if ($b_layout) : ?>

            <?php endif; ?>    


            <input type="hidden" name="url-post" id="url-post" value="<?php echo base_url('print_post/' . sanitize($headline) . '-' . $post_id); ?>" />
        <?php endif; ?>
        <?php
        $CI = & get_instance();
        $CI->load->config("huffas");
        ?>

        <!-- Post Wrapper Start -->

        <div class="post-wrapper-container">

            <div class="post-wrapper">

                <div style="width: 100%;">
                    <!--                    <a id="google-play-link" href="https://play.google.com/store/apps/details?id=com.champs21.schoolapp" target="_blank">
                                            <img style="width: 100%;"  src="<?php // echo base_url("styles/layouts/tdsfront/image/app_name.jpg");        ?>">
                                        </a>-->
                    <a href="http://www.classtune.com">
                        <img style="width: 100%;"  src="<?php echo base_url("styles/layouts/tdsfront/image/728x90-px-banner.gif"); ?>">
                    </a>
        <!--            <a id="world-cup-play-link" href="<?php // echo base_url();                                        ?><?php // echo $CI->config->config['world_cup_quize_link']                                        ?>" ><img style="width: 100%; height:223px;" src="<?php // echo base_url("styles/layouts/tdsfront/image/world-cup-page-banner.jpg");                                        ?>"></a>-->
                </div>

                <div class="inner-container">

                    <!-- Ad sense Place Top -->
                    <!--            <div class="add-wrapper-top">
                                    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                                     champs21-single-page-top 
                                    <ins class="adsbygoogle"
                                         style="display: inline-block; width:100%; height:90px; background-color: transparent;"
                                         data-ad-client="ca-pub-2246193625592975"
                                         data-ad-slot="7582729847">
                                    </ins>
                                    <script>
                                        (adsbygoogle = window.adsbygoogle || []).push({});
                                    </script>
                                    <img src="/styles/ads/ksrm.jpg" />
                                </div>-->
                    <!-- Ad sense Place Top -->

                    <!-- AddThis Smart Layers END -->
                    <!-- AddThis Button BEGIN -->
                    <?php if ($b_layout) : ?>
                        <?php if ($post_type == 1 || $post_type == 3) : ?>    
                            <div class="noPrint" style="clear: both; width: 93%; text-align: right;  "><a title="<?php echo $headline; ?>" id="PrinterButton" class="printer" href="javascript:;" onclick="load_print_popup('<?php echo base_url('print_post/' . sanitize($headline) . '-' . $post_id); ?>');
                                            return false;"></a></div>
                            <div class="noPrint" style="clear: both; height: 5px;"></div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <!-- AddThis Button END -->  

                    <?php if (strlen($shoulder) > 0) : ?>
                        <?php if ($post_type == 1 || $post_type == 3) : ?>    
                            <p class="sports-inner-container-paragraph5">
                                <em class="sports-inner-container-em-01" style=""><?php echo $shoulder; ?></em>
                            </p>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div <?php if ($school_id != 0): ?> class="col-md-12" <?php else: ?> class="col-md-12" <?php endif; ?>>

                        <?php if ($post_type == 1 || $post_type == 3) : ?>    
                            <h1 id="headline" class="f2" style="font-size: 30px;">
                                <?php if ($school_id != 0): ?>
                                    <?php $school_obj = new Schools($school_id); ?>
                                    <span>
                                        <?php echo $school_obj->name; ?>
                                    </span>
                                <?php else: ?> 

                                    <span><?php echo ((isset($display_name) && $display_name != "")) ? $display_name : $name; ?></span>
                                <?php endif; ?>

                                <?php echo $headline; ?>
                                <!-- Related Post Old Code -->
                                <?php /* if (isset($is_breaking) && $is_breaking && (!isset($breaking_expire) || ($breaking_expire == null) || ($breaking_expire > date("Y-m-d H:i:s")))): ?><sup style="color: #f00; font-size: 10px; padding-left:5px;">Breaking</sup><?php endif; */ ?>
                            </h1>
                        <?php else : ?>
                            <h1 id="headline" class="f2" style="font-size: 30px; text-align: center;">
                                <?php echo $headline; ?>
                            </h1>
                        <?php endif; ?>

                        <?php if (strlen($title) > 0) : ?>
                            <?php $datediff = get_post_time($published_date); ?>
                            <div class="by_line" ><?php if ($user_type == 2): ?>Candled&nbsp;<?php endif; ?>By <span class="f4" style="color: #DB3434;"><?php echo $title; ?></span> <span class="f5"><?php echo $datediff; ?> ago</span></div>
                            <div style="clear: both;"></div>
                        <?php endif; ?>

                    </div>

                    <?php if ($school_id == 0) { ?>
                        <?php $widget->run('socialbar', $post_id, $main_post_id, $main_headline, $headline, $user_view_count, $wow_count, $language, $other_language, $good_read_single, $s_lang); ?>
                    <?php } ?>

                    <div class="clearfix"></div>

                    <?php if (strlen($sub_head) > 0) : ?>
                        <p style="padding: 1px;">
                            <em class="sports-inner-container-em-02"><?php echo $sub_head; ?></em>
                        </p>
                    <?php endif; ?>

                    <div class="post materials_and_byline">
                        <?php if ($post_type == 1 || $post_type == 3) : ?> 
                            <?php if (!check_lead_image($content, $lead_material) && check_lead_image($content, $lead_material)) : ?>
                                <div style="margin-bottom:10px;">
                                    <img width="100%" class="toolbar" style="cursor: pointer;" alt="" src="<?php echo base_url() . $lead_material; ?>">
                                    <?php if ($lead_source): ?>
                                        <div id="img-source" style="color: rgb(114, 114, 2); font-style: italic; font-size: 12px; text-align: left;">Source: <?php echo $lead_source; ?></div>
                                    <?php endif; ?>

                                    <?php if ($lead_caption): ?>
                                        <div class="img_caption" style="clear: both; color: rgb(114, 114, 2); font-style: italic; text-align: center;font-size:12px;"><?php echo $lead_caption; ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <style>
                                .post #content a
                                {
                                    margin-bottom: 0px;
                                    margin-right: 0px;
                                }
                            </style>

                            <?php if ($pdf_top == 1): ?>
                                <?php if (count($all_attachment) > 0): ?>
                                    <?php foreach ($all_attachment as $value): ?>
                                        <?php $attachment = $value->file_name; ?>
                                        <?php if ($value->show): ?>

                                            <iframe src="http://docs.google.com/viewer?url=<?php echo base_url() . $attachment; ?>&embedded=true" style="width: 98%; height: 500px;" frameborder="0"></iframe>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                            <div id="content" class="content-post">
                                <?php
                                $already_showed = false;
                                if (strpos($content, "http://champs21.") !== false) {
                                    $content = str_replace("http://champs21.", "http://www.champs21.", $content);
                                }
                                if (strpos($content, "[[gallery]]") !== false) {
                                    if (file_exists('gallery/xml/post/post_' . $post_id . ".xml") && $mobile_view_type == 2) {

                                        //$widget = new Widget;

                                        $gallery_html = '</p><div  class="ym-grid"> 
                        <div style="text-align:center; width:95%; margin: 0 auto;">
                            <div style="display:none;" class="html5gallery" data-responsive="true" data-skin="horizontal "data-thumbshowtitle="false" data-width="600" data-height="270"  data-showsocialmedia="false"  
                         data-resizemode="fill" 
                         data-xml="' . base_url() . 'gallery/xml/post/post_' . $post_id . '.xml" >
                        </div>
                        </div>
                        </div></p>';


                                        $content = str_replace("[[gallery]]", $gallery_html, $content);
                                    } else {
                                        $content = str_replace("[[gallery]]", "", $content);
                                    }
                                    $already_showed = true;
                                }
                                echo $content;
                                ?>

                                <!-- Assessment -->
                                <?php if ($has_assessment) { ?>
                                    <div class="inner-container_wrapper">
                                        <?php
                                        if ($go_to_assessment) {

                                            $str_level = '';
                                            if ($assessment_has_levels) {
                                                $str_level = '/' . $next_level;
                                            }

                                            $assess_url = base_url('quiz/' . sanitize($assessment->title) . '-' . $assessment->type . '-' . $assessment->id) . $_SERVER['REQUEST_URI'] . $str_level;
                                        } else {
                                            $assess_url = '#';
                                        }
                                        ?>
                                        <a href="<?php echo $assess_url; ?>">
                                            <div class="assessment_icon">
                                                <img src="/styles/layouts/tdsfront/image/assesment_icon.png">
                                            </div>
                                            <div class="inner-container_assessment">
                                                <div class="assessment_dialogue">
                                                    <p class="f2" style="padding-top: 7px;">How smart are you?</p>
                                                    <div class="clearfix"></div>
                                                    <p class="f2">Take this quiz and ..</p>
                                                </div>
                                                <div class="assessment_find_out">
                                                    <div class="find_out f5">Find Out</div>
                                                    <div class="assessment-next">
                                                        <div class="assessment-next-arrow"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="clearfix"></div>
                                <?php } ?>
                                <!-- Assessment -->

                            </div>
                        <?php else : ?>
                            <div class="center">
                                <?php $s_gk_answers = $content; ?>
                                <?php $a_gk_answers = explode(",", $s_gk_answers); ?>
                                <?php if (!$attempt) : ?>
                                    <?php
                                    $b = 1;
                                    foreach ($a_gk_answers as $answer) :
                                        ?>
                                        <?php $a_answer = explode("|", $answer); ?>
                                        <?php $a_color_layout = explode("|", $layout_color); ?>
                                        <div id="answer_<?php echo $post_id; ?>" class="gk_layout_single_<?php echo $layout; ?> <?php echo ( free_user_logged_in() ) ? 'gk_answers' : 'login-user'; ?>" style="background: <?php echo ($layout == 1) ? (( $b == 1 || $b == 4 ) ? $a_color_layout[1] : $a_color_layout[0]) : (( $b % 2 == 0 ) ? $a_color_layout[1] : $a_color_layout[0]); ?>; ">
                                            <?php echo $a_answer[0]; ?>
                                        </div>
                                        <?php
                                        $b++;
                                    endforeach;
                                    ?>
                                <?php else : ?>
                                    <?php
                                    $b = 1;
                                    foreach ($a_gk_answers as $answer) :
                                        ?>
                                        <?php $a_answer = explode("|", $answer); ?>
                                        <?php $a_color_layout = explode("|", $layout_color); ?>
                                        <?php if ($user_answer == $a_answer[0]) : ?>
                                            <div id="answer_<?php echo $post_id; ?>" class="gk_layout_single_<?php echo $layout; ?>" style="background: <?php echo ($is_correct == 1) ? '#0A0' : '#F00'; ?>; ">
                                                <?php echo $a_answer[0]; ?>
                                            </div>
                                        <?php else : ?>
                                            <div id="answer_<?php echo $post_id; ?>" class="gk_layout_single_<?php echo $layout; ?> <?php echo ( free_user_logged_in() ) ? '' : ''; ?>" style="background: <?php echo ($layout == 1) ? (( $b == 1 || $b == 4 ) ? $a_color_layout[1] : $a_color_layout[0]) : (( $b % 2 == 0 ) ? $a_color_layout[1] : $a_color_layout[0]); ?>; ">
                                                <?php echo $a_answer[0]; ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php
                                        $b++;
                                    endforeach;
                                    ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <!-- If post has video then include those here -->
                        <?php if ($post_type == 1 || $post_type == 3) : ?>  
                            <?php if ($b_layout) : ?>
                                <?php if (count($post_videos) == 1 && $post_videos->video_type != NULL && !file_exists('gallery/xml/post/post_' . $post_id . ".xml")) : ?>
                                    <div class="video_div video_div_post noPrint">
                                        <?php if ($post_videos->video_type == "youtube") : ?>
                                            <iframe src="http://www.youtube.com/embed/<?php echo $post_videos->video_id; ?>?rel=0&wmode=transparent" height="405" width="540" allowfullscreen="" frameborder="0"></iframe>
                                        <?php elseif ($post_videos->video_type == "vimeo") : ?>
                                            <iframe src="//player.vimeo.com/video/<?php echo $post_videos->video_id; ?>" width="540" height="405" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                                        <?php endif; ?>
                                    </div>
                                <?php elseif (count($post_images) == 1 && !is_null($post_images->material_url) && !file_exists('gallery/xml/post/post_' . $post_id . ".xml")) : ?>
                                    <div class="image_div image_div_post noPrint">
                                        <img class="toolbar" src="<?php echo base_url() . $post_images->material_url; ?>" alt="<?php echo $post_images->caption; ?>" width="100%" />
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>  
                        <!-- Post Video -->
                        <div style="clear: both; height: 2px;"></div>

                    </div>

                    <?php if ($post_type == 1 || $post_type == 3) : ?>  
                        <?php if ($b_layout) : ?>    
                            <?php
                            if (!$already_showed && $mobile_view_type == 2 && file_exists('gallery/xml/post/post_' . $post_id . ".xml")) {
                                $widget->run('postgallery', $post_id);
                            }
                            ?> 

                            <?php if ($resource): ?> 

                                <?php
                                $i = 0;
                                foreach ($resource as $value) :
                                    ?>
                                    <?php if ($i == 0): ?> 
                                        <div class="sports-inner-container_resource-post noPrint" ><h3>Download Resource</h3>
                                        <?php endif; ?>
                                        <p>
                                            <a href="<?= base_url() . $value->material_url ?>" target="_blank" ><?= ($value->caption) ? $value->caption : "Download Resource"; ?></a>
                                        </p>
                                        <?php if ($i == 0): ?> 
                                        </div>
                                    <?php endif; ?>
                                    <?php
                                    $i++;
                                endforeach;
                                ?> 

                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($b_layout) : ?>

                            <?php if ($pdf_top == 0): ?>
                                <?php if (count($all_attachment) > 0): ?>
                                    <?php foreach ($all_attachment as $value): ?>
                                        <?php $attachment = $value->file_name; ?>
                                        <?php if ($value->show): ?>

                                            <iframe src="http://docs.google.com/viewer?url=<?php echo base_url() . $attachment; ?>&embedded=true" style="width: 98%; height: 500px;" frameborder="0"></iframe>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endif; ?>


                            <?php if (count($all_attachment) > 0): ?>
                                <div class="f2">
                                    <div style=" cursor: pointer; padding-top: 25px; margin-bottom:5px;">
                                        Download Resource
                                    </div>
                                </div>
                                <?php foreach ($all_attachment as $value): ?>
                                    <?php $attachment = $value->file_name; ?>
                                    <div class="col-lg-2">   
                                        <div style=" cursor: pointer;">
                                            <a href="
                                            <?php
                                            $str_f_path = $attachment;
                                            $url = base_url('download?f_path=' . $str_f_path);

                                            echo $url;
                                            ?>">
                                                <img width="80px" src="<?php echo base_url(); ?>styles/layouts/tdsfront/image/downloads.png" />
                                            </a>
                                            <br />
                                            <a href="
                                            <?php
                                            $str_f_path = $attachment;
                                            $url = base_url('download?f_path=' . $str_f_path);

                                            echo $url;
                                            ?>">
                                                   <?php echo $value->caption ?>
                                            </a>    
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endif; ?>


                        <?php if ($school_id == 0) { ?>
                            <?php $widget->run('socialbar', $post_id, $main_post_id, $main_headline, $headline, $user_view_count, $wow_count, $language, $other_language, $good_read_single, $s_lang); ?>
                        <?php } ?>

                        <div class="clearfix"></div>

                        <?php if ($post_show_publish_date): ?>    
                            <p class="sports-inner-container-paragraph10">
                                <strong class="sports-inner-container-font12">Published: </strong><em class="sports-inner-container-font12"><?php echo date("g:i a l, F d, Y", strtotime($published_date)); ?></em>
                            </p>
                        <?php endif; ?>

                        <?php if ($post_show_updated_date && $updated != Null): ?> 
                            <p   class="sports-inner-container-paragraph10">
                                <strong class="sports-inner-container-font12">Last modified: </strong><em class="sports-inner-container-font12"><?php echo date("g:i a l, F d, Y", strtotime($updated)); ?></em>
                            </p>
                        <?php endif; ?>

                        <div style="clear:both;margin-top:20px;"></div>
                        <?php if ($related_tags != "" && $b_layout): ?> 
                            <p class="related_tags">
                                <strong class="headline f2">Tags: </strong>
                            <div>
                                <em class="sports-inner-container-font12" style="clear: both;"><?php echo $related_tags; ?></em>
                            </div>
                            </p>     
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($b_layout) : ?>    

                                                                                                                                                                            <!--	<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>-->
                        <div class="noPrint">
                            <center>
                                <?php
                                $adplace_helper = new Adplace;
                                $adplace_helper->printAds(28, 0, FALSE, '0', 'details');
                                ?>
                                <!-- News Details Below article -->
                <!--		<ins class="adsbygoogle"
                                         style="display:inline-block;width:468px;height:60px"
                                         data-ad-client="ca-pub-1017056533261428"
                                         data-ad-slot="8432625397"></ins>
                                <script>
                                (adsbygoogle = window.adsbygoogle || []).push({});
                                </script>-->
                            </center>	
                        </div>

                        <?php if ($has_outbrain) : ?>   
                            <?php echo $outbrain_content; ?>
                        <?php endif; ?>
                        <?php if ($has_disqus && $can_comment == 1): ?>

                            <div class="noPrint">
                                <center>

                                    <?php
                                    $adplace_helper = new Adplace;
                                    $adplace_helper->printAds(36, 0, FALSE, '0', 'details');
                                    ?>

                                </center>	
                            </div>
                            <a target="_blank" style="float:left;clear:both;background-color:#2A2A2A;color:#fff; padding:5px 10px; margin-left:260px;" href="<?php echo base_url() ?>comment-policy">Comment Policy</a>

                            <?php echo $disqus_content; ?>                 
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($b_layout) : ?>

                        <!-- Ad Sense Place Bottom -->
                        <!--            <div class="add-wrapper-bottom">
                                        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                                         champs21-single-page-bottom 
                                        <ins class="adsbygoogle"
                                             style="display:inline-block; width:100%; height:90px; background-color: transparent;"
                                             data-ad-client="ca-pub-2246193625592975"
                                             data-ad-slot="4489662643">
                                        </ins>
                                        <script>
                                            (adsbygoogle = window.adsbygoogle || []).push({});
                                        </script>
                                        <img src="/styles/ads/ad.png" />
                                    </div>-->
                        <!-- Ad Sense Place Bottom -->

                        <hr /> 

                        <!--Next and Previous Button-->
                        <?php if ($school_id == 0): ?>
                            <div class="next-previous col-lg-2">
                                <?php if ($has_more) : ?>

                                    <?php if ($has_previous) : ?>
                                        <div class="previous">
                                            <span><a href="<?php echo $previous_news_link; ?>">Previous</a></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($has_next_news) : ?>
                                        <div class="next">
                                            <span><a href="<?php echo $next_news_link; ?>">Next</a></span>
                                        </div>
                                    <?php endif; ?>

                                <?php endif; ?> 

                            </div>
                        <?php endif; ?>
                        <!--Next and Previous Button-->

                    <?php endif; ?>

                    <div class="clearfix"></div>

                    <div class="inner-container_related check-also">
                        <!--div class="related_news_headline f2">
                            Check Out These Sections Also
                        </div-->
                        <div class="related_post">
                            <?php
                            if (is_string($s_ad_image) && $s_ad_image != "") {
                                echo $s_ad_image;
                            } else if (is_array($s_ad_image) && $s_ad_image['visible'] && ($s_ad_image['position'] == 'bottom')) {
                                echo $s_ad_image['banner'];
                            }
                            ?>
                        </div>
                    </div>


                </div>

            </div>
            <!-- Post Wrapper Ends -->

            <!-- Suggested Post Wrapper Start -->
            <div class="suggested-post-container">
                <div class="adme-div">
                    <!--                    <a href="/spellingbee" style="display: block">
                                            <img src="<?php // echo base_url('styles/layouts/tdsfront/spelling_bee/2015/spellingbee_single_ad.png');        ?>">
                                        </a>-->
                    <a href="javascript:void(0);" style="display: block">
                        <img src="<?php echo base_url('styles/layouts/tdsfront/image/337x280px.gif'); ?>">
                    </a>
                </div>
                <!--<div class="clearfix"></div>

                <div class="suggest-header-div">
                    <div class="f2">suggested for you</div>
                </div>

                <div id="demo2">                    
                    <?php //$widget->run('postdata', 'index', '', 'inner', FALSE, 0, 'index', 0, 9, 0, '', NULL, true, $post_id); ?>
                    <span class="als-prev"></span>
                    <span class="als-next"></span>
                </div>-->
                <div class="clearfix"></div>
                <?php $widget->run('singlepage_postbox',$post_id); ?>
                <div class="clearfix"></div>
            </div>

            <div class="close_suggestion f2">
                Hide
            </div>

            <!-- Suggested Post Wrapper Ends -->
            <div class="clearfix"></div>
            <div class="row" style="padding:10px;">
                
                <div class="col-md-12 col-sm-12">
                     

                    <style>
                        .flexslider .slides > li
                        {
                            margin-right:20px;
                        }
                        .flexslider .slides > li:last-child 
                        {
                            margin-right:0px;
                        }
                        .flex-caption34534535 {
                            width: 100%;
                            padding: 3%;
                            left: 0;
                            bottom: 0;
                            background: rgba(0, 0, 0, 0.6) none repeat scroll 0 0;
                            color: #fff;
                            text-shadow: 0 -1px 0 rgba(0,0,0,.3);
                            font-size: 14px;
                            line-height: 18px;
                          }

div.flex-caption{  
    background-color: black;
    bottom: 0;
    color: white;
    font-family: "tahoma";
    font-size: 15px;
    opacity: 1;
    filter:alpha(opacity=100); 
    position: absolute;
    width: 302px;
}  
p.description_content{  
    padding:10px;  
    margin:0px;  
}  
                          li.css a {
                            border-radius: 0;
                          }
                          .flex-direction-nav a
                          {
                              line-height: 33px;                              
                          }
                          .flex-control-nav
                          {
                              bottom:-18px;
                          }
                          .flexslider
                          {
                              box-shadow:0 0px 0px rgba(0, 0, 0, 0.2) !important;
                          }
                          .flex-direction-nav{margin:0px;}
                    </style>
                    
                    <script>
                        $(window).load(function(){
                            $('.flexslider').flexslider({
                              animation: "slide",
                              animationLoop: true,
                              itemWidth: 302,
                              itemMargin: 5,
                              minItems: 2,
                              maxItems: 3,
                              start: function(slider){
                                $('body').removeClass('loading');
                              }
                            });
                          });
                    </script>
                </div>
            </div>
        </div>
        <!-- Post Wrapper Container Ends -->

        <div class="clearfix"></div>

        <?php if ($post_type == 4) : $ar_color = explode('|', $layout_color); ?>
            <input type="hidden" id="gk_layout_color" name="gk_layout_color" value="<?php echo $ar_color[0]; ?>" />
            <div id="results_<?php echo $post_type; ?>" class="results" style="margin: 0 20px;">

            </div>
        <?php endif; ?>

        <?php  if ($post_type == 1 || $post_type == 3) : ?>
            <?php if ($has_related && $b_layout && $school_id == 0) : ?>
                <div class="f2" style="font-size:33px;padding:0px 25px ">
                    You May Also Like
                </div>
                <div class="inner-container_related" style="margin: 20px; width: 96%;">
                    
                    <div class="related_post">
                        
                        <section class="slider">
                            <div class="flexslider carousel">
                                <ul class="slides">
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        <?php
                        $i = 0;
                        foreach ($related_news as $news):
                        ?>
                            <?php if ($i % 2 == 0) : ?>
                    <!--          <div style="clear: both; height: 30px;"></div>-->
                            <?php endif; ?>
                             <li style="width:302px; float: left; display: block;">
                                    
                                    
                               

                                    <?php
                                        $image_related = "";
                                        if (isset($news->lead_material) && $news->lead_material) {
                                            $image_related = $news->lead_material;
                                        } else if (isset($news->image) && $news->image) {

                                            $image_related = $news->image;
                                        }
                                    ?>
                                    <?php if ($image_related): ?>
                                        <div style="height:180px;overflow:hidden;float: left; border:0;"><a href="<?php echo $news->new_link; ?>" style="border:0px;"><img src="<?php echo $image_related; ?>" width="120" height="120" style="overflow:hidden;float: left; border:0; margin-right:15px;" /></a></div>
                                    <?php endif; ?>

                                    
                                        <div class="flex-caption" style="margin:0px;line-height:20px; "><p class="description_content"><a style="color:#fff;" href="<?php echo $news->new_link; ?>"><?php echo $news->title; ?></a></p></div>
<!--                                        <p style="margin:0px; line-height:16px;font-size:12px;"><a style="color:black;" href="<?php echo $news->new_link; ?>"><?php echo $news->content; ?></a></p>-->
                                   
                                
                            </li>
                            
                            <?php
                            $i++;
                        endforeach;
                        ?>
                    
                    
                    
                    
                    
                    
                    
                    
                    
                                </ul>
                            </div>
                        </section>
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    </div>
                </div>
            <?php endif; ?>
        <?php endif;  ?>

        <div class="clearfix"></div>

        <?php
        echo "here";
        exit;
//        $more_cache_name = 'MORE_OF_' . $parent_category_id . '_FOR_POST_' . $post_id;
//        $s_more_content = $CI->cache->get($more_cache_name);
//
//        if ($s_more_content !== false) {
//            $s_more_content = $s_more_content;
//        } else {
            ?>
            <?php
//            ob_start();
            ?>

            <div class="more-news-wrapper">

                <div class="more-news-header f2">
                    More in <?php echo $parent_category; ?>
                </div>

                <div class="more-news f2">
                    <?php
                    if ($parent_category_id == 59) {
                        $mix_category = (isset($display_name) && $display_name != "") ? $display_name : $name;
                    } else {
                        $mix_category = $obj_child_categories;
                    }
                    ?>
                    <?php
                    $obj_widget = new Widget;
                    $obj_widget->run('postdata', $parent_category, $parent_category_id, 'inner', FALSE, 0, 'index', 0, 9, 0, '', $mix_category, FALSE, $post_id);
                    ?>
                </div>

            </div>

            <?php
//            $s_more_content = ob_get_contents();
//            ob_end_clean();
//        }
//        echo $s_more_content;
        ?>
        <!--div id="gif_ad" style="position:fixed;bottom:0px;width:350px;margin: 0px auto;right:-270px;display:none; " >
            <span class="gif_ad_close" style="cursor: pointer;padding: 10px;position: absolute;right: 22px;top: 6px;"></span>
            <center>               
                <a href="http://www.champs21.com/spellingbee">                    
                    <img src="<?php //echo base_url('styles/layouts/tdsfront/spelling_bee/2015/images/joto-dur-toto-300x600.jpg');?>" style="width:100%;">
                </a>
            </center>
        </div-->
        <script type="text/javascript">

            function load_print_popup(url)
            {
                var leftPosition, topPosition;
                //Allow for borders.
                leftPosition = (window.screen.width / 2) - ((730 / 2) + 10);
                //Allow for title and status bars.
                topPosition = (window.screen.height / 2) - ((730 / 2) + 50);
                window.open(url, '<?php echo time(); ?>', 'width=730,height=730,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=' + leftPosition + ',top=' + topPosition);
                return false;
            }
            function print_page( )
            {
                window.print();
                return false;
            }
            $(document).ready(function () {
                //        setInterval(function () {
                //            if($('#google-play-link').is(':visible'))
                //            {
                //               $('#google-play-link').hide();
                //               $( "#world-cup-play-link" ).show();
                //               
                //            }
                //            else
                //            {
                //              $('#world-cup-play-link').hide();
                //              $( "#google-play-link" ).show();
                //            }
                //        }, 8000);
<?php if (!free_user_logged_in()): ?>

                    $(".check_login").each(function () {
                        if ($(this).attr("id").length > 0)
                        {
                            $(this).parent().attr("href", "#");
                            $(this).parent().addClass("before-login-user");
                            $(this).parent().attr("data", $(this).attr("id"));
                            $(".ads-image").removeClass("check_login");
                        }
                        else
                        {
                            $(this).addClass("before-login-user");
                            $(this).attr("href", "#");
                            $(this).removeClass("candlepopup");
                        }
                    });
<?php endif; ?>

                if ($('.suggested-post-container #grid_1').length > 0) {
                    $("#demo2").als({
                        visible_items: ($('.suggested-post-container .adme-div').length > 1) ? 1 : 2,
                        scrolling_items: 1,
                        orientation: "vertical",
                        circular: "yes",
                        autoscroll: "no"
                    });
                }

                $(".ads-image").removeClass("toolbar");
                $(document).on("mouseover", ".ads-image", function () {
                    var sourcesrc = $(this).attr("src");

                    var ar_src = sourcesrc.split("/");
                    var imagename = ar_src[ar_src.length - 1];
                    var ar_image = imagename.split(".");
                    var hover_image = $("#base_url").val() + "styles/layouts/tdsfront/images/ads/final/" + ar_image[0] + "_hover." + ar_image[1];
                    $(this).attr("src", hover_image);
                });
                $(document).on("mouseout", ".ads-image", function () {
                    var sourcesrc = $(this).attr("src").replace("_hover", "");
                    $(this).attr("src", sourcesrc);
                });

            });
            //addthis_toolbox


        </script>

        <div class="noPrint">
            <center>

                <?php
                $adplace_helper = new Adplace;
                $adplace_helper->printAds(29, 0, FALSE, '0', 'details');
                ?>
                <!-- News Details Below article -->
<!--		<ins class="adsbygoogle"
                         style="display:inline-block;width:468px;height:60px"
                         data-ad-client="ca-pub-1017056533261428"
                         data-ad-slot="8432625397"></ins>
                <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
                </script>-->
            </center>	
        </div>
    </div>
</div>
<style>
    #world-cup-play-link
    {
        display:none;
    }
    #content img {
        height: auto;
        max-width: 100%;
    }
    .addthis_toolbox{
        background: #F7F7F7;
        border: none;
        position: relative;
    }
    .addthis_toolbox-float{
        background: #F7F7F7;
        border: 1px solid #ccc;
        position: fixed;
        top: 0;
        width: 68%;
        margin: 0 20px 0 20px;
        display: none;
    }
    .addthis_button_facebook_share iframe{
        width: 105px;
        height: 30px;
    }
    .addthis_button_facebook_like div{
        width: 78px;
        height: 30px;
    }
    .addthis_button_facebook_like div span {
        height: 20px;
        vertical-align: bottom;
        z-index: 1001;
    }
    .content-post {
        margin-top: 10px;
    }
    .next-previous {
        float: right;
        font-size: 16px;
        letter-spacing: 0.08em;
        margin: auto;
        text-align: center;
        width: 100%;
    }
    .next-previous a {
        color: #ffffff;
    }
    .next {
        background-color: #bfc3c6;
        border-radius: 5px;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        -ms-border-radius: 5px;
        -o-border-radius: 5px;
        color: #ffffff;
        cursor: pointer;
        float: right;
        padding: 10px 12px;
    }
    .previous {
        background-color: #bfc3c6;
        border-radius: 5px;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        -ms-border-radius: 5px;
        -o-border-radius: 5px;
        color: #ffffff;
        cursor: pointer;
        float: left;
        padding: 10px 12px;
    }
    .next:hover, .previous:hover {
        background-color: #373737;
        -webkit-transition: background-color 0.5s ease;
        -moz-transition: background-color 0.5s ease;
        -o-transition: background-color 0.5s ease;
        -ms-transition: background-color 0.5s ease;
        transition: background-color 0.5s ease;
    }
    .inner-container {
        float: left;
        padding: 0 30px 30px;
        width: 100%;
    }
    .language {
        cursor: pointer;
        padding: 10px;
    }
    .language a {
        float: left;
    }
    .language a em {
        color: #B6BEC0;
        font-size: 14px;
    }
    .language a em.active{
        color: #3E3C3D;
    }
    .language a em:not(.active):hover {
        color: #ffffff;
        background-color: #93989C;
        -webkit-transition: background-color 0.5s ease;
        -moz-transition: background-color 0.5s ease;
        -o-transition: background-color 0.5s ease;
        -ms-transition: background-color 0.5s ease;
        transition: background-color 0.5s ease;
    }
    .good-read-column {
        background-color: #fb3c2d;
        cursor: pointer;
        float: right;
        padding: 0px 0;
    }
    .good-read-column:hover{
        background-color: #93989C;
        -webkit-transition: background-color 0.5s ease;
        -moz-transition: background-color 0.5s ease;
        -o-transition: background-color 0.5s ease;
        -ms-transition: background-color 0.5s ease;
        transition: background-color 0.5s ease;
    }
    .fancybox-wrap {
        top: 50px !important;
    }
    .add-wrapper-top{
        padding: 20px 0 10px;
        width: 100%
    }
    .add-wrapper-top img{
        width: 100%
    }
    .add-wrapper-bottom{
        padding: 20px 0 10px;
        width: 100%
    }
    .add-wrapper-bottom img{
        width: 100%
    }
    .inner-container_wrapper{
        float: left;
        margin: 20px 0;
        width: 80%;
    }
    .inner-container_assessment{
        border: 1px solid #ddd;
        box-shadow: 0 4px 0 0 #ddd;
        float: left;
        width: 80%;
    }
    .assessment_dialogue{
        border-right: 1px solid #ddd;
        float: left;
        width: 60%;
    }
    .assessment_icon{
        float: left;
    }
    .assessment_icon img{
        float: left;
        width: 85px;
    }
    .assessment_dialogue p{
        line-height: 20px;
        padding: 0 10px;
    }
    .assessment_find_out{
        float: right;
        width: 40%;
    }
    .find_out{
        color: #ce1b22;
        float: left;
        font-size: 26px;
        font-weight: bold;
        letter-spacing: 2px;
        margin: 0;
        padding-top: 15px;
        width: 85%;
        text-align: center;
        text-transform: uppercase;
    }
    .assessment-next {
        cursor: pointer;
        margin: auto;
        padding: 20px 0 0 10px;
        float: left;
        width: 10%;
    }
    .assessment-next-arrow {
        border-color: transparent transparent transparent #b1b9bb;
        border-style: solid;
        border-width: 15px 0 15px 13px;
        height: 0;
        margin: auto;
        width: 0;

        border-radius: 7px;
        -moz-border-radius: 7px;
        -webkit-border-radius: 7px;
        -ms-border-radius: 7px;
        -o-border-radius: 7px;
    }
    .box-5 {
        cursor: pointer;
        width: 20%;
    }
    .social-bar {
        float: left;
    }
    .good-read-label {
        color: #ffffff;
        cursor: pointer;
        float: right;
        font-size: 12px;
        margin-right: 15px;
        padding-top: 10px;
    }
    .check-also {
        border: none;
        float: left;
        margin: 0;
        width: 100%;
    }
    .check-also .related_post {
        float: left;
        padding: 20px 0 0;
    }
    .check-also .related_post p {
        clear: both;
        float: left;
        margin-top: 10px;
    }
    .check-also .related_post p a img {
        cursor: pointer;
        float: left;
        margin-left: 1%;
        margin-right: 1%;
        width: 48%;
    }

    /* new design */
    .post-wrapper-container {
        float: left;
        position: relative;
        width: 100%;
    }
    .post-wrapper {
        float: left;
        padding: 15px 0 0 25px;
        width: 65%;
        transition: all 0.25s ease-out 0s;
        -moz-transition: all 0.25s ease-out 0s;
        -webkit-transition: all 0.25s ease-out 0s;
        -o-transition: all 0.25s ease-out 0s;
        -ms-transition: all 0.25s ease-out 0s;
    }
    .close_suggestion {
        background-color: #ed1c24;
        border-radius: 0;
        color: #ffffff;
        cursor: pointer;
        font-size: 12px;
        opacity: 1;
        padding: 2px 10px;
        position: absolute;
        right: -7px;
        text-align: left;
        top: 50px;
        transform: rotate(90deg);
        visibility: visible;
        width: 6%;
    }
    .suggested-post-container {
        float: left;
        width: 33%;
        transition: all 0.25s ease-out 0s;
        -moz-transition: all 0.25s ease-out 0s;
        -webkit-transition: all 0.25s ease-out 0s;
        -o-transition: all 0.25s ease-out 0s;
        -ms-transition: all 0.25s ease-out 0s;
    }
    .suggested-post-container-hidden {
        float: right !important;
        opacity: 0;
        overflow: hidden;
        transition: all 0.25s ease-in 0s;
        visibility: hidden;
        width: 0 !important;
    }
    .post-wrapper-expand {
        width: 96.5%;
    }
    .suggested-post-container ul li {
        width: 100%;
    }
    .suggested-post-container .assessment-seen-div .seen-image {
        float: left;
        margin-left: 5px;
        margin-right: 0;
        padding: 0;
        width: 26%;
    }
    .suggested-post-container .assessment-seen-div .seen {
        float: left;
        margin-top: 0 !important;
        padding-top: 2px !important;
        width: 68%;
    }
    .suggested-post-container .assessment-seen-div .seen span {
        float: left;
        margin-left: 0 !important;
        margin-top: 0 !important;
        width: 100%;
    }
    .adme-div {
        display: none;
        margin: 0 auto;
        width: 97%;
    }
    .adme-div img {
        padding: 15px 10px;
        width: 100%;
    }
    .suggest-header-div {
        width: 92%;
        margin: 0 auto;
    }
    .suggest-header-div div {
        box-shadow: 0 8px 0 0 #333333;
        font-size: 31px;
        margin-bottom: 15px;
        padding: 0;
        text-transform: uppercase;
        width: 100%;
    }
    .more-news-wrapper {
        margin: 40px 0 20px;
        padding: 5px 25px 0;
    }
    .more-news-header {
        color: #333333;
        font-size: 30px;
        margin: 0 auto 20px;
    }
    .more-news {
        margin: 0;
        padding: 0;
    }
    .more-news > div:nth-child(3) {
        width: 100% !important;
    }
    .akmanda-excerpt {
        float: left;
        padding: 10px;
        width: 90%;
    }
    #grid li.post .assessment-seen-div .seen-image {
        margin-left: 5px;
        padding: 3px 0;
        width: 25%;
    }
    #grid li.post .assessment-seen-div .seen {
        width: 64%;
    }
    #grid li.post .assessment-seen-div .seen span {
        width: 100%;
    }
    /* new design */

    /* ALS */
    .als-container {
        position: relative;
        width: 97%;
        margin: 0px auto;
    }

    .als-viewport {
        position: relative;
        overflow: hidden;
        margin: 0px auto;
        margin-left:7px;
        width: 100% !important;
        padding: 0 10px !important;
    }

    .als-wrapper {
        position: relative;
        list-style: none;
        margin: 0 0 10px 0px;
        list-style: outside none none;
        margin: 10px auto !important;
        max-width: 100% !important;
        padding: 0;
    }
    .als-item {
        position: relative;
        display: block;
        cursor: pointer;
        float: left;
    }
    #demo2 .als-item {
        margin: 5px 0;
        padding: 0px 0px;
        min-height: 120px;
        min-width: 100px;
    }

    #demo2 .als-item img {
        display: block;
        margin: 0 auto;
        vertical-align: middle;
    }

    .als-prev, .als-next {
        background-color: transparent;
        background-image: url("../styles/layouts/tdsfront/images/nextarrow.png");
        background-repeat: no-repeat; 
        position: absolute;
        cursor: pointer;
        border: 0 none;
        clear: none;
        float: right;
        height: 40px;
        position: static;
        margin-left: auto;
        margin-top: 30px;
        transform: rotate(90deg);
        width: 40px;
    }
    #demo2 .als-next {
        background-position: 2px 0;
    }
    #demo2 .als-prev {
        transform: rotate(-90deg);
    }
    #grid_1 li.post {
        margin-bottom: 5px;
    }
    #grid_1 {
        list-style: outside none none;
        margin: 10px auto !important;
        max-width: 100%;
        padding: 0;
    }
    #grid li.post {
        padding: 10px 5px 0;
    }
    /* ALS */

    @media all and (min-width: 319px) and (max-width: 479px){
        .inner-container {
            margin: 0 !important;
        }
        .post-wrapper {
            float: none;
            width: 96%;
        }
        .inner-container .social-bar {
            height: auto;
        }
        .next-previous {
            width: 70%;
        }
        .by_line {
            font-size: 14px !important;
        }
        .suggested-post-container {
            margin-top: 15px;
            width: 96%;
        }
        .box-5 {
            width: 100%;
        }
        .language {
            float: left;
        }
    }

    @media all and (min-width: 480px) and (max-width: 800px){
        .inner-container {
            margin: 0 !important;
        }
        .post-wrapper {
            float: none;
            width: 96%;
        }
        .inner-container .social-bar {
            height: auto;
        }
        .by_line {
            font-size: 14px !important;
        }
        .suggested-post-container {
            margin-top: 15px;
            width: 96%;
        }
        .next-previous {
            width: 45%;
        }
        .box-5 {
            width: 100%;
        }
        .language {
            float: left;
        }
    }

    @media all and (min-width: 801px) and (max-width: 1024px){
        .inner-container {
            margin: 0 !important;
        }
        .post-wrapper {
            float: none;
            width: 96%;
        }
        .inner-container .social-bar {
            height: auto;
        }
        .by_line {
            font-size: 14px !important;
        }
        .box-5 {
            width: 100%;
        }
        .language {
            float: left;
        }
        .suggested-post-container {
            margin-top: 15px;
            width: 96%;
        }
        .next-previous {
            width: 30%;
        }
    }
    @media all and (min-width: 1025px) and (max-width: 1280px){
        .good-read-label {
            padding-bottom: 13px;
            padding-top: 10px;
        }
    }
</style>
<?php
//$s_inner_content = ob_get_contents();
//ob_end_clean();
//
//$CI->cache->save($more_cache_name, $s_more_content, 86400);
//$CI->cache->save($cache_name, $s_inner_content, 86400);
//
//echo $s_inner_content;
//echo "here";
?>

<script type="text/javascript">

    $(document).on("click", ".gif_ad_close", function () {
        $('#gif_ad').hide();
    });

    $(window).load(function () {
        /*var html_frm_reg = $('#gif_ad').html();
        
        var cookieValue = readCookie('spellingbee_ad');

        if (cookieValue != 1) {
            $.fancybox({
                width: 350,
                content: html_frm_reg,
                height: 'auto',
                transitionIn: 'fade',
                transitionOut: 'fade',
                openEffect: 'elastic',
                openSpeed: 350,
                fitToView: false,
                autoSize: false,
                padding: 0,
                margin: 0
                ,afterLoad: function () {
                    setTimeout(function () {
                        $.fancybox.close();
                    }, 12000);
                }
            });

            createCookie('spellingbee_ad', 1);

        }*/

    });

</script>
