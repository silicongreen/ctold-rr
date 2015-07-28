<?php $arCustomNews = getFormatedContentAll($news, 125); ?>

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
                        <?php //} else { ?>
                            <a id="join_spellbee_reg" class="play_image" style="border:0px;" href="javascript:void(0);">
                                <img id="play_image_1" class="no_toolbar" src="/styles/layouts/tdsfront/spelling_bee/2015/images/play.png" />
                                <img id="play_image_2" class="no_toolbar" src="/styles/layouts/tdsfront/spelling_bee/2015/images/play-hover.png" style="display:none;" />
                            </a>
                        <?php //} ?>
                        
                    <?php //} else { ?>
                        <a class="play_image login-user" style="border:0px;" href="javascript:void(0);">
                            <img id="play_image_1" class="no_toolbar" src="/styles/layouts/tdsfront/spelling_bee/2015/images/play.png" />
                            <img id="play_image_2" class="no_toolbar" src="/styles/layouts/tdsfront/spelling_bee/2015/images/play-hover.png" style="display:none;" />
                        </a>
                    <?php //} ?>
                    
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


        </div> 

    </div> 

</li>
<script type="text/javascript"> 
$('.example2demo').popupWindow({ 
centerBrowser:1 ,
height:600,
width:800,
resizable:1
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