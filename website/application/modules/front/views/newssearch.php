<?php
    $s_ci_key = (isset($ci_key)) ? $ci_key : NULL; 
    $widget = new Widget;
?>
<?php $s_ci_key = (isset($ci_key)) ? $ci_key : NULL; ?>
<div class="container" style="width: 77%;min-height:250px;">
    <?php $widget->run('postdata', "index", $s_category_ids, "search", FALSE, 0, "index", 0, 9,0, $q); ?>
</div>