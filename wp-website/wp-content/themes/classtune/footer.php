<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */
?>

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
                    <img src="<?php bloginfo('template_url'); ?>/images/logo/happy-face.png" alt="" title="" width="25%" style="position:absolute;left:0px;z-index:0;margin-top:-25px;" />
                    <div style=" position: relative;top: -15px;z-index: 1;">
                        <!--a href="" style="background-color: #fff;color: #64B846;font-size: 20px;padding: 20px 40px;text-decoration: none;border-radius:5px;	-moz-border-radius:5px;	-webkit-border-radius:5px;border:1px solid #fff;box-shadow: 0 4px 2px -2px gray;">
                        Create Your FREE Account</a-->
                        <div class="postlist-tab" style=" background: none; position: relative;top: -60px;z-index: 1;">
                            <ul>
                                <li>
                                    <a href="javascript:void(0);" style="cursor: default;background: #E5ECF2 none repeat scroll 0 0;color:#999!important;">Registration Free! <i class="fa fa-long-arrow-right"></i></a>
                                    <a href="<?php echo get_site_url(); ?>/admin-user" >I'm School Admin</a>
                                    <a href="<?php echo get_site_url(); ?>/student-user" >I'm Student</a>
                                    <a href="<?php echo get_site_url(); ?>/guardian-user" >I'm Parent</a>
                                    <a href="<?php echo get_site_url(); ?>/teacher-user" >I'm Teacher</a>
                                </li>
                            </ul>
                        </div>
                        <?php get_template_part( 'contact' ); ?> <?php //$this->load->view("layout/contact"); ?>								
                    </div>

                    <div class="ym-wbox footer noPrint" style=" position: relative; bottom: 0px; display: block;">

                        <div class="poweredby f5">    
                            <p>&copy; Classtune 2015</p>
                        </div>
                        <div style="float: left;margin-left: 370px;margin-top: 0px;">
                            <a href="<?php echo get_site_url(); ?>" title="">
                                <img src="<?php bloginfo('template_url'); ?>/images/logo/classtune-footer-logo.png" alt="" title="" width="170" height="" />
                            </a>
                        </div>

                        <div class="footerlink f5">
                            <ul>									
                                <li><a href="<?php echo get_site_url(); ?>/terms" style="color:#999;">Terms</a></li>
                                <li style="color:#64B846;">|</li>
                                <li><a href="<?php echo get_site_url(); ?>/privacypolicy" style="color:#999;">Privacy Policy</a></li>
                                <li style="color:#64B846;">|</li>
                                <li><a href="<?php echo get_site_url(); ?>/copyright" style="color:#999;">Copyright</a></li>									
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
<?php echo $lang = reset(explode('_', get_locale()));?>
<div id="mainnav">
    <ul>
        <li class="before"><span>Home</span></li>
        <!--li class="start act"><span>About us</span></li-->
        <li class="cron"><span>Features</span>
            <ul>
                <li><a href="<?php echo get_site_url().'/'.$lang; ?>/admin-page<?php echo "-".$lang;?>" >School Admin</a></li>
                <li><a href="<?php echo get_site_url().'/'.$lang; ?>/student-page<?php echo "-".$lang;?>" >Student</a></li>
                <li><a href="<?php echo get_site_url().'/'.$lang; ?>/guardian-page<?php echo "-".$lang;?>" >Parent</a></li>
                <li><a href="<?php echo get_site_url().'/'.$lang; ?>/teacher-page<?php echo "-".$lang;?>" >Teacher</a></li>
            </ul>
        </li>
		<li><a href="<?php echo get_site_url().'/'.$lang; ?>/supports<?php echo "-".$lang;?>"><span>Support</span></a>
        <li class="images"><span>Contact</span></li>
        <?php get_template_part( 'login' ); ?> <?php //$this->load->view("layout/login"); ?>
		<?php pll_the_languages(array('dropdown'=>1));  ?>
    </ul>
    <div id="homelink">
        <a href="<?php echo get_site_url(); ?>" title="" >
            <img src="<?php bloginfo('template_url'); ?>/images/logo/classtune.png" alt="" title="" width="215" height="" />
        </a>
    </div>
</div>

<!--div id="preloader" style="width: 100%; height: 100%; position: fixed; top: 0px; left: 0px; background: transparent url(<?php bloginfo('template_url'); ?>/images/80percentwhite.png) repeat top left; z-index: 999999;">
    <img src="<?php //bloginfo('template_url'); ?>/images/preloader.gif" alt="" title="" />
    <div class="counter"><span id="count">0</span>&nbsp;/&nbsp;150</div>

    <div id="startinfotext"><div id="c735" class="csc-default"><div class="csc-text"><p class="bodytext"><b>Loading...</b></p></div></div></div>

</div-->
<!--div id="ribbon">
    <a style="display:block" target="_blank" href="http://www.bettshow.com/Exhibitor/ClassTune">
	<img src="<?php //bloginfo('template_url'); ?>/images/bett_ribbon.png" class="ribbon" />
	</a>
</div-->

</div>

		
	</div><!-- .site-inner -->
</div><!-- .site -->

<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery_timers.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery.stellar.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery.mousewheel.min.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/px.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/owl.carousel.min.js"></script>	
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/owl.custome.classtune.js"></script>	






</body>
</html>
