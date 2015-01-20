
<?php
$seen_class = getclassactionbox($news->user_view_count);
$wow_class = getclassactionbox($news->wow_count);

?>
<div class="col-lg-12" style="z-index:10;width: 93.6%;position:absolute;/* border-right:1px solid #DADEDF; */ min-height: 39px;background: black;background: rgba(25, 25, 25, .5);">
    <div style="width:<?php echo $seen_class['width']; ?>px;float:left; margin-top:5px;">
        <div class="seen-image col-lg-<?php echo $seen_class['class1']; ?>" style="text-align:right;margin-top:7px;" ><img  class="no_toolbar"  src="<?php echo base_url("styles/layouts/tdsfront/images/social/seen.png"); ?>" /></div>
        <div class="seen col-lg-<?php echo $seen_class['class2']; ?>" style="margin-top:4px;" ><span  class="f2"  style="font-size:12px;margin-left:5px;margin-top:6px;color:#fff">Seen <?php echo $seen_class['new_count']; ?></span></div>
    </div>
</div>