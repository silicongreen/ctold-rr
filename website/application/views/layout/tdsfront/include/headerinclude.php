<!--[if lt IE 7 ]><html class="ie ie6" lang="en-US"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en-US"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en-US"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html><!--<![endif]-->
<head prefix="og: http://ogp.me/ns#">	
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta name = "apple-mobile-web-app-capable" content = "yes" /> 
        <meta name="alexaVerifyID" content="rXOHeRArHGSEJfOH2THlJGs2o6g"/>
    <?php if ( isset($fb_contents) && !is_null($fb_contents) ) : ?>
            <?php 
                    foreach($fb_contents as $key => $value) : 
                        
                        if($key=="image")
                        {
                            if(strpos($value,"http://")===false)
                            {
                                if($value=="")
                                {
                                   $value = "styles/layouts/tdsfront/images/no_image/fb-new.jpg"; 
                                }    
                                $value = base_url().$value;
                                
                                
                            }
//                            list($width_main, $height_main, $type_main, $attr_main) = getimagesize($value);
//                            if(!isset($width_main))
//                            {
                                $value = str_replace("facebook/", "", $value);
                                
//                            }   
                            if(strpos($value,"bd.")!==false)
                            {
                                $value = str_replace("bd.", "www.", $value);
                            }
                        }
                
                    ?>
					
        <meta property="og:<?php echo $key; ?>" content="<?php echo str_replace('"', "", $value); ?>" />
        <meta name="twitter:<?php echo $key; ?>" content="<?php echo str_replace('"', "", $value); ?>" />
            <?php endforeach; ?>
			
        <meta name="twitter:card" content="summary" />
        <meta name="twitter:site" content="@champs21" />
        <?php endif; ?>
        <?php if(isset($fb_contents['url'])): ?>
<!--          <link rel="canonical" href="<?php //echo $fb_contents['url']; ?>">-->
        <?php else: ?>
<!--           <link rel="canonical" href="http://www.champs21.com/">-->
        <?php endif; ?>    
		
    <link rel="icon" href=<?php echo base_url("styles/layouts/tdsfront/images/favicon.ico"); ?> type="image/x-icon">
	
    <!--[if ie]><meta http-equiv='X-UA-Compatible' content="IE=edge,IE=9,IE=8,chrome=1" /><![endif]-->
    <title><?php echo $title; ?></title>
   
    <?php
        // Add any keywords
        echo ( isset($keywords) ) ? meta('keywords', $keywords) : '';

        // Add a discription
        if ( isset($description) && strlen(trim($description)) > 0 )
        {
            echo ( isset($description) ) ? meta('description', $description) : '';
        }
        else if ( isset($fb_contents) && !is_null($fb_contents) )
        {
            echo ( isset($fb_contents['description']) ) ? meta('description', $fb_contents['description']) : '';
            if ( isset($fb_contents['image']) && strlen($fb_contents['image']) > 0 )
            {
                echo '<link rel="image_src" href="' . $fb_contents['image']  . '" />';
            }
        }


        // Add a robots exclusion
        echo ( isset($no_robots) ) ? meta('robots', 'noindex,nofollow') : '';
    ?>
    
    
    
    <link rel="stylesheet" id="bootstrap-css" href="<?php echo base_url('css/champs21.css'); ?>" type="text/css" media="all" />
    <link rel="stylesheet" id="contact-form-7-css" href="<?php echo base_url('css/styles.css'); ?>" type="text/css" media="all" />
    <!--link rel="stylesheet" id="bootstrap-css" href="<?php //echo base_url('css1/all.css'); ?>" type="text/css" media="all" /-->
    <!--link rel="stylesheet" id="contact-form-7-css" href="<?php //echo base_url() ?>css/styles.css?v=1" type="text/css" media="all" /-->
<!--    <link rel="stylesheet" href="<?php //echo base_url('styles/plugins/fancybox/fancybox.css'); ?>" type="text/css" media="all" />-->
    
    <style>
        .fluid-width-video-wrapper{
            width:100%;
            position:relative;
            padding:0;
        }
        .fluid-width-video-wrapper iframe,.fluid-width-video-wrapper object,.fluid-width-video-wrapper embed {
            position:absolute;
            top:0;
            left:0;
            width:100%;
            height:100%;
        }
    </style>
    
    <script type="text/javascript"> var addthis_config = {"data_track_addressbar":false, "data_track_clickback" : false};</script>
    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-52bca22436b47685&async=1"></script> 
   <script type="text/javascript" src="<?php echo base_url('js/top-main.js'); ?>"></script> 

    
    <?php if ( isset($name) && file_exists(FCPATH . "styles\\layouts\\tdsfront\\css\\" . strtolower($name) . ".css") ) : ?>
    <?php $extra_css = base_url() . "styles/layouts/tdsfront/css/" . strtolower($name) . ".css"; ?>
    <link rel="stylesheet" id="jetpack-widgets-css" href="<?php echo $extra_css; ?>" type="text/css" media="all">
    <?php endif;  ?>

    <style type="text/css">img#wpstats{display:none}</style><style type="text/css">#header, .sm-clean ul{background:#222222}nav.menu .sm-clean a{color:#ffffff}.sm-clean a span.sub-arrow{border-top-color:#ffffff}.sm-clean ul a:hover{background:#fb3c2d}ul.sm ul li:first-child{border-color:#fb3c2d}.footer-social li a{color:#fb3c2d}.footer-social li a{border-color:#fb3c2d}.widget-title, #akmanda_author-2 h3{color:#000000}.widget-area a, table{color:#fb3c2d}.widget-title{background:#ffffff}.akmanda_newsletter_widget .widget-body{background:#2e3639}.intro-post .icon.post-type{background-color:#fb3c2d}.intro-post .icon.post-type{color:#ffffff}.post-title h2 a, .page-title h2 a{color:#000000}.post-title h2 a:hover, .page-title h2 a:hover{color:#fb3c2d}.more, input#submit, .input-group-btn:last-child>.btn, .wpcf7-submit{background:#fb3c2d}.more:hover, input#submit:hover, .input-group-btn:last-child>.btn:hover, .wpcf7-submit:hover{background-color:#000}.more{border-color:#000}.pagination .current, .pagination a:hover, .nav-next a, .nav-previous a{background:#fb3c2d}.pagination .current, .pagination a:hover, .nav-next a, .nav-previous a, .pagination a{border-color:#fb3c2d}.pagination a{color:#fb3c2d}.nav-next a:hover, .nav-previous a:hover{background-color:#000000}.nav-next a:hover, .nav-previous a:hover{border-color:#000000}.post-content p{color:#666}.bord{background-color:#e5e5e5}.post-content, .widget-title, .searchform input.field, #akmanda_author-2, #primary-sidebar ul, #primary-sidebar li, .comment-list, .comment-respond{border-bottom-color:#d1d1d2}#akmanda_author-2{border-color:#d1d1d2}.post-content{background:#ffffff}.su-spoiler-style-default > .su-spoiler-title{color:#ffffff}.su-spoiler-style-default > .su-spoiler-title{background-color:#fb3c2d}.su-tabs .su-tabs-nav span{color:#ffffff}.su-tabs .su-tabs-nav span:hover, .su-tabs-nav span.su-tabs-current{color:#222}.su-tabs-nav span.su-tabs-current{background:#ffffff}.su-tabs-nav span:hover{background:#ffffff}.su-tabs-nav, .su-tabs-nav span, .su-tabs-panes, .su-tabs-pane{background:#fb3c2d}.su-tabs-nav, .su-tabs-nav span, .su-tabs-panes, .su-tabs-pane{border-color:#fb3c2d}.quote-wrap blockquote p{color:#000000}.quote-wrap blockquote{background:#ffffff}.quote-wrap blockquote .icon{color:#fb3c2d}.quote-wrap blockquote{border-color:#fb3c2d}.quote-wrap cite{color:#000}#footer p{color:#ffffff}#footer{background:#222222}</style> <style type="text/css" title="dynamic-css" class="options-output">body{background-color:#e7e7e7;}body{font-weight:400;font-style:normal;color:#000;}h1,h2,h3,h4,h5,h6{font-weight:400;font-style:normal;color:#000;}nav.menu a{font-family:Bree Serif;font-weight:400;font-style:normal;color:#ffffff;}a{color:#fb3c2d;}a:hover{color:#f94a3e;}a:active{color:#f94a3e;}</style>
    <link rel="stylesheet" id="sidebar-css" href="<?php echo base_url('css/sidebar.css'); ?>" type="text/css" media="all" />
    <!--link rel="stylesheet" id="sidebar-css" href="<?= base_url() ?>css/sidebar.css?v=1" type="text/css" media="all"-->
   


<?php
        // Always add the main stylesheet
        //echo link_tag( array( 'href' => 'styles/layouts/tdsfront/css/style.css', 'media' => 'screen', 'rel' => 'stylesheet' ) ) . "\n";
        // Add any additional stylesheets
        if (isset($css)) {
            foreach ($css as $href => $media) {
                if ( $href != "0" )
                     echo link_tag(array('href' => $href . '?v=' . $js_version, 'media' => $media, 'rel' => 'stylesheet')) . "\n";
            }
        }
        ?>
        <style media="print">
            .noPrint{ display: none; }
            .yesPrint{ display: block !important; }
             body {
                    -webkit-font-smoothing: antialiased;
                    text-rendering: optimizeLegibility;
                }
        </style>
        <!--[if lte IE 7]>
        <link href="<?php echo base_url(); ?>styles/layouts/tdsfront/css/yaml/core/iehacks.css" rel="stylesheet" type="text/css" />
        <![endif]-->

        <!--[if lt IE 9]>
        <script src="<?php echo base_url(); ?>js/ie9.js"></script>
        <![endif]-->
        <?php if($ci_key == 'contact_us'): ?>
            <script src="https://maps.googleapis.com/maps/api/js"></script>
            <script>
                function initialize() {

                    var myLatlng = new google.maps.LatLng(23.791403,90.406353);
                    var mapOptions = {
                        zoom: 14,
                        center: myLatlng,
                        mapTypeId: google.maps.MapTypeId.ROADMAP
                    }

                    var mapCanvas = document.getElementById('map_canvas');
                    var map = new google.maps.Map(mapCanvas, mapOptions);

                    var marker = new google.maps.Marker({
                        position: myLatlng,
                        map: map,
                        title:"Champs21.com"
                    });

                    marker.setMap(map);
                }

                google.maps.event.addDomListener(window, 'load', initialize);
            </script>
        <?php endif; ?>
<!-- Facebook Conversion Code for Candle Page Views -->
<script>(function() {
        var _fbq = window._fbq || (window._fbq = []);
        if (!_fbq.loaded) {
          var fbds = document.createElement('script');
          fbds.async = true;
          fbds.src = '//connect.facebook.net/en_US/fbds.js';
          var s = document.getElementsByTagName('script')[0];
          s.parentNode.insertBefore(fbds, s);
          _fbq.loaded = true;
        }
      })();
      window._fbq = window._fbq || [];
      window._fbq.push(['track', '6021307459450', {'value':'0.00','currency':'USD'}]);
</script>
<noscript>
    <img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?ev=6021307459450&amp;cd[value]=0.00&amp;cd[currency]=USD&amp;noscript=1" />
</noscript>
</head>