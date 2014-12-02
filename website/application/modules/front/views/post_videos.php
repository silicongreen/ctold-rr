<div class="container" style="width: 77%;min-height:250px;">
<input type="hidden" id="post_id_value" value="<?php echo $post_id; ?>" />
<?php if ( ! $b_layout ) :?>
<div class="noPrint" style="float: left;"><img src="<?php echo base_url('styles/layouts/tdsfront/images/printer1.png'); ?>" class="pinter_page noPrint" style="cursor: pointer;" onClick="print_page();" />
<?php endif; ?>
<div class="sports-inner-news yesPrint" style="padding: 5px 25px 0 25px;">    
        <?php if ( $b_layout ) : ?>
        <div style="float:left;">
            <a href="<?php echo create_link_url(sanitize($name)); ?>">
                <h1 class="title noPrint f2" style="color:#93989C;">
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
    
<!--    <div style="float:right;">
        <center>		
             News Detials headline above
             <?php /* ?>
            <?php if (sanitize($name) == "eca.") { ?>
                <img src="<?php echo base_url('/upload/ads/banners/eca.jpg'); ?>" width="350" >
            <?php } else { ?>
                <img src="<?php echo base_url('/upload/ads/banners/' . sanitize($name) . '.jpg'); ?>" width="350" >

            <?php } ?>
            <?php
            //$adplace_helper = new Adplace;
            //$adplace_helper->printAds( 27, null, FALSE,"0",'details' );
            ?>
            
            <?php */ ?>
              

        </center>
    </div>-->
    
    <div style="clear:both;"></div>
        <?php $related_category = $a_category_ids[0]; ?>
        <?php $parent_category = $name; ?>
        <?php if ( $has_categories ) : ?>
        <div class="sub-categories f5">
            <li class="layout_<?php echo $post_type; ?>">All</li>
            <?php foreach ( $obj_child_categories as $categories ) :?>
            <?php 
                if(in_array($categories->id, $a_category_ids))
                {
                   $related_category =  $categories->id;
                }        
            ?>
            <li class="layout_<?php echo $post_type; ?> <?php echo ( in_array($categories->id, $a_category_ids) ) ? "selected": ""; ?>">
            <!--<li class="layout_<?php //echo $post_type; ?> <?php //echo ( in_array($categories->id, $a_category_ids) ) ? "selected_" . $post_type : ""; ?>">-->
                <a href="<?php echo base_url(sanitize($parent_category) . "/" . sanitize($categories->name));?>" style="<?php echo ( in_array($categories->id, $a_category_ids) ) ? "" : "color: #93989C;"; ?>">
                   
                        <?php 
                            if(isset($categories->display_name) && $categories->display_name!="")
                            {
                               echo $categories->display_name; 
                            }
                            else
                            {
                                echo $categories->name;
                            }
                         ?>
                </a>
            </li>
            <?php if ( in_array($categories->id, $a_category_ids) ) : ?>
            <?php $name = $categories->name; ?>
            <?php endif; ?>
            <?php endforeach;?>
        </div>
        <?php endif; ?>
</div>



    
<?php if ( $b_layout ) : ?>
<style>
    .addthis_toolbox{
        background: #F7F7F7;
        border: 1px solid #ccc;
        height: 60px;
        position: relative;
        border-radius: 10px 10px 0 0;
    }
    .addthis_toolbox div{
        height: 60px;
    }
    .addthis_toolbox-float{
        background: #F7F7F7;
        border: 1px solid #ccc;
        height: 60px;
        position: fixed;
        top: 0;
        width: 68%;
        margin: 0 20px 0 20px;
        display: none;
    }    
    .addthis_toolbox-float div{
        height: 60px;
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
/*        width: 450px !important;*/
        z-index: 1001;
    }
    .seen-image{
        padding-bottom: 7px;
        padding-left: 0;
    }
    .seen-image img {
        width: 22px;
    }
    .seen{
        padding-top: 5px;
    }
    .seen h2{
        color: #b1b8ba;
        font-family: tahoma;
        font-size: 14px;
        line-height: 0;
    }
    .good-read-text{
        float: left;
        height: 60px;
        padding-top: 2px;
    }
    .good-read-image{
        margin-left: auto;
        margin-right: auto;
        padding-top: 8px;
        text-align: center;
    }
    .good-read-text h2{
        color: #FFF;
        font-size: 25px;
        font-weight: 600;
    }
    .good-read-button{
        cursor: pointer;
        padding: 0;
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
        padding: 2px 12px;
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
        padding: 2px 12px;
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
        padding: 0 30px 50px;
    }
    .language {
        cursor: pointer;
        padding: 0 0 11px 5px;
    }
    .language em {
        background-color: #dfdfdf;
        padding: 10px 10px 10px 8px;
    }
    .language a em {
        color: #93989c;
        font-size: 8px;
    }
    .language a em.active{
        color: #FB3C2D;
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
</style>

<?php endif; ?>    
    

        <input type="hidden" name="url-post" id="url-post" value="<?php echo base_url('print_post/' . sanitize($headline) . '-' . $post_id); ?>" />
        <?php endif; ?>
        <div class="inner-container" style="margin: 10px 20px;">
        <!-- AddThis Smart Layers END -->
        <!-- AddThis Button BEGIN -->
        <?php if ( $b_layout) : ?>
        <?php if ( $post_type == 1 || $post_type == 3) : ?>    
        <div class="noPrint" style="clear: both; width: 93%; text-align: right;  "><a title="<?php echo $headline; ?>" id="PrinterButton" class="printer" href="javascript:;" onclick="load_print_popup('<?php echo base_url('print_post/' . sanitize($headline) . '-' . $post_id); ?>'); return false;"></a></div>
        <div class="noPrint" style="clear: both; height: 5px;"></div>
        <?php endif; ?>
        <?php endif; ?>
<!-- AddThis Button END -->  
    <?php ob_start(); ?>        
        <?php if (strlen($shoulder) > 0) : ?>
        <?php if ( $post_type == 1 || $post_type == 3) : ?>    
        <p class="sports-inner-container-paragraph5">
            <em class="sports-inner-container-em-01" style=""><?php echo $shoulder; ?></em>
        </p>
        <?php endif; ?>
        <?php endif; ?>
        
        <div class="col-md-9">
            
            <?php if ( $post_type == 1 || $post_type == 3) : ?>    
            <h1 id="headline" class="f2" style="font-size: 30px;">
                <span><?php echo $name; ?></span>
                <?php echo $headline; ?>
                <?php if (isset($is_breaking) && $is_breaking && (!isset($breaking_expire) || ($breaking_expire == null) || ($breaking_expire > date("Y-m-d H:i:s")))): ?><sup style="color: #f00; font-size: 10px; padding-left:5px;">Breaking</sup><?php endif; ?>
            </h1>
            <?php else : ?>
            <h1 id="headline" class="f2" style="font-size: 30px; text-align: center;">
                <?php echo $headline; ?>
            </h1>
            <?php endif; ?>
            
            <?php if (strlen($title) > 0) : ?>
                <?php $datediff = get_post_time($published_date); ?>
                <div class="by_line" >By <i class="f4"><?php echo $title; ?></i> <span class="f5"><?php echo $datediff; ?> ago</span></div>
                <div style="clear: both;"></div>
            <?php endif; ?>

        </div>
        
        <div class="col-md-3 social-bar">
            <div class="col-lg-3" style="padding: 10px 9px 13px 2px; text-align: center;">
                
                <div class="col-md-12 language">
                    <a href="#">
                        <em style="clear: both;" class="active sports-inner-container-font12">
                            <?php echo get_language($language);?>
                        </em> 
                    </a>
                </div>
                
                <?php if (!empty($other_language)) : ?>
                    <?php
                    $ar_lang = explode(",", $s_lang);
                    foreach ($ar_lang as $lang) {
                        ?>
                        <?php $a_l = explode("-", $lang); ?>
                        <?php if (count($a_l) > 1 && $a_l[1] == 0) : ?>
                            <div class="col-md-12 language">
                                <a href="<?php echo base_url(sanitize($main_headline) . '-' . $main_post_id); ?>">
                                    <em style="clear: both;" class="sports-inner-container-font12">
                                        <?php echo get_language($a_l[0]); ?>
                                    </em>
                                </a>
                            </div>
                        <?php else : ?>
                            <div class="col-md-12 language">
                                <a href="<?php echo base_url(sanitize($main_headline) . '-' . $main_post_id . '/' . $a_l[0]); ?>">
                                    <em style="clear: both;" class="sports-inner-container-font12">
                                        <?php echo get_language($a_l[0]); ?>
                                    </em>
                                </a>
                            </div>

                        <?php endif; ?>

                    <?php } ?>
                <?php endif; ?>
                
            </div>
            
            <div class="col-lg-6" style="border-left: 1px solid #ddd; border-right: 1px solid #ddd; padding: 5px 15px 8px 0;">
                <div class="col-md-12">
                    <div class="seen-image col-lg-3"><img src="<?php echo base_url("styles/layouts/tdsfront/images/social/seen.png"); ?>" /></div>
                    <div class="seen col-lg-9"><h2 class=""><?php echo $user_view_count; ?></h2></div>
                </div>
                <div class="col-md-12">
                    <a class="addthis_button_facebook_like" fb:like:layout="button_count" fb:like:href="<?php echo create_link_url(NULL, $headline,$post_id,false,true,false); ?>"></a>
                </div>
                <div class="col-md-12">
                    <a class="addthis_button_facebook_share" fb:share:layout="horizontal"  addthis:title="<?php echo $headline; ?>" addthis:url="<?php echo create_link_url(NULL, $headline,$post_id); ?>" addthis:description="<?php echo $fb_desc; ?>"></a>
                </div>
            </div>
            <div class="col-lg-3 good-read-column" style="padding: 10px 0;">
                <div class="good-read-button normal <?php echo ( free_user_logged_in() ) ? "" : "login-user"; ?>">
                    <div class="good-read-image"><img src="<?php echo base_url("styles/layouts/tdsfront/images/social/good-read.png"); ?>" width="35" /></div>
                    <?php echo $good_read_single; ?>
                </div>
                <div class="f2 normal" style="cursor: pointer; text-align: center; font-size: 14px; color: #ffffff;">Good Read</div>
            </div>
            
            <script type="text/javascript">var addthis_config = {"data_track_addressbar":false, "data_track_clickback" : false};</script>
            <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-52bca22436b47685"></script>
            
        </div>
        
        <div class="clearfix"></div>
        
        <?php if (strlen($sub_head) > 0) : ?>
            <p style="padding: 1px;">
                <em class="sports-inner-container-em-02"><?php echo $sub_head; ?></em>
            </p>
        <?php endif; ?>
        
        <div class="post materials_and_byline">
            <?php if ( $post_type == 1 || $post_type == 3 ) : ?> 
            <?php if (!check_lead_image($content, $lead_material) && check_lead_image($content, $lead_material)) : ?>
            <div style="margin-bottom:10px;">
                <img width="100%" class="toolbar" style="cursor: pointer;" alt="" src="<?php echo base_url() . $lead_material; ?>">
                <?php if($lead_source): ?>
                    <div id="img-source" style="color: rgb(114, 114, 2); font-style: italic; font-size: 12px; text-align: left;">Source: <?php echo $lead_source;?></div>
                <?php endif; ?>
                
                <?php if($lead_caption): ?>
                    <div class="img_caption" style="clear: both; color: rgb(114, 114, 2); font-style: italic; text-align: center;font-size:12px;"><?php echo $lead_caption;?></div>
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
            
            <?php if ($pdf_top==1): ?>
                <?php if (count($all_attachment)>0): ?>
                   <?php foreach($all_attachment as $value):?>
                    <?php $attachment = $value->file_name; ?>
                    <?php if ($value->show): ?>

                    <iframe src="http://docs.google.com/viewer?url=<?php echo base_url() . $attachment; ?>&embedded=true" style="width: 98%; height: 500px;" frameborder="0"></iframe>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php endif; ?>
            <?php endif; ?>
            <div class="clearfix"></div>
            <div class="col-md-8" style="margin-top:20px;">
                    <video width="100%"  controls>
                        <source src="<?php echo base_url().$video_file?>" type="video/mp4">
                    </video>
            </div>
            <div class="col-md-4" style="margin-top:20px;">
                   <?php
                        $widget = new Widget;
                        $widget->run('relatedvideos', $related_category,$post_id); 
                   ?>  
            </div>
            <div class="clearfix"></div>
            <div id="content" class="content-post">
                <?php 
                $already_showed = false;
                if(strpos($content,"bd.thedailystar.")!==false)
                {
                    $content = str_replace("bd.thedailystar.", "www.thedailystar.", $content);
                }
                if(strpos($content,"[[gallery]]")!==false)
                {
                    if (file_exists('gallery/xml/post/post_' . $post_id . ".xml"))
                    {
                        
                        //$widget = new Widget;
                        
                        $gallery_html = '</p><div  class="ym-grid"> 
                        <div style="text-align:center; width:95%; margin: 0 auto;">
                            <div style="display:none;" class="html5gallery" data-skin="horizontal" data-thumbshowtitle="false" data-width="600" data-height="270"  data-showsocialmedia="false"  
                         data-resizemode="fill" 
                         data-xml="'.base_url().'gallery/xml/post/post_'.$post_id.'.xml" >
                        </div>
                        </div>
                        </div></p>';
                        
                        
                        $content = str_replace("[[gallery]]", $gallery_html, $content);
                        
                    }
                    else
                    {
                        $content = str_replace("[[gallery]]","", $content);
                    }    
                    $already_showed = true;
                    
                }
                echo $content; ?>
            </div>
            <?php else : ?>
            <div class="center">
                <?php $s_gk_answers = $content; ?>
                <?php $a_gk_answers = explode(",", $s_gk_answers); ?>
                <?php if ( ! $attempt ) : ?>
                <?php $b = 1; foreach( $a_gk_answers as $answer ) : ?>
                    <?php $a_answer = explode("|", $answer); ?>
                    <?php $a_color_layout = explode("|", $layout_color); ?>
                    <div id="answer_<?php echo $post_id; ?>" class="gk_layout_single_<?php echo $layout ; ?> <?php echo ( free_user_logged_in() ) ? 'gk_answers' : 'login-user'; ?>" style="background: <?php echo ($layout == 1) ? (( $b == 1 || $b == 4 ) ? $a_color_layout[1] : $a_color_layout[0]) : (( $b %2 == 0 ) ? $a_color_layout[1] : $a_color_layout[0]); ?>; ">
                        <?php echo $a_answer[0] ; ?>
                    </div>
                <?php $b++; endforeach; ?>
                <?php else :?>
                <?php $b = 1; foreach( $a_gk_answers as $answer ) : ?>
                    <?php $a_answer = explode("|", $answer); ?>
                    <?php $a_color_layout = explode("|", $layout_color); ?>
                    <?php if ( $user_answer == $a_answer[0] ) : ?>
                    <div id="answer_<?php echo $post_id; ?>" class="gk_layout_single_<?php echo $layout ; ?>" style="background: <?php echo ($is_correct == 1) ? '#0A0' : '#F00'; ?>; ">
                        <?php echo $a_answer[0] ; ?>
                    </div>
                    <?php else :?>
                    <div id="answer_<?php echo $post_id; ?>" class="gk_layout_single_<?php echo $layout ; ?> <?php echo ( free_user_logged_in() ) ? '' : ''; ?>" style="background: <?php echo ($layout == 1) ? (( $b == 1 || $b == 4 ) ? $a_color_layout[1] : $a_color_layout[0]) : (( $b %2 == 0 ) ? $a_color_layout[1] : $a_color_layout[0]); ?>; ">
                        <?php echo $a_answer[0] ; ?>
                    </div>
                    <?php endif; ?>
                <?php $b++; endforeach; ?>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <!-- If post has video then include those here -->
            <?php if ( $post_type == 1 || $post_type == 3) : ?>  
            <?php if ( $b_layout ) : ?>
            <?php if ( count($post_videos) == 1 && $post_videos->video_type != NULL && ! file_exists('gallery/xml/post/post_' . $post_id . ".xml") ) : ?>
                <div class="video_div video_div_post noPrint">
                    <?php if ( $post_videos->video_type == "youtube" ) : ?>
                        <iframe src="http://www.youtube.com/embed/<?php echo $post_videos->video_id; ?>?rel=0&wmode=transparent" height="405" width="540" allowfullscreen="" frameborder="0"></iframe>
                    <?php elseif ( $post_videos->video_type == "vimeo" ) :  ?>
                        <iframe src="//player.vimeo.com/video/<?php echo $post_videos->video_id; ?>" width="540" height="405" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                    <?php endif; ?>
                </div>
            <?php elseif ( count($post_images) == 1 && ! is_null($post_images->material_url) && ! file_exists('gallery/xml/post/post_' . $post_id . ".xml") ) : ?>
                <div class="image_div image_div_post noPrint">
                    <img class="toolbar" src="<?php echo base_url() . $post_images->material_url;?>" alt="<?php echo $post_images->caption;?>" width="100%" />
                </div>
            <?php endif; ?>
            <?php endif; ?>
            <?php endif; ?>  
            <!-- Post Video -->
            <div style="clear: both; height: 2px;"></div>
            
        </div>
        
        <?php if ( $post_type == 1 || $post_type == 3) : ?>  
        <?php if ( $b_layout ) : ?>    
        <?php
        if (!$already_showed && file_exists('gallery/xml/post/post_' . $post_id . ".xml"))
        {
            $widget = new Widget;
            $widget->run('postgallery', $post_id);
        }
        ?> 

        <?php if ($resource): ?> 

            <?php $i = 0;
            foreach ($resource as $value) : ?>
                    <?php if ($i == 0): ?> 
                    <div class="sports-inner-container_resource-post noPrint" ><h3>Download Resource</h3>
                    <?php endif; ?>
                    <p>
                        <a href="<?= base_url() . $value->material_url ?>" target="_blank" ><?= ($value->caption) ? $value->caption : "Download Resource"; ?></a>
                    </p>
                    <?php if ($i == 0): ?> 
                    </div>
                    <?php endif; ?>
            <?php $i++;
        endforeach; ?> 
      
        <?php endif; ?>
        <?php endif; ?>
        <?php if ( $b_layout) : ?>
            
            <?php if ($pdf_top==0): ?>
                <?php if (count($all_attachment)>0): ?>
                   <?php foreach($all_attachment as $value):?>
                    <?php $attachment = $value->file_name; ?>
                    <?php if ($value->show): ?>

                    <iframe src="http://docs.google.com/viewer?url=<?php echo base_url() . $attachment; ?>&embedded=true" style="width: 98%; height: 500px;" frameborder="0"></iframe>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php endif; ?>
            <?php endif; ?>
       
    
        <?php if (count($all_attachment)>0): ?>
        <div class="f2">
                <div style=" cursor: pointer; padding-top: 25px; margin-bottom:5px;">
                   Download Resource
                </div>
        </div>
        <?php foreach($all_attachment as $value):?>
        <?php $attachment = $value->file_name; ?>
        
            
            <div class="col-lg-2">   
                <div style=" cursor: pointer;">
                    <a href="
                        <?php
                            $str_f_path = $attachment;
                            $url = base_url('download?f_path=' . $str_f_path);
                            
                            echo  $url;
                        ?>">
                        <img width="80px" src="<?php echo base_url(); ?>styles/layouts/tdsfront/image/downloads.png" />
                    </a>
                    <br />
                    <a href="
                        <?php
                            $str_f_path = $attachment;
                            $url = base_url('download?f_path=' . $str_f_path);
                            
                            echo  $url;
                        ?>">
                        <?php echo $value->caption ?>
                    </a>    
                </div>
            </div>
        <?php endforeach; ?>
        <?php endif; ?>
        <?php endif; ?>
        
        <?php if ( $post_show_publish_date ): ?>    
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

		 <?php 
        $s_post_content = ob_get_contents();
        ob_end_clean(); 
        $cache_name = "POST_" . $post_id;
        if ( ! isset($_GET['archive']) && $b_layout  )
        {
            $CI = & get_instance();
            $CI->cache->save($cache_name, $s_post_content, 60 * 60 * 24);
        }
        echo $s_post_content;
    ?>
        <?php if ( $b_layout ) : ?>    
        
<!--	<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>-->
<div class="noPrint">
	<center>
            
            <?php
                    $adplace_helper = new Adplace;
                    $adplace_helper->printAds( 28, 0, FALSE,'0','details' );
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
    <?php if ($has_outbrain)  : ?>   
    <?php echo $outbrain_content; ?>
    <?php endif; ?>
    <?php if( $has_disqus && $can_comment == 1): ?>
       
        <div class="noPrint">
            <center>

                <?php
                        $adplace_helper = new Adplace;
                        $adplace_helper->printAds( 36, 0, FALSE,'0','details' );
                ?>
     
            </center>	
        </div>
        <a target="_blank" style="float:left;clear:both;background-color:#2A2A2A;color:#fff; padding:5px 10px; margin-left:260px;" href="<?php echo base_url()?>comment-policy">Comment Policy</a>
		
        <?php echo $disqus_content; ?>                 
    <?php endif;?>
    <?php endif;?>    
     
    <?php if ( $b_layout) : ?>
    
        
    <hr  /> 
    
    <!--Next and Previous Button-->
    
    <div class="next-previous col-lg-2">
        <?php if ( $has_more ) : ?>
        
            <?php if ( $has_previous ) : ?>
            <div class="previous">
                <span><a href="<?php echo $previous_news_link; ?>">Previous</a></span>
            </div>
            <?php endif; ?>

            <?php if ( $has_next_news ) : ?>
            <div class="next">
                <span><a href="<?php echo $next_news_link; ?>">Next</a></span>
            </div>
            <?php endif; ?>
        
        <?php endif; ?> 
       
    </div>
    
    
    <!--Next and Previous Button-->
    
    
    <script type="text/javascript">var addthis_config = {"data_track_addressbar":false, "data_track_clickback" : false};</script>
    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-52bca22436b47685"></script>
    <?php endif; ?>
</div>
<?php if ( $post_type == 4 ) : $ar_color = explode('|', $layout_color); ?>
    <input type="hidden" id="gk_layout_color" name="gk_layout_color" value="<?php echo $ar_color[0]; ?>" />
    <div id="results_<?php echo $post_type; ?>" class="results" style="margin: 0 20px;">
        
    </div>
<?php endif; ?>
<?php if ( $post_type == 1 || $post_type == 3) : ?>
<?php if ( $has_related && $b_layout ) : ?>
<div class="inner-container_related" style="margin: 20px; width: 96%;">
    <div class="related_news_headline f2">
        Related Post
    </div>
    <div class="related_post">
    <?php $i = 0; foreach( $related_news as $news ): ?>
        <?php if ( $i % 2 == 0 ) : ?>
        <div style="clear: both; height: 30px;"></div>
        <?php endif; ?>
        <div class="related_post_content">
            <div class="image">
                <?php
                    list($width_main, $height_main, $type_main, $attr_main) = getimagesize($news->image);
                
                 ?>
                <?php if(isset($width_main) && $width_main>0): ?>
                <img src="<?php echo $news->image; ?>" width="90%" />
                <?php endif; ?>
            </div>
            <div class="content_data">
                <span><a href="<?php echo $news->new_link; ?>"><?php echo $news->title; ?></a></span>
                <div class="content"><?php echo $news->content; ?></div>
            </div>
        </div>
    <?php $i++; endforeach; ?>
    </div>
</div>
<?php endif; ?>     
<?php endif; ?>
        
<script type="text/javascript">
    function load_print_popup( url )
    {
            var leftPosition, topPosition;
            //Allow for borders.
            leftPosition = (window.screen.width / 2) - ((730 / 2) + 10);
            //Allow for title and status bars.
            topPosition = (window.screen.height / 2) - ((730 / 2) + 50);
            window.open(url,'<?php echo time();?>','width=730,height=730,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=' + leftPosition + ',top=' + topPosition);
            return false;
    }
    function print_page( )
    {
            window.print();
            return false;
    }
    
    //addthis_toolbox
    
    
</script>

<div class="noPrint">
	<center>
            
            <?php
                    $adplace_helper = new Adplace;
                    $adplace_helper->printAds( 29, 0, FALSE,'0','details' );
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