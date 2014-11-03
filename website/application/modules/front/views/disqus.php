<!--<div id="disqus_thread"></div>

<script type="text/javascript">
/* <![CDATA[ */
    var DsqLocal = {
        'trackbacks': [
        ],
        'trackback_url': "<?php echo base_url() . sanitize($headline);?>/<?php echo md5($tds_post_id);?>"    };
/* ]]> */
</script>
<script type="text/javascript">
/* <![CDATA[ */
(function() {
    var dsq = document.createElement('script'); dsq.type = 'text/javascript';
    dsq.async = true;
    dsq.src = '//' + disqus_shortname + '.' + 'disqus.com' + '/embed.js';
    (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
})();
/* ]]> */
</script>-->

<div id="disqus_thread" class="noPrint"></div>
<script type="text/javascript">
/* <![CDATA[ */
    var disqus_container_id = 'disqus_thread';
    var disqus_domain = 'disqus.com';
    var disqus_shortname = "dailystarnews"; 
    var disqus_identifier = '<?php echo $tds_post_id;?>';
    var disqus_title = "<?php echo str_replace('"', "", $headline);?>";
    var disqus_url = '<?php echo base_url() . sanitize($headline);?>-<?php echo $tds_post_id;?>';
        var disqus_config = function () {
        var config = this; 
        config.language = '';

        /*
           All currently supported events:
            * preData — fires just before we request for initial data
            * preInit - fires after we get initial data but before we load any dependencies
            * onInit  - fires when all dependencies are resolved but before dtpl template is rendered
            * afterRender - fires when template is rendered but before we show it
            * onReady - everything is done
         */

        config.callbacks.preData.push(function() {
            document.getElementById(disqus_container_id).innerHTML = '';
        });
                config.callbacks.onReady.push(function() {
            
            var script = document.createElement('script');
            script.async = true;
            script.src = '?cf_action=sync_comments&post_id=<?php echo $tds_post_id;?>';

            var firstScript = document.getElementsByTagName( "script" )[0];
            firstScript.parentNode.insertBefore(script, firstScript);
        });
                    };
/* ]]> */
</script>
<script type="text/javascript">
    /* <![CDATA[ */
        var DsqLocal = {
            'trackbacks': [
            ],
            'trackback_url': "<?php echo base_url() . sanitize($headline);?>/<?php echo md5($tds_post_id);?>"    };
    /* ]]> */
    
    /* * * DON'T EDIT BELOW THIS LINE * * */
    (function() {
        var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
        dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
    })();
</script>