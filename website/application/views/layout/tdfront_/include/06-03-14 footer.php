<!--<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>

<ins class="adsbygoogle"
     style="display:inline-block;width:728px;height:90px"
     data-ad-client="ca-pub-1017056533261428"
     data-ad-slot="9136229792"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>-->
<!-- Archive Wide Ad 970x920 -->
<!--<center>
<ins class="adsbygoogle"
     style="display:inline-block;width:728px;height:90px;margin-bottom: 10px"
     data-ad-client="ca-pub-1017056533261428"
     data-ad-slot="3474375398"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
</center>-->



<?php                    
    $adplace_helper = new Adplace;
    $s_ci_key = (isset($ci_key)) ? $ci_key : '0';
    if($this->uri->segment(1)=="")
    {       
        //$adplace_helper->printAds( 16, null, FALSE, $s_ci_key );
		?>
		<center>
		<!-- Archive Wide Ad 970x920 -->
<ins class="adsbygoogle"
     style="display:inline-block;width:728px;height:90px;margin-bottom: 10px"
     data-ad-client="ca-pub-1017056533261428"
     data-ad-slot="3474375398"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
		</center>
    <?}
    else
    {
            $s_ci_key = "0";
    }
?>
<?php if($this->uri->segment(1)!="" && count($this->uri->segment_array()) == 1 ):?> 
    <?php
        //$adplace_helper = new Adplace;
        //$adplace_helper->printAds( 21, null, FALSE, 'section' );
    ?><div class="noPrint">
	<center>
	<!-- Category bottom ad -->
<ins class="adsbygoogle"
     style="display:inline-block;width:728px;height:90px"
     data-ad-client="ca-pub-1017056533261428"
     data-ad-slot="1487781395"></ins>
	 <script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
</center>	
</div>
<?php endif; ?>
<?php if($this->uri->segment(2)!="" && count($this->uri->segment_array()) == 2 ):?> 
    <?php 
        //$adplace_helper = new Adplace;
        //$adplace_helper->printAds( 29, null, FALSE, 'details' );
    ?>
	
<?php endif; ?>
<div class="clear_both" style="margin-bottom:10px;"></div>

<?php
$ci_key = (isset($ci_key)) ? $ci_key : 'index';
$widget = new Widget;
if (isset($pagelayout['footer_widget']) && !empty($pagelayout['footer_widget'])) {
    foreach ($pagelayout['footer_widget'] as $value) {
        $widget->run($value, $ci_key);
    }
} else {
    show_media_gallery($ci_key, 'footer', 'top', true);
    if(!isset($magazinheaderslide))
    {
    $widget->run('bottomslider');
    }
    show_media_gallery($ci_key, 'footer', 'bottom', true);
}

$widget->run('menufooter');
?>







<!--###############################################################################-->
<div class="bottom-menu-info ym-gr">
    <h1>
        <img src="<?php echo  base_url() ?>styles/layouts/tdsfront/images/footer-logo.png" alt="Footer Logo" width="100%" />
    </h1>

    <p>@<?php echo date('Y'); ?> thedailystar.net. All Rights Reserved</p>
    
    <div id="dp_div">
        <?php $date = (isset($_GET['archive']) && strlen($_GET['archive']) != "0") ? $_GET['archive'] : date('Y-m-d');?>
        <div id="dp6" data-date="<?php echo $date;?>" data-date-format="yyyy-mm-dd"></div>
        <?php if ( isset($_GET['archive']) &&  strlen($_GET['archive']) != "0"  ) : ?>
            <div class="today"><a href="<?php echo base_url() ?>"><span><img src="<?php echo base_url().'/styles/layouts/tdsfront/css/img/prev.png'?>" /></span><span> Today</span></a></div>
        <?php else: ?>
            <input type="hidden" id="disable_active_datepicker" value="disable_active_datepicker" />
        <?php endif; ?>
    
    <br/>

    <!--div id="datepicker_1"></div-->

    </div>
    
    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>