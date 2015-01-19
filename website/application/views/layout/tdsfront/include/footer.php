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
        <p>Powered by <a href="http://www.team-creative.net" style="color:red;">Team Creative</a></p>
    </div>
<!--<script type="text/javascript">
//THIS IS FOR ONLY SNOW FALL EFFECT
//snowStorm.snowColor = '#fff'; // blue-ish snow!?
//snowStorm.autoStart = true;
//snowStorm.flakesMaxActive = 165;  // show more snow on screen at once
//snowStorm.useTwinkleEffect = true; // let the snow flicker in and out of view
//snowStorm.snowCharacter = '•';
</script>-->
   
    

<script type="text/javascript" src="<?php echo base_url('js/main-bottom.js'); ?>"></script>

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
 