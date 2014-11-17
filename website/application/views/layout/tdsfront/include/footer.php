<?php
$ci_key = (isset($ci_key)) ? $ci_key : 'index';
$widget = new Widget;


//$widget->run('menufooter');
?>
<a href="#" class="champs21_scrollToTop"><!--Scroll To Top--></a>
<div class="footerlink f5">
    <ul>
        <li><a href="<?php echo base_url('about-us');?>">About Us</a></li>
        <li>|</li>
        <li><a href="<?php echo base_url('terms');?>">Terms</a></li>
        <li>|</li>
        <li><a href="<?php echo base_url('privacy-policy');?>">Privacy Policy</a></li>
        <li>|</li>
        <li><a href="<?php echo base_url('copyright');?>">Copyright</a></li>
        <li>|</li>
        <li><a href="/contact-us">Contact Us</a></li>
<!--        <li>|</li>
        <li><a href="#">Online purchase</a></li>
        <li>|</li>
        <li><a href="#">Career</a></li>
        <li>|</li>
                        -->
    </ul>
</div>






    <!--###############################################################################-->
    <div class="poweredby f5">    
        <p>Powered by <a href="<?php echo base_url();?>" style="color:red;">Champs21.com</a></p>




           
        
<!--<script type="text/javascript" src="<?= base_url() ?>merapi/jquery_002.js"></script>-->
<!--<script type="text/javascript" src="<?= base_url() ?>merapi/scripts.js"></script>
<script type="text/javascript" src="<?= base_url() ?>merapi/devicepx-jetpack.js"></script>
<script type="text/javascript" src="<?= base_url() ?>merapi/pluginsFoot.js"></script>
<script type="text/javascript" src="<?= base_url() ?>merapi/main.js"></script>
<script type="text/javascript" src="<?= base_url() ?>merapi/loopJs.js"></script>

<script type="text/javascript" src="<?= base_url() ?>Profiler/jquery.form.min.js"></script>
<script type="text/javascript" src="<?= base_url() ?>Profiler/bootstrap.js"></script>
<script type="text/javascript" src="<?= base_url() ?>Profiler/jquery.easing.min.js"></script>
<script type="text/javascript" src="<?= base_url() ?>Profiler/jquery.tinyscrollbar.min.js"></script>
<script type="text/javascript" src="<?= base_url() ?>Profiler/custom_theme.js"></script>
<script type="text/javascript" src="<?= base_url() ?>Profiler/menu.js"></script>
<script type="text/javascript" src="<?= base_url() ?>Profiler/profile_script_resize.js"></script>
<script src="<?php //echo base_url('scripts/jquery/jquery.carouFredSel-6.2.1-packed.js'); ?>"></script>
<script src="<?php //echo base_url('scripts/layouts/tdsfront/js/jquery.scrollUp.min.js'); ?>"></script>
<script src="<?php //echo base_url('scripts/layouts/tdsfront/js/jquery.scrollUp_custom.js'); ?>"></script>
<script src="<?php //echo base_url('scripts/jquery/jquery.lazyload.js'); ?>"></script>
<script src="<?php //echo base_url('scripts/jquery/imgLiquid-min.js'); ?>"></script>

<script src="<?php //echo base_url('scripts/layouts/tdsfront/js/index.js'); ?>"></script>
<script src="<?php //echo base_url('scripts/layouts/tdsfront/js/index-req.js'); ?>"></script>
<script src="<?php //echo base_url('scripts/jquery/jquery.tree.js'); ?>"></script>
<script src="<?php //echo base_url('scripts/custom/customTree.js'); ?>"></script>-->
<script src="<?php echo base_url('scripts/fancybox/fancybox.js'); ?>"></script>
<script src="<?php echo base_url('scripts/layouts/tdsfront/js/jquery.liteuploader.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('js/main-bottom.js'); ?>"></script>
<script src="<?php echo base_url('gallery/html5gallery.js'); ?>"></script>
<!--<script src="<?php echo base_url('scripts/layouts/tdsfront/js/lib.js'); ?>"></script>-->


<script type="text/javascript">
    (function() {
        $('.datepicker').datepicker();
    
       
        var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
        po.src = 'https://apis.google.com/js/client:plusone.js?parsetags=explicit';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
    })();
   

    $(document).ready(function(){
        var eventTrigger = false;
        $('body').click(function(event)
        {
           
            if (event.target.id != "menu-toggle") 
            {
               
                if($('#menu-toggle').prop('checked'))
                {
                    $('#menu-toggle').prop('checked',false);
                    eventTrigger = true;
                }
                else
                {
                   eventTrigger = false; 
                }    
                
            }
            else
            {
                if(eventTrigger == true)
                {
                    eventTrigger = false;
                    return false;
                }    
                
            }    
        });
        
        
        
	
	$(window).scroll(function(){
		if ($(this).scrollTop() > 400) {
			$('.champs21_scrollToTop').fadeIn();
		} else {
			$('.champs21_scrollToTop').fadeOut();
		}
	});
	
	//Click event to scroll to top
	$('.champs21_scrollToTop').click(function(){
		$('html, body').animate({scrollTop : 0},800);
		return false;
	});
	
});
</script>

<script>
      window.fbAsyncInit = function() {
        FB.init({
          appId      : '850059515022967',
          /* appId      : '164223470298622', */
          xfbml      : false,
          version    : 'v2.1'
        });
      };

      (function(d, s, id){
         var js, fjs = d.getElementsByTagName(s)[0];
         if (d.getElementById(id)) {return;}
         js = d.createElement(s); js.id = id;
         js.src = "//connect.facebook.net/en_US/sdk.js";
         fjs.parentNode.insertBefore(js, fjs);
       }(document, 'script', 'facebook-jssdk'));
</script>
<style>
    .champs21_scrollToTop{
	width:40px; 
	height:40px;	
	background: whiteSmoke;	
	position:fixed;
	bottom:80px;
	left:4px;
	display:none;
	background: url('<?php echo base_url('styles/layouts/tdsfront/images/arrow_up.png'); ?>') no-repeat;
        background-size:40px;
}
.champs21_scrollToTop:hover{
	text-decoration:none;
        background: url('<?php echo base_url('styles/layouts/tdsfront/images/arrow_up_hover.png'); ?>') no-repeat;
        background-size:40px;
}
.container
{
    min-height:650px !important;
}
</style>    