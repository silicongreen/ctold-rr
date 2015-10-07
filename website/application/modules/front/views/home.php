<?php
    $s_ci_key = (isset($ci_key)) ? $ci_key : NULL; 
    $widget = new Widget;
    $type_cookie = get_type_cookie();
    $st_url = base_url()."front/ajax/set_type_cookie/2";  
    $te_url = base_url()."front/ajax/set_type_cookie/3";
    $pa_url = base_url()."front/ajax/set_type_cookie/4";
   
    if($type_cookie==2)
    {
        $st_url = base_url()."front/ajax/unset_type_cookie";
    } 
    if($type_cookie==3)
    {
        $te_url = base_url()."front/ajax/unset_type_cookie";
    }
    if($type_cookie==4)
    {
        $pa_url = base_url()."front/ajax/unset_type_cookie";
    }
    
//    if (isset($_COOKIE['local'])) {
//        unset($_COOKIE['local']);
//        setcookie('local', null, -1, '/');
//    }
    
    $lang = get_language_cookie();
?>
<?php $s_ci_key = (isset($ci_key)) ? $ci_key : NULL; ?>
<div class="container" style="width: 77%;">
    <div class="clearfix content-div-title">
        <div class="col-xs-1"></div>        
        <div class="col-xs-3 text-center"><h2 class="f2">
                <a href="<?php echo $st_url; ?>" <?php if($type_cookie==2): ?> style="color: #DB3434" <?php endif; ?> >
                STUDENTS
                </a>
        </h2></div>
        <div class="col-xs-2  text-center champs-bullet">&nbsp;&nbsp;</div>
        <div class="col-xs-3 text-center"><h2 class="f2">
                <a href="<?php echo $pa_url; ?>" <?php if($type_cookie==4): ?> style="color: #DB3434" <?php endif; ?> >
                PARENTS
                </a>
        </h2></div>
        <div class="col-xs-2  text-center champs-bullet">&nbsp;&nbsp;</div>
        <div class="col-xs-3 text-center"><h2 class="f2">
            <a href="<?php echo $te_url; ?>" <?php if($type_cookie==3): ?> style="color: #DB3434" <?php endif; ?> >
                TEACHERS
             </a>   
            </h2>
        </div>
        <div class="col-xs-1"></div>
    </div>
    <?php $widget->run(
            'postdata', "index", $s_category_ids, 'index', FALSE,
            0, 'index', 0, 9, 0, '', NULL, false, 0, array(), $lang
            ); ?>
</div>