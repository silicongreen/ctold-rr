<div id="imagesWrap">
    <div id="images">
        <div id="worldmap">
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
                        <p>&copy; Classtune 2015</p>
                    </div>
                    <div style="float: left;margin-left: 370px;margin-top: 0px;">
                        <a href="<?php echo base_url(); ?>" title="" target="_blank">
                            <img src="<?php echo base_url(); ?>images/logo/classtune-footer-logo.png" alt="" title="" width="200" height="" />
                        </a>
                    </div>

                    <div class="footerlink f5">
                        <ul>									
                            <li><a href="http://www.champs21.com/terms" style="color:#999;">Terms</a></li>
                            <li style="color:#64B846;">|</li>
                            <li><a href="http://www.champs21.com/privacy-policy" style="color:#999;">Privacy Policy</a></li>
                            <li style="color:#64B846;">|</li>
                            <li><a href="http://www.champs21.com/copyright" style="color:#999;">Copyright</a></li>									
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
        <li><span onclick="window.location = '<?php echo base_url(); ?>'">Home</span></li>
        <!--li><span onclick="window.location ='<?php echo base_url(); ?>?locale=about'">About us</span></li-->
        <li><span onclick="window.location = '<?php echo base_url(); ?>?locale=feature'">Features</span></li>
        <li><span onclick="window.location = '<?php echo base_url(); ?>?locale=contact'">Contact</span></li>
        <?php $this->load->view("layout/login"); ?>
    </ul>
    <div id="homelink">
        <a href="<?php echo base_url(); ?>" title="" >
            <img src="<?php echo base_url(); ?>images/logo/classtune.png" alt="" title="" width="200" height="" />
        </a>
    </div>
</div>

<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery_timers.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.stellar.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.mousewheel.min.js"></script>
<script src="<?php echo base_url(); ?>js/bootstrap.min.js"></script>




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