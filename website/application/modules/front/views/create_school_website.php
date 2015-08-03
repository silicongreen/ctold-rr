<!--<link href="scripts/flexslider/css/shCore.css" rel="stylesheet" type="text/css" />
<link href="scripts/flexslider/css/shThemeDefault.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="scripts/flexslider/css/demo.css" type="text/css" media="screen" />-->
<link rel="stylesheet" href="scripts/flexslider/flexslider.css" type="text/css" media="screen" />
<script src="scripts/flexslider/js/modernizr.js"></script>
<?php $s_ci_key = (isset($ci_key)) ? $ci_key : NULL; ?>
<div class="container" style="width: 73%;min-height:250px;">
    <div class="create_school_banner">
        <div class="title_create_school" >
            <p>Your school</p>
            <p>needs a great website</p>
        </div>
    </div>
    <div class="create_website_text">
        <div>
            <p>it's surprisingly easy to create a unique website</p>
        </div>
    </div>
</div>
<div class="container white-box-full">
    <div class="container" style="width: 73%;min-height:250px;">
        <div class="rcorners1">
            CHOOSE YOUR THEME
        </div>
        <section class="slider">
            <div class="flexslider carousel">
                <ul class="slides">
                    <?php
                    $has_premium = false;
                    foreach ($all_ar_templates as $template) {

                        if ($template['name'] != $ar_templates['name']) {
                            if ($template['price'] > 0){
                                $has_premium = true;
                                continue;
                            }
                            ?>

                            <li>
                                <div class="template-image-wrapper">

                                    <div class="template-image">
                                        <img class="template-image-image" src="<?php echo $template['cover_url']; ?>" />
                                        <div class="template-type-tag2">
                                            <img src="scripts/flexslider/images/free_icon.png" />
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>

                                    <div class="template-buttons">
                                        <button type="button" class="diselect f2" onclick="window.location.href = '<?php echo base_url('submit-new-school?id=' . $template['name']); ?>'">Select</button>
                                        <button type="button" class="view_demo f2" onclick="window.open('<?php echo base_url('demo-school-template?id=' . $template['name']); ?>','_self')">View Demo</button>
<!--                                        <button type="button" class="view_demo f2" onclick="window.open('<?php echo $template['demo_url']; ?>', '_blank')">View Demo</button>-->
                                    </div>

                                </div>
                            </li>

                            <?php
                        }
                    }
                    ?>


                </ul>
            </div>
        </section>

        <?php if ($has_premium) { ?>
            <hr style="width:100%; background: #D0D0D0; height: 1px;"/>
            <div class="submenu">
                <div class='ribbon'>
                    <a id="tabHeader_1" href='#'><span>PREMIUM THEME</span></a>
                </div>
            </div> 
        <?php } ?>

        <section class="slider">
            <div class="flexslider carousel">
                <ul class="slides">
                    <?php
                    foreach ($all_ar_templates as $template) {

                        if ($template['name'] != $ar_templates['name']) {
                            if ($template['price'] == 0)
                                continue;
                            ?>

                            <li>
                                <div class="template-image-wrapper">

                                    <div class="template-image">
                                        <img class="template-image-image" src="<?php echo $template['cover_url']; ?>" />
                                        <div class="template-type-tag">
                                            <img src="scripts/flexslider/images/Tag-500tk.png" />
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>

                                    <div class="template-buttons">
                                        <button type="button" class="diselect f2" onclick="window.location.href = '<?php echo base_url('submit-new-school?id=' . $template['name']); ?>'">Select</button>
                                        <button type="button" class="view_demo f2" onclick="window.open('<?php echo base_url('demo-school-template?id=' . $template['name']); ?>',"_self"">View Demo</button>
<!--                                        <button type="button" class="view_demo f2" onclick="window.open('<?php echo $template['demo_url']; ?>', '_blank')">View Demo</button>-->
                                    </div>

                                </div>
                            </li>

                            <?php
                        }
                    }
                    ?>



                </ul>
            </div>
        </section>

    </div>
</div>
<div class="foter_background">
    <div class="image_and_text">
        <div class='text-footer'>
            <p class="contact_us">
                Contact us
            </p> 
            <p class="phone">
                <img src="scripts/flexslider/images/phone_icon.png" /> 01740 21 21 21
            </p> 
            <p class="mail">
                <img src="scripts/flexslider/images/mail_icon.png" /><a href="mailto:info@champs21.com?Subject=School%20Template" target="_top"> info@champs21.com</a>
            </p>
        </div>  
        <div  class='text-footer'>
            <img width='250' style='margin-left:100px;' src="scripts/flexslider/images/element_1.png" />
        </div>
    </div> 


</div>

<style type="text/css" media="all">
    .image_and_text
    {
        float:left;
        clear:both;
        margin-top:150px;
        width: 100%;
    }
    .text-footer
    {
        float:left;
        width: 35%;
        margin-left: 13%;
    }
    .contact_us
    {
        font-size: 65px;
        color: #56AA89;
        font-weight: bold;
        margin-left: 10px;
        line-height: 65px;
    }
    .phone
    {
        font-size: 28px;
        color: white;
        font-weight: bold;
    }
    .mail
    {
        font-size: 25px;
        color: white;
        font-weight: bold;
    }
    .slider
    {
        box-sizing: content-box !important;
    }
    .flexslider
    {
        border: none !important;
    }
    .template-image-wrapper {
        background-color: #ffffff;
        float: left;
        width:100%;
    }
    .template-hint {
        font-size: 10px;
        color: #777777;
    }
    .template-image {
        margin-left: auto;
        margin-right: auto;
        position: relative;
    }
    .template-image-image {
        padding: 20px;
        width: 100%;
    }
    .template-type-tag2 {
        left: 14px;
        position: absolute;
        top: 15px;
    }
    .template-type-tag {
        left: 13px;
        position: absolute;
        top: 11px;
    }
    .template-type-tag img {
        width: 60%;
    }
    .template-buttons {
        padding: 0 20px 15px;
    }
    .template-buttons button {
        border: 0 none;
        border-radius: 8px;
        color: #ffffff;
        font-size: 12px;
        padding: 10px 6px;
        width: 48%;
    }
    .diselect {
        background-color: #dc3434;
    }
    .view_demo {
        background-color: #414F58;
    }
    .white-box-full .slider
    {
        margin-top:100px;
    }
    .white-box-full .rcorners1
    {
        border-radius: 23px;
        background: #ffffff;
        border: 6px solid #DFDFDF;
        padding: 7px 10px 15px 10px;
        width: 210px;
        height: 50px;
        margin: -25px auto;
        font-weight: bold;
    }
    #content-wrapper
    {
        margin-bottom: 0 !important;
    }
    .flex-direction-nav a
    {
        overflow: visible !important;
    }

    .footer
    {
        border-top: none !important;
    }
    .foter_background
    {
        float: left;
        clear: both;
        width: 100%;
        height: 593px;
        
        
        background-attachment: scroll;
        background-color: rgba(0, 0, 0, 0);
        background-image: url("scripts/flexslider/images/bg-bottom.png");
        background-position: 0 top;
        background-repeat: no-repeat;
        background-size: 100%;
    }

    .container .create_school_banner
    {
        background-attachment: scroll;
        background-color: rgba(0, 0, 0, 0);
        background-image: url("images/school/create_school_banner.png");
        background-position: 0 top;
        background-repeat: no-repeat;
        background-size: 100%;
        height: 420px;
        margin-top: 19px;
        width: 100%;
    }
    .title_create_school
    {
        float: left;
        margin-top: 28%;
        margin-left: 6%;

    }
    .title_create_school p 
    {
        font-size: 46px;
        color: white;
        line-height: 48px;
    }
    .create_website_text
    {
        float:left;
        width:100%;
        margin-top: 45px;
        margin-bottom: 45px;
    }
    .create_website_text div
    {
        width: 50%;
        margin: 0 auto;
    }
    .create_website_text div p
    {

        font-size: 46px;
        color: black;
        line-height: 48px;
        font-weight: bold;
        text-align: center;
    }
    .white-box-full
    {
        float: left;
        clear: both;
        width: 100%;
        min-height: 900px;
        background-color: #ffffff;
    }


    /*ribbin css*/
    .submenu {
        width:300px;
        margin:-53px auto 0px auto;
    }

    .ribbon:after, .ribbon:before {
        margin-top:0.5em;
        content: "";
        float:left;
        border:1.5em solid #56B0B0;
    }

    .ribbon:after {
        border-right-color:transparent;
    }

    .ribbon:before {
        border-left-color:transparent;
    }

    /*Links*/
    .ribbon a:link { 
        color:#ffffff;
        text-decoration:none;
        float:left;
        height:3.5em;
        overflow:hidden;
    }
    /*Animated Folds*/
    .ribbon span {
        background:#56B0B0;
        display:inline-block;
        line-height:3em;
        padding:0 2em;
        margin-top:0.5em;
        position:relative;

        -webkit-transition: background-color 0.2s, margin-top 0.2s;  /* Saf3.2+, Chrome */
        -moz-transition: background-color 0.2s, margin-top 0.2s;  /* FF4+ */
        -ms-transition: background-color 0.2s, margin-top 0.2s;  /* IE10 */
        -o-transition: background-color 0.2s, margin-top 0.2s;  /* Opera 10.5+ */
        transition: background-color 0.2s, margin-top 0.2s;
    }

    .ribbon a span,.ribbon a span {
        background:#56B0B0;
        margin-top:0;
    }

    .ribbon span:before {
        content: "";
        position:absolute;
        top:3em;
        left:0;
        border-right:0.5em solid #9B8651;
        border-bottom:0.5em solid #56B0B0;
    }

    .ribbon span:after {
        content: "";
        position:absolute;
        top:3em;
        right:0;
        border-left:0.5em solid #9B8651;
        border-bottom:0.5em solid #56B0B0;
    }


</style>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="js/libs/jquery-1.7.min.js">\x3C/script>')</script>

<!-- FlexSlider -->
<script defer src="scripts/flexslider/jquery.flexslider.js"></script>

<script type="text/javascript">
                                            $(function () {
                                                SyntaxHighlighter.all();
                                            });
                                            $(window).load(function () {
                                                $('.flexslider').flexslider({
                                                    slideshow: false,
                                                    animation: "slide",
                                                    animationLoop: false,
                                                    itemWidth: 210,
                                                    itemMargin: 0,
                                                    minItems: 2,
                                                    maxItems: 4,
                                                    prevText: "", //String: Set the text for the "previous" directionNav item
                                                    nextText: "",
                                                    controlNav: false,
                                                    start: function (slider) {
                                                        $('body').removeClass('loading');
                                                    }
                                                });
                                            });
</script>


<!-- Syntax Highlighter -->
<!--  <script type="text/javascript" src="scripts/flexslider/js/shCore.js"></script>
<script type="text/javascript" src="scripts/flexslider/js/shBrushXml.js"></script>
<script type="text/javascript" src="scripts/flexslider/js/shBrushJScript.js"></script>-->

<!-- Optional FlexSlider Additions -->
<script src="scripts/flexslider/js/jquery.easing.js"></script>
<script src="scripts/flexslider/js/jquery.mousewheel.js"></script>
<script defer src="scripts/flexslider/js/demo.js"></script>