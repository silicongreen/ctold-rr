  
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
        
        <div id="main" class="col-xs-12 site-main clearfix">
            <div id="content-wrapper" style="margin-bottom:100px;" class="wrapper col-md-12 clearfix"><!-- Start header -->
                <?php
                    if($full_template) {
                        echo $header;
                    }
                ?>
    
                <div style="margin-top:80px;"></div>
                <?php echo $content; ?>
            </div> 
                    
            <!-- End left -->
            <?php #if(!isset($obj_menus->has_right) || $obj_menus->has_right==1): ?>
            <!-- <aside data-scroll-reveal-complete="true" data-scroll-reveal-initialized="true" id="" class="col-md-3" role="complementary" data-scroll-reveal="bottom" style="padding-right:0px;">
                <?php #echo $side_bar; ?>
            </aside> --><!-- #primary-sidebar -->
            <?php #endif; ?>
                    
                    <!-- End RIGHT  --> 
        </div>
      
          
                <div style="clear: both;" class="noPrint"></div>
                <?php if($full_template) { ?>
                    
                        <?php echo $footer; ?>
                                           <!-- End Footer -->
                <?php } ?>
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
    
    <!--script src="<?php //echo $s_url; ?>js/<?php //echo $js; ?>?v=<?php //echo $js_version; ?>"></script-->
    
    <!-- LOAD JAVASCRIPTS, Now we have to DIG OUT the Minify issues on our server -->
   
   


    
    <!-- -->
    
    
    
        <script type='text/javascript'> 
            
        var _gaq = _gaq || [];
         _gaq.push(['_setAccount', 'UA-19173520-2']);
         _gaq.push(['_setDomainName', 'www.champs21.com']);
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

//        (function(d, s, id) {
//                var js, fjs = d.getElementsByTagName(s)[0];
//                if (d.getElementById(id))
//                    return;
//                js = d.createElement(s);
//                js.id = id;
//                js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=280663678634006";
//                fjs.parentNode.insertBefore(js, fjs);
//        }(document, 'script', 'facebook-jssdk'));
        </script>
    </body>
</html>