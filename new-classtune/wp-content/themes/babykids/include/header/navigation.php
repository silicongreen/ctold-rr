<!--start header-->
<div class="nicdark_bg_<?php echo $redux_demo['header_background']; ?> nicdark_section nicdark_shadow nicdark_radius_bottom fade-down">
    
    <!--start container-->
    <div class="nicdark_container nicdark_clearfix">

        <div class="grid grid_12 percentage">
                
            <div class="nicdark_space20"></div>

            <!--logo-->
            <div class="nicdark_logo nicdark_marginleft10">
                <a href="<?php echo home_url(); ?>"><img alt="" src="<?php echo esc_url( $redux_demo['logo']['url'] ); ?>"></a>                                   
            </div>
            <!--end logo-->

            <!--start btn left/right sidebar open-->
            <?php if ($redux_demo['header_right_sidebar'] == 1) { ?> <a class="nicdark_btn_icon nicdark_zoom nicdark_bg_<?php echo $redux_demo['header_background_btn_right_sidebar']; ?>_hover nicdark_right_sidebar_btn_open nicdark_marginright10 nicdark_bg_<?php echo $redux_demo['header_background_btn_right_sidebar']; ?> extrasmall nicdark_radius white right"><i class="<?php echo $redux_demo['header_icon_btn_right_sidebar']; ?>"></i></a> <?php } else {}; ?>
            <?php if ($redux_demo['header_left_sidebar'] == 1) { ?> <a class="nicdark_btn_icon nicdark_zoom nicdark_bg_<?php echo $redux_demo['header_background_btn_left_sidebar']; ?>_hover nicdark_left_sidebar_btn_open nicdark_marginright20 nicdark_bg_<?php echo $redux_demo['header_background_btn_left_sidebar']; ?> extrasmall nicdark_radius white right"><i class="<?php echo $redux_demo['header_icon_btn_left_sidebar']; ?>"></i></a> <?php } else {}; ?>
            <!--end btn left/right sidebar open-->

            <?php wp_nav_menu( array( 'theme_location' => 'main-menu' ) ); ?>    
        
            <div class="nicdark_space20"></div>

        </div>

    </div>
    <!--end container-->

</div>
<!--end header-->