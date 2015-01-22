<?php
$seen_class = getclassactionbox($news->user_view_count);
$wow_class = getclassactionbox($news->wow_count);

?>
<?php if($news->post_type == 1): ?>
<a href="<?php echo base_url() . sanitize($news->headline) . "-" . $news->post_id; ?>" style="color: white;">
    <div class="assessment-seen-div" style="">
        <div style="width:<?php echo $seen_class['width']; ?>px;float:left; margin-top:5px;">
            <div class="seen-image col-lg-<?php echo $seen_class['class1']; ?>" style="text-align:right;margin-top:7px;" ><img  class="no_toolbar"  src="<?php echo base_url("styles/layouts/tdsfront/images/social/seen.png"); ?>" /></div>
            <div class="seen col-lg-<?php echo $seen_class['class2']; ?>" style="margin-top:4px;" ><span  class="f2"  style="font-size:12px;margin-left:5px;margin-top:6px;color:#fff">Seen <?php echo $seen_class['new_count']; ?></span></div>
        </div>
        <?php if($news->assessment_id): ?>
        <div class="assessment-div" style="width:150px;float:left; margin-left:5px;">
            <img class="no_toolbar toolbar" height="30px" src="<?php echo base_url(); ?>/styles/layouts/tdsfront/image/assessment-gray.png" style="cursor: pointer; height: 25px; margin-top:5px;float: left;"> 
            <span class="f2 assessment-span">Take Assessment</span>
        </div>
        <?php endif; ?>

    </div>
</a>    
<?php endif; ?>
  