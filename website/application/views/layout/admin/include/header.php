<!DOCTYPE html>
<!--[if lt IE 7]> <html lang="en-us" class="no-js ie6"> <![endif]-->
<!--[if IE 7]>    <html lang="en-us" class="no-js ie7"> <![endif]-->
<!--[if IE 8]>    <html lang="en-us" class="no-js ie8"> <![endif]-->
<!--[if IE 9]>    <html lang="en-us" class="no-js ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en-us" class="no-js"> <!--<![endif]-->
	<head>
		<meta charset="utf-8">

		<title>Champs21.com Admin</title>

  		<meta name="description" content="Champs21.com Admin">
  		<meta name="author" content="Champs21.com">



		<meta name="viewport" content="width=device-width; initial-scale=1; maximum-scale=1;">
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />

		<link href="<?php echo base_url()?>images/interface/iOS_icon.png" rel="apple-touch-icon">

                <?php if ( isset($has_daterange_stat) && $has_daterange_stat == true) : ?>
                    <link href="<?php echo  base_url() ?>scripts/timepicker/bootstrap.min.css" rel="stylesheet" type="text/css" >
                <?php endif; ?>

		<link rel="stylesheet" href="<?php echo base_url()?>styles/adminica/reset.css">
		


		<!-- NOTE: The following css files have been combined and minified into plugins.css

		<link rel="stylesheet" href="styles/plugins/colorpicker/colorpicker.css">
		<link rel="stylesheet" href="styles/plugins/datatables/datatables.css">
		<link rel="stylesheet" href="styles/plugins/elfinder/elfinder.css">
		<link rel="stylesheet" href="styles/plugins/fancybox/fancybox.css">
		<link rel="stylesheet" href="styles/plugins/fullcalendar/fullcalendar.css">
		<link rel="stylesheet" href="styles/plugins/isotope/isotope.css">
		<link rel="stylesheet" href="styles/plugins/multiselect/multiselect.css">
		<link rel="stylesheet" href="styles/plugins/select2/select2.css">
		<link rel="stylesheet" href="styles/plugins/selectbox/selectbox.css">
		<link rel="stylesheet" href="styles/plugins/slidernav/slidernav.css">
		<link rel="stylesheet" href="styles/plugins/slidernav/smallipop.css">
		<link rel="stylesheet" href="styles/plugins/syntaxhighlighter/syntaxhighlighter.css">
		<link rel="stylesheet" href="styles/plugins/syntaxhighlighter/shThemeDefault.css">
		<link rel="stylesheet" href="styles/plugins/tagit/tagit.css">
		<link rel="stylesheet" href="styles/plugins/themeroller/themeroller.css">
		<link rel="stylesheet" href="styles/plugins/tinyeditor/tinyeditor.css">
		<link rel="stylesheet" href="styles/plugins/tiptip/tiptip.css">
		<link rel="stylesheet" href="styles/plugins/uistars/uistars.css">
		<link rel="stylesheet" href="styles/plugins/uitotop/uitotop.css">
		<link rel="stylesheet" href="styles/plugins/uniform/uniform.css"> -->
		<link rel="stylesheet" href="<?php echo base_url()?>styles/plugins/all/plugins.css">


		<!-- NOTE: The following css files have been combined and minified into all.css

		<link rel="stylesheet" href="styles/adminica/text.css">
		<link rel="stylesheet" href="styles/adminica/grid.css">
		<link rel="stylesheet" href="styles/adminica/main.css">
		<link rel="stylesheet" href="styles/adminica/mobile.css">
		<link rel="stylesheet" href="styles/adminica/base.css">
		<link rel="stylesheet" href="styles/adminica/ie.css">
		<link rel="stylesheet" href="styles/themes/switcher.css"> -->
		<link rel="stylesheet" href="<?php echo base_url()?>styles/adminica/all.css">


		<!-- Style Switcher

		The following stylesheet links are used by the styleswitcher to allow for dynamically changing the Adminica layout, nav, skin, theme and background.
		Styleswitcher documentation: http://style-switcher.webfactoryltd.com/documentation/

		layout_switcher.php	: layout - fluid by default.								(eg. styles/themes/layout_switcher.php?default=layout_fixed.css)
		nav_switcher.php	: header and sidebar nav  positioning - sidebar by default.	(eg. styles/themes/nav_switcher.php?default=header_top.css)
		skin_switcher.php 	: Adminica skin - dark by default.							(eg. styles/themes/skin_switcher.php?default=theme_light.css)
		theme_switcher.php 	: colour theme - black/grey by default.						(eg. styles/themes/theme_switcher.php?default=theme_red.css)
		bg_switcher.php 	: background image - dark boxes by default.					(eg. styles/themes/bg_switcher.php?default=bg_honeycomb.css)	-->

		
		<link rel="stylesheet" href="<?php echo base_url()?>styles/themes/switcher.css" >
		<link rel="stylesheet" href="<?php echo base_url()?>styles/themes/theme_navy.css" >
		<link rel="stylesheet" href="<?php echo base_url()?>styles/themes/bg_honeycomb.css" >

		<link rel="stylesheet" href="<?php echo base_url()?>styles/adminica/colours.css"> 
                <?php if ( isset($page) && $page == "Gallery") : ?>
                    <script src="<?php echo base_url()?>scripts/jquery/jquery.js"></script>
                    <script src="<?php echo base_url()?>scripts/jquery/jqueryui.js"></script>
                    <script src="<?php echo base_url()?>scripts/validation/validation.js"></script>
                    <script src="<?php echo base_url()?>scripts/uniform/uniform.js"></script>
                    <script src="<?php echo base_url()?>scripts/fancybox/fancybox.js"></script>
                <?php endif;?>
<!--
		<script src="<?php echo base_url()?>scripts/jquery/jquery.js"></script>
		<script src="<?php echo base_url()?>scripts/jquery/jqueryui.js"></script>
		<script src="<?php echo base_url()?>scripts/modernizr/modernizr.js"></script>
		<script src="<?php echo base_url()?>scripts/prefixfree/prefixfree.js"></script>
		<script src="<?php echo base_url()?>scripts/pjax/pjax.js"></script>
		<script src="<?php echo base_url()?>scripts/isotope/isotope.js"></script>
		<script src="<?php echo base_url()?>scripts/autogrow/autogrow.js"></script>
		<script src="<?php echo base_url()?>scripts/colorpicker/colorpicker.js"></script>
		<script src="<?php echo base_url()?>scripts/cookie/cookie.js"></script>
		
		<script src="<?php echo base_url()?>scripts/elfinder/elfinder.js"></script>
		<script src="<?php echo base_url()?>scripts/dragscroll/dragScroll.js"></script>
		<script src="<?php echo base_url()?>scripts/tinyeditor/tinyeditor.js"></script>
		<script src="<?php echo base_url()?>scripts/fancybox/fancybox.js"></script>
		<script src="<?php echo base_url()?>scripts/flot/flot_excanvas.js"></script>
		<script src="<?php echo base_url()?>scripts/flot/flot.js"></script>
		<script src="<?php echo base_url()?>scripts/flot/flot_resize.js"></script>
		<script src="<?php echo base_url()?>scripts/flot/flot_pie.js"></script>
		<script src="<?php echo base_url()?>scripts/flot/flot_pie_update.js"></script>
		<script src="<?php echo base_url()?>scripts/fullcalendar/fullcalendar.js"></script>
		<script src="<?php echo base_url()?>scripts/fullcalendar/fullcalendar_gcal.js"></script>
		<script src="<?php echo base_url()?>scripts/hoverintent/hoverIntent.js"></script>
		<script src="<?php echo base_url()?>scripts/iscroll/iscroll.js"></script>
		<script src="<?php echo base_url()?>scripts/knob/knob.js"></script>
		<script src="<?php echo base_url()?>scripts/multiselect/multiselect.js"></script>
		<script src="<?php echo base_url()?>scripts/select2/select2.js"></script>
		<script src="<?php echo base_url()?>scripts/selectbox/selectbox.js"></script>
		<script src="<?php echo base_url()?>scripts/slidernav/slidernav.js"></script>
		<script src="<?php echo base_url()?>scripts/smallipop/smallipop.js"></script>
		<script src="<?php echo base_url()?>scripts/sparkline/sparkline.js"></script>
		<script src="<?php echo base_url()?>scripts/syntaxhighlighter/shCore.js"></script>
		<script src="<?php echo base_url()?>scripts/syntaxhighlighter/shBrushJScript.js"></script>
		<script src="<?php echo base_url()?>scripts/syntaxhighlighter/shBrushXml.js"></script>
		<script src="<?php echo base_url()?>scripts/tagit/tagit.js"></script>
		<script src="<?php echo base_url()?>scripts/timepicker/timepicker.js"></script>
		<script src="<?php echo base_url()?>scripts/tinyeditor/tinyeditor.js"></script>
		<script src="<?php echo base_url()?>scripts/tiptip/tiptip.js"></script>
		<script src="<?php echo base_url()?>scripts/touchpunch/touchpunch.js"></script>
		<script src="<?php echo base_url()?>scripts/uistars/uistars.js"></script>
		<script src="<?php echo base_url()?>scripts/uitotop/uitotop.js"></script>
		<script src="<?php echo base_url()?>scripts/uniform/uniform.js"></script>
		<script src="<?php echo base_url()?>scripts/validation/validation.js"></script>
               -->
                <?php if ( isset($page) && $page == "Gallery") : ?>
               
                <?php else : ?>
		<script src="<?php echo base_url()?>scripts/plugins-min.js"></script>
                <?php endif; ?>
                <script src="<?php echo base_url()?>scripts/timepicker/datetimepicker.js"></script>

                <script src="<?php echo base_url()?>scripts/datatables/datatables.js"></script>
		

		<script src="<?php echo base_url()?>scripts/adminica/adminica_ui.js"></script>
		<script src="<?php echo base_url()?>scripts/adminica/adminica_mobile.js"></script>
		<script src="<?php echo base_url()?>scripts/adminica/adminica_datatables.js"></script>
		<script src="<?php echo base_url()?>scripts/adminica/adminica_calendar.js"></script>
		<script src="<?php echo base_url()?>scripts/adminica/adminica_charts.js"></script>
		<script src="<?php echo base_url()?>scripts/adminica/adminica_gallery.js"></script>
		<script src="<?php echo base_url()?>scripts/adminica/adminica_various.js"></script>
		<script src="<?php echo base_url()?>scripts/adminica/adminica_wizard.js"></script>
		<script src="<?php echo base_url()?>scripts/adminica/adminica_forms.js"></script>
		<script src="<?php echo base_url()?>scripts/adminica/adminica_load.js"></script>
                
                <?php if ( isset($has_daterange_stat) && $has_daterange_stat == true) : ?>
                    <script src="<?php echo  base_url() ?>scripts/timepicker/moment.js" type="text/javascript"></script>  
                    <link href="<?php echo  base_url() ?>scripts/timepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" >
                    <script src="<?php echo  base_url() ?>scripts/timepicker/daterangepicker.js" type="text/javascript"></script> 
                <?php endif; ?>
		
                <?php if ( isset($has_daterange) && $has_daterange == true) : ?>
                    <script src="<?php echo  base_url() ?>scripts/timepicker/moment.js" type="text/javascript"></script>  
                    <link href="<?php echo  base_url() ?>scripts/timepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" >
                    <link href="<?php echo  base_url() ?>scripts/timepicker/bootstrap.min.css" rel="stylesheet" type="text/css" >
                    <script src="<?php echo  base_url() ?>scripts/timepicker/daterangepicker.js" type="text/javascript"></script>  
                <?php endif; ?>
                
                <?php if ( isset($page) && $page == "Gallery") : ?>
                <?php else : ?>
                    
                    <script src="<?php echo base_url()?>scripts/custom/customValidation.js"></script>
                    <script src="<?php echo base_url()?>scripts/custom/customDatatables.js?v=2"></script>
                <?php endif; ?>
		</head>
	<body>
            <input type="hidden" id="base_url" value="<?php echo base_url()?>" />
            <input type="hidden" id="base_url" value="<?php echo base_url()?>" />
            <input type="hidden" id="controllername" value="<?php echo $this->router->fetch_class();?>" >
            <div id="main_shadow" style="height: 100%; width: 100%;"></div>