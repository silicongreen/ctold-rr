<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" href="<?php echo base_url('styles/layouts/tdsfront/images/favicon.ico');?>" type="image/x-icon">

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Champs21 School Template</title>
    <link rel="stylesheet" href="scripts/flexslider/flexslider.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="merapi/style/bootstrap.css" type="text/css" media="screen" />
    <script src="scripts/flexslider/js/modernizr.js"></script>
    
</head>

<body>   
 <?php $s_ci_key = (isset($ci_key)) ? $ci_key : NULL; ?>
    <input type="hidden" id="base_url" value="<?php echo base_url();?>">
    <div id="page" class="page">
        <!-- Header -->
        <div class="wrapper-bg">
            <header id="top" class="header">                
                    <div class="container" style="width: 75%;">
                        <div class="row">
                            <div class="col-md-3">                        
                                <a href="<?php echo base_url();?>"><img src="<?php echo base_url('styles/layouts/tdsfront/images/logo-new.png');?>" style="width:80%;" alt="logo"></a>                        
                            </div>
                            <div class="col-md-6">
                                <div class="template-buttons">
                                <button type="button" class="diselect f2" onclick="window.location.href = '<?php echo base_url('submit-new-school?id=' . $_GET['id']); ?>'">Select</button>
                                <button type="button" class="view_demo f2" onclick="window.history.back();">Back</button>
                                </div>
                            </div>
                            <div class="col-md-3 col-md-offset-2">
                                <select class="form-control" onchange="showHideOther(this.value);">
                                    <?php
                                    $has_premium = false;
                                    $i = 1;
                                    foreach ($all_ar_templates as $template) {

                                        if ($template['name'] != $ar_templates['name']) {
                                            if ($template['name'] == $_GET['id']){
                                                $tmplate_path = $template['demo_url'];
                                            }
                                            ?>
                                    <option value="<?php echo $template['name'];?>" <?php if($template['name'] == $_GET['id']){ echo "selected";}?>>Template <?php echo $i;?>( <?php echo $template['name'];?> )</option>
                                    <?php
                                        }
                                        $i++;
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                    </div>
            </header>
        </div>
        <!-- About -->
        <div class="container">            
            
            <iframe id='iframe2' src="<?php echo $tmplate_path;?>" frameborder="0" style="overflow: hidden; height: 100%; width: 100%; position: absolute;" height="100%" width="100%"></iframe>
        </div>
</div>
   




<style type="text/css" media="all">
    .wrapper-bg
    {
        background-color: #ccc;
        padding:20px;
    }
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
        background: url("scripts/flexslider/images/bg-bottom.png");
        background-size: contain;
    }

    .container .create_school_banner
    {
        margin-top:19px;
        background: url("images/school/create_school_banner.png");
        height: 70%;
        background-size: contain;
        background-repeat: no-repeat;
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
function showHideOther(value){    
    var base_url = $("#base_url").val();
    window.open(base_url+ "demo-school-template?id=" + value ,'_self');
}
                                            
</script>


<!-- Syntax Highlighter -->
<!--  <script type="text/javascript" src="scripts/flexslider/js/shCore.js"></script>
<script type="text/javascript" src="scripts/flexslider/js/shBrushXml.js"></script>
<script type="text/javascript" src="scripts/flexslider/js/shBrushJScript.js"></script>-->

<!-- Optional FlexSlider Additions -->
<script src="scripts/flexslider/js/jquery.easing.js"></script>
<script src="scripts/flexslider/js/jquery.mousewheel.js"></script>
<script defer src="scripts/flexslider/js/demo.js"></script>
<script defer src="Profiler/bootstrap.js"></script>

    
</body>

</html>
