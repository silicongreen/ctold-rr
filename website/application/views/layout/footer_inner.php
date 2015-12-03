<div id="imagesWrap" style="top:1300px;">
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
        <?php $this->load->view("layout/login"); ?>
    </ul>
    <div id="homelink">
        <a href="<?php echo base_url(); ?>" title="" >
            <img src="<?php echo base_url(); ?>images/logo/classtune.png" alt="" title="" width="215" height="" />
        </a>
    </div>
</div>

</div>


<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery_timers.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.stellar.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.mousewheel.min.js"></script>
<script src="<?php echo base_url(); ?>js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.flexslider.js"></script>	




<script type="text/javascript">

            $(window).load(function () {
                $('#carousel').flexslider({
                    animation: "slide",
                    controlNav: false,
                    animationLoop: false,
                    slideshow: true,
                    slideshowSpeed:10000,
                    pauseOnHover:true,
                    itemWidth: 140,
                    itemMargin: 0,
                    asNavFor: '#slider'
                });

                $('#slider').flexslider({
                    animation: "slide",
                    controlNav: false,
                    animationLoop: true,
                    slideshow: true,
                    slideshowSpeed:10000,
                    pauseOnHover:true,
                    sync: "#carousel",
                    start: function (slider) {
                        $('body').removeClass('loading');
                    }
                });
            });
// Load this script once the document is ready
$(document).ready(function () {
   
 // Get all the thumbnail
 $('div.thumbnail-item').mouseenter(function(e) {
 
  // Calculate the position of the image tooltip
  x = e.pageX - $(this).offset().left;
  y = e.pageY - $(this).offset().top;
 
  // Set the z-index of the current item,
  // make sure it's greater than the rest of thumbnail items
  // Set the position and display the image tooltip
  $(this).css('z-index','1500')
  .children("div.tooltip")
  .css({'top': y + 10,'left': x + 20,'display':'block'});
    
 }).mousemove(function(e) {
    
  // Calculate the position of the image tooltip  
  x = e.pageX - $(this).offset().left;
  y = e.pageY - $(this).offset().top;
    
  // This line causes the tooltip will follow the mouse pointer
  $(this).children("div.tooltip").css({'top': y + 10,'left': x + 20});
    
 }).mouseleave(function() {
    
  // Reset the z-index and hide the image tooltip
  $(this).css('z-index','10000')
  .children("div.tooltip")
  .animate({"opacity": "hide"}, "fast");
 });
 
});

</script>



</body>
</html>