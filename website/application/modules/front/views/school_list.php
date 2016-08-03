<?php $s_ci_key = (isset($ci_key)) ? $ci_key : NULL; ?>

<div class="container" style="width: 77%;min-height:250px;">	 
    <div style="margin:30px 20px;height:60px;">
        <div style="float:left">
            <h2 class="f2">School Information</h2>
        </div>
    </div>
    <?php
    $widget = new Widget;
    $widget->run('champs21schoolsearch', $ci_key);
    ?>
    <div class="clearfix"></div>
    <div>
        <ul style="margin: 30px 20px;">
            <li>
                <div class="srch_page_title">
                    <span style="color:#dadada">Our featured</span><span style="color:#60cb97">&nbsp;SCHOOLS</span>
                </div>
            </li>
            <?php foreach ($schooldata as $row) : ?>
                <?php
//                if (isset($row['picture']) && $row['picture']) {
//                    $row['logo'] = $row['picture'];
//                }
                ?>
                <li style="list-style:none;">
                    <div class="srch_item_container" <?php echo ( isset($row['is_paid']) && ($row['is_paid'] == 1) ) ? 'style="background-color: #FFF1E0"' : ''; ?>>
                        <div class="srch_item_pic">
                            <!--<img src="<?php // echo base_url($row['logo']); ?>" width="220">-->
                            <img src="http://schools.champs21.com/elements/images/uploads/<?php echo $row['sites_id'] .'_' . $row['sites_cover']; ?>" width="220">
                        </div>
                        <div class="srch_item_info">
                            <p class="f2 s1"><a style="color:#60cb97;" href="<?php echo base_url() . 'schools/' . sanitize($row['sites_name']); ?>"><?php echo $row['sites_name']; ?></a></p>                            
                            <p class="f5 s2" style="color:#9CD64E;"><?php echo $row['sites_medium'] . ', ' . $row['sites_level']; ?></p>
                            <p class="f5 s3" style="color:#000;"><?php echo $row['sites_address']; ?></p>
<!--                            <p class="btn_item_visit">
                                <a style="color:#60cb97;" href="<?php // echo base_url() . 'schools/' . sanitize($row['name']); ?>">
                                    <button class="red" type="button" style="width:20%;">
                                        <span class="clearfix f2">
                                            Visit
                                        </span>
                                    </button>
                                </a>
                            </p>-->
                        </div>

                        <?php
//                        $ex_class = ' before-login-user';
//                        $str_join_btn_text = 'Join In +';
//                        if (free_user_logged_in()) {
//                            if (!isset($user_school_status[$row['id']])) {
//                                $ex_class = ' btn_user_join_school';
//                            } else {
//                                if ($user_school_status[$row['id']] == '1') {
//                                    $ex_class = ' btn_leave_school';
//                                    $str_join_btn_text = 'Leave';
//                                }
//                                if ($user_school_status[$row['id']] == '0') {
//                                    $ex_class = ' processing';
//                                    $str_join_btn_text = 'Processing';
//                                }
//                            }
//                        }
                        ?>

                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<style type="text/css">

    #backgroundPopup { 
        z-index:5000;
        position: fixed;
        display:none;
        height:100%;
        width:100%;
        background:#000000;	
        top:0px;
        left:0px;
    }
    #toPopup {
        font-family: "lucida grande",tahoma,verdana,arial,sans-serif;
        background: none repeat scroll 0 0 #FFFFFF;
        padding: 40px 20px !important;	
        border-radius: 3px 3px 3px 3px;
        color: #333333;
        display: block !important;
        font-size: 14px;
        position: relative !important;
        left: 0px !important;
        top: 0px !important;
        width: 96.2% !important;
        margin:30px 20px !important;
    }
    div.loader {
        background: url("../merapi/img/bx_loader.gif") no-repeat scroll 0 0 transparent;
        height: 32px;
        width: 32px;
        display: none;
        z-index: 9999;
        top: 40%;
        left: 50%;
        position: absolute;
        margin-left: -10px;
    }
    div.close {
        background: url("../merapi/img/close.png") no-repeat scroll 0 0 transparent;
        bottom: 30px;
        cursor: pointer;
        float: right;
        height: 30px;
        left: 10px;
        position: relative;
        width: 31px;
        display:none !important;
    }
    .join-wrapper{
        float: left;
        width: 30%;
        margin-top: 30px;
        text-align: right;
    }
    .red{
        background-color: #BBBBBB;
        border: none;
        line-height: 15px;
        color: #fff;
        height: 40px;
        width: 50%;
        box-shadow: 0 3px 2px 0 #bbb;
        -o-box-shadow: 0 3px 2px 0 #bbb;
        -ms-box-shadow: 0 3px 2px 0 #bbb;
        -moz-box-shadow: 0 3px 2px 0 #bbb;
        -webkit-box-shadow: 0 3px 2px 0 #bbb;
    }
    .red:hover{
        background-color: #60cb97;
        -webkit-transition: background-color 0.5s ease;
        -moz-transition: background-color 0.5s ease;
        -o-transition: background-color 0.5s ease;
        -ms-transition: background-color 0.5s ease;
        transition: background-color 0.5s ease;
    }
    .email_txt{
        background-color: #adb2b5;
        border-radius: 0px;
        -moz-border-radius: 0px;
        -webkit-border-radius: 0px;
        -o-border-radius: 0px;
        -ms-border-radius: 0px;
        color: #000000 !important;
        font-size: 13px !important;
        height: 45px !important;
        width: 49.52%;
    }
    .center{
        margin-left: auto;
        margin-right: auto;
        padding-top: 15px;
        text-align: center;
        width: 100%;
    }
    fieldset input.custom_checkbox[type="radio"] {
        clear: both;
        cursor: pointer;
        display: block;
        float: left;
        height: 15px;
        margin-top: 8px !important;
        width: 15px;
    }
    .large{
        width: 100%;
    }
    .processing {
        background-image: url('/styles/layouts/tdsfront/image/processing.png');
        background-position: right 7px center;
        background-repeat: no-repeat;
        background-size: 25px auto;
        text-align: left;
    }

    .srch_page_title
    {
        background: #fff;padding: 30px 20px;font-size:35px;font-family:'Bree Serif';margin-top:-40px;
    }
    .srch_item_container
    {
        background:#FFF;padding:20px;height:200px;overflow:hidden;
    }
    .srch_item_pic
    {
        float:left;width:25%;height:160px;overflow: hidden;
    }
    .srch_item_info
    {
        float:left;width:45%;
    }
    .srch_item_info .s1
    {
        font-size:22px;
    }
    .srch_item_info .s2
    {
        font-size:16px;
    }
    .srch_item_info .s3
    {
        font-size:14px;
    }
    .srch_item_info .btn_item_visit
    {
        display: block;
    }
    @media all and (min-width: 200px) and (max-width: 314px) {
        .srch_page_title
        {
            background: #fff;padding: 15px 15px;font-size:15px;font-family:'Bree Serif';margin-top:0px;
        }
        .srch_item_container
        {
            background:#FFF;padding:20px;height:auto;overflow:hidden;
        }
        .srch_item_pic
        {
            float:none;width:100%;height:160px;overflow: hidden;
        }
        .srch_item_info
        {
            float:none;width:100%;
        }
        .srch_item_info .s1
        {
            font-size:15px;
        }
        .srch_item_info .s2
        {
            font-size:12px;
        }
        .srch_item_info .s3
        {
            font-size:12px;
        }
        .srch_item_info .btn_item_visit
        {
            display: none;
        }
        .join-wrapper{
            float:none;width:100%;
            margin-top: 30px;
            text-align: right;
        }
    }
</style>