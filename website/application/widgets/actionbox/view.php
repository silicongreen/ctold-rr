
<?php
$seen_class = getclassactionbox($news->user_view_count);
$wow_class = getclassactionbox($news->wow_count);

?>

<div class="action-box" style="background:#F7F7F7">
    
    <div data="wow" id="wow_<?php echo $news->post_id; ?>" class="<?php if($news->can_wow==1): ?>wow_class<?php endif; ?> col-lg-4 <?php echo ( free_user_logged_in() || wow_login()==false ) ? "" : "before-login-user"; ?>" 
         style="border-right:1px solid #DADEDF;min-height: 39px; cursor: pointer;">
        <div>
            <div class="seen-image col-lg-<?php echo $wow_class['class1']; ?>" style="text-align:right;margin-top:8px;" >
                <?php if($news->can_wow==1): ?>
                <img class="no_toolbar" width="20" src="<?php echo base_url("styles/layouts/tdsfront/images/social/wow.png"); ?>" />
                <?php else: ?>
                    <img  class="no_toolbar" width="20" src="<?php echo base_url("styles/layouts/tdsfront/images/social/wow-hover.png"); ?>" />
                <?php endif; ?>    
            
            </div>
            <div class="seen col-lg-<?php echo $wow_class['class2']; ?>" style="margin-top:8px;" ><span class="f2" style="font-size:12px;margin-left:5px;margin-top:6px;color:#666">WoW <?php echo $wow_class['new_count']; ?></span></div>
        </div>
    </div>
    <div class="share_class addthis_toolbox col-lg-4" id="share_<?php echo $news->post_id; ?>" style="border-right:1px solid #DADEDF; min-height: 39px;">
        <a class="addthis_button_compact" id="addthisbutton_<?php echo $news->post_id; ?>" <?php if($news->can_share==1): ?> style="display:none;" <?php endif; ?> addthis:url="<?php echo create_link_url(NULL, $news->headline,$news->post_id,false,true,false); ?>" addthis:title="<?php echo $news->headline; ?>" id="full_leader_board">
            <div class="seen-image col-lg-2" style="text-align:right;margin-top:12px;margin-left:20%;" ><img  class="no_toolbar"  src="<?php echo base_url("styles/layouts/tdsfront/images/social/share_minicon_normal.png"); ?>" /></div>
            <div class="seen col-lg-7" >
                <span class="clearfix f2" style="float:left;font-size:12px;margin-left:8px;margin-top:8px;color:#666">
                    Share
                </span>
            </div>    
        </a> 
        <?php if($news->can_share==1): ?> 
            <a id="school_share_<?php echo $news->post_id; ?>"  href="javascript:void(0);" onclick="sharebrowser(<?php echo $news->post_id;?>)">
                <div class="seen-image col-lg-2" style="text-align:right;margin-top:12px;margin-left:20%;" ><img  class="no_toolbar"  src="<?php echo base_url("styles/layouts/tdsfront/images/social/share_minicon_normal.png"); ?>" /></div>
                    <div class="seen col-lg-7" >
                        <span class="clearfix f2" style="float:left;font-size:12px;margin-left:8px;margin-top:8px;color:#666">
                            Share
                        </span>
                    </div>    
            </a>
        <?php endif; ?>
    </div>
    
    <div class="col-lg-4">
        <?php            
            if($target == "good_read"):
                if($category_id > 0)
                {}
                else {$category_id = 0;}
        ?>
        <div style="margin:9px auto; float:none; display:block;"  data="read_later" id="read_later_remove_<?php echo $news->post_id; ?>_<?php echo $category_id; ?>" class="f2 read_later_remove <?php echo ( free_user_logged_in() ) ? "" : "before-login-user"; ?>">&nbsp;&nbsp;&nbsp;Remove</div>
        <?php else: ?>
        <div style="margin:9px auto; float:none; display:block;"  data="read_later" id="read_later_<?php echo $news->post_id; ?>" class="f2 read_later <?php echo ( free_user_logged_in() ) ? "" : "before-login-user"; ?>">&nbsp;&nbsp;&nbsp;Read Later</div>
        <?php endif; ?>
    </div>
 </div>