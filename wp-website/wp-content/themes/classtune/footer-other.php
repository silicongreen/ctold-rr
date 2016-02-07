<div id="imagesWrap">
    <div id="images">
        <div id="worldmap">
            <h2 class="f2" style="left: 35%;position: absolute;text-align: center;top: 40px;">
                <i>Accessible from anywhere...</i>
            </h2>
            <img src="<?php bloginfo('template_url'); ?>/images/cover/web-device.png" alt="" title="" width="100%" />
        </div>

    </div>

    <div id="thanks_endWrap" class="slide" data-stellar-ratio="1" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0">
        <div id="thanks_end">					
            <div class="bottom">

                <div class="contact_form">
                    <img src="<?php bloginfo('template_url'); ?>/images/logo/happy-face.png" alt="" title="" width="25%" style="position:absolute;left:0px;z-index: 0;margin-top:-25px;" />
                    <div style=" position: relative;top: -15px;z-index: 1;">
                        <a href="" style="background-color: #fff;color: #64B846;font-size: 20px;padding: 20px 40px;text-decoration: none;border-radius:5px;	-moz-border-radius:5px;	-webkit-border-radius:5px;border:1px solid #fff;box-shadow: 0 4px 2px -2px gray;">
                            Create Your FREE Account</a>
                    </div>
                    <?php get_template_part( 'contact' ); ?> 									
                </div>

                <div class="ym-wbox footer noPrint" style=" position: relative; bottom: 0px; display: block;">

                    <div class="poweredby f5">    
                        <p>&copy; Classtune 2015</p>
                    </div>
                    <div style="float: left;margin-left: 370px;margin-top: 0px;">
                        <a href="<?php echo get_site_url(); ?>" title="" target="_blank">
                            <img src="<?php bloginfo('template_url'); ?>/images/logo/classtune-footer-logo.png" alt="" title="" width="170" height="" />
                        </a>
                    </div>

                    <div class="footerlink f5">
                        <ul>									
                            <li><a href="<?php echo get_site_url(); ?>/terms<?php echo "-".$lang;?>" style="color:#999;">Terms</a></li>
                            <li style="color:#64B846;">|</li>
                            <li><a href="<?php echo get_site_url(); ?>/privacypolicy<?php echo "-".$lang;?>" style="color:#999;">Privacy Policy</a></li>
                            <li style="color:#64B846;">|</li>
                            <li><a href="<?php echo get_site_url(); ?>/copyright<?php echo "-".$lang;?>" style="color:#999;">Copyright</a></li>									
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


</div>

<div id="mainnav">
    <ul>
        <li><span onclick="window.location = '<?php echo get_site_url(); ?>'">Home</span></li>
        <!--li><span onclick="window.location ='<?php echo get_site_url(); ?>?locale=about'">About us</span></li-->
        <li><span onclick="window.location = '<?php echo get_site_url(); ?>?locale=feature'">Features</span>
            <ul>
                <li><a href="<?php echo get_site_url().'/'.$lang; ?>/admin-user<?php echo "-".$lang;?>" >School Admin</a></li>
                <li><a href="<?php echo get_site_url().'/'.$lang; ?>/student-user<?php echo "-".$lang;?>" >Student</a></li>
                <li><a href="<?php echo get_site_url().'/'.$lang; ?>/guardian-user<?php echo "-".$lang;?>" >Parent</a></li>
                <li><a href="<?php echo get_site_url().'/'.$lang; ?>/teacher-user<?php echo "-".$lang;?>" >Teacher</a></li>
            </ul>
        </li>
		<li><a href="<?php echo get_site_url().'/'.$lang; ?>/supports<?php echo "-".$lang;?>"><span>Support</span></a>
        <li><span onclick="window.location = '<?php echo get_site_url().'/'.$lang; ?>?locale=contact'">Contact</span></li>
        <?php get_template_part( 'login' ); ?>
		<?php pll_the_languages(array('dropdown'=>1));?>
    </ul>
    <div id="homelink">
        <a href="<?php echo get_site_url(); ?>" title="" >
            <img src="<?php bloginfo('template_url'); ?>/images/logo/classtune.png" alt="" title="" width="200" height="" />
        </a>
    </div>
</div>

<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery_timers.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery.stellar.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery.mousewheel.min.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/bootstrap.min.js"></script>

</body>
</html>
<style>
    #imagesWrap
    {
        position: relative;
        top:5px;
        left:0px;

    }
</style>    