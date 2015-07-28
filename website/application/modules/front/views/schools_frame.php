<!--<script src="/scripts/layouts/tdsfront/js/iframe_resizer/js/iframeResizer.min.js"></script>-->
<?php $s_ci_key = (isset($ci_key)) ? $ci_key : NULL; ?>

<script>         
    document.domain = 'champs21.com';

    function resizeIframe(obj) {
      obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
      console.log(obj.contentWindow.document.body.scrollHeight);
    }

</script>

<iframe src="http://schoolpage.champs21.com/<?php echo $school_name;?>" width="100%" frameborder="0" scrolling="no" onload="resizeIframe(this);" ></iframe>

<!--<div>-->
    <?php // echo $school_page_header; ?>
    <?php // echo $school_page_body; ?>
<!--</div>-->

<!--<script type="text/javascript">
height="3318" 
    iFrameResize({
        log: true,
        checkOrigin: false,
        enableInPageLinks: true,
        heightCalculationMethod: 'documentElementScroll',
        interval: 64
        
    });
</script>-->

