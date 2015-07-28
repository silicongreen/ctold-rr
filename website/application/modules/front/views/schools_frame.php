<script type="text/javascript">
  function resizeIframe(obj) {
    obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
    obj.style.width = obj.contentWindow.document.body.scrollWidth + 'px';
    console.log(obj.contentWindow.document.body.scrollHeight);
    console.log(obj.contentWindow.document.body.scrollWidth);
  }
</script>
<?php $s_ci_key = (isset($ci_key)) ? $ci_key : NULL; ?>

<iframe src="http://schoolpage.champs21.com/<?php echo $school_name;?>" frameborder="0" scrolling="no" id="iframe" onload='resizeIframe(this);' />
