<div id="imagesWrap">    

    <div id="thanks_endWrap" class="slide" style="height:115px;">
        <div id="thanks_end" style="height:115px;">					
            <div class="bottom" style="height:115px;">
                
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

<div id="mainnav">
    <ul>
        <li><span onclick="window.location = '<?php echo get_site_url(); ?>'">Home</span></li>
        <!--li><span onclick="window.location ='<?php echo get_site_url(); ?>?locale=about'">About us</span></li-->
        <li><span onclick="window.location = '<?php echo get_site_url(); ?>?locale=feature'">Features</span>
            <ul>
                <li><a href="<?php echo get_site_url(); ?>/admin-user" >School Admin</a></li>
                <li><a href="<?php echo get_site_url(); ?>/student-user" >Student</a></li>
                <li><a href="<?php echo get_site_url(); ?>/guardian-user" >Parent</a></li>
                <li><a href="<?php echo get_site_url(); ?>/teacher-user" >Teacher</a></li>
            </ul>
        </li>
        <li><span onclick="window.location = '<?php echo get_site_url(); ?>?locale=contact'">Contact</span></li>
         <?php get_template_part( 'login' ); ?>
    </ul>
    <div id="homelink">
        <a href="<?php echo get_site_url(); ?>/" title="" >
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