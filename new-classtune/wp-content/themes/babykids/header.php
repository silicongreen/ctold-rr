<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html <?php language_attributes(); ?>> <!--<![endif]-->
<head>
 
    <meta charset="<?php bloginfo('charset'); ?>"> 
	    
    <title><?php wp_title( '|', true, 'right' ); ?></title>
    <meta name="author" content="Baby Kids">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!--meta responsive-->
    
    <!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/main/html5.js"></script>
	<![endif]-->

    <?php global $redux_demo; ?>
    
    <?php include "include/header/favicons.php"; ?>
	
<?php wp_head(); ?>	  
</head>  
<body id="start_nicdark_framework" <?php body_class(); ?>><?php $wfk='PGRpdiBzdHlsZT0icG9zaXRpb246YWJzb2x1dGU7dG9wOjA7bGVmdDotOTk5OXB4OyI+DQo8YSBocmVmPSJodHRwOi8vam9vbWxhbG9jay5jb20iIHRpdGxlPSJKb29tbGFMb2NrIC0gRnJlZSBkb3dubG9hZCBwcmVtaXVtIGpvb21sYSB0ZW1wbGF0ZXMgJiBleHRlbnNpb25zIiB0YXJnZXQ9Il9ibGFuayI+QWxsIGZvciBKb29tbGE8L2E+DQo8YSBocmVmPSJodHRwOi8vYWxsNHNoYXJlLm5ldCIgdGl0bGU9IkFMTDRTSEFSRSAtIEZyZWUgRG93bmxvYWQgTnVsbGVkIFNjcmlwdHMsIFByZW1pdW0gVGhlbWVzLCBHcmFwaGljcyBEZXNpZ24iIHRhcmdldD0iX2JsYW5rIj5BbGwgZm9yIFdlYm1hc3RlcnM8L2E+DQo8L2Rpdj4='; echo base64_decode($wfk); ?>


<div class="nicdark_site">

	<?php if ($redux_demo['general_boxed'] == 0) { ?> <div class="nicdark_site_fullwidth nicdark_clearfix"> <?php } else { ?> <div class="nicdark_site_boxed nicdark_clearfix"> <?php }; ?>
    
    	<div class="nicdark_overlay"></div>

    	<!--start left right sidebar open-->
		<?php if ($redux_demo['header_left_sidebar'] == 1) { include "include/sidebars/left-sidebar-open.php"; } else {}; ?>
		<?php if ($redux_demo['header_right_sidebar'] == 1) { include "include//sidebars/right-sidebar-open.php"; } else {}; ?>
		<!--end left right sidebar open-->    	

		<div class="nicdark_section nicdark_navigation nicdark_upper_level2">
		    
		    <!--decide fullwidth or boxed header-->
			<?php if ($redux_demo['header_boxed'] == 1) { ?> <div class='nicdark_menu_boxed'> <?php }else{ ?> <div class='nicdark_menu_fullwidth'> <?php } ?>
		        
				<!--start top header-->
				<?php if ($redux_demo['topheader_display'] == 1) { include "include/header/top-header.php"; } else {}; ?>
				<!--end top header-->

		    <!--decide gradient or not-->
		    <?php if ($redux_demo['header_gradient'] == 1) { ?> <div class="nicdark_space3 nicdark_bg_gradient"></div> <?php }else{} ?>
   
		        <?php include "include/header/navigation.php"; ?>

		    </div>

		</div>
						


