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

<!--<button type="button" class="btn btn-popup btn-info btn-lg" data-toggle="modal" data-target="#feedbackModal"><h1>Student</h1><p>View Demo</p></button>-->
  <div class="modal fade hidden-xs" id="feedbackModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Feedback</h4>
        </div>
        <div class="modal-body">
            <h3>Please Give Us a Feedback</h3>
            <?php echo do_shortcode( "[ninja_form id=7]" ); ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>
    
<div class="modal fade visible-xs-block" id="loginModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Login</h4>
        </div>
        <div class="modal-body">
            <input type="text" title="Please Fill Out This Field" placeholder="Username*">
            <input type="text" title="Please Fill Out This Field" placeholder="Password*">
            <a class="row col-sm-12 forgot-password" href="#">Forgot Password?</a>
            <button class="btn btn-lg btn-success btn-proceed">Proceed</button>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>
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
						


