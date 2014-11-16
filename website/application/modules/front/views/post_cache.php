<div class="ym-gbox sports-inner-news">    
    <h1 class="<?php echo strtolower($name); ?> title"><?php echo $name; ?></h1>
<!--	<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>-->
    
        <?php
            //$adplace_helper = new Adplace;
            //$adplace_helper->printAds( 27, 0, FALSE,'0','details' );
         ?>
<!--	<ins class="adsbygoogle"
		 style="display:inline-block;width:100%;height:60px"
		 data-ad-client="ca-pub-1017056533261428"
		 data-ad-slot="9552350193"></ins>
	<script>
	(adsbygoogle = window.adsbygoogle || []).push({});
	</script>-->
    <div class="sports-inner-container">
   
    <!-- AddThis Smart Layers END -->
        <!-- AddThis Button BEGIN -->
        <style>
            .addthis_toolbox{
                background: #FCFCFC;
                padding: 10px 0 0px 75px;
            }
            .addthis_button_facebook_share iframe{
                width: 105px;
                height: 30px;
            }
            .addthis_button_facebook_like div{
                width: 95px;
                height: 30px;
            }
            .addthis_button_tweet{
                width: 90px !important;
                height: 30px;
            }
            .addthis_button_google_plusone{
                width: 75px !important;
                height: 30px;
            }
            .addthis_button_pinterest_pinit{
                width: 90px !important;
                height: 30px;
            }
            .addthis_pill_style{
                width: 90px !important;
                height: 30px;
                float: right;
            }
        </style>
        <?php if ( $post_id == 6283 ) : ?>
            <span class='st_facebook_hcount' displayText='Facebook'></span>
            <span class='st_fblike_hcount' displayText='Facebook Like'></span>
            <span class='st_googleplus_hcount' displayText='Google +'></span>
            <span class='st_twitter_hcount' displayText='Tweet'></span>
            <span class='st_linkedin_hcount' displayText='LinkedIn'></span>
            <span class='st_pinterest_hcount' displayText='Pinterest'></span>
            <span class='st_email_hcount' displayText='Email'></span>
            <span class='st_sharethis_hcount' displayText='ShareThis'></span>
            <script type="text/javascript">var switchTo5x=false;</script>
            <script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
            <script type="text/javascript">stLight.options({publisher: "8c1f0e5e-b6d2-41aa-9353-d0071ee61c1b", doNotHash: false, doNotCopy: false, hashAddressBar: true});</script>
        <?php endif; ?>
        <div class="addthis_toolbox addthis_default_style">
            <a class="addthis_button_facebook_share" fb:share:layout="medium" ></a>
            <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
            <a class="addthis_button_tweet"></a>
            <a class="addthis_button_google_plusone" g:plusone:size="medium"></a>
            <a class="addthis_button_pinterest_pinit" pi:pinit:layout="horizontal"></a>
            <a class="addthis_counter addthis_pill_style"></a>
        </div>
<!-- AddThis Button END -->
        
    <?php echo $content; ?>
        <div class="addthis_toolbox addthis_default_style">
            <a class="addthis_button_facebook_share" fb:share:layout="medium" ></a>
            <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
            <a class="addthis_button_tweet"></a>
            <a class="addthis_button_google_plusone" g:plusone:size="medium"></a>
            <a class="addthis_button_pinterest_pinit" pi:pinit:layout="horizontal"></a>
            <a class="addthis_counter addthis_pill_style"></a>
        </div>
        <script type="text/javascript">
            var addthis_config = {"data_track_addressbar":false};
        </script>
        <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-52bca22436b47685"></script>
        
        <div style="clear:both; height: 10px;"></div>
        
<!--	<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>-->
<!--	<ins class="adsbygoogle"
		 style="display:inline-block;width:100%;height:60px"
		 data-ad-client="ca-pub-1017056533261428"
		 data-ad-slot="6481368990"></ins>
	<script>
	(adsbygoogle = window.adsbygoogle || []).push({});
	</script>    -->
    <?php echo $outbrain_content; ?>
    <?php if($can_comment == 1): ?>
        <?php echo $disqus_content; ?>                 
    <?php endif;?>
</div>

</div>

<?php //echo $related_news_content; ?>