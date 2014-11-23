<?php $s_ci_key = (isset($ci_key)) ? $ci_key : NULL; ?>

<div class="container" style="width: 77%;min-height:250px;">	 
    <div style="margin:30px 20px;height:60px;">
        <div style="float:left">
            <h2 class="f2">School Information</h2>
        </div>
        <div class="header-bg" style="display:block;float:right;  margin-top:5px;">
            <div style="float: left;margin:5px;">                
                <form method="get" class="searchform" action="<?php echo base_url('search'); ?>" role="search">                    
                    <input class="field" name="s" id="s" class='search' placeholder="Search this site" type="search" style="border-radius: 6px; -moz-border-radius: 6px; -webkit-border-radius: 6px; width: 220px; margin-top: 3px;">
                    <input class="submit search-button" value="" type="submit" />
                </form>                
            </div>
        </div>
    </div>
    <?php
    $widget = new Widget;
    $widget->run('champs21schoolsearch', $ci_key);
    ?>
    <div style="">
        <ul style="margin: 30px 20px;">
            <?php foreach ($schooldata as $row) : ?>
                <?php
                if (isset($row['picture']) && $row['picture']) {
                    $row['logo'] = $row['picture'];
                }
                ?>
                <li style="list-style:none;">
                    <div style="background: #FFF; padding: 20px; height: 200px; overflow: hidden;">
                        <div style="float:left;width:20%;">
                            <img src="<?php echo base_url($row['logo']); ?>" width="160">
                        </div>
                        <div style="float:left;width:50%;">
                            <p class="f2" style="font-size:30px;"><a href="<?php echo base_url() . 'schools/' . sanitize($row['name']); ?>"><?php echo $row['name']; ?></a></p>
                            <p class="f5" style="font-size:16px;color:#9CD64E;"><?php echo $row['district']; ?></p>
                            <p class="f5" style="font-size:16px;color:#000;"><?php echo $row['medium']; ?></p>
                            <p class="f5" style="font-size:16px;color:#000;"><?php echo $row['level']; ?></p>
                            <p class="f5" style="font-size:14px;"><?php echo $row['location']; ?></p>
                        </div>
                        
                        <div class="join-wrapper">
                            <button id="<?php echo $row['id']; ?>" data="school_join" class="red <?php echo (free_user_logged_in()) ? 'btn_user_join_school' : 'before-login-user'; ?>" type="button">
                                <span class="clearfix f2">
                                    Join In
                                </span>
                            </button>
                        </div>
                        
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<?php if(free_user_logged_in()) { ?>
<div id="school_join_frm_wrapper" style="display: none;">
    
    <?php echo form_open('', array('class' => 'validate_form', 'id' => 'school_join_frm', 'enctype' => 'multipart/form-data', 'autocomplete' => 'off')); ?>

        <div class="clearfix" style="margin-left: auto; margin-right: auto; width: 90%; margin-top: 0px; ">

            <div>
                
                <input type="hidden" id="schl_id" name="schl_id" value="" />
                
                <fieldset class="reg_logo">
                    <div>
                        <img src="<?php echo base_url('styles/layouts/tdsfront/image/magicmart_red.png'); ?>" width="60px" alt="Chmaps21.com" />
                    </div>
                </fieldset>

                <fieldset class="reg_title f2">
                    <div>
                         <?php echo 'Join To Your School'; ?>
                        <div class="clearfix horizontal-line"></div>
                    </div>
                </fieldset>

                <?php if ( empty($model->user_type) || ($model->user_type == 1) ) { ?>
                    <fieldset class="hell_box">

                        <div style="text-align: left; padding: 15px 0 0 51px;">
                            <label class="user_type_dialob_label">I am a... </label>
                        </div>

                        <div class="user_type_div">
                            <ul class="radio-holder">
                                <?php $i = 0; foreach ($join_user_types as $key => $value) { ?>
                                    <li class="user_type_radio" <?php echo ($i > 0) ? 'style="padding-left: 60px !important;"' : '' ?>>
                                        <input class="css-checkbox" id="<?php echo $value; ?>" name="user_type" value="<?php echo $key; ?>" type="radio" 
                                            <?php

                                            if (($model->user_type >= 0 ) && ($key == $model->user_type)) {
                                                echo 'checked="checked"';
                                            } else {
                                                echo ($key == 0) ? 'checked="checked"' : '';
                                            }
                                            ?>
                                        >
                                        <label for="<?php echo $value; ?>" class="css-label"></label>
                                        <label for="<?php echo $value; ?>" class="user_type_label"><?php echo $value; ?></label>
                                    </li>
                                <?php $i; } ?>
                            </ul>
                        </div>
                    </fieldset>

                    <div class="clearfix" <?php echo ($edit) ? 'style="margin-bottom: 30px"' : ''; ?>></div>
                <?php } ?>

                <fieldset class="grades_ul">
                    <label class="select_grades">Select Grades <span>(you may choose multiple grades)</span></label>
                    <ul class="radio-holder">
                        <?php $i = 0; foreach ($grades as $value) { ?>
                            <li<?php echo ($i == 0) ? ' style="padding-left: 20px !important;"' : ''; ?>>
                                <input class="custom_checkbox" id="<?php echo $value->id; ?>" name="grade_ids[]" value="<?php echo $value->id; ?>" type="checkbox"
                                    <?php
                                        $ar_grade_ids = explode(',', $model->grade_ids);

                                        if ($edit && in_array($value->id, $ar_grade_ids)) {
                                            echo 'checked="checked"';
                                        }
                                    ?>
                                >
                                <label class="checkbox_label_txt" for="<?php echo $value->id; ?>"><?php echo $value->grade_name; ?></label>
                            </li>
                        <?php $i; } ?>
                    </ul>
                </fieldset>
                
                <fieldset>
                    <div class="center">
                        <input placeholder="Admission NO." class="f5 email_txt" id="admission_no" name="admission_no" value="" type="text" maxlength="15" />
                        <input placeholder="Class Roll NO." class="f5 email_txt" id="roll_no" name="roll_no" value="" type="text" maxlength="15" />
                    </div>
                </fieldset>
                    
            </div>

<!--            <div class="clearfix" style="margin-left: auto; margin-right: auto; margin-top: 20px; text-align: center; margin-bottom: 20px;">
                <button id="btn_submit_user_join" class="red" type="submit">
                    <span class="clearfix f2">
                        <?php echo 'Join In'; ?>
                    </span>
                </button>
            </div>-->

        </div>

    <?php echo form_close(); ?>
</div>
<?php } ?>

<style>
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
        width: 96% !important;
        z-index: 6000 !important;
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
        background-color: #DE3427;
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
        background-color: #BBBBBB;
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
        width: 49%;
    }
    .center{
        margin-left: auto;
        margin-right: auto;
        padding-top: 15px;
        text-align: center;
        width: 100%;
    }
</style>