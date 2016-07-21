<div id="imagesWrap">
    <div id="images">
        <div id="worldmap">
            <h2 class="f2" style="left: 35%;position: absolute;text-align: center;top: 40px;">
                <i>Accessible from anywhere...</i>
            </h2>
            <img src="<?php echo base_url(); ?>images/cover/web-device.png" alt="" title="" width="100%" />
        </div>

    </div>

    <div id="thanks_endWrap" class="slide" data-stellar-ratio="1" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0">
        <div id="thanks_end">					
            <div class="bottom">

                <div class="contact_form">
                    <img src="<?php echo base_url('images/logo/happy-face.png');?>" alt="" title="" width="25%" style="position:absolute;left:0px;z-index: 0;margin-top:-25px;" />
                    <div style=" position: relative;top: -15px;z-index: 1;">
                        <a href="" style="background-color: #fff;color: #64B846;font-size: 20px;padding: 20px 40px;text-decoration: none;border-radius:5px;	-moz-border-radius:5px;	-webkit-border-radius:5px;border:1px solid #fff;box-shadow: 0 4px 2px -2px gray;">
                            Create Your FREE Account</a>
                    </div>
                    <?php $this->load->view("layout/contact"); ?>								
                </div>

                <div class="ym-wbox footer noPrint" style=" position: relative; bottom: 0px; display: block;">

                    <div class="poweredby f5">    
                        <p>&copy; ClassTune <?php echo date("Y");?><span style="color:#64B846;"> | </span>(+880)-1740212121 <span style="color:#64B846;"> | </span> <a href="mailto:info@classtune.com" style="color:#999;">Email : info@classtune.com</a> </p>
                    </div>
                    <div class="footer_logo">
                        <a href="<?php echo base_url(); ?>" title="" target="_blank">
                            <img src="<?php echo base_url(); ?>images/logo/classtune-footer-logo.png" alt="" title="" width="170" height="" />
                        </a>
                    </div>

                    <div class="footerlink f5">
                        <ul>									
                            <!--li><a href="mailto:info@classtune.com" style="color:#999;">Email : info@classtune.com</a></li>
							<li style="color:#64B846;">|</li-->
							<li><a href="<?php echo base_url(); ?>landing/terms" style="color:#999;">Terms</a></li>
                            <li style="color:#64B846;">|</li>
                            <li><a href="<?php echo base_url(); ?>landing/privacypolicy" style="color:#999;">Privacy Policy</a></li>
                            <li style="color:#64B846;">|</li>
                            <li><a href="<?php echo base_url(); ?>landing/copyright" style="color:#999;">Copyright</a></li>									
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


</div>
<div class="arrow_box" style="border-radius: 5px;
position: fixed;
right: 11px;
top: 300px;
width: 128px;z-index:2;">
 <a href="#thanks_endWrap"><img src="<?php echo base_url(); ?>images/sticky.png" style="width:120px;" /></a>
</div>
<div id="mainnav">
    <ul>
        <li><span onclick="window.location = '<?php echo base_url(); ?>'">Home</span></li>
        <!--li><span onclick="window.location ='<?php echo base_url(); ?>?locale=about'">About us</span></li-->
        <li><span onclick="window.location = '<?php echo base_url(); ?>?#cronWrap'">Features</span>
            <ul>
                <li><a href="<?php echo base_url(); ?>signup/admin" >School Admin</a></li>
                <li><a href="<?php echo base_url(); ?>signup/student" >Student</a></li>
                <li><a href="<?php echo base_url(); ?>signup/guardian" >Parent</a></li>
                <li><a href="<?php echo base_url(); ?>signup/teacher" >Teacher</a></li>
            </ul>
        </li>
        <li><span onclick="window.location = '<?php echo base_url(); ?>?locale=contact'">Contact</span></li>
        <?php $this->load->view("layout/login"); ?>
    </ul>
    <div id="homelink">
        <a href="<?php echo base_url(); ?>" title="" >
            <img src="<?php echo base_url(); ?>images/logo/classtune.png" alt="" title="" width="200" height="" />
        </a>
    </div>
        <!--div id="ribbon">
		<a style="display:block" target="_blank" href="http://www.bettshow.com/Exhibitor/ClassTune">
		<img src="<?php echo base_url(); ?>images/bett_ribbon.png" class="ribbon" />
		</a>
	</div-->
</div>

<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery_timers.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.stellar.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.mousewheel.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/bootstrap.min.js"></script>


<script>
 (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
 (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
 m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
 })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

 ga('create', 'UA-72261383-1', 'auto');
 ga('send', 'pageview');

</script>
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