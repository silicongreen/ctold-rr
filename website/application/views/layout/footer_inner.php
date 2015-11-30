<div id="imagesWrap" style="top:1300px;">
    <div id="images">
        <div id="worldmap">
            <img src="<?php echo base_url(); ?>images/test/CLASSTUNE-JOIN-TODAY.png" alt="" title="" width="100%" />
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
                    <div style="height:550px;padding-top:30px;">
                        <h2 class="f2" style="margin-bottom:30px;"><i>Write to us...</i></h2>
                        <form>
                            <div class="row" style="width:500px;margin:0px auto;">
                                <div style="float:left;width:47%;">
                                    <input type="text" name="name" placeholder="Name">
                                </div>
                                <div style="float:right;width:47%;">
                                    <input type="text" name="name" placeholder="Email">
                                </div>
                            </div>
                            <div class="row" style="width:500px;margin:0px auto;">
                                <input type="text" name="name" placeholder="Subject">
                            </div>
                            <div class="row" style="width:500px;margin:0px auto;">
                                <textarea row="5" name="massage"></textarea>
                            </div>
                            <div class="row" style="width:500px;margin:0px auto;">
                                <div style="float:left;width:30%;">
                                    <input class="button_c" id="submit" name="submit" type="submit" value="Submit">
                                </div>
                                <div style="float:left;width:30%;">
                                    <button type="submit" class="button_c" id="submit">Reset</button>
                                </div>
                            </div>
                        </form>

                    </div>								
                </div>
                <!--img src="<?php echo base_url(); ?>images/test/CLASSTUNE-FOOTER.png" alt="" title="" width="100%" /-->
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
        <li ><a style="color:#000;text-decoration:none;" href="<?php base_url() ?>/login"><span>Login</span></a></li>
    </ul>
    <div id="homelink">
        <a href="<?php echo base_url(); ?>" title="" >
            <img src="<?php echo base_url(); ?>images/logo/classtune.png" alt="" title="" width="200" height="" />
        </a>
    </div>
</div>

<div id="scrollinfo">
    <div id="c248" class="csc-default"><div class="csc-text"><p class="bodytext"><br />BITTE SCROLLEN</p></div></div>
    <div id="cursor">
        &nbsp;
    </div>
</div>

<div id="ipadinfo">
    <div class="ipadinfoPad">
        <div id="c823" class="csc-default"><div class="csc-text"><p class="bodytext">Bitte drehen Sie Ihr Ipad&nbsp;in das Querformat fÃ¼r eine optimale Darstellung.</p></div></div>
    </div>
</div>
</div>

<div id="awwwards" class="left black">
    <a href="http://www.awwwards.com" target="_blank">best websites of the world</a>
</div>


<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery_timers.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.stellar.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.mousewheel.min.js"></script>
<script src="http://diary21.champs21.com/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.flexslider.js"></script>	




<script type="text/javascript">

            $(window).load(function () {
                $('#carousel').flexslider({
                    animation: "slide",
                    controlNav: false,
                    animationLoop: false,
                    slideshow: true,
                    itemWidth: 140,
                    itemMargin: 0,
                    asNavFor: '#slider'
                });

                $('#slider').flexslider({
                    animation: "slide",
                    controlNav: false,
                    animationLoop: true,
                    slideshow: true,
                    sync: "#carousel",
                    start: function (slider) {
                        $('body').removeClass('loading');
                    }
                });
            });
</script>



</body>
</html>