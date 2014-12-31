<script type="text/javascript" src="<?php echo base_url('js/top-main.js'); ?>"></script> 
<?php
$widget = new Widget;
$widget->run('postdata', "index", $s_category_ids, 'index');
?>
<script type="text/javascript" src="<?php echo base_url('js/main-bottom.js'); ?>"></script>