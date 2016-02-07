<?php $lang = reset(explode('_', get_locale()));?>
<?php

switch ($lang) {
    case "en":
        $login = "Log in";$contact = "Contact";$support = "Support";$teacher = "Teacher";$parent = "Parent";$student = "Student";$sAdmin = "School Admin";$feature = "Features";$home = "Home";$term = "Terms";$pp = "Privacy Policy";$cr = "Copyright";
        break;
    case "bn":
        $login = "লগইন";$contact = "যোগাযোগ";$support = "সহায়তা";$teacher = "শিক্ষক";$parent = "অভিভাবক";$student = "শিক্ষার্থী";$sAdmin = "স্কুল অ্যাডমিন";$feature = "বিস্তারিত";$home = "হোম";$term = "শর্তাবলী";$pp = "গোপনীয়তা নীতি";$cr = "কপিরাইট";
        break;
    default:
        echo "Your favorite color is neither red, blue, nor green!";
}
?>
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

<div id="mainnav">
    <ul>
        <li><span onclick="window.location = '<?php echo get_site_url().'/'.$lang; ?>'"><?php echo $home;?></span></li>
        <!--li><span onclick="window.location ='<?php echo get_site_url(); ?>?locale=about'">About us</span></li-->
        <li><span onclick="window.location = '<?php echo get_site_url().'/'.$lang; ?>?locale=feature'"><?php echo $feature;?></span>
            <ul>
                <li><a href="<?php echo get_site_url().'/'.$lang; ?>/admin-page<?php echo "-".$lang;?>" ><?php echo $sAdmin;?></a></li>
                <li><a href="<?php echo get_site_url().'/'.$lang; ?>/student-page<?php echo "-".$lang;?>" ><?php echo $student;?></a></li>
                <li><a href="<?php echo get_site_url().'/'.$lang; ?>/guardian-page<?php echo "-".$lang;?>" ><?php echo $parent;?></a></li>
                <li><a href="<?php echo get_site_url().'/'.$lang; ?>/teacher-page<?php echo "-".$lang;?>" ><?php echo $teacher;?></a></li>
            </ul>
        </li>
		<li><a href="<?php echo get_site_url().'/'.$lang; ?>/supports<?php echo "-".$lang;?>"><span><?php echo $support;?></span></a>
        <li><span onclick="window.location = '<?php echo get_site_url().'/'.$lang; ?>?locale=contact'"><?php echo $contact;?></span></li>
         <?php get_template_part( 'lang_'.$lang.'/login' ); ?>	
		 <li><?php pll_the_languages(array('dropdown'=>1));?></li>
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