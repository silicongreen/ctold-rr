<<<<<<< .mine
<?php
    $s_ci_key = (isset($ci_key)) ? $ci_key : NULL; 
    $widget = new Widget;
?>
<?php $s_ci_key = (isset($ci_key)) ? $ci_key : NULL; ?>
<div class="container" style="width: 77%;">
    
    <div class="clearfix content-div-title">
        <div class="col-xs-1"></div>
        <div class="col-xs-1"></div>
        <div class="col-xs-2 text-center"><h2 class="f2">STUDENTS</h2></div>
        <div class="col-xs-2  text-center champs-bullet">&nbsp;&nbsp;</div>
        <div class="col-xs-2 text-center"><h2 class="f2">TEACHERS</h2></div>
        <div class="col-xs-2  text-center champs-bullet">&nbsp;&nbsp;</div>
        <div class="col-xs-2 text-center"><h2 class="f2">PARENTS</h2></div>
    </div>
    <?php $widget->run('postdata', "index", $s_category_ids, 'index'); ?>
</div>

=======
<?php
    $s_ci_key = (isset($ci_key)) ? $ci_key : NULL; 
    $widget = new Widget;
?>
<?php $s_ci_key = (isset($ci_key)) ? $ci_key : NULL; ?>
<div class="container" style="width: 77%;">
    
    <div class="clearfix content-div-title">
        <div class="col-xs-1"></div>        
        <div class="col-xs-3 text-center"><h2 class="f2">STUDENTS</h2></div>
        <div class="col-xs-2  text-center champs-bullet">&nbsp;&nbsp;</div>
        <div class="col-xs-3 text-center"><h2 class="f2">TEACHERS</h2></div>
        <div class="col-xs-2  text-center champs-bullet">&nbsp;&nbsp;</div>
        <div class="col-xs-3 text-center"><h2 class="f2">PARENTS</h2></div>
        <div class="col-xs-1"></div>
    </div>
    <?php $widget->run('postdata', "index", $s_category_ids, 'index'); ?>
</div>

>>>>>>> .r519
