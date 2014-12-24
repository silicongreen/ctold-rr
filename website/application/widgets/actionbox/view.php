
<?php
$seen_class = getclassactionbox($news->user_view_count);
$wow_class = getclassactionbox($news->wow_count);

?>
<div class="action-box" style="background:#F7F7F7">
    <div data="wow" id="wow_<?php echo $news->post_id; ?>" class="<?php if($news->can_wow==1): ?>wow_class<?php endif; ?> col-lg-4 <?php echo ( free_user_logged_in() || wow_login()==false ) ? "" : "before-login-user"; ?>" 
         style="border-right:1px solid #DADEDF;min-height: 39px; cursor: pointer;">
        <div  style="width:<?php echo $wow_class['width']; ?>px; margin: 0 auto;">
            <div class="seen-image col-lg-<?php echo $wow_class['class1']; ?>" style="text-align:right;margin-top:8px;" >
                <?php if($news->can_wow==1): ?>
                    <img width="20" src="<?php echo base_url("styles/layouts/tdsfront/images/social/wow.png"); ?>" />
                <?php else: ?>
                    <img width="20" src="<?php echo base_url("styles/layouts/tdsfront/images/social/wow-hover.png"); ?>" />
                <?php endif; ?>    
            
            </div>
            <div class="seen col-lg-<?php echo $wow_class['class2']; ?>" style="margin-top:8px;" ><span class="f2" style="font-size:12px;margin-left:5px;margin-top:6px;color:#666">WoW <?php echo $wow_class['new_count']; ?></span></div>
        </div>
    </div>
    <div class="col-lg-4" style="border-right:1px solid #DADEDF; min-height: 39px;">
            <div style="width:<?php echo $seen_class['width']; ?>px; margin: 0 auto;">
                <div class="seen-image col-lg-<?php echo $seen_class['class1']; ?>" style="text-align:right;margin-top:8px;" ><img src="<?php echo base_url("styles/layouts/tdsfront/images/social/seen.png"); ?>" /></div>
                <div class="seen col-lg-<?php echo $seen_class['class2']; ?>" style="margin-top:8px;" ><span  class="f2"  style="font-size:12px;margin-left:5px;margin-top:6px;color:#666">Seen <?php echo $seen_class['new_count']; ?></span></div>
            </div>
    </div>
    
    <div class="col-lg-4" >
        <div style="margin:9px auto !important; float:none !important; display:block !important;"  data="read_later" id="read_later_<?php echo $news->post_id; ?>" class="f2 read_later <?php echo ( free_user_logged_in() ) ? "" : "before-login-user"; ?>">&nbsp;&nbsp;&nbsp;Read Later</div>
    </div>
 </div>   