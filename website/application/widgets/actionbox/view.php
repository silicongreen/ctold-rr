
<?php
$seen_class = getclassactionbox($news->user_view_count);
$wow_class = getclassactionbox($news->wow_count);
?>

<div class="action-box">

    <div data="wow" id="wow_<?php echo $news->post_id; ?>" class="<?php if ($news->can_wow == 1): ?>wow_class<?php endif; ?> col-lg-4 <?php echo ( free_user_logged_in() || wow_login() == false ) ? "" : "before-login-user"; ?> action-box-wrapper">
        <div class="seen-image col-lg-<?php echo $wow_class['class1']; ?>">
            <?php if ($news->can_wow == 1): ?>
                <img class="no_toolbar" width="20" src="<?php echo base_url("styles/layouts/tdsfront/images/social/wow.png"); ?>" />
            <?php else: ?>
                <img  class="no_toolbar" width="20" src="<?php echo base_url("styles/layouts/tdsfront/images/social/wow-hover.png"); ?>" />
            <?php endif; ?>    

        </div>
        <div class="seen col-lg-<?php echo $wow_class['class2']; ?>">
            <span class="f2">WoW <?php echo $wow_class['new_count']; ?></span>
        </div>
    </div>

    <div class="share_class addthis_toolbox col-lg-4 action-box-wrapper" id="share_<?php echo $news->post_id; ?>">
        <a class="addthis_button_compact" id="addthisbutton_<?php echo $news->post_id; ?>" <?php if ($news->can_share == 1): ?> style="display:none;" <?php endif; ?> addthis:url="<?php echo create_link_url(NULL, $news->headline, $news->post_id, false, true, false); ?>" addthis:title="<?php echo $news->headline; ?>" id="full_leader_board">
            <div class="seen-image col-lg-4">
                <img style="height: 37px; width: 20px;" class="no_toolbar" src="<?php echo base_url("styles/layouts/tdsfront/images/social/share_minicon_normal.png"); ?>" />
            </div>
            <div class="seen col-lg-8">
                <span class="clearfix f2">Share</span>
            </div>    
        </a> 
        <?php if ($news->can_share == 1): ?> 
            <a id="school_share_<?php echo $news->post_id; ?>"  href="javascript:void(0);" onclick="sharebrowser(<?php echo $news->post_id; ?>)">
                <div class="seen-image col-lg-4">
                    <img style="height: 37px; width: 20px;" class="no_toolbar"  src="<?php echo base_url("styles/layouts/tdsfront/images/social/share_minicon_normal.png"); ?>" />
                </div>
                <div class="seen col-lg-8">
                    <span class="clearfix f2">Share</span>
                </div>    
            </a>
        <?php endif; ?>
    </div>

    <?php
    if ($target == "good_read") {
        if ($category_id > 0) {
            
        } else {
            $category_id = 0;
        }
        $str_div_id = 'read_later_remove_' . $news->post_id . '_' . $category_id;
        $str_div_cl = 'read_later_remove';
        $str_icn = 'user_trash.png';
        $str_text = 'Remove';
        ?>

        <?php
    } else {
        $str_div_id = 'read_later_' . $news->post_id;
        $str_div_cl = 'read_later';
        $str_icn = 'read_later.png';
        $str_text = 'Read Later';
        ?>

    <?php } ?>

    <div data="read_later" id="<?php echo $str_div_id; ?>" class="col-lg-4 action-box-wrapper <?php echo $str_div_cl; ?> <?php echo ( free_user_logged_in() ) ? "" : "before-login-user"; ?>">
        <div class="seen-image col-lg-4">
            <img class="no_toolbar" width="20" src="<?php echo base_url("/merapi/img/{$str_icn}"); ?>" />
        </div>
        <div class="seen col-lg-8">
            <span class="f2"><?php echo $str_text; ?></span>
        </div>
    </div>

</div>
