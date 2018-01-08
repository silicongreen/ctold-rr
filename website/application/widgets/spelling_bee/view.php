<?php
$arCustomNews = getFormatedContentAll($news, 125);
//echo "<pre>";
//print_r($news);exit;

$active_video_slider = true;
$active_banner_slider = true;
$active_common_view = false;
?>

<script type="text/javascript" src="<?php echo base_url('styles/layouts/tdsfront/spelling_bee/jquery.popupWindow.js'); ?>"></script>
<li  id="post-<?php echo $news->post_id; ?>" class="post-<?php echo $news->post_id; ?> <?php echo $s_post_class; ?> type-post post-
     content-showed status-publish format-image has-post-thumbnail hentry 
     category-post-format tag-description tag-image tag-people tag-text 
     col-sm-8 <?php echo ($i < $count_show) ? "shown" : ""; ?>  post-
     boxes ">

    <?php
    $widget = new Widget;
    $widget->run('seenassessment', $news);
    ?>

    <div class="post-content clearfix spellingbee_post">
        <div class="intro-post spellingbee">
            <!--active_banner_slider start here-->
            <?php
            if ($active_banner_slider == true):
                ?>
                <div class="slider1" style="height:356px;overflow: hidden;">
                    <div id="myCarousel" class="carousel" data-ride="carousel">
                        <!-- Indicators -->
                        <ol class="carousel-indicators">
                            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                            <li data-target="#myCarousel" data-slide-to="1"></li>
                            <li data-target="#myCarousel" data-slide-to="2"></li>
                            <li data-target="#myCarousel" data-slide-to="3"></li>                            
                        </ol>

                        <!-- Wrapper for slides -->
                        <div class="carousel-inner" role="listbox">                            
                            <div class="item active">
                                <a class="speelingbee_banner_box" title="Spelling Bee Two" href="http://www.champs21.com/spellingbee" target="_blank">       
                                    <img src="<?php echo base_url('styles/layouts/tdsfront/home_slider/spellers-s4.jpg'); ?>" class="attachment-post-thumbnail wp-post-image no_toolbar" alt="Spelling Bee Two" style="width:100%;">
                                </a> 
                            </div>
                            
                            <div class="item">
                                <a class="speelingbee_banner_box" title="Spell Champs" href="https://play.google.com/store/apps/details?id=com.champs21.schoolapp&hl=en" target="_blank">       
                                    <img src="<?php echo base_url('styles/layouts/tdsfront/home_slider/Chhobi-0094.JPG'); ?>" class="attachment-post-thumbnail wp-post-image no_toolbar" alt="Spell Champs" style="width:100%;">
                                </a> 
                            </div>

                            <div class="item">
                                <a class="speelingbee_banner_box" title="The School of Excellence" href="http://www.champs21.com/spellingbee" target="_blank">       
                                    <img src="<?php echo base_url('styles/layouts/tdsfront/home_slider/Chhobi-0890.JPG'); ?>" class="attachment-post-thumbnail wp-post-image no_toolbar" alt="The School of Excellence" style="width:100%;">
                                </a> 
                            </div>
                            
                            <div class="item">
                                <a class="speelingbee_banner_box" title="Nation Builders" href="http://www.champs21.com/nation-builder" target="_blank">                                           
                                    <img src="<?php echo base_url('styles/layouts/tdsfront/home_slider/Chhobi-1542.JPG'); ?>" class="attachment-post-thumbnail wp-post-image no_toolbar" alt="Nation Builders" style="width:100%;">                                    
                                </a>
                                <a href="javascript:void(0);" data="candle" class="<?php echo ( free_user_logged_in() ) ? 'candlepopup' : 'before-login-user'; ?> likhun_btn"><span></span></a>
                            </div>
                            <!-- Left and right controls -->

                        </div>
                    </div>
                </div>
                <script>
                    $(document).ready(function () {
                        $('.carousel').carousel({
                            interval: 3000,
                            cycle: true
                        })
                    });
                </script>
                <style>
                    .carousel-indicators { top:330px;};
                    .carousel .item {-webkit-transition: opacity 3s; -moz-transition: opacity 3s; -ms-transition: opacity 3s; -o-transition: opacity 3s; transition: opacity 3s;}
                    .carousel .active.left {left:0;opacity:0;z-index:2;}
                    a.likhun_btn span {
                        width: 30%;
                        height: 14%;
                        position: absolute;
                        background: url("<?php echo $ecl_button; ?>") no-repeat;
                        background-position: 50% 50%;
                        background-size: 100%;
                        bottom:55px;
                        left: 225px;
                    }
                    a.likhun_btn span:hover {
                        width: 30%;
                        height: 14%;
                        position: absolute;
                        background: url("<?php echo $ecl_button; ?>") no-repeat;
                        background-position: 50% 50%;
                        background-size: 100%;
                        bottom:55px;
                        left: 225px;
                    }
                </style>

            <?php endif; ?>
            <!--active_banner_slider end here-->
            <!--active_video_slider start here-->
            <?php
            if ($active_video_slider == true):
                ?>

                <?php if (count($news->related_news_spelling_bee) > 0): ?>
                    <div class="video_play_box">
                        <a class="boxclose" id="boxclose" onclick="showVideosClose();"></a>
                        <?php foreach ($news->related_news_spelling_bee as $newsrelated): ?>
                            <p id="video_embed_<?php echo $newsrelated->id; ?>" style="display:none;"><?php echo $newsrelated->embedded; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <section class="slider">                    
                    <div id="over" class="loading_speelingbee" style="diaplay:none;">
                        <span class="Centerer"></span>
                        <img class="Centered" src="<?php echo base_url('styles/layouts/tdsfront/spelling_bee/loader_home_slider.gif'); ?>" />
                    </div>

                    <div class="flexslider carousel" style="height:200px;margin:10px 20px;overflow: hidden;diaplay:none;">
                        <h4 class="f2" style="font-size:16px;letter-spacing:0px;margin-left:0px;color:rgb(202, 35, 42);padding: 0 0 5px;">Spelling Bee Season 4 Videos</h4>
                        <ul class="slides">
                            <?php if (count($news->related_news_spelling_bee) > 0): ?>
                                <?php
                                foreach ($news->related_news_spelling_bee as $newsrelated):
                                    //echo "<pre>";
                                    //print_r($newsrelated);
                                    ?>
                                    <li style="width:145px; float: left; display: block;">                                        
                                        <a class="add-link video_play_btn" onclick="showVideos(<?php echo $newsrelated->id; ?>);" title="<?php echo $newsrelated->headline; ?>" href="javascript:void(0);<?php //echo create_link_url("index", $newsrelated->headline, $newsrelated->id)   ?>">       
                                            <span></span>
                                            <img src="<?php echo $newsrelated->lead_material; ?>" class="attachment-post-thumbnail wp-post-image no_toolbar" alt="<?php echo $newsrelated->headline; ?>" style="width:145px;height:100px;">
                                            <p style="font-family: arial;letter-spacing: 0px;line-height: 20px;">
                                                <?php
                                                if (strlen($newsrelated->headline) >= 30) {
                                                    $subtext = substr($newsrelated->headline, 0, 30);
                                                    echo $subtext . " ... ";
                                                } else {
                                                    echo $newsrelated->headline;
                                                }
                                                ?>
                                            </p>
                                        </a>        
                                    </li>                                    
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </section>
                <div class="clearfix"></div>
                <div class="video_see_more">
                    <p style="float: right;"><a href="<?php echo base_url('/videos/spelling-bee-season-4'); ?>">See More</a></p>
                </div>
                <style>
                    #over
                    {
                        position:absolute;
                        width:100%;
                        height:215px;
                        text-align: center; /*handles the horizontal centering*/
                        z-index: 100;
                        background-color: #fff;
                    }
                    /*handles the vertical centering*/
                    .Centerer
                    {
                        display: inline-block;
                        height: 100%;
                        vertical-align: middle;
                    }
                    .Centered
                    {
                        display: inline-block;
                        vertical-align: middle;
                        width:5%;
                    }

                    .slider
                    {
                        border-top: 2px solid #ccc;
                        padding-top: 0px;
                    }
                    .flexslider .slides > li
                    {
                        margin-right:10px;
                    }
                    .flexslider .slides > li:last-child 
                    {
                        margin-right:0px;
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
                    .slider ol.flex-control-nav { display: none; }                    

                    .video_play_btn{}
                    a.video_play_btn {
                        float: left;
                        position: relative;
                    }
                    a.video_play_btn span {
                        width: 100%;
                        height: 65%;
                        position: absolute;
                        background: url("<?php echo base_url('styles/layouts/tdsfront/images/icon/video_play_btn.png'); ?>") no-repeat;
                        background-position: 50% 50%;
                        background-size: 40%;
                    }
                    a.video_play_btn span:hover {
                        width: 100%;
                        height: 65%;
                        position: absolute;
                        background: url("<?php echo base_url('styles/layouts/tdsfront/images/icon/video_play_btn_hover.png'); ?>") no-repeat;
                        background-position: 50% 50%;
                        background-size: 40%;
                    }

                    .speelingbee_banner_box{display: block;}
                    .video_play_box{text-align: center;display: none;height: 356px;}
                    .video_see_more{height:20px;margin: 0px 20px 10px;}
                    .video_see_more p a{color:#000;cursor: pointer;font-family: Arial;letter-spacing:0px;font-size:13px;}
                    a.boxclose {
                        background: #605f61 none repeat scroll 0 0;
                        border: 1px solid #aeaeae;
                        color: #fff;
                        cursor: pointer;
                        display: inline-block;
                        position: absolute;
                        right: 0;
                        font-size: 30px;
                        font-weight: bold;
                        line-height: 1;
                        padding: 1px 5px;
                    }

                    .boxclose:before {
                        content: "X";
                    }


                </style>

                <script>
                    $('.loading_speelingbee').show();
                    $(window).bind("load", function () {
                        $('.loading_speelingbee').hide();

                        $(".flexslider").show();

                        $('.flexslider').flexslider({
                            animation: "slide",
                            animationLoop: true,
                            itemWidth: 145,
                            itemMargin: 3,
                            minItems: 2,
                            maxItems: 4,
                            start: function (slider) {
                                $('body').removeClass('loading');
                            }
                        });

                    });

                    function showVideos(id)
                    {
                        $('.video_play_box p').hide();
                        $('.slider1').hide();
                        $('.video_play_box').show();
                        $('#video_embed_' + id).show();
                    }
                    function showVideosClose()
                    {
                        $('.video_play_box p').hide();
                        $('.video_play_box').hide();
                        $('.slider1').show();
                    }
                    //slideshow: false,
                </script>
            <?php endif; ?>
            <!--active_video_slider end here-->
            <!--active_common_view start here-->
            <?php if ($active_common_view == true): ?>
                <div class="col-lg-6" style="padding:5px 0px;">

                    <div class="col-lg-12 float-4">
                        <a href="/spellingbee">
                            <img class="no_toolbar" style="width: 90%;" src="/styles/layouts/tdsfront/spelling_bee/2015/images/join_msg.png" />
                        </a>
                    </div>

                    <div class="col-lg-6 float-4 align-center">
                        <a href="/spellingbee">
                            <img class="no_toolbar" src="/styles/layouts/tdsfront/spelling_bee/2015/sp-logo.png" />
                        </a>
                    </div>

                    <!-- Off spellingbee play -->

                    <!--div class="col-lg-6 float-4">
                        
                    <?php //if( free_user_logged_in() ) { ?>
                    <?php
                    //$is_joined_spellbee = get_free_user_session('is_joined_spellbee');
                    //if($is_joined_spellbee == 1 || get_free_user_session('type') != 2) {
                    ?>  <a href="http://www.champs21.com/swf/spellingbee_2015/index.html" title="Spelling Bee | Season 4" class="example2demo play_image" style="border:0px;" name="Spelling Bee">
                    <!--                            <a name="windowX" title="Spelling Bee | Season 4" id="play_spellbee_4" class="play_image" style="border:0px;" href="javascript:void(0);">-->
                    <!--img id="play_image_1" class="no_toolbar" src="/styles/layouts/tdsfront/spelling_bee/2015/images/play.png" />
                    <img id="play_image_2" class="no_toolbar" src="/styles/layouts/tdsfront/spelling_bee/2015/images/play-hover.png" style="display:none;" />
                </a>
                    <?php //} else {  ?>
                <a id="join_spellbee_reg" class="play_image" style="border:0px;" href="javascript:void(0);">
                    <img id="play_image_1" class="no_toolbar" src="/styles/layouts/tdsfront/spelling_bee/2015/images/play.png" />
                    <img id="play_image_2" class="no_toolbar" src="/styles/layouts/tdsfront/spelling_bee/2015/images/play-hover.png" style="display:none;" />
                </a>
                    <?php //}  ?>
            
                    <?php //} else {  ?>
            <a class="play_image login-user" style="border:0px;" href="javascript:void(0);">
                <img id="play_image_1" class="no_toolbar" src="/styles/layouts/tdsfront/spelling_bee/2015/images/play.png" />
                <img id="play_image_2" class="no_toolbar" src="/styles/layouts/tdsfront/spelling_bee/2015/images/play-hover.png" style="display:none;" />
            </a>
                    <?php //}  ?>
        
    </div-->

                </div>

                <div class="col-lg-6 links-spell">

                    <!-- Off spellingbee gamerules -->
                    <!--a href="/gamerules">
                        <div class="col-lg-12 leader leader_board1">
                            <div class="col-lg-3">
                                <img class="no_toolbar" src="styles/layouts/tdsfront/images/spellingbee/ruls.png"  />
                            </div>
                            <div class="col-lg-8 f2" style="color: #ffffff;">
                                GAME RULES 
                            </div>
                        </div>
                    </a-->
                    <!-- Off spellingbee gamerules -->

                    <!-- Off spellingbee leaderborad -->
                    <!-- On Division qualifiers -->

                    <a href="/leaderboard">
                        <div class="col-lg-12 leader leader_board2">
                            <div class="col-lg-11">
                                <!--<div class="col-lg-12">-->
                                <!--<div class="col-lg-3">-->
                                <img class="no_toolbar" src="/styles/layouts/tdsfront/spelling_bee/2015/divisional_button_1.png" />
                            </div>
                            <!--div class="col-lg-8 f2" style="color: #ffffff;">
                                LEADER BOARD
                            </div-->

                        </div>
                    </a>

                    <!-- On Division qualifiers -->
                    <!-- Off spellingbee leaderborad -->

                    <?php
                    if (count($news->related_news_spelling_bee) > 0)
                        :
                        ?>
                        <?php
                        $i = 0;
                        foreach ($news->related_news_spelling_bee as $newsrelated):
                            ?>

                            <?php
                            if ($i == 2) {
                                break;
                            }
                            ?>
                            <?php
                            if ($i == 1):
                                ?>
                                <div class="col-lg-12 float-5">
                                    <hr style="margin:0px 0px 0px 0px; width:95%;"/>
                                </div>
                            <?php endif; ?>

                            <div class="col-lg-12 float-10">

                                <?php
                                $image_related = "";
                                if (isset($newsrelated->crop_images) && count
                                                ($newsrelated->crop_images) > 0) {
                                    $image_related = $newsrelated->crop_images[0];
                                }
                                ?>
                                <div class="col-lg-3">
                                    <?php if ($image_related): ?>
                                        <a href="" style="border:0px;"><img src="<?php echo $image_related; ?>"  /></a>
                                    <?php endif; ?>
                                </div>

                                <div class="col-lg-7">
                                    <div class="col-lg-12 height_value"  >
                                        <a href="<?php
                                        echo create_link_url
                                                ("index", $newsrelated->title, $newsrelated->id)
                                        ?>"><?php
                                               echo
                                               $newsrelated->title;
                                               ?></a>
                                    </div>
                                    <div class="col-lg-12 date-string">
                                        <?php echo $newsrelated->published_date_string; ?> Ago
                                    </div>
                                </div>
                            </div>
                            <?php
                            $i++;
                        endforeach;
                        ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?><!--active_common_view end here-->

        </div> 

    </div> 

</li>
<script type="text/javascript">
    $('.example2demo').popupWindow({
        centerBrowser: 1,
        height: 600,
        width: 800,
        resizable: 1
    });
</script>
<style>
    .height_value a {
        color: #333333;
        font-size: 18px;
    }
    @media (min-width: 300px)
    {
        .date-string
        {
            float:left;
            clear:both;
            color:#C6C5CA;
            font-size: 12px;
        }
        .height_value
        {
            height: 50px;
            overflow: hidden; 
        }

        .float-10 img
        {
            overflow:hidden;
            float: left; 
            width: 65px;
            height: 65px; 
            border:0; 
            margin-right:15px;
        }
        .leader img
        {
            overflow: hidden;
            float: left;
            width: 92%;
            /*height: 20px;*/
            border: 0;
            margin: 12px;
        }
        .align-center {
            margin-left: 17px;
        }
        .float-4 .play_image img
        {
            border: 0 none;
            float: left;
            margin: 30px 0 0 55px;
            overflow: hidden;
            width: 60%;
        }
        .float-4 img {
            border: 0 none;
            float: left;
            margin-left: 30px;
            overflow: hidden;
            width: 80%;
        }
        .spellingbee_post .col-lg-3 {
            width: 25%;
            float:left;
            margin-left:12px;
        }
        .spellingbee_post .col-lg-7 {
            width: 58.333333333333336%;
            float:left;
        }
        .type-post .spellingbee_post
        {
            border-bottom: 6px solid #FFCA0B !important;
        }

        .float-5
        {
            float:left;
            clear:both;
        }

        .float-4
        {
            float:left;
        }

        .float-10
        {
            float:left;
            clear:both;
            margin-top:15px;
            height: 78px;
        }
        .spellingbee .links-spell
        {
            color: white;
        }
        .spellingbee .col-lg-12
        {
            width: 100%;
            float:left;
            clear: both;
        }
        .spellingbee .col-lg-6
        {
            padding: 10px 0px;
            width: 100%;
            float:left;
        }
        .spellingbee .col-lg-6 .leader
        {
            margin-top: 8px;
            float:left;
        }
        .spellingbee .col-lg-6 .leader_board1 .col-lg-3
        {

            height: 70px;
            background: #C59567;
            margin-left:12px;

        }
        .spellingbee .col-lg-6 .leader_board2 .col-lg-3
        {

            height: 70px;
            background: #EAB901;
            margin-left:12px;

        }
        .spellingbee .col-lg-6 .leader_board1 .col-lg-8
        {
            background: #E6A96E;
            height: 40px;
            padding: 25px 0px 45px 12px;
            font-size: 22px;
            width: 66.66666666666666%;
            float:left;
        }
        .spellingbee .col-lg-6 .leader_board2 .col-lg-8
        {
            background: #FFCA0B;
            height: 40px;
            padding: 25px 0px 45px 12px;
            font-size: 22px;
            width: 66.66666666666666%;
            float:left;
        }
    }

    @media (min-width: 420px)
    {
        .align-center {
            margin-left: 43px;
        }
        .date-string
        {
            float:left;
            clear:both;
            color:#C6C5CA;
            font-size: 12px;
        }
        .height_value
        {
            height: 50px;
            overflow: hidden; 
        }

        .float-10 img
        {
            overflow:hidden;
            float: left; 
            width: 65px;
            height: 65px; 
            border:0; 
            margin-right:15px;
        }
        .leader img
        {
            overflow: hidden;
            float: left;
            width: 94%;
            /*height: 20px;*/
            border: 0;
            margin: 12px;
        }
        .float-4 .play_image img
        {
            border: 0 none;
            float: left;
            margin: 30px 0 0 65px;
            overflow: hidden;
            width: 60%;
        }
        .float-4 img
        {
            border: 0 none;
            float: left;
            margin-left: 30px;
            overflow: hidden;
            width: 80%;
        }
        .spellingbee_post .col-lg-3 {
            width: 25%;
            float:left;
            margin-left:12px;
        }
        .spellingbee_post .col-lg-7 {
            width: 58.333333333333336%;
            float:left;
        }
        .type-post .spellingbee_post
        {
            border-bottom: 6px solid #FFCA0B !important;
        }

        .float-5
        {
            float:left;
            clear:both;
        }

        .float-4
        {
            float:left;
        }

        .float-10
        {
            float:left;
            clear:both;
            margin-top:15px;
            height: 78px;
        }
        .spellingbee .links-spell
        {
            color: white;
        }
        .spellingbee .col-lg-12
        {
            width: 100%;
            float:left;
            clear: both;
        }
        .spellingbee .col-lg-6
        {
            padding: 10px 0px;
            width: 100%;
            float:left;
        }
        .spellingbee .col-lg-6 .leader
        {
            margin-top: 8px;
            float:left;
        }
        .spellingbee .col-lg-6 .leader_board1 .col-lg-3
        {

            height: 70px;
            background: #C59567;
            margin-left:12px;

        }
        .spellingbee .col-lg-6 .leader_board2 .col-lg-3
        {

            height: 70px;
            background: #EAB901;
            margin-left:12px;

        }
        .spellingbee .col-lg-6 .leader_board1 .col-lg-8
        {
            background: #E6A96E;
            height: 40px;
            padding: 25px 0px 45px 12px;
            font-size: 22px;
            width: 66.66666666666666%;
            float:left;
        }
        .spellingbee .col-lg-6 .leader_board2 .col-lg-8
        {
            background: #FFCA0B;
            height: 40px;
            padding: 25px 0px 45px 12px;
            font-size: 22px;
            width: 66.66666666666666%;
            float:left;
        }
    }

    @media (min-width: 360px)
    {
        .align-center {
            margin-left: 28px;
        }
        .date-string
        {
            float:left;
            clear:both;
            color:#C6C5CA;
            font-size: 12px;
        }
        .height_value
        {
            height: 50px;
            overflow: hidden; 
        }

        .float-10 img
        {
            overflow:hidden;
            float: left; 
            width: 65px;
            height: 65px; 
            border:0; 
            margin-right:15px;
        }
        .leader img
        {
            overflow: hidden;
            float: left;
            width: 92%;
            /*height: 20px;*/
            border: 0;
            margin: 12px;
        }
        .float-4 .play_image img
        {
            border: 0 none;
            float: left;
            margin: 30px 0 0 60px;
            overflow: hidden;
            width: 60%;
        }
        .float-4 img
        {
            border: 0 none;
            float: left;
            margin-left: 30px;
            overflow: hidden;
            width: 80%;
        }
        .spellingbee_post .col-lg-3 {
            width: 25%;
            float:left;
            margin-left:12px;
        }
        .spellingbee_post .col-lg-7 {
            width: 58.333333333333336%;
            float:left;
        }
        .type-post .spellingbee_post
        {
            border-bottom: 6px solid #FFCA0B !important;
        }

        .float-5
        {
            float:left;
            clear:both;
        }

        .float-4
        {
            float:left;
        }

        .float-10
        {
            float:left;
            clear:both;
            margin-top:15px;
            height: 78px;
        }
        .spellingbee .links-spell
        {
            color: white;
        }
        .spellingbee .col-lg-12
        {
            width: 100%;
            float:left;
            clear: both;
        }
        .spellingbee .col-lg-6
        {
            padding: 10px 0px;
            width: 100%;
            float:left;
        }
        .spellingbee .col-lg-6 .leader
        {
            margin-top: 8px;
            float:left;
        }
        .spellingbee .col-lg-6 .leader_board1 .col-lg-3
        {

            height: 70px;
            background: #C59567;
            margin-left:12px;

        }
        .spellingbee .col-lg-6 .leader_board2 .col-lg-3
        {

            height: 70px;
            background: #EAB901;
            margin-left:12px;

        }
        .spellingbee .col-lg-6 .leader_board1 .col-lg-8
        {
            background: #E6A96E;
            height: 40px;
            padding: 25px 0px 45px 12px;
            font-size: 22px;
            width: 66.66666666666666%;
            float:left;
        }
        .spellingbee .col-lg-6 .leader_board2 .col-lg-8
        {
            background: #FFCA0B;
            height: 40px;
            padding: 25px 0px 45px 12px;
            font-size: 22px;
            width: 66.66666666666666%;
            float:left;
        }
    }

    @media (min-width: 760px)
    {
        .align-center {
            margin-left: 70px;
        }
        .date-string
        {
            float:left;
            clear:both;
            color:#C6C5CA;
            font-size: 12px;
        }
        .height_value
        {
            height: 50px;
            overflow: hidden; 
        }

        .float-10 img
        {
            overflow:hidden;
            float: left; 
            width: 65px;
            height: 65px; 
            border:0; 
            margin-right:15px;
        }
        .leader img
        {
            overflow: hidden;
            float: left;
            width: 92%;
            border: 0;
            margin: 13px;
        }
        .float-4 .play_image img
        {
            border: 0 none;
            float: left;
            margin: 30px 0 0 30px;
            overflow: hidden;
            width: 60%;
        }
        .float-4 img
        {
            border: 0 none;
            float: left;
            margin-left: 30px;
            overflow: hidden;
            width: 100%;
        }
        .spellingbee_post .col-lg-3 {
            width: 25%;
            float:left;
        }
        .spellingbee_post .col-lg-7 {
            width: 58.333333333333336%;
            float:left;
        }
        .type-post .spellingbee_post
        {
            border-bottom: 6px solid #FFCA0B !important;
        }

        .float-5
        {
            float:left;
            clear:both;
        }

        .float-4
        {
            float:left;
        }

        .float-10
        {
            float:left;
            clear:both;
            margin-top:15px;
            height: 78px;
        }
        .spellingbee .links-spell
        {
            color: white;
        }
        .spellingbee .col-lg-12
        {
            width: 100%;
            float:left;
            clear: both;
        }
        .spellingbee .col-lg-6
        {
            padding: 10px 0px;
            width: 50%;
            float:left;
        }
        .spellingbee .col-lg-6 .leader
        {
            margin-top: 8px;
            float:left;
        }
        .spellingbee .col-lg-6 .leader_board1 .col-lg-3
        {

            height: 78px;
            background: #C59567;

        }
        .spellingbee .col-lg-6 .leader_board2 .col-lg-3
        {

            height: 78px;
            background: #EAB901;

        }
        .spellingbee .col-lg-6 .leader_board1 .col-lg-8
        {
            background: #E6A96E;
            height: 40px;
            padding: 29px 0 49px 12px;
            font-size: 22px;
            width: 66.66666666666666%;
            float:left;
        }
        .spellingbee .col-lg-6 .leader_board2 .col-lg-8
        {
            background: #FFCA0B;
            height: 40px;
            padding: 29px 0 49px 12px;
            font-size: 22px;
            width: 66.66666666666666%;
            float:left;
        }
    }

    @media (min-width: 800px)
    {
        .align-center {
            margin-left: 70px;
        }
        .height_value
        {
            font-size: 15px; 
            line-height: 16px;
        }
        .date-string
        {
            float:left;
            clear:both;
            color:#C6C5CA;
            font-size: 11px;
        }
        .date-string
        {
            float:left;
            clear:both;
            color:#C6C5CA;
            font-size: 12px;
        }
        .height_value
        {
            height: 50px;
            overflow: hidden; 
        }

        .float-10 img
        {
            overflow:hidden;
            float: left; 
            width: 65px;
            height: 65px; 
            border:0; 
            margin-right:15px;
        }
        .leader img
        {
            overflow: hidden;
            float: left;
            width: 90%;
            border: 0;
            margin: 16px;
        }
        .float-4 .play_image img
        {
            border: 0 none;
            float: left;
            margin: 30px 0 0 30px;
            overflow: hidden;
            width: 60%;
        }
        .float-4 img
        {
            border: 0 none;
            float: left;
            margin-left: 30px;
            overflow: hidden;
            width: 100%;
        }
        .spellingbee_post .col-lg-3 {
            width: 25%;
            float:left;
        }
        .spellingbee_post .col-lg-7 {
            width: 58.333333333333336%;
            float:left;
        }
        .type-post .spellingbee_post
        {
            border-bottom: 6px solid #FFCA0B !important;
        }

        .float-5
        {
            float:left;
            clear:both;
        }

        .float-4
        {
            float:left;
        }

        .float-10
        {
            float:left;
            clear:both;
            margin-top:15px;
            height: 78px;
        }
        .spellingbee .links-spell
        {
            color: white;
        }
        .spellingbee .col-lg-12
        {
            width: 100%;
            float:left;
            clear: both;
        }
        .spellingbee .col-lg-6
        {
            padding: 10px 0px;
            width: 50%;
            float:left;
        }
        .spellingbee .col-lg-6 .leader
        {
            margin-top: 8px;
            float:left;
        }
        .spellingbee .col-lg-6 .leader_board1 .col-lg-3
        {

            height: 70px;
            background: #C59567;

        }
        .spellingbee .col-lg-6 .leader_board2 .col-lg-3
        {

            height: 70px;
            background: #EAB901;

        }
        .spellingbee .col-lg-6 .leader_board1 .col-lg-8
        {
            background: #E6A96E;
            height: 40px;
            padding: 25px 0px 45px 12px;
            font-size: 22px;
            width: 66.66666666666666%;
            float:left;
        }
        .spellingbee .col-lg-6 .leader_board2 .col-lg-8
        {
            background: #FFCA0B;
            height: 40px;
            padding: 25px 0px 45px 12px;
            font-size: 22px;
            width: 66.66666666666666%;
            float:left;
        }
    }

    @media (min-width: 952px)
    {
        .align-center {
            margin-left: 70px;
        }
        .height_value
        {
            font-size: 16px; 
            line-height: 18px;
        }
        .date-string
        {
            float:left;
            clear:both;
            color:#C6C5CA;
            font-size: 11px;
        }
        .height_value
        {
            height: 35px;
            overflow: hidden; 
        }

        .float-10 img
        {
            overflow:hidden;
            float: left; 
            width: 50px;
            height: 50px; 
            border:0; 
            margin-right:12px;
        }
        .leader img
        {
            overflow: hidden;
            float: left;
            width: 90%;
            border: 0;
            margin: 5x;
        }
        .float-4 .play_image img
        {
            border: 0 none;
            float: left;
            margin: 30px 0 0 30px;
            overflow: hidden;
            width: 60%;
        }
        .float-4 img
        {
            border: 0 none;
            float: left;
            margin-left: 30px;
            overflow: hidden;
            width: 100%;
        }
        .spellingbee_post .col-lg-3 {
            width: 25%;
            float:left;
        }
        .spellingbee_post .col-lg-7 {
            width: 58.333333333333336%;
            float:left;
        }
        .type-post .spellingbee_post
        {
            border-bottom: 6px solid #FFCA0B !important;
        }

        .float-5
        {
            float:left;
            clear:both;
        }

        .float-4
        {
            float:left;
        }

        .float-10
        {
            float:left;
            clear:both;
            margin-top:15px;
            height: 60px;
        }
        .spellingbee .links-spell
        {
            color: white;
        }
        .spellingbee .col-lg-12
        {
            width: 100%;
            float:left;
            clear: both;
        }
        .spellingbee .col-lg-6
        {
            padding: 8px 0px;
            width: 50%;
            float:left;
        }
        .spellingbee .col-lg-6 .leader
        {
            margin-top: 8px;
            float:left;
        }
        .spellingbee .col-lg-6 .leader_board1 .col-lg-3
        {

            height: 60px;
            background: #C59567;

        }
        .spellingbee .col-lg-6 .leader_board2 .col-lg-3
        {

            height: 60px;
            background: #EAB901;

        }
        .spellingbee .col-lg-6 .leader_board1 .col-lg-8
        {
            background: #E6A96E;
            height: 40px;
            padding: 20px 0px 40px 12px;
            font-size: 15px;
            width: 66.66666666666666%;
            float:left;
        }
        .spellingbee .col-lg-6 .leader_board2 .col-lg-8
        {
            background: #FFCA0B;
            height: 40px;
            padding: 20px 0px 40px 12px;
            font-size: 15px;
            width: 66.66666666666666%;
            float:left;
        }
    }

    @media (min-width: 1152px)
    {
        .align-center {
            margin-left: 70px;
        }
        .date-string
        {
            float:left;
            clear:both;
            color:#C6C5CA;
            font-size: 12px;
        }
        .height_value
        {
            height: 45px;
            overflow: hidden; 
        }

        .float-10 img
        {
            overflow:hidden;
            float: left; 
            width: 60px;
            height: 60px; 
            border:0; 
            margin-right:15px;
        }
        .leader img
        {
            overflow: hidden;
            float: left;
            width: 90%;
            /*height: 16px;*/
            border: 0;
            margin: 11px;
        }
        .float-4 .play_image img
        {
            border: 0 none;
            float: left;
            margin: 30px 0 0 30px;
            overflow: hidden;
            width: 60%;
        }
        .float-4 img
        {
            border: 0 none;
            float: left;
            margin-left: 30px;
            overflow: hidden;
            width: 100%;
        }
        .spellingbee_post .col-lg-3 {
            width: 25%;
            float:left;
        }
        .spellingbee_post .col-lg-7 {
            width: 58.333333333333336%;
            float:left;
        }
        .type-post .spellingbee_post
        {
            border-bottom: 6px solid #FFCA0B !important;
        }

        .float-5
        {
            float:left;
            clear:both;
        }

        .float-4
        {
            float:left;
        }

        .float-10
        {
            float:left;
            clear:both;
            margin-top:15px;
            height: 70px;
        }
        .spellingbee .links-spell
        {
            color: white;
        }
        .spellingbee .col-lg-12
        {
            width: 100%;
            float:left;
            clear: both;
        }
        .spellingbee .col-lg-6
        {
            padding: 10px 0px;
            width: 50%;
            float:left;
        }
        .spellingbee .col-lg-6 .leader
        {
            margin-top: 8px;
            float:left;
        }
        .spellingbee .col-lg-6 .leader_board1 .col-lg-3
        {

            height: 64px;
            background: #C59567;

        }
        .spellingbee .col-lg-6 .leader_board2 .col-lg-3
        {

            height: 64px;
            background: #EAB901;

        }
        .spellingbee .col-lg-6 .leader_board1 .col-lg-8
        {
            background: #E6A96E;
            height: 40px;
            padding: 22px 0px 42px 12px;
            font-size: 22px;
            width: 66.66666666666666%;
            float:left;
        }
        .spellingbee .col-lg-6 .leader_board2 .col-lg-8
        {
            background: #FFCA0B;
            height: 40px;
            padding: 22px 0px 42px 12px;
            font-size: 22px;
            width: 66.66666666666666%;
            float:left;
        }
    }


    @media (min-width: 1280px)
    {
        .align-center {
            margin-left: 70px;
        }
        .date-string
        {
            float:left;
            clear:both;
            color:#C6C5CA;
            font-size: 12px;
        }
        .height_value
        {
            height: 50px;
            overflow: hidden; 
        }

        .float-10 img
        {
            overflow:hidden;
            float: left; 
            width: 65px;
            height: 65px; 
            border:0; 
            margin-right:15px;
        }
        .leader img
        {
            overflow: hidden;
            float: left;
            width: 98%;
            /*height: 20px;*/
            border: 0;
            margin: 12px;
        }
        .float-4 .play_image img
        {
            border: 0 none;
            float: left;
            margin: 30px 0 0 30px;
            overflow: hidden;
            width: 60%;
        }
        .float-4 img
        {
            border: 0 none;
            float: left;
            margin-left: 30px;
            overflow: hidden;
            width: 100%;
        }
        .spellingbee_post .col-lg-3 {
            width: 25%;
            float:left;
        }
        .spellingbee_post .col-lg-7 {
            width: 58.333333333333336%;
            float:left;
        }
        .type-post .spellingbee_post
        {
            border-bottom: 6px solid #FFCA0B !important;
        }

        .float-5
        {
            float:left;
            clear:both;
        }

        .float-4
        {
            float:left;
        }

        .float-10
        {
            float:left;
            clear:both;
            margin-top:15px;
            height: 78px;
        }
        .spellingbee .links-spell
        {
            color: white;
        }
        .spellingbee .col-lg-12
        {
            width: 100%;
            float:left;
            clear: both;
        }
        .spellingbee .col-lg-6
        {
            padding: 10px 0px;
            width: 50%;
            float:left;
        }
        .spellingbee .col-lg-6 .leader
        {
            margin-top: 8px;
            float:left;
        }
        .spellingbee .col-lg-6 .leader_board1 .col-lg-3
        {

            height: 70px;
            background: #C59567;

        }
        .spellingbee .col-lg-6 .leader_board2 .col-lg-3
        {

            height: 70px;
            background: #EAB901;

        }
        .spellingbee .col-lg-6 .leader_board1 .col-lg-8
        {
            background: #E6A96E;
            height: 40px;
            padding: 25px 0px 45px 12px;
            font-size: 22px;
            width: 66.66666666666666%;
            float:left;
        }
        .spellingbee .col-lg-6 .leader_board2 .col-lg-8
        {
            background: #FFCA0B;
            height: 40px;
            padding: 25px 0px 45px 12px;
            font-size: 22px;
            width: 66.66666666666666%;
            float:left;
        }
    }





    @media (min-width: 1400px)
    {
        .align-center {
            margin-left: 70px;
        }
        .date-string
        {
            float:left;
            clear:both;
            color:#C6C5CA;
            font-size: 12px;
        }
        .height_value
        {
            height: 60px;
            overflow: hidden; 
        }

        .float-10 img
        {
            overflow:hidden;
            float: left; 
            width: 75px;
            height: 75px; 
            border:0; 
            margin-right:15px;
        }
        .leader img
        {
            overflow: hidden;
            float: left;
            width: 98%;
            /*height: 28px;*/
            border: 0;
            margin: 12px;
        }
        .float-4 .play_image img
        {
            border: 0 none;
            float: left;
            margin: 30px 0 0 30px;
            overflow: hidden;
            width: 60%;
        }
        .float-4 img
        {
            border: 0 none;
            float: left;
            margin-left: 30px;
            overflow: hidden;
            width: 100%;
        }
        .spellingbee_post .col-lg-3 {
            width: 25%;
            float:left;
        }
        .spellingbee_post .col-lg-7 {
            width: 58.333333333333336%;
            float:left;
        }
        .type-post .spellingbee_post
        {
            border-bottom: 6px solid #FFCA0B !important;
        }

        .float-5
        {
            float:left;
            clear:both;
        }

        .float-4
        {
            float:left;
        }

        .float-10
        {
            float:left;
            clear:both;
            margin-top:15px;
            height: 100px;
        }
        .spellingbee .links-spell
        {
            color: white;
        }
        .spellingbee .col-lg-12
        {
            width: 100%;
            float:left;
            clear: both;
        }
        .spellingbee .col-lg-6
        {
            padding: 20px 0px;
            width: 50%;
            float:left;
        }
        .spellingbee .col-lg-6 .leader
        {
            margin-top: 8px;
            float:left;
        }
        .spellingbee .col-lg-6 .leader_board1 .col-lg-3
        {

            height: 80px;
            background: #C59567;

        }
        .spellingbee .col-lg-6 .leader_board2 .col-lg-3
        {

            height: 80px;
            background: #EAB901;

        }
        .spellingbee .col-lg-6 .leader_board1 .col-lg-8
        {
            background: #E6A96E;
            height: 40px;
            padding: 30px 0px 50px 12px;
            font-size: 22px;
            width: 66.66666666666666%;
            float:left;
        }
        .spellingbee .col-lg-6 .leader_board2 .col-lg-8
        {
            background: #FFCA0B;
            height: 40px;
            padding: 30px 0px 50px 12px;
            font-size: 22px;
            width: 66.66666666666666%;
            float:left;
        }
    }


    @media (min-width: 1600px)
    {
        .align-center {
            margin-left: 70px;
        }
        .height_value
        {
            height: 73px;
            overflow: hidden; 
        }
        .float-10 img
        {
            overflow:hidden;
            float: left; 
            width: 90px;
            height: 90px; 
            border:0; 
            margin-right:15px;
        }
        .leader img
        {
            overflow: hidden;
            float: left;
            width: 98%;
            /*height: 38px;*/
            border: 0;
            margin: 12px;
        }
        .float-4 .play_image img
        {
            border: 0 none;
            float: left;
            margin: 30px 0 0 30px;
            overflow: hidden;
            width: 60%;
        }
        .float-4 img
        {
            border: 0 none;
            float: left;
            margin-left: 30px;
            overflow: hidden;
            width: 100%;
        }
        .spellingbee_post .col-lg-3 {
            width: 25%;
            float:left;
        }
        .spellingbee_post .col-lg-7 {
            width: 58.333333333333336%;
            float:left;
        }
        .type-post .spellingbee_post
        {
            border-bottom: 6px solid #FFCA0B !important;
        }
        .date-string
        {
            float:left;
            clear:both;
            color:#C6C5CA;
            font-size: 12px;
        }
        .float-5
        {
            float:left;
            clear:both;
        }

        .float-4
        {
            float:left;
        }

        .float-10
        {
            float:left;
            clear:both;
            margin-top:15px;
            height: 100px;
        }
        .spellingbee .links-spell
        {
            color: white;
        }
        .spellingbee .col-lg-12
        {
            width: 100%;
            float:left;
            clear: both;
        }
        .spellingbee .col-lg-6
        {
            padding: 20px 0px;
            width: 50%;
            float:left;
        }
        .spellingbee .col-lg-6 .leader
        {
            margin-top: 8px;
            float:left;
        }
        .spellingbee .col-lg-6 .leader_board1 .col-lg-3
        {

            height: 92px;
            background: #C59567;

        }
        .spellingbee .col-lg-6 .leader_board2 .col-lg-3
        {

            height: 92px;
            background: #EAB901;

        }
        .spellingbee .col-lg-6 .leader_board1 .col-lg-8
        {
            background: #E6A96E;
            height: 40px;
            padding: 35px 0px 57px 12px;
            font-size: 22px;
            width: 66.66666666666666%;
            float:left;
        }
        .spellingbee .col-lg-6 .leader_board2 .col-lg-8
        {
            background: #FFCA0B;
            height: 40px;
            padding: 35px 0px 57px 12px;
            font-size: 22px;
            width: 66.66666666666666%;
            float:left;
        }
    }

</style>    
<script>
    $(document).ready(function () {

        $("#triangle-bottomright").css("border-left-width",
                $("#post-image").width() + "px");
        $(".play_image").hover(
                function () {
                    $("#play_image_1").hide();
                    $("#play_image_2").show();
                }, function () {
            $("#play_image_2").hide();
            $("#play_image_1").show();
        }
        );

    });
</script>