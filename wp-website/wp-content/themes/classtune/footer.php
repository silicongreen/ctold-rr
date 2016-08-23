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
<?php $lang = reset(explode('_', get_locale()));?>
<?php

switch ($lang) {
    case "en":
        $login = "Log in";$reg_free = "Registration Free!";$free_acc = "Create Your FREE Account";$accessible = "Accessible from anywhere...";$contact = "Contact";$support = "Support";$teacher = "Teacher";$parent = "Parent";$student = "Student";$sAdmin = "School Admin";$feature = "Features";$home = "Home";$term = "Terms";$pp = "Privacy Policy";$cr = "Copyright";
        break;
    case "bn":
        $login = "লগইন";$reg_free = "ফ্রি রেজিস্ট্রেশন !";$free_acc = "বিনামূল্যে তোমার একাউন্ট কর";$accessible = "যে কোন জায়গা থেকে প্রবেশযোগ্য...";$contact = "যোগাযোগ";$support = "সহায়তা";$teacher = "শিক্ষক";$parent = "অভিভাবক";$student = "শিক্ষার্থী";$sAdmin = "স্কুল অ্যাডমিন";$feature = "বিস্তারিত";$home = "হোম";$term = "শর্তাবলী";$pp = "গোপনীয়তা নীতি";$cr = "কপিরাইট";
        break;
	case "th":
        $login = "เข้าสู่ระบบ";$reg_free = "สมัครฟรี";$free_acc = "ลงทะเบียนฟรี";$accessible = "เข้าถึงได้จากทุกที่...";$contact = "ติดต่อ";$support = "สนับสนุน";$teacher = "ฉันเป็นครู";$parent = "ฉันเป็นผู้ปกครอง";$student = "ฉันเป็นนักเรียน";$sAdmin = "ฉันเป็นผู้ดูแลระบบ";$feature = "ฟีเจอร์";$home = "หน้าหลัก";$term = "ข้อตกลงและเงื่อนไข";$pp = "นโยบายความเป็นส่วนตัว";$cr = "ลิขสิทธิ์";
        break;
    default:
        $login = "Log in";$reg_free = "Registration Free!";$free_acc = "Create Your FREE Account";$accessible = "Accessible from anywhere...";$contact = "Contact";$support = "Support";$teacher = "Teacher";$parent = "Parent";$student = "Student";$sAdmin = "School Admin";$feature = "Features";$home = "Home";$term = "Terms";$pp = "Privacy Policy";$cr = "Copyright";
}
?>
<div id="imagesWrap" style="top:1850px;">
    <div id="images">
        <div id="worldmap">
            <h2 class="f2" style="left: 35%;position: absolute;text-align: center;top: 40px;">
                <i><?php echo $accessible;?></i>
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
                                    <a href="javascript:void(0);" style="cursor: default;background: #E5ECF2 none repeat scroll 0 0;color:#999!important;"><?php echo $reg_free;?> <i class="fa fa-long-arrow-right"></i></a>
                                    <a href="<?php echo get_site_url().'/'.$lang; ?>/admin-user<?php echo "-".$lang;?>" ><?php echo $sAdmin;?></a>
                                    <a href="<?php echo get_site_url().'/'.$lang; ?>/student-user<?php echo "-".$lang;?>" ><?php echo $student;?></a>
                                    <a href="<?php echo get_site_url().'/'.$lang; ?>/guardian-user<?php echo "-".$lang;?>" ><?php echo $parent;?></a>
                                    <a href="<?php echo get_site_url().'/'.$lang; ?>/teacher-user<?php echo "-".$lang;?>" ><?php echo $teacher;?></a>
                                </li>
                            </ul>
                        </div>
                        <?php get_template_part( 'lang_'.$lang.'/contact' ); ?> 										
                    </div>

                    <div class="ym-wbox footer noPrint" style=" position: relative; bottom: 0px; display: block;">

                        <div class="poweredby f5">    
                            <p>&copy; ClassTune <?php echo date("Y");?><span style="color:#64B846;"> | </span>(+880)-1740212121 <span style="color:#64B846;"> | </span> <a href="mailto:info@classtune.com" style="color:#999;">Email : info@classtune.com</a> </p>
                        </div>
						<div class="footer_logo">
                            <a href="<?php echo get_site_url(); ?>" title="">
                                <img src="<?php bloginfo('template_url'); ?>/images/logo/classtune-footer-logo.png" alt="" title="" width="170" height="" />
                            </a>
                        </div>
                        

                        <div class="footerlink f5">
                            <ul>									
								<!--li><a href="mailto:info@classtune.com" style="color:#999;">Email : info@classtune.com</a></li>
								<li style="color:#64B846;">|</li-->
								<li><a href="<?php echo get_site_url().'/'.$lang; ?>/terms<?php echo "-".$lang;?>" style="color:#999;"><?php echo $term;?></a></li>
								<li style="color:#64B846;">|</li>
								<li><a href="<?php echo get_site_url().'/'.$lang; ?>/privacypolicy<?php echo "-".$lang;?>" style="color:#999;"><?php echo $pp;?></a></li>
								<li style="color:#64B846;">|</li>
								<li><a href="<?php echo get_site_url().'/'.$lang; ?>/copyright<?php echo "-".$lang;?>" style="color:#999;"><?php echo $cr;?></a></li>									
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
 <a href="<?php get_site_url(); ?>/#thanks_endWrap"><img src="<?php bloginfo('template_url'); ?>/images/sticky.png" style="width:120px;" /></a>
</div>
<div id="mainnav">
    <ul>
        <li class="before"><a href="<?php echo get_site_url(); ?>"><span><?php echo $home;?></span></a></li>
        <!--li class="start act"><span>About us</span></li-->
        <li class="cron"><a href="<?php get_site_url(); ?>/#cronWrap"><span><?php echo $feature;?></span></a>
            <ul>
                <li><a href="<?php echo get_site_url().'/'.$lang; ?>/admin-user<?php echo "-".$lang;?>" ><?php echo $sAdmin;?></a></li>
                <li><a href="<?php echo get_site_url().'/'.$lang; ?>/student-user<?php echo "-".$lang;?>" ><?php echo $student;?></a></li>
                <li><a href="<?php echo get_site_url().'/'.$lang; ?>/guardian-user<?php echo "-".$lang;?>" ><?php echo $parent;?></a></li>
                <li><a href="<?php echo get_site_url().'/'.$lang; ?>/teacher-user<?php echo "-".$lang;?>" ><?php echo $teacher;?></a></li>
            </ul>
        </li>
		<li><a href="<?php echo get_site_url().'/'.$lang; ?>/supports<?php echo "-".$lang;?>"><span><?php echo $support;?></span></a>
        <li class="images"><a href="<?php get_site_url(); ?>/#thanks_endWrap"><span><?php echo $contact;?></span></a></li>
        <?php get_template_part( 'lang_'.$lang.'/login' ); ?>	
		<li><?php pll_the_languages(array('dropdown'=>1));  ?></li>
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
<!--script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/px.js"></script-->
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery.flexslider.js"></script>	
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/owl.carousel.min.js"></script>	
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/owl.custome.classtune.js"></script>	


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
  var n = 0; 
 // Get all the thumbnail
 $('div.thumbnail-item').mouseenter(function(e) {
 
    // Calculate the position of the image tooltip
    x = e.pageX - $(this).offset().left;
    y = e.pageY - $(this).offset().top;
 
    //$(this).css('z-index','1500');
    
    if(n === 0)
    {
        var  img = $(this).find('img').attr("src");
  
        var html = '<div class="tooltip"><img src="'+img+'" alt="" style="width:100%;" /></div>'
        console.log(html);
        $(html).insertAfter("#slider .flex-viewport").css({'top': y - 130,'left': x + 150,'display':'block','opacity':1,'z-index':'15000000'});
        n = 1;
    }
    
       
 }).mousemove(function(e) {
    
  // Calculate the position of the image tooltip  
  x = e.pageX - $(this).offset().left;
  y = e.pageY - $(this).offset().top;
    
  // This line causes the tooltip will follow the mouse pointer
  //$(this).children("div.tooltip").css({'top': y + 10,'left': x + 20});
  $(".flex-viewport").next( "div.tooltip" ).css({'top': y - 130,'left': x + 150});
    
 }).mouseleave(function() {
    if(n = 1)
    {
        $( "div.tooltip" ).css('display','none;'); 
        $(".flex-viewport").next( "div.tooltip" ).remove();     
        $( "div.tooltip" ).animate({"opacity": "hide"}, "fast");
        n = 0;
    }    

 });
 
});

$(function() {
  $('a[href*="#"]:not([href="#"])').click(function() {
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
      if (target.length) {
        $('html, body').animate({
          scrollTop: target.offset().top
        }, 1000);
        return false;
      }
    }
  });
});

</script>

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
