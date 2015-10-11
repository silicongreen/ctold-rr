<div class="clearfix"></div>

<div class="second-header-wrapper">
    <div class="second-header">

        <div class="icon-wrapper">
            <ul>
            <?php if(get_free_user_session('paid_id') && get_free_user_session('paid_school_code')) { ?>
                <li data="magic_mart" class="before-login-user-back" onclick="location.href='<?php echo $my_school_menu_uri; ?>'">
                    <div class="icon">
                        <img src="/styles/layouts/tdsfront/images/icon/second_header/diary21.png">
                    </div>
                    <div class="text">Diary21</div>
                </li>
            <?php } ?>

            <?php if( free_user_logged_in() ) { ?>
                <li onclick="location.href='<?php echo base_url('/good-read'); ?>'">
            <?php } else { ?>
                <li data="good_read" class="before-login-user">
            <?php } ?>
                    <div class="icon">
                        <img src="/styles/layouts/tdsfront/images/icon/second_header/good_read.png" />
                    </div>
                    <div class="text">Good Read</div>
                </li>
                
                <li data="candle" class="<?php echo ( free_user_logged_in() ) ? 'candlepopup' : 'before-login-user'; ?>">
                    <div class="icon">
                        <img src="/styles/layouts/tdsfront/images/icon/second_header/candle.png" />
                    </div>
                    <div class="text">Candle</div>
                </li>
                
                <li data="magic_mart" class="before-login-user-back" onclick="location.href='<?php echo base_url() . 'schools'; ?>'">
                    <div class="icon">
                        <img src="/styles/layouts/tdsfront/images/icon/second_header/my_school.png" />
                    </div>
                    <div class="text">My School</div>
                </li>
                <?php if($this->config->config['android_app_dl_popup_show'] == true){ ?>
                <li data="android-app" class="pop-without-login">
                    <div class="icon">
                        <img src="/styles/layouts/tdsfront/images/icon/second_header/my_app.png" />
                    </div>
                    <div class="text">My App</div>
                </li>
                <?php } ?>
            </ul>
        </div>

        <div class="lang-wrapper" style="display:none;">
            <ul>
                <li data=""><div>All</div></li>
                <li data="ENG"><div>English</div></li>
                <li data="BAN"><div>বাংলা</div></li>
            </ul>
        </div>

    </div>
</div>

<style>
    .second-header-wrapper {
        background-color: #AAB2B5;
        float: left;
        margin: 80px 0 0 0;
        padding: 0;
        width: 100%;
    }
    .second-header {
        margin-left: auto;
        margin-right: auto;
        width: 62%;
    }
    .second-header ul  {
        list-style: outside none none;
        margin: 0;
    }
    .second-header ul li {
        cursor: pointer;
        display: inline;
        float: left;
        margin: 0;
        padding: 5px 0;
    }
    .second-header ul li div {
        color: #ffffff;
        float: left;
        font-size: 12px;
    }
    .second-header ul li:first-child {
        margin-left: 0;
    }
    .second-header ul li:last-child {
        margin-right: 0;
    }
    .icon-wrapper {
        float: left;
        top: 0;
        width: 69%;
    }
    .icon-wrapper ul {

    }
    .icon-wrapper ul li {
        background-color: #7a8388;
        box-shadow: 1px 9px 10px -10px #222 inset;
        -moz-box-shadow: 1px 9px 10px -10px #222 inset;
        -webkit-box-shadow: 1px 9px 10px -10px #222 inset;
        -ms-box-shadow: 1px 9px 10px -10px #222 inset;
        -o-box-shadow: 1px 9px 10px -10px #222 inset;
        margin-left: 1px;
        width: 19%;

        transition: all 200ms ease-in-out;
        -moz-transition: all 200ms ease-in-out;
        -webkit-transition: all 200ms ease-in-out;
        -ms-transition: all 200ms ease-in-out;
        -o-transition: all 200ms ease-in-out;
    }
    .icon-wrapper ul li:hover {
        background-color: #DC3434;
    }
    .icon-wrapper ul li div.icon {
        width: 30%;
    }
    .icon-wrapper ul li div.icon img {
        padding: 3px;
        width: 90%;
    }
    .icon-wrapper ul li div.text {
        padding-top: 6px;
        width: 65%;
    }

    .lang-wrapper {
        float: right;
        width: 30%;
    }
    .lang-wrapper ul {

    }
    .lang-wrapper ul li {
        padding: 11px 8px;
        position: relative;
        width: 25%;
    }
    .lang-wrapper ul li:first-child {
        width: 15%;
    }
    .lang-wrapper ul li:last-child {
        width: 20%;
    }
    .lang-wrapper ul li div {
        display: block;
        text-align: center;
        width: 100%;
    }

    .lang-wrapper ul li.active {
        background: #DC3434;
    }
    .lang-wrapper ul li.active:after {
        top: 100%;
        left: 50%;
        border: solid transparent;
        content: " ";
        height: 0;
        width: 0;
        position: absolute;
        pointer-events: none;
        border-color: rgba(220, 52, 52, 0);
        border-top-color: #DC3434;
        border-width: 10px;
        margin-left: -10px;
    }
    .lang-wrapper ul li:hover {
        background: #DC3434;
    }
    .lang-wrapper ul li:hover:after {
        top: 100%;
        left: 50%;
        border: solid transparent;
        content: " ";
        height: 0;
        width: 0;
        position: absolute;
        pointer-events: none;
        border-color: rgba(220, 52, 52, 0);
        border-top-color: #DC3434;
        border-width: 10px;
        margin-left: -10px;
    }
</style>
