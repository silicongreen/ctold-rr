<?php echo $headerinclude; ?>
    
    
    <body>
        <div id="fb-root" class="noPrint"></div>
        <input type="hidden" id="base_url" value="<?php echo base_url(); ?>" />
        
        <input type="hidden" id="ci_key_main" value="<?php echo (isset($ci_key)) ? $ci_key : 0; ?>" />
        <input type="hidden" id="zero_comment_show" value="<?php echo (int) $zero_comment_show ?>" />
        <input type="hidden" id="show_more" value="<?php echo (int) $show_more ?>" />
        <input type="hidden" id="show_overlay" value="<?php echo (int) $show_overlay ?>" />
        
        <?php if ( isset($_GET['archive']) &&  strlen($_GET['archive']) != "0"  ) : ?>
            <input type="hidden" id="archive" value="<?php echo $_GET['archive']; ?>" />
        <?php endif; ?>
        
        <div class="ym-wrapper">
            <div class="ym-wbox noPrint"><!-- Start header -->
                <?php echo $header; ?>
            </div><!-- End header -->
            <div class="ym-wbox container">    <!-- Start container -->
                <div class="exclusive-box noPrint">
                    <?php echo $exclusive; ?>
                </div>
                <?php $magazine_class = ""; ?>
                <?php if(isset($magazinheaderslide) && $magazinheaderslide): ?>
                    <?php echo $magazinheaderslide; $magazine_class = "left_right_magazine_97";?>
                    
                <?php endif;?>
                <div class="ym-grid ym-column <?php echo $magazine_class;?>">
                     <?php 
                     $extra_css = "";
                     if(isset($obj_menus->has_right) && $obj_menus->has_right==0)
                     {
                        $extra_css = "style='width:100%'";
                        
                     } 
                    ?>
                    
                    
                    
                    <div class="ym-col1 left" <?php echo $extra_css; ?>>            <!-- Start left --> 
                        <?php echo $content; ?>
                    </div> 
                    
                    <!-- End left -->
                    <?php if(!isset($obj_menus->has_right) || $obj_menus->has_right==1): ?>
                    <div class="ym-col3 right ym-gr noPrint"><!-- Start Right -->
                        <?php echo $side_bar; ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- End RIGHT  --> 
                </div>
                <?php if(isset($magazinheaderslide) && $magazinheaderslide): ?>
                    </div>
                <?php endif;?>
                <div style="clear: both;" class="noPrint"></div>

                <div class="ym-wbox footer noPrint">  <!-- Start Footer -->
                    <?php echo $footer; ?>
                </div>                        <!-- End Footer -->
            </div>                             <!-- End container -->

        </div>
        </div>

<?php $js = ( isset($target) ) ? $target : "index"; ?>
        <?php 
        $s_url = base_url();

        $this->load->config("tds");
//            if (strpos(base_url(), "http://www.") !== FALSE )
//            {
//                $s_url = str_replace("http://www.", "http://bd.", base_url());
//            }
    ?>
    
    <script src="<?php echo $s_url; ?>js/<?php echo $js; ?>.js?v=<?php echo $js_version; ?>"></script>
        
        <script type='text/javascript'> 

        var _gaq = _gaq || [];
         _gaq.push(['_setAccount', 'UA-27385659-1']);
         _gaq.push(['_setDomainName', 'thedailystar.net']);
         _gaq.push(['_trackPageview']);
         (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript';
        ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' :
        'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(ga, s);
         })();
         
         /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
        var disqus_shortname = 'dailystarnews'; 

        /* * * DON'T EDIT BELOW THIS LINE * * */
        (function () {
            var s = document.createElement('script');  s.async = true;
            s.type = 'text/javascript';
            s.src = '//' + disqus_shortname + '.disqus.com/count.js';
            (document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
        }());

        (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id))
                    return;
                js = d.createElement(s);
                js.id = id;
                js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=280663678634006";
                fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
        </script>
    </body>
</html>