<div class="col-md-12 social-bar row">

    <div class="col-md-2 box-5">                    
        <div class="seen-image f2">
            <img src="<?php echo base_url("styles/layouts/tdsfront/images/social/seen.png"); ?>" style="margin-left: 5px !important; margin-top: 3px;" />
            <span>Seen</span>
        </div>
        <div class="seen"><h2 class=""><?php echo $user_view_count; ?></h2></div>
    </div>

    <div id="wow_<?php echo $post_id; ?>" class="wow_class_single col-md-2 box-5">
        <div class="seen-image f2">
            <img src="<?php echo base_url("styles/layouts/tdsfront/images/social/wow.png"); ?>" />
            <span>Wow</span>
        </div>
        <div class="seen"><h2 class=""><?php echo $wow_count; ?></h2></div>
    </div>

    <div id="share_<?php echo $post_id; ?>" class="share_class col-md-2 box-5">
        <a class="addthis_button_compact f2" id="addthisbutton_<?php echo $post_id; ?>" <?php if (can_sharepost($post_id) == 1): ?> style="display:none;" <?php endif; ?> addthis:url="<?php echo create_link_url(NULL, $headline, $post_id); ?>" addthis:title="<?php echo $headline; ?>" id="full_leader_board">
            <div class="seen-image">
                <img src="<?php echo base_url("styles/layouts/tdsfront/images/social/share_minicon_normal.png"); ?>" />
                <span>Share</span>
            </div>
            <div class="seen"></div>
        </a>
        <?php if (can_sharepost($post_id) == 1): ?> 
            <a class="f2" id="school_share_<?php echo $post_id; ?>" href="javascript:void(0);" onclick="sharebrowser(<?php echo $post_id; ?>)">
                <div class="seen-image">
                    <img src="<?php echo base_url("styles/layouts/tdsfront/images/social/share_minicon_normal.png"); ?>" />
                    <span>Share</span>
                </div>
                <div class="seen"></div>
            </a>
        <?php endif; ?>
    </div>

    <div class="col-md-2 box-5">

        <div class="col-md-12 language">
            <a href="#">
                <em class="active sports-inner-container-font12 f2">
                    <?php echo get_language($language); ?>
                </em> 
            </a>
            
            <?php if (!empty($other_language) && $s_lang) { ?>
                <?php
                $ar_lang = explode(",", $s_lang);
                foreach ($ar_lang as $lang) {
                    ?>
                    <?php $a_l = explode("-", $lang); ?>
                    <?php if (count($a_l) > 1 && $a_l[1] == 0) { ?>
                        <a href="<?php echo base_url(sanitize($main_headline) . '-' . $main_post_id); ?>">
                            <em class="sports-inner-container-font12 f2">
                                &nbsp;/
                                <?php echo get_language($a_l[0]); ?>
                            </em>
                        </a>
                    <?php } else {
                        $url_lang = ($post_id == $main_post_id) ? $a_l[0] : '';
                        ?>
                        <a href="<?php echo base_url( sanitize($main_headline) . '-' . $main_post_id . '/' . $url_lang ); ?>">
                            <em class="sports-inner-container-font12 f2">
                                &nbsp;/
                                <?php echo get_language($a_l[0]); ?>
                            </em>
                        </a>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
        </div>

    </div>

    <div class="col-md-2 box-5 good-read-column">
        <div class="good-read-button normal <?php echo ( free_user_logged_in() ) ? "" : "login-user"; ?>">
            <div class="good-read-image">
                <img src="<?php echo base_url("styles/layouts/tdsfront/images/social/good-read.png"); ?>" width="22" />
            </div>
            <?php echo $good_read_single; ?>
        </div>
        <div class="f2 normal good-read-label">Good Read</div>
    </div>

</div>

<style type="text/css">
    .wow_class_single .seen-image img {
        width: 17px;
    }
    .good-read-image img {
        width: 18px;
    }
    .seen-image{
        color: #b1b8ba;
        float: left;
        font-size: 12px;
        margin-right: 5px;
        padding: 10px 0;
        width: 52%;
    }
    .seen-image img {
        float: left;
        margin-left: 5px;
        width: 20px;
    }
    .seen-image span {
        float: left;
        margin-left: 5px;
    }
    .seen {
        float: left;
        margin-left: 0;
        width: 43%;
    }
    .seen h2{
        color: #b1b8ba;
        float: left;
        font-family: tahoma;
        font-size: 12px;
        line-height: 15px;
    }
    .good-read-text{
        float: left;
        height: 60px;
        padding-top: 2px;
    }
    .good-read-image{
        float: right;
        margin: 7px 13px 5px 0;
    }
    .good-read-text h2{
        color: #FFF;
        font-size: 25px;
        font-weight: 600;
    }
    .good-read-button{
        cursor: pointer;
        padding: 0;
    }
    .share_class .seen-image {
        width: 60%;
    }
    .share_class .seen {
        width: 20%;
    }
    .share_class a {
        position: relative;
    }
    .addthis_button_compact {
        display: block;
        float: left;
        width: 100%;
    }
</style>