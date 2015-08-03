<!--<script src="/scripts/layouts/tdsfront/js/iframe_resizer/js/iframeResizer.min.js"></script>-->
<?php $s_ci_key = (isset($ci_key)) ? $ci_key : NULL; ?>

<script>
    document.domain = 'champs21.com';

    function resizeIframe(obj) {
        obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
        console.log(obj.contentWindow.document.body.scrollHeight);
    }

</script>

<iframe id="school_iframe" src="http://schoolpage.champs21.com/<?php echo $school_name; ?>" width="100%" frameborder="0" scrolling="no" onload="resizeIframe(this);" ></iframe>
<style>
   #content-wrapper 
   {
       margin-bottom: 0px !important;
   }
</style>    
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

<script type="text/javascript">

    // schoolpage end
//    $(document).ready(function () {
//        window.parent.postMessage("ready", "*");
//
//        $('a').click(function (event) {
//            event.preventDefault();
//            var el_href = $(this).attr('href');
//            if (el_href.indexOf('#') > -1) {
//                window.parent.postMessage({"setAnchor": el_href}, "*");
//            }
//        });
//    });


    // site end
//    window.addEventListener('message', function (event) {
//        if (event.data == 'ready') {
//            sendHash();
//        }
//        
//        if (anchor = event.data['setAnchor']) {
//            console.log(event.data + 'set anchor');
//            window.location.href = anchor;
//        }
//        
//        if (offset = event.data['offset']) {
//            console.log(event.data + 'offset');
//            window.scrollTo(0, $('iframe').offset().top + offset);
//        }
//        
//    });
//
//    sendHash = function () {
//        hash = window.location.hash.substring(1);
//        console.log('hash');
//        $('iframe')[0].contentWindow.postMessage({"findElement": hash}, '*');
//        console.log($('iframe')[0].attr('src'));
//    }
//
//    $(window).on('hashchange', sendHash);

//    window.addEventListener('message', function (event) {
//        if (anchor = event.data['findElement']) {
//            element = $('[href="' + anchor + '"]');
//            window.parent.postMessage({"offset": element.offset().top}, "*");
//        }
//    });

//    window.addEventListener('message', function (event) {
//        if (offset = event.data['offset']) {
//            window.scrollTo(0, $('iframe').offset().top + offset);
//        }
//    });

</script>