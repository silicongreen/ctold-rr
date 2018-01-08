<script type="text/javascript" src="<?php echo base_url('styles/layouts/tdsfront/spelling_bee/jquery.popupWindow.js'); ?>"></script>
<div class="home_box">    
    <!--    <div style="width: 100%;">
            <div class="flying_bee1">
                <img id="flying_bee1_bg" src="<?php // echo base_url('styles/layouts/tdsfront/spelling_bee/2015/images/BG.png');        ?>" style="width:100%;height: 410px;">
                <div id="over1">
                    <span class="Centerer1"></span>
                    <img class="Centered1" id="flying_bee1_logo" src="<?php // echo base_url('styles/layouts/tdsfront/spelling_bee/2015/images/sp-logo-2.png');        ?>" style="">                   
                </div>
                <img id="season_4" src="<?php // echo base_url('styles/layouts/tdsfront/spelling_bee/2015/images/season-4.png');        ?>" style="">
            </div>
            <div class="flying_bee1_content">
                <div class="f5 join_msg_box">
                    <center><img id="join_msg" src="<?php // echo base_url('styles/layouts/tdsfront/spelling_bee/2015/images/join_msg.png');        ?>" style="width:75%;"></center>
                </div>
                <div class="sp_btn_pack_box">
                    <nav id="sp_btn_pack">
    <?php // if( free_user_logged_in() ) { ?>
    <?php
    // $is_joined_spellbee = get_free_user_session('is_joined_spellbee');
//                    if($is_joined_spellbee == 1 || get_free_user_session('type') != 2){
    ?> 
                        a href="http://www.champs21.com/swf/spellingbee_2015/index.html" title="Spelling Bee | Season 4" class="example2demo sp_btn_1" style="float: left;width:110px;position:relative;z-index: 100;" name="Spelling Bee">
                            <img src="<?php // echo base_url('styles/layouts/tdsfront/spelling_bee/2015/images/play.png');        ?>" style="width:100%;" onMouseOver="MouseRollover(this)" onMouseOut="MouseOut(this)">
                        </a 
                        <a name="windowX" title="Spelling Bee | Season 4" id="play_spellbee_4" style="float: left;width:110px;" href="javascript:void(0);">
                            <img src="<?php // echo base_url('styles/layouts/tdsfront/spelling_bee/2015/images/play.png');        ?>" style="width:100%;" onMouseOver="MouseRollover(this)" onMouseOut="MouseOut(this)">
                        </a>
    <?php // } else {  ?>
                            a class="sp_btn_1" id="join_spellbee_reg" style="float: left;width:110px;position:relative;z-index: 100;" href="javascript:void(0);">
                                <img src="<?php // echo base_url('styles/layouts/tdsfront/spelling_bee/2015/images/play.png');        ?>" style="width:100%;" onMouseOver="MouseRollover(this)" onMouseOut="MouseOut(this)">
                            </a
    <?php
    // }
//                    } else { 
    ?>
                        a class="f2 login-user sp_btn_1" style="float: left;width:110px;position:relative;z-index: 100;" href="javascript:void(0);">
                            <img src="<?php // echo base_url('styles/layouts/tdsfront/spelling_bee/2015/images/play.png');       ?>" style="width:100%;" onMouseOver="MouseRollover(this)" onMouseOut="MouseOut(this)">
                        </a
    <?php // } ?>
    <?php // if( free_user_logged_in() ) { ?>
    <?php
    // $is_joined_spellbee = get_free_user_session('is_joined_spellbee');
//                    if($is_joined_spellbee == 0 && get_free_user_session('type') == 2){
    ?>                    
                        a href="javascript:void(0);" id="join_spellbee_reg" class="f2 button-filter1 sp_btn_2" style="position:relative;z-index: 90;">Join</a
                        
    <?php // } } else {   ?>
                    a href="javascript:void(0);" class="f2 button-filter1 login-user sp_btn_2" style="position:relative;">Join</a
    <?php // }   ?>
                    <a href="<?php // echo base_url('leaderboard');        ?>" class="f2 button-filter2 sp_btn_3" style="position:relative;z-index: 80;">Divisional Participants</a>
                    <a href="https://www.facebook.com/spellbangladesh" target="_blank" class="f2 button-filter3 sp_btn_4" style="position:relative;z-index: 70;">Facebook</a>
                    </nav>
                </div>
                <div class="jointext">
                    Spelling Bee is back with its 4th season in Bangladesh! Join the Spelling Bee Competition and join the top spellers of the country to fight for the trophy and the prestigious title of Spelling Bee Champion.
                </div>
            </div>
        </div>-->

    <div class="bee-wrapper">
        <img src="/styles/layouts/tdsfront/spelling_bee/2015/slides/sb_bee.png" />
    </div>

    <?php
    $CI = &get_instance();
    $CI->load->config("huffas");
    $nb_config = $CI->config->config['nation-builder'];
    $candle_button = $nb_config['3rd-column']['candle_button'];
    ?>

    <div class="new-sb-banner flexslider">
        <ul class="slides">
            <li onclick="location.href = '<?php echo base_url() . 'nation-builder'; ?>'">
                <img src="/styles/layouts/tdsfront/home_slider/slider-nation-builder.png" />
                <?php // if (!empty($candle_button)) { ?>
                    <!--<img class="banner-button" src="<?php // echo $candle_button; ?>" >-->
                <?php // } ?>
            </li>
            <li onclick="location.href = '<?php echo base_url() . 'spellingbee'; ?>'">
                <img src="/styles/layouts/tdsfront/home_slider/dhorjo-joto-dur-toto-913x504.jpg" />
            </li>
            <li data="android-app" class="pop-without-login">
                <img src="/styles/layouts/tdsfront/spelling_bee/2015/slides/slider-spellchamps-prize.jpg" />
            </li>
            <li onclick="location.href = '<?php echo base_url() . 'spellingbee'; ?>'">
                <img src="/styles/layouts/tdsfront/spelling_bee/2015/slides/slider_3.png" />
            </li>
        </ul>
    </div>

</div>

<div class="spellingbee_ct"></div>

<style type="text/css">
    .bee-wrapper {
        float: left;
        width: 32%;
    }
    .bee-wrapper img {
        float: right;
        height: 85%;
        margin-top: 30px;
    }
    .new-sb-banner {
        float: left;
        height: 100%;
        width: 68%;
    }
    .flexslider {
        cursor: pointer;
    }
    .flexslider .slides img {
        height: 100%;
    }
    .flex-direction-nav li {
        line-height: 40px;
    }
    .flex-control-nav {
        display: none;
    }
    .flexslider .slides li:first-child {
        position: relative;
    }
    .flexslider .slides li:first-child img.banner-button {
        bottom: 16%;
        height: auto;
        position: absolute;
        right: 36%;
        width: 30%;
    }
</style>

<script type="text/javascript">
    function MouseRollover(MyImage) {
        MyImage.src = "styles/layouts/tdsfront/spelling_bee/2015/images/play-hover.png";
    }
    function MouseOut(MyImage) {
        MyImage.src = "styles/layouts/tdsfront/spelling_bee/2015/images/play.png";
    }

    $('.example2demo').popupWindow({
        centerBrowser: 1,
        height: 600,
        width: 800,
        resizable: 1
    });

    var numOfItemsSp = $('#sp_btn_pack a').length;
    $('#sp_btn_pack a').hide();

    $(window).bind("load", function () {
        setTimeout(
                function ()
                {

                    $('.jointext').addClass('jointext_slide');
                    $('.sp_btn_1').show();
                    $('.sp_btn_1').addClass('play_btn_spin');
                    if (numOfItemsSp == 4)
                    {
                        setTimeout(
                                function ()
                                {
                                    $('.sp_btn_2').show();
                                    $('.sp_btn_2').addClass('sp_btn_2_slide');
                                }, 1000);
                        setTimeout(
                                function ()
                                {
                                    $('.sp_btn_3').show();
                                    $('.sp_btn_3').addClass('sp_btn_3_slide');
                                }, 1500);
                        setTimeout(
                                function ()
                                {
                                    $('.sp_btn_4').show();
                                    $('.sp_btn_4').addClass('sp_btn_4_slide');
                                }, 2000);
                    }
                    else
                    {
                        setTimeout(
                                function ()
                                {
                                    $('.sp_btn_3').show();
                                    $('.sp_btn_3').addClass('sp_btn_33_slide');
                                }, 1500);
                        setTimeout(
                                function ()
                                {
                                    $('.sp_btn_4').show();
                                    $('.sp_btn_4').addClass('sp_btn_44_slide');
                                }, 2000);
                    }



                }, 3000);
    });

    $(window).load(function () {
        $('.flexslider').flexslider();
    });
</script>

<style>
    .sp_btn_pack_box
    {
        clear: both;
        float:left;
        width: 100%;
        margin-top:10px;
    }
    .join_msg_box
    {
        float:left;width: 80%;font-size: 20px;
    }
    #over1
    {
        position:absolute;
        width:100%;
        height:410px;
        top:0px;
        text-align: center; /*handles the horizontal centering*/
    }
    .Centerer1
    {
        display: inline-block;
        height: 100%;
        vertical-align: middle;
    }
    .Centered1
    {
        width:60%;
        display: inline-block;
        vertical-align: middle;    
    }
    .flying_bee {
        background-image: url("<?php echo base_url('styles/layouts/tdsfront/spelling_bee/2015/images/bugs_27.gif'); ?>");
        background-position: left top;
        background-repeat: no-repeat;
        background-size: 80% auto;
        height: 40px;
        left: 150px;
        position: absolute;
        top: 132px;
        width: 60px;
    }

    .flying_bee1 { 
        background-image: url("<?php echo base_url('styles/layouts/tdsfront/spelling_bee/2015/images/BG.png'); ?>");
        background-position: left top;
        background-repeat: no-repeat;
        background-size: 408px 410px;
        width: 41%;
        float:left;
        height: 410px;
        left: 0px;
        position: relative;
        top: 0px;
        overflow:hidden;
        z-index: 10;
    }
    .flying_bee1_content
    {
        width: 58%;
        float:right;
        z-index: 100;
    }
    .anim {
        -webkit-animation: spin 1s 1 ease-in;  
        -moz-animation: spin 1s 1 ease-in;  
        -o-animation: spin 1s 1 ease-in;  
        animation: spin 1s 1 ease-in;  
    }
    /* Webkit, Chrome and Safari */
    @-webkit-keyframes spin {
        from {-webkit-transform:scale(2) rotate(0deg);}
        to {-webkit-transform:scale(1) rotate(360deg);}
    }
    /* Mozilla Firefox 15 below */
    @-moz-keyframes spin {    
        from {-moz-transform:scale(2) rotate(0deg);}
        to {-moz-transform:scale(1) rotate(360deg);}
    }
    /* Opera 12.0 */
    @-o-keyframes spin {    
        from {-o-transform:scale(2) rotate(0deg);}
        to {-o-transform:scale(1) rotate(360deg);}
    }
    /* W3, Opera 12+, Firefox 16+ */
    @keyframes spin {    
        from {transform:scale(2) rotate(0deg);}
        to {transform:scale(1) rotate(360deg);}
    }

    #flying_bee1_logo
    {
        -webkit-animation: spinlogo 1s forwards;
        -moz-animation: spinlogo 1s forwards;
        -o-animation: spinlogo 1s forwards;
        animation: spinlogo 1s forwards;

        -webkit-animation-delay: 1s;    
        -moz-animation-delay: 1s;    
        -o-animation-delay: 1s;    
        animation-delay: 1s;
        opacity:0;
    }

    /* Webkit, Chrome and Safari */
    @-webkit-keyframes spinlogo {
        from {-webkit-transform:scale(.1) rotate(0deg);opacity:1;}
        to {-webkit-transform:scale(1) rotate(360deg);opacity:1;}
    }
    /* Mozilla Firefox 15 below */
    @-moz-keyframes spinlogo {    
        from {-moz-transform:scale(.1) rotate(0deg);opacity:1;}
        to {-moz-transform:scale(1) rotate(360deg);opacity:1;}
    }
    /* Opera 12.0 */
    @-o-keyframes spinlogo {    
        from {-o-transform:scale(.1) rotate(0deg);opacity:1;}
        to {-o-transform:scale(1) rotate(360deg);opacity:1;}
    }
    /* W3, Opera 12+, Firefox 16+ */
    @keyframes spinlogo {    
        from {transform:scale(.1) rotate(0deg);opacity:1;}
        to {transform:scale(1) rotate(360deg);opacity:1;}
    }
    .play_btn_spin
    {
        -webkit-animation: spinplay 1s forwards;    
        -moz-animation: spinplay 1s forwards;    
        -o-animation: spinplay 1s forwards;    
        animation: spinplay 1s forwards;
    }

    /* Webkit, Chrome and Safari */
    @-webkit-keyframes spinplay {
        from {-webkit-transform:rotate(0deg);margin-left:580px;}
        to {-webkit-transform:rotate(720deg);margin-left:0px;;}
    }
    /* Mozilla Firefox 15 below */
    @-moz-keyframes spinplay {    
        from {-moz-transform:rotate(0deg);margin-left:580px;}
        to {-moz-transform:rotate(720deg);margin-left:0px;;}
    }
    /* Opera 12.0 */
    @-o-keyframes spinplay {    
        from {-o-transform:rotate(0deg);margin-left:580px;}
        to {-o-transform:rotate(720deg);margin-left:0px;;}
    }
    /* W3, Opera 12+, Firefox 16+ */
    @keyframes spinplay {    
        from {transform:rotate(0deg);margin-left:580px;}
        to {transform:rotate(720deg);margin-left:0px;;}
    }
    .sp_btn_2_slide
    {
        -webkit-animation: spbtn_2_slide 1s forwards;    
        -moz-animation: spbtn_2_slide 1s forwards;    
        -o-animation: spbtn_2_slide 1s forwards;    
        animation: spbtn_2_slide 1s forwards;
    }

    /* Webkit, Chrome and Safari */
    @-webkit-keyframes spbtn_2_slide {
        from {margin-left:-110px;opacity:0;}
        to {margin-left:0px;opacity:1;}
    }
    /* Mozilla Firefox 15 below */
    @-moz-keyframes spbtn_2_slide {    
        from {margin-left:-110px;opacity:0;}
        to {margin-left:0px;opacity:1;}
    }
    /* Opera 12.0 */
    @-o-keyframes spbtn_2_slide {    
        from {margin-left:-110px;opacity:0;}
        to {margin-left:0px;opacity:1;}
    }
    /* W3, Opera 12+, Firefox 16+ */
    @keyframes spbtn_2_slide {    
        from {margin-left:-110px;opacity:0;}
        to {margin-left:0px;opacity:1;}
    }

    .sp_btn_3_slide
    {
        -webkit-animation: spbtn_3_slide 1s forwards;    
        -moz-animation: spbtn_3_slide 1s forwards;    
        -o-animation: spbtn_3_slide 1s forwards;    
        animation: spbtn_3_slide 1s forwards;
    }

    /* Webkit, Chrome and Safari */
    @-webkit-keyframes spbtn_3_slide {
        from {margin-left:-183px;opacity:0;}
        to {margin-left:0px;opacity:1;}
    }
    /* Mozilla Firefox 15 below */
    @-moz-keyframes spbtn_3_slide {    
        from {margin-left:-183px;opacity:0;}
        to {margin-left:0px;opacity:1;}
    }
    /* Opera 12.0 */
    @-o-keyframes spbtn_3_slide {    
        from {margin-left:-183px;opacity:0;}
        to {margin-left:0px;opacity:1;}
    }
    /* W3, Opera 12+, Firefox 16+ */
    @keyframes spbtn_3_slide {    
        from {margin-left:-183px;opacity:0;}
        to {margin-left:0px;opacity:1;}
    }
    .sp_btn_33_slide
    {
        -webkit-animation: spbtn_33_slide 1s forwards;    
        -moz-animation: spbtn_33_slide 1s forwards;    
        -o-animation: spbtn_33_slide 1s forwards;    
        animation: spbtn_33_slide 1s forwards;
    }

    /* Webkit, Chrome and Safari */
    @-webkit-keyframes spbtn_33_slide {
        from {margin-left:-110px;opacity:0;}
        to {margin-left:0px;opacity:1;}
    }
    /* Mozilla Firefox 15 below */
    @-moz-keyframes spbtn_33_slide {    
        from {margin-left:-110px;opacity:0;}
        to {margin-left:0px;opacity:1;}
    }
    /* Opera 12.0 */
    @-o-keyframes spbtn_33_slide {    
        from {margin-left:-110px;opacity:0;}
        to {margin-left:0px;opacity:1;}
    }
    /* W3, Opera 12+, Firefox 16+ */
    @keyframes spbtn_33_slide {    
        from {margin-left:-110px;opacity:0;}
        to {margin-left:0px;opacity:1;}
    }

    .sp_btn_4_slide
    {
        -webkit-animation: spbtn_4_slide 1s forwards;    
        -moz-animation: spbtn_4_slide 1s forwards;    
        -o-animation: spbtn_4_slide 1s forwards;    
        animation: spbtn_4_slide 1s forwards;
    }

    /* Webkit, Chrome and Safari */
    @-webkit-keyframes spbtn_4_slide {
        from {margin-left:-328px;opacity:0;}
        to {margin-left:0px;opacity:1;}
    }
    /* Mozilla Firefox 15 below */
    @-moz-keyframes spbtn_4_slide {    
        from {margin-left:-328px;opacity:0;}
        to {margin-left:0px;opacity:1;}
    }
    /* Opera 12.0 */
    @-o-keyframes spbtn_4_slide {    
        from {margin-left:-328px;opacity:0;}
        to {margin-left:0px;opacity:1;}
    }
    /* W3, Opera 12+, Firefox 16+ */
    @keyframes spbtn_4_slide {    
        from {margin-left:-328px;opacity:0;}
        to {margin-left:0px;opacity:1;}
    }

    .sp_btn_44_slide
    {
        -webkit-animation: spbtn_44_slide 1s forwards;    
        -moz-animation: spbtn_44_slide 1s forwards;    
        -o-animation: spbtn_44_slide 1s forwards;    
        animation: spbtn_44_slide 1s forwards;
    }

    /* Webkit, Chrome and Safari */
    @-webkit-keyframes spbtn_44_slide {
        from {margin-left:-185px;opacity:0;}
        to {margin-left:0px;opacity:1;}
    }
    /* Mozilla Firefox 15 below */
    @-moz-keyframes spbtn_44_slide {    
        from {margin-left:-185px;opacity:0;}
        to {margin-left:0px;opacity:1;}
    }
    /* Opera 12.0 */
    @-o-keyframes spbtn_44_slide {    
        from {margin-left:-185px;opacity:0;}
        to {margin-left:0px;opacity:1;}
    }
    /* W3, Opera 12+, Firefox 16+ */
    @keyframes spbtn_44_slide {    
        from {margin-left:-185px;opacity:0;}
        to {margin-left:0px;opacity:1;}
    }
    .jointext
    {
        float:left;width: 41%;
        font-size: 12px;
        margin-top: 200px;
        letter-spacing: 0px;
        margin-right: 20px;
        transition: all 0.5s ease-in 0s;
        position:absolute;
    }
    .jointext_slide
    {
        -webkit-animation: jointext_slidedown 1s forwards;    
        -moz-animation: jointext_slidedown 1s forwards;    
        -o-animation: jointext_slidedown 1s forwards;    
        animation: jointext_slidedown 1s forwards;
    }

    /* Webkit, Chrome and Safari */
    @-webkit-keyframes jointext_slidedown {
        from {margin-top:200px;opacity:1;}
        to {margin-top:330px;opacity:1;}
    }
    /* Mozilla Firefox 15 below */
    @-moz-keyframes jointext_slidedown {    
        from {margin-top:200px;opacity:1;}
        to {margin-top:330px;opacity:1;}
    }
    /* Opera 12.0 */
    @-o-keyframes jointext_slidedown {    
        from {margin-top:200px;opacity:1;}
        to {margin-top:330px;opacity:1;}
    }
    /* W3, Opera 12+, Firefox 16+ */
    @keyframes jointext_slidedown {    
        from {margin-top:200px;opacity:1;}
        to {margin-top:330px;opacity:1;}
    }


    #season_4 {
        position: relative;
        float:right;
        bottom: 0px;
        width: 120px;
        -webkit-animation: slide 0.5s forwards;
        -moz-animation: slide 0.5s forwards;
        -o-animation: slide 0.5s forwards;
        animation: slide4 0.5s forwards;

        -webkit-animation-delay: 1s;    
        -moz-animation-delay: 1s;    
        -o-animation-delay: 1s;    
        animation-delay: 1s;
    }
    /* Webkit, Chrome and Safari */
    @-webkit-keyframes slide4 {
        100% { bottom: 70px; }
    }
    /* Mozilla Firefox 15 below */
    @-moz-keyframes slide4 {    
        100% { bottom: 70px; }
    }
    /* Opera 12.0 */
    @-o-keyframes slide4 {    
        100% { bottom: 70px; }
    }
    /* W3, Opera 12+, Firefox 16+ */
    @keyframes slide4 {
        100% { bottom: 70px; }
    }

    .home_box {  
        height: 410px;

    }

    .button-filter1 {
        background-color: #F4A91C;
        border: 1px solid #b3b3b3;  
        color: #fff;
        cursor: pointer;
        display: block;
        float: left;
        font-size: 17px;
        font-weight: normal;
        padding: 10px 17px;
        margin-top:39px;
        text-decoration: none;
        transition: all 0.25s ease-in 0s;
    }

    .button-filter1:hover, .button-filter1:active {
        background-color: #FF8F35;
        color: #ffffff;
        transition: all 0.25s linear 0s;
    }
    .button-filter2 {
        background-color: #63BF8E;
        border: 1px solid #b3b3b3;  
        color: #fff;
        cursor: pointer;
        display: block;
        float: left;
        font-size: 17px;
        font-weight: normal;
        padding: 10px 17px;
        margin-top:39px;
        text-decoration: none;
        transition: all 0.25s ease-in 0s;
    }

    .button-filter2:hover, .button-filter2:active {
        background-color: #61A581;
        color: #ffffff;
        transition: all 0.25s linear 0s;
    }
    .button-filter3 {
        background-color: #2E7EB1;
        border: 1px solid #b3b3b3;  
        color: #fff;
        cursor: pointer;
        display: block;
        float: left;
        font-size: 17px;
        font-weight: normal;
        padding: 10px 17px;
        margin-top:39px;
        text-decoration: none;
        transition: all 0.25s ease-in 0s;
    }

    .button-filter3:hover, .button-filter3:active {
        background-color: #0F547F;
        color: #ffffff;
        transition: all 0.25s linear 0s;
    }

</style>
<style>
    #join_msg {

        position: relative;
        -webkit-animation: bounce 1s forwards;    
        -moz-animation: bounce 1s forwards;    
        -o-animation: bounce 1s forwards;    
        animation: bounce 1s forwards;

        -webkit-animation-delay: 2s;
        -moz-animation-delay: 2s;
        -o-animation-delay: 2s;
        animation-delay: 2s;
        opacity:0;
    }

    /* Webkit, Chrome and Safari */

    @-webkit-keyframes bounce {
        0% {
            -webkit-transform:translateY(-100%);
            opacity: 0;
        }
        5% {
            -webkit-transform:translateY(-100%);
            opacity: 0;
        }
        15% {
            -webkit-transform:translateY(0);
            padding-bottom: 5px;
        }
        30% {
            -webkit-transform:translateY(-50%);
        }
        40% {
            -webkit-transform:translateY(0%);
            padding-bottom: 6px;
        }
        50% {
            -webkit-transform:translateY(-30%);
        }
        70% {
            -webkit-transform:translateY(0%);
            padding-bottom: 7px;
        }
        80% {
            -webkit-transform:translateY(-15%);
        }
        90% {
            -webkit-transform:translateY(0%);
            padding-bottom: 8px;
        }
        95% {
            -webkit-transform:translateY(-10%);
        }
        97% {
            -webkit-transform:translateY(0%);
            padding-bottom: 9px;
        }
        99% {
            -webkit-transform:translateY(-5%);
        }
        100% {
            -webkit-transform:translateY(0);
            padding-bottom: 9px;
            opacity: 1;
        }
    }

    /* Mozilla Firefox 15 below */
    @-moz-keyframes bounce {
        0% {
            -moz-transform:translateY(-100%);
            opacity: 0;
        }
        5% {
            -moz-transform:translateY(-100%);
            opacity: 0;
        }
        15% {
            -moz-transform:translateY(0);
            padding-bottom: 5px;
        }
        30% {
            -moz-transform:translateY(-50%);
        }
        40% {
            -moz-transform:translateY(0%);
            padding-bottom: 6px;
        }
        50% {
            -moz-transform:translateY(-30%);
        }
        70% {
            -moz-transform:translateY(0%);
            padding-bottom: 7px;
        }
        80% {
            -moz-transform:translateY(-15%);
        }
        90% {
            -moz-transform:translateY(0%);
            padding-bottom: 8px;
        }
        95% {
            -moz-transform:translateY(-10%);
        }
        97% {
            -moz-transform:translateY(0%);
            padding-bottom: 9px;
        }
        99% {
            -moz-transform:translateY(-5%);
        }
        100% {
            -moz-transform:translateY(0);
            padding-bottom: 9px;
            opacity: 1;
        }
    }

    /* Opera 12.0 */
    @-o-keyframes bounce {
        0% {
            -o-transform:translateY(-100%);
            opacity: 0;
        }
        5% {
            -o-transform:translateY(-100%);
            opacity: 0;
        }
        15% {
            -o-transform:translateY(0);
            padding-bottom: 5px;
        }
        30% {
            -o-transform:translateY(-50%);
        }
        40% {
            -o-transform:translateY(0%);
            padding-bottom: 6px;
        }
        50% {
            -o-transform:translateY(-30%);
        }
        70% {
            -o-transform:translateY(0%);
            padding-bottom: 7px;
        }
        80% {
            -o-transform:translateY(-15%);
        }
        90% {
            -o-transform:translateY(0%);
            padding-bottom: 8px;
        }
        95% {
            -o-transform:translateY(-10%);
        }
        97% {
            -o-transform:translateY(0%);
            padding-bottom: 9px;
        }
        99% {
            -o-transform:translateY(-5%);
        }
        100% {
            -o-transform:translateY(0);
            padding-bottom: 9px;
            opacity: 1;
        }
    }

    /* W3, Opera 12+, Firefox 16+ */
    @keyframes bounce {
        0% {
            transform:translateY(-100%);
            opacity: 0;
        }
        5% {
            transform:translateY(-100%);
            opacity: 0;
        }
        15% {
            transform:translateY(0);
            padding-bottom: 5px;
        }
        30% {
            transform:translateY(-50%);
        }
        40% {
            transform:translateY(0%);
            padding-bottom: 6px;
        }
        50% {
            transform:translateY(-30%);
        }
        70% {
            transform:translateY(0%);
            padding-bottom: 7px;
        }
        80% {
            transform:translateY(-15%);
        }
        90% {
            transform:translateY(0%);
            padding-bottom: 8px;
        }
        95% {
            transform:translateY(-7%);
        }
        97% {
            transform:translateY(0%);
            padding-bottom: 9px;
        }
        99% {
            transform:translateY(-3%);
        }
        100% {
            transform:translateY(0);
            padding-bottom: 9px;
            opacity: 1;
        }
    }
</style>


