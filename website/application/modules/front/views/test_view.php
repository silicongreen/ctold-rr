<script type="text/javascript">
    

    function resizeIframe(obj) {
        var height = obj.contentWindow.document.body.scrollHeight + 0; 
        obj.style.height = height + 'px';
    }
   

</script>
<html>
    <head>
        <title>Select School</title>
        <link rel="stylesheet" id="bootstrap-css" href="<?php echo base_url('merapi/style/bootstrap.css'); ?>" type="text/css" media="all" />
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div style="margin:0 auto; width:80%">
            <iframe id="iframe_change_height" onload="resizeIframe(this);" src="<?php echo base_url('front/paid/select_school?back_url=http://diary21.champs21.com&user_type=4'); ?>" style="border:0;" width="100%" scrolling="no"  />
        </div>
    </body>
</html>


 

