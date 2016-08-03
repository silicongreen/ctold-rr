<div class="clearfix"></div>

<div class="second-header-wrapper second-header-wrapper-show">
    <div class="second-header">

        <div class="icon-wrapper">
            <ul>
                <?php if (get_free_user_session('paid_id') && get_free_user_session('paid_school_code')) { ?>
                    <li data="magic_mart" class="before-login-user-back" onclick="location.href = '<?php echo $my_school_menu_uri; ?>'" data-toggle="tooltip" data-placement="bottom" data-original-title="Your School Management System.">
                        <div class="icon">
                            <img src="/styles/layouts/tdsfront/images/icon/second_header/diary21.png">
                        </div>
                        <div class="text">Diary21</div>
                    </li>
                <?php } ?>

                <?php if (free_user_logged_in()) { ?>
                    <li onclick="location.href = '<?php echo base_url('/good-read'); ?>'" data-toggle="tooltip" data-placement="bottom" data-original-title="Save articles in your personal folder.">
                    <?php } else { ?>
                    <li data="good_read" class="before-login-user" data-toggle="tooltip" data-placement="bottom" data-original-title="Save articles in your personal folder.">
                    <?php } ?>
                    <div class="icon">
                        <img src="/styles/layouts/tdsfront/images/icon/second_header/good_read.png" />
                    </div>
                    <div class="text">Good Read</div>
                </li>

                <li data="candle" class="<?php echo ( free_user_logged_in() ) ? 'candlepopup' : 'before-login-user'; ?>" data-toggle="tooltip" data-placement="bottom" data-original-title="Write to us!">
                    <div class="icon">
                        <img src="/styles/layouts/tdsfront/images/icon/second_header/candle.png" />
                    </div>
                    <div class="text">Candle</div>
                </li>

                <li data="magic_mart" class="before-login-user-back" onclick="location.href = '<?php echo base_url() . 'schools'; ?>'" data-toggle="tooltip" data-placement="bottom" data-original-title="Search/Create school website.">
                    <div class="icon">
                        <img src="/styles/layouts/tdsfront/images/icon/second_header/my_school.png" />
                    </div>
                    <div class="text">My School</div>
                </li>
                <?php if ($this->config->config['android_app_dl_popup_show'] == true) { ?>
                    <li data="android-app" class="pop-without-login" data-toggle="tooltip" data-placement="bottom" data-original-title="Download Champs21 Android Application.">
                        <div class="icon">
                            <img src="/styles/layouts/tdsfront/images/icon/second_header/my_app.png" />
                        </div>
                        <div class="text">Android</div>
                    </li>
                <?php } ?>
            </ul>
        </div>

        <div class="lang-wrapper">
            <ul>
                <li data="BAN"><div>বাংলা</div></li>
                <li data="ENG"><div>English</div></li>
                <li data="" class="active"><div>All</div></li>
            </ul>
        </div>

    </div>
</div>

<style type="text/css" media="all">
    div.tooltip-inner {
        text-align: center;
        font-size: 12px;
        max-width: 280px;
        width: 280px;
    }
    .container {
        margin-top: 120px;
    }
    .second-header-wrapper-hide {
        visibility: hidden;
        opacity: 0;

        transition: all 300ms ease-in-out;
        -webkit-transition: all 300ms ease-in-out;
        -ms-transition: all 300ms ease-in-out;
        -moz-transition: all 300ms ease-in-out;
        -o-transition: all 300ms ease-in-out;
    }
    .second-header-wrapper-show {
        visibility: visible;
        opacity: 1;

        transition: all 300ms ease-in-out;
        -webkit-transition: all 300ms ease-in-out;
        -ms-transition: all 300ms ease-in-out;
        -moz-transition: all 300ms ease-in-out;
        -o-transition: all 300ms ease-in-out;
    }

    .second-header-wrapper {
        background-color: #AAB2B5;
        float: left;
        margin: 77px 0 0;
        padding: 0;
        position: fixed;
        width: 100%;
        z-index: 1;
    }
    .second-header {
        margin-left: auto;
        margin-right: auto;
        width: 73%;
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
        margin-right: 4px;
        width: 30%;
    }
    .lang-wrapper ul {
        float: right;
        width: 100%;
    }
    .lang-wrapper ul li {
        float: right;
        padding: 11px 8px;
        position: relative;
        width: 25%;
    }
    .lang-wrapper ul li:first-child {
        width: 18%;
    }
    .lang-wrapper ul li:last-child {
        width: 13%;
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

    @media all and (min-width: 1920px) {
        .icon-wrapper ul li div.icon {
            width: 25%;
        }
        .icon-wrapper ul li div.text {
            font-size: 16px;
            padding-top: 10px;
        }
        .lang-wrapper ul li {
            padding: 15px 8px;
        }
    }
    @media all and (min-width: 1680px) and (max-width: 1919px) {
        .icon-wrapper ul li div.icon {
            width: 27%;
        }
        .icon-wrapper ul li div.text {
            font-size: 14px;
            padding-top: 10px;
        }
        .lang-wrapper ul li {
            padding: 14px 8px;
        }
    }
    @media all and (min-width: 1551px) and (max-width: 1679px) {
        .icon-wrapper ul li div.icon {
            width: 29%;
        }
        .icon-wrapper ul li div.text {
            font-size: 14px;
            padding-top: 11px;
        }
        .lang-wrapper ul li {
            padding: 15px 8px;
        }
    }
    @media all and (min-width: 1440px) and (max-width: 1550px) {
        .icon-wrapper ul li div.icon {
            width: 30%;
        }
        .icon-wrapper ul li div.text {
            font-size: 14px;
            padding-top: 10px;
        }
        .lang-wrapper ul li {
            padding: 14px 8px;
        }
    }
    @media all and (min-width: 1333px) and (max-width: 1439px) {
        .icon-wrapper ul li div.icon {
            width: 29%;
        }
        .lang-wrapper ul li {
            padding: 12px 8px;
        }
    }
    @media all and (min-width: 1281px) and (max-width: 1332px) {
        .icon-wrapper ul li div.icon {
            width: 30%;
        }
        .icon-wrapper ul li div.text {
            font-size: 12px;
            padding-top: 8px;
        }
        .lang-wrapper ul li {
            padding: 12px 8px;
        }
    }
    @media all and (min-width: 1180px) and (max-width: 1280px) {
        .icon-wrapper ul li div.icon {
            width: 30%;
        }
        .icon-wrapper ul li div.text {
            font-size: 12px;
            padding-top: 8px;
        }
        .lang-wrapper ul li {
            padding: 12px 8px;
        }
    }
    @media all and (min-width: 1025px) and (max-width: 1179px) {
        .icon-wrapper ul li div.icon {
            width: 25%;
        }
        .icon-wrapper ul li div.icon img {
            padding: 2px 0;
            width: 90%;
        }

        .icon-wrapper ul li div.text {
            font-size: 11px;
            padding-top: 4px;
            width: 74%;
        }
        .lang-wrapper ul li {
            padding: 9px 0;
        }
        .lang-wrapper ul li div {
            font-size: 11px;
        }
    }
    @media all and (min-width: 950px) and (max-width: 1024px) {
        .icon-wrapper ul li div.icon {
            width: 25%;
        }
        .icon-wrapper ul li div.icon img {
            padding: 2px 0;
            width: 90%;
        }

        .icon-wrapper ul li div.text {
            font-size: 10px;
            padding-top: 3px;
            width: 74%;
        }
        .lang-wrapper ul li {
            padding: 7px 0;
        }
        .lang-wrapper ul li div {
            font-size: 10px;
        }
    }
    @media all and (min-width: 800px) and (max-width: 949px) {
        .header-logo-div {
            left: 0 !important;
            position: absolute !important;
            top: 6px !important;
            width: 20% !important;
        }
        .image-logo {
            width: 120px !important;
        }
        .second-header {
            width: 85%;
        }
        .icon-wrapper ul li div.icon {
            width: 25%;
        }
        .icon-wrapper ul li div.icon img {
            padding: 2px 0;
            width: 90%;
        }

        .icon-wrapper ul li div.text {
            font-size: 10px;
            padding-top: 3px;
            width: 74%;
        }
        .lang-wrapper ul li {
            padding: 7px 0;
        }
        .lang-wrapper ul li div {
            font-size: 10px;
        }
    }
</style>

<script type="text/javascript">

    var lastScrollTop = 0;
    $(window).scroll(function (event) {
        var st = $(this).scrollTop();
        if (st > lastScrollTop && st > 100) {
            $('.second-header-wrapper').removeClass('second-header-wrapper-show');
            $('.second-header-wrapper').addClass('second-header-wrapper-hide');
        } else {
            $('.second-header-wrapper').removeClass('second-header-wrapper-hide');
            $('.second-header-wrapper').addClass('second-header-wrapper-show');
        }
        lastScrollTop = st;
    });

</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.second-header ul li').tooltip();
    });
</script>