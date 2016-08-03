<header class="champs-header col-xs-12 clearfix">
    
    <?php if( get_free_user_session('paid_id') && get_free_user_session('paid_school_code')) { ?>
        <input type="hidden" id="paid_school_code" value="<?php echo get_free_user_session('paid_school_code'); ?>">
    <?php } ?>
    
    <?php if(!$user_profile_complete) { ?>
        <input type="hidden" name="user_profile_complete" id="user_profile_complete" value="nai">
    <?php } ?>
    
    <?php if($can_school_canlde) { ?>
        <input type="hidden" name="can_school_canlde" id="can_school_canlde" value="ase">
    <?php } ?>

    <div class="col-xs-12">
        <?php if (array_key_exists($ci_key_for_cover, $this->config->config['cover']) && $this->config->config['cover'][$ci_key_for_cover] ) : ?>
        <img src="<?php echo base_url($this->config->config['cover-image'][$ci_key_for_cover]); ?>" width="100%" class="image-logo" alt="logo">
        <?php elseif ((array_key_exists($ci_key_for_cover, $this->config->config['LOGO']) && $this->config->config['LOGO'][$ci_key_for_cover]) ||
                (array_key_exists("allpage", $this->config->config['LOGO']) && $this->config->config['LOGO']["allpage"])) : ?>
        <div class="header-new champs-header" style="background: #fff url('styles/layouts/tdsfront/images/doodle-f.png?v=<?php echo time();?>') no-repeat;background-size:cover;position: fixed; width: 100%;margin: 0px auto; height: 80px; padding: 18px 5px; z-index:1000;">
            
            <div class="logo-div">
                <a href="<?php echo base_url(); ?>" ><img  src="<?php echo base_url('styles/layouts/tdsfront/images/logo-new.png'); ?>" class="image-logo" alt="logo"></a>
            </div>
            <div style="float: left;width: 65%;height:50px;">
                
                
                    <?php
                    $widget = new Widget;
                    $widget->run('champs21newmenu');
                    ?> 
                
            </div>
            
            <?php if( free_user_logged_in() ): ?>
            <div class="header-logo-div">
                <a href="<?php echo base_url(); ?>" ><img  src="<?php echo base_url('styles/layouts/tdsfront/images/logo-new.png'); ?>" class="image-logo" alt="logo"></a>
            </div>
            <?php endif;?>
            
            <?php
            
                $has_profile_img = FALSE;
                //$profile_image_url = base_url('upload/free_user_profile_images/2.jpg');
                $profile_image_url = base_url('Profiler/images/right/nopic.png');
                
                if( free_user_logged_in() ){
                    $user_data = get_free_user_session();

                    if( !empty($user_data['profile_image'] )){
                        //$profile_image_url = base_url() . 'upload/free_user_profile_images/' . $user_data->profile_image;
                        $profile_image_url = $user_data['profile_image'];
                        $has_profile_img = TRUE;
                        
                    }else{
                        $_user_data = get_user_data();
                        
                        $user_data = get_free_user_session();
                            
                        if( !empty($_user_data->profile_image) ){
                            $profile_image_url = $_user_data->profile_image;
                            $has_profile_img = TRUE;
                        }
                        
                    }
                }
                
            ?>
            
            <div class="login_reg_div">
                <div class="search_box_head">
                    <div style="float: left; margin:10px 5px 5px 15px;">
                        <img src="/styles/layouts/tdsfront/image/search.png" />
                    </div>
                    
                    <div class="search-elm-holder-div" style="display: none;">
                        <div class="search-elm-list-div">
                            <ul class="">
                                <li style="cursor: pointer;">
                                    <input class="field" autocomplete="off" name="s" id="s-auto" class='search' placeholder="Search this site" type="search" style="width: 100%;">
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div id="divResult"></div>
                    
                </div>
                
                <div class="login_reg_div_box">
                    
                    <?php
//                        var_dump(free_user_logged_in());exit;
                    ?>
                    
                    <?php if( !free_user_logged_in() ){ ?>
                        <div id="settings_div" class="mobile_log_reg_box" style="float: right; float: right; margin: 10px 0 0; padding: 0px; background-color: #fff;">
                            <div style="" class="settings-btn"></div>
                        </div>
                        <div class="settings-elm-holder-div" style="display: none;">
                            <div class="settings-elm-list-div">
                                <ul class="">
                                    <li style="cursor: pointer;">
                                        <a title="Register Now" id="data" href="javascript:void(0);" class="register-user register-user-btn f2">Register Now</a>
                                    </li>
                                    <li style="cursor: pointer;">
                                        <a title="Login" id="data" href="javascript:void(0);" class="login-user login-user-btn f2">Login</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <ul class="tz_social">
                            <li style="cursor: pointer;">
                                <a title="Register Now" id="data" href="javascript:void(0);" class="register-user register-user-btn f2">Register Now</a>
                            </li>
                            <li style="cursor: pointer;">
                                <a title="Login" id="data" href="javascript:void(0);" class="login-user login-user-btn f2">Login</a>
                            </li>
                        </ul>
                    
                    <?php } else { ?>

                        <div id="profile_image_div" style="
                                    float: left; width: 46px;
                                    height: 44px;
                                    border-radius: 28px;
                                    -webkit-border-radius: 28px;
                                    -moz-border-radius: 28px;
                                    background: url(<?php echo $profile_image_url; ?>) no-repeat;
                                    background-position: 50%;
                                    background-size:125%;" ><!--mcr-all-player-mugshot img {
display: block;
width: 46px;
height: 46px;
/* border: 2px solid #dedede; */
-webkit-border-radius: 100%;
-moz-border-radius: 100%;
-ms-border-radius: 100%;
-o-border-radius: 100%;
/* border-radius: 100%; */
}-->
                           
                        </div>
                        <div class="user_profile_name" >
                            <h3 class="f2" style="font-size:15px;margin: 10px 0px;"><?php echo $user_data['nick_name']; ?></h3>
                        </div>

                        <div id="settings_div" style="float: right; float: right; margin: 10px 0 0; padding: 0px; background-color: #fff;">
                            <div style="" class="settings-btn"></div>
                        </div>

                        <div class="settings-elm-holder-div" style="display: none;">
                            <div class="mobile_profile_box" style="height:65px;background-color: #C82329;padding:10px;">
                                        <div id="profile_image_div_mobile" style="
                                                    float: left; width: 46px;
                                                    height: 44px;
                                                    border-radius: 28px;                                                    
                                                    -webkit-border-radius: 28px;
                                                    -moz-border-radius: 28px;
                                                    background: url(<?php echo $profile_image_url; ?>) no-repeat;
                                                    background-position: 50%;
                                                    background-size:125%;" >

                                        </div>
                                        <div class="user_profile_name_mobile" >
                                            <h3 class="f2" style="color:#fff;font-size:15px;margin: 10px 0px;"><?php echo $user_data['nick_name']; ?></h3>
                                        </div>
                            </div>
                            <div class="settings-elm-list-div">
                                <ul>                                    
                                    <li id="free_user_profile">
                                        <div class="settings-elm-list-profile"></div>
                                        <label>Update Profile</label>
                                    </li>
                                    
                                    <li id="free_user_profile_picture">
                                        <div class="settings-elm-list-profile-picture"></div>
                                        <label for="profile_image_file">Update Profile Picture</label>
                                    </li>
                                    
                                    <li id="pref_li">
                                        <div class="settings-elm-list-pref"></div>
                                        <label>Preference Settings</label>
                                    </li>
                                    
                                    <li id="logout_li">
                                        <div class="settings-elm-list-logout"></div>
                                        <label>Log Out</label>
                                    </li>                                    
                                </ul>
                            </div>
                        </div>
                        
                    <?php } ?>
                    <!--REGISTRATION-->
                </div>
                
                <div id="upload_profile_picture" style="display: none; " >                    
                    <input id="profile_image_file" type="file" name="profile_image" class="profile_image" />
                    <div id="prograssbar" style="display:none;margin-top:10px;background:#FFCCBA;width:0%; text-align: center; padding:10px 0px;">0%</div>
                </div>
                
                <!-- Registration Form -->
                <div id="frm_reg" style="display: none;">

                <?php
                    $action = base_url('register_user');
                    $frm_id = 'reg_frm';
                    if($edit){
                        $action = base_url('update_profile');
                        $frm_id = 'update_profile_frm';
                    }
                    $paid_schools = get_paid_school_droupdown();
                ?>

                <?php echo form_open($action, array('class' => 'validate_form', 'id' => $frm_id, 'enctype' => 'multipart/form-data', 'autocomplete' => 'off')); ?>

                    <div class="clearfix" style="margin-left: auto; margin-right: auto; width: 90%; margin-top: 0px; ">

                        <div>
                            
                            <fieldset class="reg_logo">
                                <div>
                                    <img src="<?php echo base_url('styles/layouts/tdsfront/image/register.png'); ?>" width="60px" alt="Chmaps21.com" />
                                </div>
                            </fieldset>

                            <fieldset class="reg_title f2">
                                <div>
                                     <?php echo ($edit) ? 'Update Your Profile' : 'Register'; ?>

                                    <?php if($edit){ ?>
                                        <div class="clearfix horizontal-line"></div>
                                     <?php }?>
                                </div>
                            </fieldset>

                            <?php if($edit){ ?>
                            <div style="text-align: center; font-size: 20px; padding: 15px 0px 20px;">Mark Your Preferred Name</div>
                            <?php }?>
                            
                            <?php if ( !$edit || empty($model->user_type) ) { ?>
                                <fieldset class="hell_box">
                                    
                                    <div style="text-align: left; padding: 15px 0 0 51px;">
                                        <label class="user_type_dialob_label">I am a... </label>
                                    </div>
                                    
                                    <div class="user_type_div">
                                        <ul class="radio-holder">
                                            <?php $i = 0; foreach ($free_user_types as $key => $value) { ?>
                                                <li class="user_type_radio" <?php echo ($i > 0) ? 'style="padding-left: 60px !important;"' : '' ?>>
                                                    <input class="css-checkbox user_type_class" id="<?php echo $value; ?>" name="user_type" value="<?php echo $key; ?>" type="radio" 
                                                        <?php

                                                        if ($edit && ($model->user_type >= 0 ) && ($key == $model->user_type)) {
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
                                <?php
                                    $this->load->config("huffas");
                                    if((isset($this->config->config['paid_registration']) && $this->config->config['paid_registration']) || $_GET['test']=="paid_school"):
                                 ?>
                            <fieldset id="paid_school_and_code_change" style="display:none;">

                                    <ul class="radio-holder drop-big" style="margin:10px 0 10px 33px;">    
                                        <li>
                                            <fieldset style="color:black;">Teacher/Student Of A Premium School (Optional)?</fieldset>

                                            <div class="custom_dropdown" style="float:left; width:100%;">
                                                <div style="float:left; width:70%;">
                                                   <?php echo $paid_schools; ?>
                                                </div>
                                                <input  style="float: left; width: 29%; margin-top: 9px; height:32px; border: 1px solid black;" placeholder="School Code" id="school_code" name="school_code" value="" type="text" maxlength="60" >
                                            </div>
                                            

                                        </li>
                                        
                                        
                                    </ul>
                                </fieldset>
                                <?php endif; ?>
                               

                                <div class="clearfix" <?php echo ($edit) ? 'style="margin-bottom: 30px"' : ''; ?>></div>
                            <?php } ?>

                            <?php if ( !$edit ) { ?>

        <!--                        <div class="sign_up_text">
                                    Sign up using email address
                                </div>-->

                                <fieldset>
                                    <div class="center">
                                        <input placeholder="Enter Email Address" class="email_txt" id="email" name="email" value="<?php echo $model->email; ?>" type="text" maxlength="60" <?php echo ($edit) ? 'readonly="readonly"' : ''; ?>>
                                        <input placeholder="Re-enter Email Address" class="email_txt" id="cnf_email" name="cnf_email" value="<?php echo $model->email; ?>" type="text" maxlength="60" <?php echo ($edit) ? 'readonly="readonly"' : ''; ?>>
                                    </div>
                                </fieldset>

                                <fieldset>
                                    <div class="center">
                                        <input placeholder="Enter Password (Minimum 6 Charecters)" class="email_txt" id="password" name="password" value="" type="password" maxlength="60" />
                                        <input placeholder="Re-enter Password" class="email_txt" id="cnf_password" name="cnf_password" value="" type="password" maxlength="60" />
                                    </div>
                                </fieldset>

                            <?php } ?>

                            <?php if ($edit) { ?>
                                <fieldset>

                                    <ul class="radio-holder" style="display: block;">
                                        <li>
                                            <div class="name_text_lable_div">
                                                <input class="css-checkbox" id="first_name" name="nick_name" value="1" type="radio" <?php echo ($model->nick_name == '1') ? 'checked="checked"' : ''; ?>>
                                                <label for="first_name" class="css-label"></label>
                                            </div>
                                            <label for="first_name" class="user_type_label">
                                                <input class="name_text" placeholder="First Name" id="first_name" name="first_name" value="<?php echo $model->first_name; ?>" type="text" maxlength="60">
                                            </label>
                                        </li>
                                        <li>
                                            <div class="name_text_lable_div">
                                                <input class="css-checkbox" id="middle_name" name="nick_name" value="2" type="radio" <?php echo ($model->nick_name == '2') ? 'checked="checked"' : ''; ?>>
                                                <label for="middle_name" class="css-label"></label>
                                            </div>
                                            <label for="middle_name" class="user_type_label">
                                                <input class="name_text" placeholder="Middle Name" id="middle_name" name="middle_name" value="<?php echo $model->middle_name; ?>" type="text" maxlength="60" >
                                            </label>
                                        </li>
                                        <li>
                                            <div class="name_text_lable_div">
                                                <input class="css-checkbox" id="last_name" name="nick_name" value="3" type="radio" <?php echo ($model->nick_name == '3') ? 'checked="checked"' : ''; ?>>
                                                <label for="last_name" class="css-label"></label>
                                            </div>
                                            <label for="last_name" class="user_type_label">
                                                <input class="name_text" placeholder="Last Name" id="last_name" name="last_name" value="<?php echo $model->last_name; ?>" type="text" maxlength="60">
                                            </label>
                                        </li>
                                    </ul>

                                </fieldset>

                                <fieldset>

                                    <ul class="radio-holder">
                                        <li>
                                            <div class="custom_dropdown">
                                                <div>
                                                    <?php
                                                    //$class_string = '';
                                                    $class_string = 'id="tds_country_id" class="droppify"';
                                                    echo form_dropdown('tds_country_id', $country, $country['id'], $class_string);
                                                    ?>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <input class="name_text" placeholder="District" id="district" name="district" value="<?php echo $model->district; ?>" type="text" maxlength="60">
                                        </li>
                                    </ul>

                                </fieldset>

                                <fieldset>

                                    <ul class="radio-holder">    
                                        <li>
                                            <fieldset>Division</fieldset>

                                            <div class="custom_dropdown_dob">
                                                <div>
                                                    <?php                                                    
                                                    $division[NULL] = $model->division;

                                                    $divsion_data['Dhaka'] = 'Dhaka';
                                                    $divsion_data['Chittagong'] = 'Chittagong';
                                                    $divsion_data['Rajshahi'] = 'Rajshahi';
                                                    $divsion_data['Khulna'] = 'Khulna';
                                                    $divsion_data['Sylhet'] = 'Sylhet';
                                                    $divsion_data['Rangpur'] = 'Rangpur';
                                                    $divsion_data['Barisal'] = 'Barisal';
                                                    

                                                    $class_string = 'id="division" class="droppify"';
                                                    echo form_dropdown('division',$divsion_data, $division,  $class_string);
                                                    ?>
                                                </div>
                                            </div>

                                        </li>
                                        <li>
                                            <input placeholder="880" class="name_text country_code" id="country_code" name="country_code" value="" type="text" maxlength="6">
                                        </li>
                                        <li>
                                            <input placeholder="Mobile Number" class="name_text mobile_no" id="mobile_no" name="mobile_no" value="<?php echo $model->mobile_no; ?>" type="text" maxlength="60">
                                        </li>
                                    </ul>
                                </fieldset>

                                <fieldset>

                                    <ul class="radio-holder">
                                        <li>
                                            <div class="custom_dropdown_dob">
                                                <div>
                                                    <?php
                                                    $dob_day = NULL;
                                                    $dob_month = NULL;
                                                    $dob_year = NULL;

                                                    if (isset($model->dob) && !empty($model->dob) && ($model->dob != '0000-00-00')) {
                                                        $ar_dob = explode('-', $model->dob);

                                                        $dob_day = $ar_dob[2];
                                                        $dob_month = $ar_dob[1];
                                                        $dob_year = $ar_dob[0];
                                                    }

                                                    $days[NULL] = 'Day';
                                                    for ($i = 1; $i <= 31; $i++) {
                                                        if (strlen($i) < 2) {
                                                            $i = '0' . $i;
                                                        }
                                                        $days[$i] = $i;
                                                    }

                                                    $class_string = 'id="dob_day" class="droppify"';
                                                    echo form_dropdown('dob_day', $days, $dob_day, $class_string);
                                                    ?>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="custom_dropdown_dob">
                                                <div>
                                                    <?php

                                                    $months[NULL] = 'Month';
                                                    for($i = 1; $i <= 12; $i++){
                                                        if(strlen($i) < 2){
                                                            $i = '0'.$i;
                                                        }
                                                        $months[$i] = $i;
                                                    }

                                                    $class_string = 'id="dob_month" class="droppify"';
                                                    echo form_dropdown('dob_month', $months, $dob_month, $class_string);
                                                    ?>
                                                <div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="custom_dropdown_dob">
                                                <div>
                                                    <?php

                                                    $year = date('Y');

                                                    $years[NULL] = 'Year';

                                                    $num = $year - 70;

                                                    for($i = $year; $i >= $num; $i--){
                                                        if(strlen($i) < 2){
                                                            $i = '0'.$i;
                                                        }
                                                        $years[$i] = $i;
                                                    }

                                                    $class_string = 'id="dob_year" class="droppify"';
                                                    echo form_dropdown('dob_year', $years, $dob_year, $class_string);
                                                    ?>
                                                <div>
                                            </div>
                                        </li>

                                    </ul>

                                </fieldset>
                                
                                <?php if( ($user_data['type'] == 1) || ($user_data['type'] == 4) ) { ?>
                                <fieldset>
                                    <div class="selectMedium">
                                        <?php
                                        $ar_input_data =array(
                                            'class' => 'text_field',
                                            'placeholder' => 'Occupation',
                                            'maxlength' => '255',
                                            'size' => '560',
                                            'name' => 'occupation',
                                            'value' => $model->occupation,
                                        );
                                        echo form_input($ar_input_data);
                                        ?>
                                    </div>
                                </fieldset>
                                <?php } ?>

                                <?php if( ($user_data['type'] == 2) || ($user_data['type'] == 3) ) { ?>
                                <fieldset>
                                    <div class="selectMedium" style="margin-bottom:20px;">
                                        <?php
                                        $ar_input_data =array(
                                            'class' => 'text_field_school',
                                            'id' => 'search-box1',
                                            'placeholder' => 'School Name',
                                            'maxlength' => '255',
                                            'size' => '560',
                                            'name' => 'school_name',
                                            'value' => $model->school_name,
                                        );
                                        echo form_input($ar_input_data);
                                        ?>
                                    
                                                    <div id="suggesstion-box1"></div>
                                    </div>
                                </fieldset>

                                <?php if( $user_data['type'] == 3 ) { ?>
                                <fieldset>
                                    <div class="selectMedium">
                                        <?php
                                        $ar_input_data_tf =array(
                                            'class' => 'text_field',
                                            'placeholder' => 'Teaching For (Years and Month)',
                                            'maxlength' => '255',
                                            'size' => '560',
                                            'name' => 'teaching_for',
                                            'value' => $model->teaching_for,
                                        );
                                        echo form_input($ar_input_data_tf);
                                        ?>
                                    </div>
                                </fieldset>
                                <?php } ?>

                                <fieldset>
                                    <div class="custom_dropdown_medium">
                                        <div>
                                            <?php
                                            $class_string = 'id="medium" class="droppify"';
                                            echo form_dropdown('medium', $medium, $model->medium, $class_string);
                                            ?>
                                        </div>
                                    </div>
                                </fieldset>

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
                                <?php } ?>


                                <fieldset>
                                    <div>
                                        <label class="gender_label">I am a</label>
                                        <ul class="radio-holder gender_ul">
                                            <li style="padding-top: 0px;">
                                                <input class="css-checkbox" id="male" name="gender" value="1" type="radio" <?php echo ($model->gender == '1') ? 'checked="checked"' : ''; ?>>
                                                <label for="male" class="user_type_label">Male</label>
                                            </li>
                                            <!-- class="user_type_radio" -->
                                            <li style="padding-top: 0px;">
                                                <input class="css-checkbox" id="female" name="gender" value="0" type="radio" <?php echo ($model->gender == '0') ? 'checked="checked"' : ''; ?>>
                                                <label for="female" class="user_type_label">Female</label>
                                            </li>
                                        </ul>
                                    </div>
                                </fieldset>

                            <?php } ?>

                        </div>

                        <div class="clearfix" style="margin-left: auto; margin-right: auto; margin-top: 20px; text-align: center; margin-bottom: 20px;">
                            <button id="btn_free_user" class="red" type="submit">
                                <span class="clearfix f2">
                                    <?php echo ($edit) ? 'Proceed' : 'Create my account'; ?>
                                </span>

                                <?php if(!$edit){ ?>
        <!--                            <div class="plus-right">
                                        <div></div>
                                    </div>-->
                                <?php } ?>

                            </button>
                        </div>

                        <?php if (!$edit) { ?>
                        <div class="clearfix center"><strong>Or Register with </strong></div>

                        <div class="sns-button-div">
                            <div style="height: 100%; margin-left: auto; margin-right: auto; width: 105px;">
                                <div class="fb-button">
                                    <button class="fb-reg-btn"></button>
                                </div>

                                <div class="google-button">
                                    <button class="google-reg-btn"></button>
                                </div>

                            </div>
                        </div>

                        <?php } ?>

                    </div>

                <?php echo form_close(); ?>

            </div>
                <!-- Registration Form -->
                
                <!-- Login Form -->
                <div id="frm_login" class="f5" style="display: none;">

                    <?php echo form_open('login_user', array('class' => 'validate_form', 'id' => 'login_frm', 'enctype' => 'multipart/form-data', 'autocomplete' => 'off')); ?>

                        <div class="clearfix" style="margin-left: auto; margin-right: auto; width: 100%; margin-top: 10px; ">

                            <div>
                                <fieldset class="login_logo">
                                    <div>
                                        <img src="<?php echo base_url('styles/layouts/tdsfront/image/Login.png'); ?>" width="60px" alt="Chmaps21.com" />
                                    </div>
                                    <div class="not_registered">
                                        Not registered yet?
                                    </div>
                                    <div class="sign_up_free">
                                        <label class="register-user">Sign Up Now!</label><span class="register-user">FREE</span>
                                    </div>
                                </fieldset>
                                
                                <fieldset class="f2 login_title">
                                    <div>
                                        LOGIN
                                    </div>
                                </fieldset>

                                <?php if (!empty($errors)) { ?>
                                    <ul class="login_errorMessage">
                                        <?php
                                        if (!empty($errors['model_errors'])) {
                                            foreach ($errors['model_errors'] as $e) {
                                                ?>
                                                <li><?php echo $e; ?></li>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </ul>
                                <?php } ?>

                                <fieldset>
                                    <div class="login_text_div">
                                        <input class="login_text login_user_name_back_image login_back_image_property" id="email" name="email" value="" type="text" maxlength="60" placeholder="Email" size="60">
                                    </div>
                                </fieldset>

                                <fieldset>
                                    <div class="login_text_div">
                                        <input class="login_text login_password_back_image login_back_image_property" id="password" name="password" value="" type="password" maxlength="60" placeholder="Password"  size="60">
                                    </div>
                                </fieldset>

                                <fieldset>

                                    <div>
                                        <div class="login_remember_me_div">
                                            <input id="remember_me_chk" name="remember_me" value="1" type="checkbox" checked="checked">
                                            <!--<label class="login_checkbox_label" for="remember_me"></label>-->
                                            <label class="f5 login_checkbox_label_txt">Remember me</label>
                                        </div>

                                        <div class="f5 login_reset_password">
<!--                                            <a href="javascript:void(0);">Forgot Password?</a>-->
                                        </div>
                                    </div>

                                </fieldset>

                            </div>

                            <div class="clearfix" style="margin-left: auto; margin-right: auto; width:100%;">
                                <button class="login_red" type="submit">
                                    <span class="clearfix f2">
                                        Sign in
                                    </span>
                                    <div class="login_arrow-right"></div>
                                </button>
                            </div>

                            <div class="clearfix center" style="padding-top: 60px;"><strong>Or, login with</strong></div>

                            <div class="sns-button-div" style="width: 100%">
                                <div class="sns-button-box" style="height: 100%; margin-left: auto; margin-right: auto; width: 105px;">
                                    <div class="fb-button">
                                        <button class="fb-login-btn"></button>
                                    </div>

                                    <div class="google-button">
                                        <button id="signinButton" class="google-login-btn"></button>
                                    </div>

                                </div>
                            </div>

                        </div>

                    <?php echo form_close(); ?>

                </div>
                <!-- Login Form -->
                <div id="frm_spellbee_reg" style="display: none;">
                    <?php
                    $action = base_url('register_user');
                    $frm_id = 'reg_frm';
                    if($edit){
                        $action = base_url('update_spellingbee_profile');
                        $frm_id = 'update_spellingbee_profile_frm';
                    }
                    ?>

                    <?php echo form_open($action, array('class' => 'validate_form', 'id' => $frm_id, 'enctype' => 'multipart/form-data', 'autocomplete' => 'off')); ?>

                        <div class="clearfix" style="margin-left: auto; margin-right: auto; width: 90%; margin-top: 0px; ">
                            <div>
                                
                                <fieldset class="reg_logo">
                                    <div>
                                        <img src="<?php echo base_url('styles/layouts/tdsfront/image/register.png'); ?>" width="60px" alt="Chmaps21.com" />
                                    </div>
                                </fieldset>

                                <fieldset class="reg_title f2">
                                    <div>
                                         <?php echo ($edit) ? 'Update Profile For Spelling Bee' : 'Update Profile For Spelling Bee'; ?>

                                        <?php if($edit){ ?>
                                            <div class="clearfix horizontal-line"></div>
                                         <?php }?>
                                    </div>
                                </fieldset>
                                
                                <?php if($edit){ ?>
                                    <div style="text-align: center; font-size: 20px; ">&nbsp;</div>
                                <?php }?>
                                    
                                <fieldset>First Name</fieldset>
                                <fieldset>
                                    <ul class="radio-holder">    
                                        <li>
                                            <div class="frmSearch">
                                                
                                                <input name="first_name" value="<?php echo $model->first_name?>" type="text" id="first_name" class="name_text school_name" placeholder="First Name" required="required" />                                               
                                            </div>

                                        </li>
                                    </ul>
                                </fieldset>
                                    
                                <fieldset>School Name</fieldset>
                                <fieldset>
                                    <ul class="radio-holder">    
                                        <li>
                                            
                                            <div class="frmSearch">
                                                <input name="school_name" value="<?php echo $model->school_name?>" type="text" id="search-box1" class="name_text school_name"  placeholder="School Name" required="required" />
                                                    <div id="suggesstion-box1"></div>
                                            </div>

                                        </li>
                                    </ul>
                                </fieldset>
                                
                                <fieldset>Mobile Number</fieldset>
                                <fieldset>
                                    <ul class="radio-holder">    
                                        <li>
                                            <input placeholder="880" class="name_text country_code" id="country_code" name="country_code" value="" type="text" maxlength="6" />
                                        </li>
                                        
                                        <li>
                                            <input placeholder="Mobile Number" class="name_text mobile_no" id="mobile_no" name="mobile_no" value="<?php echo $model->mobile_no; ?>" type="text" maxlength="20" required="required" />
                                        </li>
                                    </ul>
                                </fieldset>
                                
                                <fieldset>Division</fieldset>
                                <fieldset>
                                    <ul class="radio-holder">
                                        
                                        <li>
                                            <div class="custom_dropdown_dob">
                                                <div>
                                                    <?php
                                                    $division = $model->division;
                                                    
                                                    $divsion_data['Dhaka'] = 'Dhaka';
                                                    $divsion_data['Chittagong'] = 'Chittagong';
                                                    $divsion_data['Rajshahi'] = 'Rajshahi';
                                                    $divsion_data['Khulna'] = 'Khulna';
                                                    $divsion_data['Sylhet'] = 'Sylhet';
                                                    $divsion_data['Rangpur'] = 'Rangpur';
                                                    $divsion_data['Barisal'] = 'Barisal';
                                                    

                                                    $class_string = 'id="division" class="droppify"';
                                                    echo form_dropdown('division', $divsion_data, $division, $class_string);
                                                    ?>
                                                </div>
                                            </div>
                                        </li>
                                        
                                    </ul>

                                </fieldset>
                                <div class="clearfix" style="margin-left: auto; margin-right: auto; margin-top: 20px; text-align: center; margin-bottom: 20px;">
                                    <button id="btn_free_user" class="red" type="submit">
                                        <span class="clearfix f2">
                                            <?php echo ($edit) ? 'Proceed' : 'Create my account'; ?>
                                        </span>

                                        <?php if(!$edit){ ?>
                <!--                            <div class="plus-right">
                                                <div></div>
                                            </div>-->
                                        <?php } ?>

                                    </button>
                                </div>
                            </div>
                        </div>

                    <?php echo form_close(); ?>
                </div>
                <div style="width: 10%; float: left;">
                    <?php if( free_user_logged_in() ){ ?>
                    <ul class="ch-grid-header">
                        <li>
                                <?php if ( get_notification() ) : ?>
                                    <div class="circle"><?php echo get_notification(); ?></div>
                                <?php endif; ?>
                                <!--div class="ch-item-header">				
                                        <div class="ch-info-wrap-header">
                                                <div class="ch-info-header">
                                                        <div class="ch-info-front-header ch-img-1-header"></div>
                                                        <div class="ch-info-back-header">
                                                            <img class="ch-img-1-header-hover " src="<?php //echo base_url('merapi/images/icon/notification_hover.png');?>">
                                                        </div>	
                                                </div>
                                        </div>
                                        <!--<span>Notification</span>-->
                                </div-->

                        </li>
                    </ul>
                    <?php } ?>
                </div> 
				
            </div>
            
            
<!--            <div class="ad-div" style="float: right; width: 58%; text-align: right; margin-right: 5px; display: inline-table; padding-top: 20px;">
                <?php
//                    $adplace_helper = new Adplace;
//                    $adplace_helper->printAds( 1, 0, FALSE );
                ?>
            </div>-->
        </div>
        <?php endif; ?>
        
        <!--MObile MEnu start-->
        
                                <?php
                                $widget = new Widget;
                                $widget->run('champs21mobilemenu');
                                ?> 
        
        <!--MObile MEnu END-->
        
        <div class="fixed-menu">
            <ul class="fixed-menu-ul">
                <?php
                    if(get_free_user_session('paid_id') && get_free_user_session('paid_school_code')) { ?>
                        <li data="magic_mart" class="before-login-user-back" onclick="location.href='<?php echo $my_school_menu_uri; ?>'">                        
                            <div class="<?php echo $school_icon_class;?>">&nbsp;</div> 
                        </li>
                <?php } ?>
                <?php if( free_user_logged_in() ) { ?>
                    <li onclick="location.href='<?php echo base_url('/good-read'); ?>'">
                <?php } else { ?>
                    <li data="good_read" class="before-login-user">
                <?php } ?>    
                    <div class="icon-good-read">&nbsp;</div>
               </li>
                
                <li data="candle" class="<?php echo ( free_user_logged_in() ) ? 'candlepopup' : 'before-login-user'; ?>">
                    <div class="icon-candle">&nbsp;</div> 
                </li>
                <li data="magic_mart" class="before-login-user-back" onclick="location.href='<?php echo base_url() . 'schools'; ?>'">                
                    <div class="icon-my-school">&nbsp;</div> 
                </li>
<!--                <li data="school_template" class="before-login-user-back" onclick="location.href='<?php echo base_url() . 'create-school-website'; ?>'">                
                    <div class="icon-school-template">&nbsp;</div> 
                </li>-->
                <?php if($this->config->config['android_app_dl_popup_show'] == true):?>
                    <?php #$ua = strtolower($_SERVER['HTTP_USER_AGENT']); ?>
                    <?php #if(stripos($ua,'android') !== false): ?>
                    <li data="android-app" class="pop-without-login">                        
                        <div class="icon-mobile-app">&nbsp;</div> 
                    </li>
                    <?php #endif; ?>
                <?php endif; ?>
<!--                <li data="create-page" class="before-login-user-back" onclick="location.href='<?php echo base_url('/createpage'); ?>'">                        
                    <div class="icon-create-page">&nbsp;</div> 
                </li>-->
            </ul>
               
        </div>

        <!--   Good Read, Candle, Magic Mart and Read Later Pop up     -->
        <div id="before-login-user-fancy" style="display: none;">
            <div id="before-login-user-wrapper">

                <div class="before-login-user-header">
                    <div class="f2 before-login-user-header-label">
                        
                    </div>
                    <div class="before-login-user-icon-wrapper">
                        <img src="/styles/layouts/tdsfront/image/good_read_red_icon.png" width="75" />
                    </div>

                </div>

                <div class="before-login-user-body">

                    <div class="custom_message"></div>
                    <p class="common_message">This feature is only for registered users. Please register with us and get many more interesting features.</p>

                    <div class="login-user-btn-wrapper">
                        <button class="login_red login-user-btn login-user" type="button">
                            <span class="clearfix f2">
                                Login
                            </span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
        
        <div id="global-popup-box" style="display: none;">
            <div id="before-login-user-wrapper">

                <div class="before-login-user-header">
                    <div class="f2 before-login-user-header-label">
                        
                    </div>
                    <div class="before-login-user-icon-wrapper">
                        <img src="/styles/layouts/tdsfront/image/good_read_red_icon.png" width="75" />
                    </div>

                </div>

                <div class="before-login-user-body">

                    <div class="custom_message"></div>
                    <p class="common_message"><a href="<?php echo $this->config->config['android_app_dl_url']?>" target="_blank">Click Here</a></p>

                    
                </div>

            </div>
        </div>
        <div id="global-popup-box-for-spellato" style="display: none;">
            <div id="before-login-user-wrapper">

                <div class="before-login-user-header">
                    <div class="f2 before-login-user-header-label">
                        
                    </div>
                    <div class="before-login-user-icon-wrapper">
                        <img src="/styles/layouts/tdsfront/image/good_read_red_icon.png" width="75" />
                    </div>

                </div>

                <div class="before-login-user-body">

                    <div class="custom_message"></div>
                    <p class="common_message"><a href="<?php echo base_url('styles/layouts/tdsfront/spelling_bee/spellato/spellato_2015_pdf.pdf'); ?>" target="_blank">Click Here For PDF</a></p>
                    <p class="common_message"><a href="https://play.google.com/store/apps/details?id=com.champs21.spellato&hl=en" target="_blank">Click Here For Android App</a></p>

                    
                </div>

            </div>
        </div>
        <!--   Good Read, Candle, Magic Mart and Read Later Pop up     -->
        
                <?php
                        $widget = new Widget;
                        $widget->run('champs21candle', $ci_key);
                ?>
                <?php
                        //$widget = new Widget;
                        //$widget->run('champs21schoolsearch', $ci_key);
                ?>
        <!--<div class="category-fixed-menu">
            <div class="f2 category-fixed-menu-box">            
                <label class="category-fixed-menu-title" for="menu-toggle">Menu</label>
         
                <input type="checkbox" id="menu-toggle"/>
                <div class="category-fixed-menu-list" id="category-fixed-menu-list">
                    <?php
                    //$widget = new Widget;
                    //$widget->run('champs21slidemenu');
                    ?>  
                </div>          
            </div>            
        </div> --> 
        
        
        
        
        
        
        
        <div class="header-bg" style="display:none;float:left; width:100%; padding: 3px 25px 0 30px; border-bottom: 1px solid #ccc;">
            <div style="float: left;margin:5px;">
                <?php if (array_key_exists($ci_key_for_cover, $this->config->config['cover']) && $this->config->config['cover'][$ci_key_for_cover] ) : ?>
                <form method="get" class="searchform" action="<?php echo base_url('search'); ?>" role="search">
                    <a href="<?php echo base_url(); ?>" style="margin-right: 10px; " >
                        <img width="25%;" src="<?php echo base_url('styles/layouts/tdsfront/images/logo-no-slogan.png'); ?>" class="image-logo" alt="logo">
                    </a>
                    <input class="field" name="s" id="s" class='search' placeholder="Search this site" type="search" style="border-radius: 6px; -moz-border-radius: 6px; -webkit-border-radius: 6px; width: 220px; margin-top: 3px;">
                    <input class="submit search-button" value="" type="submit" />
                </form>
                
                <?php elseif ((array_key_exists($ci_key_for_cover, $this->config->config['LOGO']) && $this->config->config['LOGO'][$ci_key_for_cover]) ||
                (array_key_exists("allpage", $this->config->config['LOGO']) && $this->config->config['LOGO']["allpage"])) : ?>
                
                <form method="get" class="searchform" action="<?php echo base_url('search'); ?>" role="search">
<!--                    <a href="<?php //echo base_url(); ?>" >
                        <img width="25%;" src="<?php //echo base_url('styles/layouts/tdsfront/images/logo-no-slogan.png'); ?>" class="image-logo" alt="logo">
                    </a>-->
                    <input class="field" name="s" id="s" class='search' placeholder="Search this site" type="search" style="border-radius: 6px; -moz-border-radius: 6px; -webkit-border-radius: 6px; width: 220px; margin-top: 3px;">
                    <input class="submit search-button" value="" type="submit" style="right: 2px;">
                </form>
                <?php else : ?>
                <a href="<?php echo base_url(); ?>" >
                    <img width="65%;" src="<?php echo base_url('styles/layouts/tdsfront/images/logo-no-slogan.png'); ?>" class="image-logo" alt="logo">
                </a>
                <?php endif; ?>
            </div>
			
            
        </div>
    </div>
    
    <div id="tree_div" style="display: none;">
       
       <?php echo form_open('set_preference', array('class' => 'validate_form', 'enctype' => 'multipart/form-data', 'id' => 'pref_frm', 'style' => 'background-color: #F9F9F9;', 'autocomplete' => 'off')); ?>
           
           <div style="margin-top: 20px;">
               
               <div>
                   <fieldset class="login_logo">
                       <div>
                           <img src="<?php echo base_url('styles/layouts/tdsfront/image/champs_logo.png'); ?>" width="250px" alt="Chmaps21.com">
                       </div>
                   </fieldset>

                   <fieldset class="login_title">
                       <div>
                           Set Your Preference
                       </div>
                   </fieldset>

                   <?php if (!empty($errors)) { ?>
                       <ul class="login_errorMessage">
                           <?php
                           if (!empty($errors['model_errors'])) {
                               foreach ($errors['model_errors'] as $e) {
                                   ?>
                                   <li><?php echo $e; ?></li>
                                   <?php
                               }
                           }
                           ?>
                       </ul>
                   <?php } ?>

               </div>

               <div style="margin-left: auto;margin-right: auto; padding-top: 30px; width: 45%;">
                   
                   <?php echo $category_tree;?>
<!--                    <ul id="tree">
                       
                  </ul>-->
               </div>

               <div class="clearfix" style="margin-left: auto; margin-right: auto; margin-top: 30px; text-align: center; width: 100%; float: left; ">
                   <button id="btn_pref" class="red" type="submit">
                       <span class="clearfix f5">
                           Set Preference
                       </span>
                   </button>
               </div>

           </div>
       
       <?php echo form_close(); ?>
       
   </div>
    
    <div class="alert-errors" id="alert-errors" style="display: none;">

      <div class="alert-header col-lg-12">
          Oops!
      </div>

      <div class="col-lg-12 horizontal-line-1"></div>

      <div class="col-lg-12 err-list-wrap"></div>

      <div class="close-wrapper close-wrapper-btn col-lg-12">

          <div class="close_btn">
              <span><a href="javascript:void(0);">OK</a></span>
          </div>

      </div>

   </div>
    
</header>

<?php 
$sencond_menu['my_school_menu_uri'] = $my_school_menu_uri;
$sencond_menu['school_icon_class '] = $school_icon_class;
$widget->run('champs21secondheader',$sencond_menu);?>
<script>
    function selectCountry(val) {
    $("body #search-box1").val(val);
    $("body #suggesstion-box1").hide();
    }
</script>
<script type="text/javascript">
jQuery(function($) {
if($('.header-logo-div').is(':visible')) {
    $( ".logo-div" ).css( "display",'none' );
}
});
</script>
<style type="text/css">
    .fancybox-wrap { 
        top: 30px !important; 

    }  
    .frmSearch {}
    #country-list{float:left;list-style:none;margin:0;padding:0;width:380px;}
    #country-list li{float:none;margin-bottom:0px;padding: 10px; background:#FAFAFA;border-bottom:#F0F0F0 1px solid;}
    #country-list li:hover{background:#F0F0F0;}
    #search-box1{padding: 10px;border: #F0F0F0 1px solid;}
    #suggesstion-box1 {
        display:none;
        max-height: 150px;
        overflow-y: scroll;
        position: absolute;
        border: #ccc 1px solid;
        font-size:12px;
        z-index:1000;
    }
    .text_field_school {
        background-color: #adb2b5;
        border-color: #8f979a !important;
        border-radius: 0;
        color: #000000 !important;
        font-family: tahoma,georgia,arial,serif;
        height: 51px !important;  
        width: 100%;
    }    
    .image-logo
    {
        width:210px;
    }
    .logo-div
    {
        float: left; 
        width: 17%; 
        padding-left: 20px;        
    }

    .login_reg_div
    {
        display: inline-block;
        float: left;
        padding-right: 15px;
        position: absolute;
        top: 17px;
        width: auto;
    }
    .login_reg_div_box
    {
        width: auto;
        display:inline-block;
        float: left;
    }
    .header-logo-div
    {
        display:none;
    }
    .user_profile_name
    {
        float: left; 
        padding: 0 0 0 10px;
    }

    .mobile_profile_box
    {
        display:none;
    }
    .mobile_log_reg_box
    {
        display:none;
    }
    @media all and (min-width: 200px) and (max-width: 314px) {
        .fancybox-wrap
        {
            width:90% !important;
        }
        .fancybox-inner
        {
            width:100% !important;
        }
        #before-login-user-wrapper
        {
            width: 90% !important;
        }
        .login_red
        {
            width:75% !important;
        }
        .header-logo-div
        {

            width: 52% !important;

        }
        .search_box_head
        {
            display:none;
        }
        .second-header-wrapper
        {
            display:none;
        }
        .fixed-menu
        {
            display:inline-block !important;;
        }
    }
    @media all and (min-width: 315px) and (max-width: 449px) {
        .fancybox-wrap
        {
            width:90% !important;
        }
        .fancybox-inner
        {
            width:100% !important;
        }
        #before-login-user-wrapper
        {
            width: 100% !important;
        }
        .header-logo-div
        {
            float:left;                
            width: 63%;
            text-align:center;
            margin:0px auto;        
            display:block;
        }
        .ch-grid-header
        {
            margin-right:20px !important;
        }
        .second-header-wrapper
        {
            display:none;
        }
        .fixed-menu
        {
            display:inline-block !important;;
        }
    }
    @media all and (max-width: 449px) {

        .header-logo-div
        {
            float:left;                
            width: 63%;
            text-align:center;
            margin:0px auto;        
            display:block;
        }
        .container
        {
            width:100% !important;
        }
        .login_reset_password
        {
            font-size:11px;
        }
        .login_checkbox_label_txt
        {
            font-size:11px;
        }
        #login_frm fieldset input[type="checkbox"]
        {
            margin-top:5px !important;
        }
        .logo-div
        {		
            float:left;                
            width: 65%;
            text-align:center;
            margin:0px auto;
            padding-left: 0px;
        }

        /*.login_reg_div
        {	
                width: 100%;
                padding-right: 0px;
                text-align:center;
        }
        .login_reg_div_box
        {
            float:none;
            width: 90%;
            margin:0px auto;
        }*/
        .tz_social
        {
            display:none;
        }
        .login_reg_div_box
        {
            margin-right:-15px;
            display:block !important;
        }
        .ch-grid-header
        {
            margin-right:20px !important;
        }
        .mobile_log_reg_box
        {
            display:block;
        }
        #settings_div
        {
            margin:0px !important;
        }
        .settings-btn {
            background: url("/styles/layouts/tdsfront/image/menu-icon.png") no-repeat scroll center center / 100% auto transparent !important;
            cursor: pointer;
            height: 40px !important;
            width: 40px !important;
        }
        .mobile_profile_box
        {
            display:block;
        }
        .user_profile_name_mobile
        {
            float: left; 
            padding: 0 0 0 10px;
        }
        #profile_image_div
        {
            display:none;
        }
        .user_profile_name
        {
            display:none;
        }
        .settings-elm-holder-div
        {
            right:0px !important;
        }







        .category-fixed-menu
        {
            top:115px;            
        }
        .fixed-menu
        {
            top:initial !important; 
            top:auto !important; 
            bottom:0px;
            width:100%;
            background:#DB3434;
            z-index:1000;
        }
        .fixed-menu-ul
        {
            width:100%;
            text-align:center;
        }
        .fixed-menu li
        {
            display:inline-block !important;
        }
        .icon-good-read
        {
            width:60px !important;
            height:43px !important;
            top:-1px;
        }
        .icon-candle
        {
            width:60px !important;
            height:43px !important;
            top:-1px;
        }
        .icon-magic-mart
        {
            width:60px !important;
            height:43px !important;
            top:-1px;
        }
        .icon-my-school
        {
            width:60px !important;
            height:43px !important;
            top:-1px;
        }
        .icon-school-template
        {
            width:60px !important;
            height:43px !important;
            top:-1px;
        }
        .icon-mobile-app
        {
            width:60px !important;
            height:43px !important;
            top:-1px;
        }
        .icon-diary21-school
        {
            width:60px !important;
            height:43px !important;
            top:-1px;
        }
        .category-fixed-menu-box label {
            margin-bottom:0px;
            float:right;
            width:100%;
            border-bottom : 2px solid #6c5754;
            padding:10px !important;
            font-size:8px;
        }
        .category-fixed-menu
        {
            display:none;
        }
        .new_top_menu
        {
            display:none;
        }
        .responsive_height
        {
            display:block !important;
            border-top:2px solid gray;
        }
        .champs-header
        {
            height:134px;
            border-bottom:2px solid red;
            background-size:auto;
        }
        .header-new
        {
            /*height:65px !important;*/
        }
        .header-new
        {
            width:100% !important;
        }
        ul.radio-holder li{        
            padding-left: 0px !important;
            padding-right: 20px !important;
        }

        #candletoPopup
        {
            position:absolute;
            left:0px !important;
            width:90% !important;
            margin: 0px 5%;
        }
        .candle_left_box
        {
            float:left;
            width:35%;
            height:auto !important;
        }
        .candle_right_box
        {
            float:none !important;
            width:auto !important;
            border-left:0px solid #ccc !important;
            height:auto !important;
            padding-left:0px !important; 
        }
        .candle_right_box_p1
        {
            position:relative;
            top:40px;
            left:20px;
            font-size:35px !important;
            line-height:40px !important;
        }
        .candle_right_box_p2
        {
            float:left;
        }
        .slide #section_form label
        {
            width:100%
        }
        .search_box_head
        {
            display:none;
        }
        .col-lg-4
        {
            width:33.33%;
            float:left;
        }
        .read_later
        {
            font-size:9px !important;
        }
        .seen span
        {
            font-size:9px !important;
        }
        .second-header-wrapper
        {
            display:none;
        }
        .fixed-menu
        {
            display:inline-block !important;;
        }
    } 
    @media all and (min-width: 450px) and (max-width: 599px) {
        .container
        {
            width:100% !important;
        }
        .login_reset_password
        {
            font-size:12px;
        }
        .login_checkbox_label_txt
        {
            font-size:12px;
        }
        #login_frm fieldset input[type="checkbox"]
        {
            margin-top:5px !important;
        }
        .logo-div
        {		
            float:none;                
            width: 100%;
            margin:0px auto;
            padding-left: 0px;
            text-align:center;
        }

        /*.login_reg_div
        {	
                width: 100%;
                padding-right: 0px;
                text-align:center;
        }
        .login_reg_div_box
        {
            float:none;
            width: 90%;
            margin:0px auto;
        }
        .tz_social a
        {
            font-size:5px;
        }*/
        .fixed-menu
        {
            display:inline-block !important;;
        }
        .fixed-menu
        {
            top:initial !important;  
            top:auto !important; 
            bottom:0px;
            width:100%;
            background:#DB3434;
            z-index:1000;
        }
        .fixed-menu-ul
        {
            width:100%;
            text-align:center;
        }
        .fixed-menu li
        {
            display:inline-block !important;
        }
        .second-header-wrapper
        {
            display:none;
        }
        .icon-good-read
        {
            width:60px !important;
            height:43px !important;
            top:-1px;
        }
        .icon-diary21-school
        {
            width:60px !important;
            height:43px !important;
            top:-1px;
        }
        .icon-candle
        {
            width:60px !important;
            height:43px !important;
            top:-1px;
        }
        .icon-magic-mart
        {
            width:60px !important;
            height:43px !important;
            top:-1px;
        }
        .icon-my-school
        {
            width:60px !important;
            height:43px !important;
            top:-1px;
        }
        .icon-school-template
        {
            width:60px !important;
            height:43px !important;
            top:-1px;
        }
        .icon-mobile-app
        {
            width:60px !important;
            height:43px !important;
            top:-1px;
        }
        .category-fixed-menu-box label {
            margin-bottom:0px;
            float:right;
            width:100%;
            border-bottom : 2px solid #6c5754;
            padding:10px !important;
            font-size:8px;
        }
        .category-fixed-menu
        {
            display:none;
        }
        .new_top_menu
        {
            display:none;
        }
        .responsive_height
        {
            display:block !important;
            border-top:2px solid gray;
        }
        .champs-header
        {
            height:154px;
            border-bottom:2px solid red;
            background-size:auto;
        }
        .header-new
        {
            height:100px !important;
        }
        .header-new
        {
            width:100% !important;
        }
        .fancybox-wrap
        {
            width:70% !important;
        }
        .fancybox-inner
        {
            width:100% !important;
        }

        #before-login-user-wrapper
        {
            width: 100% !important;
        }
        ul.radio-holder li{        
            padding-right: 20px !important;
            padding-left: 0px !important;
        }
        /*HEADER START*/
        .header-logo-div
        {
            float:left;                
            width: 55%;
            text-align:center;
            margin:0px auto;        
            display:block;
        }
        .logo-div
        {		
            float:left;                
            width: 55%;
            text-align:center;
            margin:0px auto;
            padding-left: 0px;
        }
        .tz_social
        {
            display:none;
        }
        .login_reg_div_box
        {
            margin-right:-15px;
            display:block !important;
        }
        .ch-grid-header
        {
            margin-right:35px !important;
            margin-top:10px;
        }
        .mobile_log_reg_box
        {
            display:block;
        }
        #settings_div
        {
            margin:0px !important;
        }
        .settings-btn {
            background: url("/styles/layouts/tdsfront/image/menu-icon.svg") no-repeat scroll center center / 100% auto transparent !important;
            cursor: pointer;
            height: 40px !important;
            width: 40px !important;
            margin-right:10px;
            margin-top:10px;
        }
        .mobile_profile_box
        {
            display:block;
        }
        .user_profile_name_mobile
        {
            float: left; 
            padding: 0 0 0 10px;
        }
        #profile_image_div
        {
            display:none;
        }
        .user_profile_name
        {
            display:none;
        }
        .settings-elm-holder-div
        {
            right:0px !important;
        }
        /*CNADLE START*/       
        #candletoPopup
        {
            position:absolute;
            left:0px !important;
            width:90% !important;
            margin: 0px 5%;
        }
        .candle_left_box
        {
            float:left;
            width:32%;
            height:auto !important;
        }
        .candle_right_box
        {
            float:none !important;
            width:auto !important;
            border-left:0px solid #ccc !important;
            height:auto !important;
            padding-left:0px !important; 
        }
        .candle_right_box_p1
        {
            position:relative;
            top:40px;
            left:20px;
            font-size:50px !important;
            line-height:50px !important;
        }
        .candle_right_box_p2
        {
            float:left;
        }
        .search_box_head
        {
            display:none;
        }
        .col-lg-4
        {
            width:33.33%;
            float:left;
        }
        .read_later
        {
            font-size:9px !important;
        }
        .seen span
        {
            font-size:9px !important;
        }

    }
    @media all and (min-width: 600px) and (max-width: 799px) {
        .container
        {
            width:100% !important;
        }
        .login_reset_password
        {
            font-size:14px;
        }
        .login_checkbox_label_txt
        {
            font-size:14px;
        }
        #login_frm fieldset input[type="checkbox"]
        {
            margin-top:7px !important;
        }

        .fixed-menu
        {
            display:inline-block !important;;
        }
        .fixed-menu
        {
            top:initial !important;  
            top:auto !important; 
            bottom:0px;
            width:100%;
            background:#DB3434;
            z-index:1000;
        }
        .fixed-menu-ul
        {
            width:100%;
            text-align:center;
        }
        .fixed-menu li
        {
            display:inline-block !important;
        }
        .second-header-wrapper
        {
            display:none;
        }
        .icon-good-read
        {
            width:60px !important;
            height:43px !important;
            top:-1px;
        }
        .icon-diary21-school
        {
            width:60px !important;
            height:43px !important;
            top:-1px;
        }
        .icon-candle
        {
            width:60px !important;
            height:43px !important;
            top:-1px;
        }
        .icon-magic-mart
        {
            width:60px !important;
            height:43px !important;
            top:-1px;
        }
        .icon-my-school
        {
            width:60px !important;
            height:43px !important;
            top:-1px;
        }
        .icon-school-template
        {
            width:60px !important;
            height:43px !important;
            top:-1px;
        }
        .icon-mobile-app
        {
            width:60px !important;
            height:43px !important;
            top:-1px;
        }
        .category-fixed-menu-box label {
            margin-bottom:0px;
            float:right;
            width:100%;
            border-bottom : 2px solid #6c5754;
            padding:10px !important;
            font-size:8px;
        }
        .category-fixed-menu
        {
            display:none;
        }
        .new_top_menu
        {
            display:none;
        }
        .responsive_height
        {
            display:block !important;
            border-top:2px solid gray;
        }
        .champs-header
        {
            height:154px;
            border-bottom:2px solid red;
            background-size:auto;
        }
        .header-new
        {
            height:100px !important;
        }
        .header-new
        {
            width:100% !important;
        }
        .fancybox-wrap
        {
            width:50% !important;
        }
        .fancybox-inner
        {
            width:100% !important;
        }

        #before-login-user-wrapper
        {
            width: 100% !important;
        }
        ul.radio-holder li{        
            padding-right: 20px !important;
            padding-left: 0px !important;
        }











        .header-logo-div
        {
            float:left;                
            width: 45%;
            text-align:center;
            margin:0px auto;        
            display:block;
            padding-left: 20px;
            padding-top: 5px;
        }
        .logo-div
        {		
            float:left;                
            width: 45%;
            text-align:center;
            margin:0px auto;
            padding-left: 20px;
            padding-top: 5px;
        }
        .tz_social
        {
            display:none;
        }
        .login_reg_div_box
        {
            margin-right:-15px;
            display:block !important;
        }
        .mobile_log_reg_box
        {
            display:block;
        }
        .ch-grid-header
        {
            margin-right:45px !important;
            margin-top:15px;
        }
        #settings_div
        {
            margin:0px !important;
        }
        .settings-btn {
            background: url("/styles/layouts/tdsfront/image/menu-icon.svg") no-repeat scroll center center / 100% auto transparent !important;
            cursor: pointer;
            height: 40px !important;
            width: 40px !important;
            margin-right:20px;
            margin-top:15px;
        }
        .mobile_profile_box
        {
            display:block;
        }
        .user_profile_name_mobile
        {
            float: left; 
            padding: 0 0 0 10px;
        }
        #profile_image_div
        {
            display:none;
        }
        .user_profile_name
        {
            display:none;
        }
        .settings-elm-holder-div
        {
            right:0px !important;
        }
        /*CNADLE START*/       
        #candletoPopup
        {
            position:absolute;
            left:0px !important;
            width:90% !important;
            margin: 0px 5%;
        }
        .candle_left_box
        {
            float:left;
            width:32%;
            height:auto !important;
        }
        .candle_right_box_p1
        {            
            font-size:50px !important;
            line-height:50px !important;
        }
        .search_box_head
        {
            display:none;
        }
        .col-lg-4
        {
            width:33.33%;
            float:left;
        }

    }
    @media all and (min-width: 800px) and (max-width: 991px) {

        .header-logo-div
        {
            float:left;                
            width: 40%;
            text-align:center;
            margin:0px auto;        
            display:block;
        }
        .logo-div
        {		
            float:left;                
            width: 35%;
            text-align:center;
            margin:0px auto;
            padding-left: 0px;
        }
        .fancybox-wrap
        {
            width:45% !important;
        }
        .fancybox-inner
        {
            width:100% !important;
        }

        #before-login-user-wrapper
        {
            width: 100% !important;
        }
        ul.radio-holder li{        
            padding-right: 20px !important;
            padding-left: 0px !important;
        }
        .col-lg-4
        {
            width:33.33%;
            float:left;
        }
        .fixed-menu
        {
            display:none;
        }
    }
    @media all and (min-width: 992px) and (max-width: 1251px) {
        .fancybox-wrap
        {
            width:35% !important;
        }
        .fancybox-inner
        {
            width:100% !important;
        }

        #before-login-user-wrapper
        {
            width: 100% !important;
        }
        ul.radio-holder li{        
            padding-right: 20px !important;
            padding-left: 0px !important;
        }
        .fixed-menu
        {
            display:none;
        }
    }


    .responsive_height
    {
        display:none;
    }
    .category-fixed-menu
    {
        padding:0;
        position:fixed;
        right:0px;
        z-index:1000;
        height:100%;
    }
    .category-fixed-menu-box
    {    
        float:right;
    }
    .category-fixed-menu-title
    {
        color:#FFFFFF;
        background:#DB3434;
        padding:20%;
        cursor:pointer; 
    }
    #category-fixed-menu-close
    {
        color:#FFFFFF;
        background:#DB3434;
        padding:20px;
        padding-left:70px;
        cursor:pointer;
        display: none;

    }
    .fixed-menu
    {
        display:none;
    }
    .fixed-menu
    {
        padding:0;
        position:fixed;
        left:0px;
        top:100px;
    }
    .category-fixed-menu-list{

        display: block;
        background:#414952;
        float:right;
        clear:both;
        width:210px;
        overflow:hidden;
        -webkit-box-shadow: 0 8px 6px -6px black;
        -moz-box-shadow: 0 8px 6px -6px black;
        box-shadow: 0 8px 6px -6px black;
        -webkit-transition: all 1s ease-in-out;
        -moz-transition: all 1s ease-in-out;
        -o-transition: all 1s ease-in-out;
        transition: all 1s ease-in-out;
        margin-right:-210px;
    }
    .category-fixed-menu-box label {
        margin-bottom:0px;
        float:right;
        width:100%;
        border-bottom : 2px solid #6c5754;
        padding:20px;
    }
    #menu-toggle {
        display:none;

    }
    #menu {
        display:none;
    }
    #menu-toggle:checked + #category-fixed-menu-list{
        -webkit-transition: all 1s ease-in-out;
        -moz-transition: all 1s ease-in-out;
        -o-transition: all 1s ease-in-out;
        transition: all 1s ease-in-out;
        display:block;
        margin-right:0px;

    }

    .fixed-menu-ul
    {
        padding:0;
        margin:0px;
    }
    .fixed-menu li
    {
        position: relative;
        display: block;
        margin-bottom: 4px;
        cursor:pointer;
    }
    .icon-good-read
    {
        background: url(<?php echo base_url('styles/layouts/tdsfront/image/good_read_red.png'); ?>) no-repeat;
        background-size:70%;    
        width:118px;
        height:79px;
    }

    .icon-good-read:hover
    {
        background: url(<?php echo base_url('styles/layouts/tdsfront/image/good_read_black.png'); ?>) no-repeat;
        background-size:70%;    
    }
    .icon-candle
    {
        background: url(<?php echo base_url('styles/layouts/tdsfront/image/candle_red.png'); ?>) no-repeat;
        background-size:70%;    
        width:118px;
        height:79px;
    }
    .icon-candle:hover
    {
        background: url(<?php echo base_url('styles/layouts/tdsfront/image/candle_black.png'); ?>) no-repeat;
        background-size:70%;    
    }
    .icon-my-school
    {
        background: url(<?php echo base_url('styles/layouts/tdsfront/image/my_school.png'); ?>) no-repeat;
        background-size:70%;
        width:118px;
        height:79px;
    }
    .icon-my-school:hover
    {
        background: url(<?php echo base_url('styles/layouts/tdsfront/image/my_school_black.png'); ?>) no-repeat;
        background-size:70%;    
    }
    .icon-school-template
    {
        background: url(<?php echo base_url('styles/layouts/tdsfront/image/create_website_red.png'); ?>) no-repeat;
        background-size:70%;
        width:118px;
        height:79px;
    }
    .icon-school-template:hover
    {
        background: url(<?php echo base_url('styles/layouts/tdsfront/image/create_website_hover.png'); ?>) no-repeat;
        background-size:70%;    
    }
    .icon-diary21-school
    {
        background: url(<?php echo base_url('styles/layouts/tdsfront/image/diary21_red.png'); ?>) no-repeat;
        background-size:70%;
        width:118px;
        height:79px;
    }
    .icon-diary21-school:hover
    {
        background: url(<?php echo base_url('styles/layouts/tdsfront/image/diary21_black.png'); ?>) no-repeat;
        background-size:70%;    
    }
    .icon-create-page
    {
        background: url(<?php echo base_url('styles/layouts/tdsfront/image/createpage_red.png'); ?>) no-repeat;
        background-size:70%;
        width:118px;
        height:79px;
    }
    .icon-create-page:hover
    {
        background: url(<?php echo base_url('styles/layouts/tdsfront/image/createpage_black.png'); ?>) no-repeat;
        background-size:70%;    
    }
    .icon-mobile-app
    {
        background: url(<?php echo base_url('styles/layouts/tdsfront/image/android_icon.png'); ?>) no-repeat;
        background-size:70%;    
        width:118px;
        height:79px;
    }
    .icon-mobile-app:hover
    {
        background: url(<?php echo base_url('styles/layouts/tdsfront/image/play_store_icon_hover.png'); ?>) no-repeat;
        background-size:70%;    
    }       
    <!--</style>
<style type="text/css">-->
    /* Nurul Islam   */
    .alert-errors {
        background-color: #060A0B;
        width: 33%;
        margin: auto;
        position: fixed;
        top: 20%;
        left: 33%;
        position: fixed;
        text-align: center;
    }
    .alert-header {
        color: #ffffff;
        font-size: 25px;
        font-weight: bold;
        padding: 10px 10px 10px 30px;
        text-align: left;
    }
    .horizontal-line-1 {
        border-bottom: 4px solid #3D3D3D;
        margin: 0;
    }
    .err-list-wrap {
        padding: 30px 10px 10px;
        text-align: left;
    }
    .err-list {
        color: #4d4d4b;
        font-size: 18px;
    }
    .close-wrapper {
        background-color: #3D3D3D;
        padding: 10px 25px;
    }
    .close-wrapper-btn {
        letter-spacing: 0.08em;
    }
    .close-wrapper-btn a {
        color: #0C0C0C;
        font-size: 22px;
    }
    .close-wrapper-btn .close_btn {
        background-color: #c9c9cb;
        border-radius: 5px;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        -ms-border-radius: 5px;
        -o-border-radius: 5px;
        cursor: pointer;
        padding: 6px;
    }
    .close-wrapper-btn .close_btn:hover {
        background-color: #E0E1E3;
        -webkit-transition: background-color 0.5s ease;
        -moz-transition: background-color 0.5s ease;
        -o-transition: background-color 0.5s ease;
        -ms-transition: background-color 0.5s ease;
        transition: background-color 0.5s ease;
    }
    /* Login Form Starts*/
    .login_logo{
        margin: 10px auto;
        text-align: center;
        width: 100% !important;
    }
    .login_title{
        color: #000000;
        font-size: 35px;
        height: 48px;
        margin: 10px auto 0;
        text-align: center;
        width: 100% !important;
    }

    #login_frm fieldset{
        clear: both;
        margin: 1px auto 0;
        width: 100% !important;
    }

    .login_label_side label{
        clear: both;
        width: 100%;
    }

    .login_red{
        background-color: #DE3427;
        border-color: #DE3427;
        border-bottom-color: #C1160F;
        font-size: 20px;
        border-radius: 8px;
        color: #fff;
        clear: both;
        height: 40px;
        width: 100%;
        border-radius: 5px;
        -o-border-radius: 5px;
        -moz-border-radius: 5px;
        -ms-border-radius: 5px;
        -webkit-border-radius: 5px;
        box-shadow: 2px 5px 10px 2px #bbb;
        -o-box-shadow: 2px 5px 10px 2px #bbb;
        -ms-box-shadow: 2px 5px 10px 2px #bbb;
        -moz-box-shadow: 2px 5px 10px 2px #bbb;
        -webkit-box-shadow: 2px 5px 10px 2px #bbb;
        position: absolute;
    }

    .login_arrow-right {
        border-bottom: 6px solid transparent !important;
        border-left: 10px solid #fff !important;
        border-top: 6px solid transparent !important;
        float: right;
        height: 0 !important;
        margin-right: 110px !important;
        margin-top: -20px;
        position: relative;
        width: 0 !important;
    }

    .login_red-gradient{
        background: #DE3427; /* Old browsers */
        background: -moz-linear-gradient(top,  #DE3427 23%, #6d0019 100%); /* FF3.6 */
        background: -webkit-gradient(linear, left top, left bottom, color-stop(23%,#DE3427), color-stop(100%,#6d0019)); /* Chrome,Safari4 */
        background: -webkit-linear-gradient(top,  #DE3427 23%,#6d0019 100%); /* Chrome10,Safari5.1 */
        background: -o-linear-gradient(top,  #DE3427 23%,#6d0019 100%); /* Opera 11.10 */
        background: -ms-linear-gradient(top,  #DE3427 23%,#6d0019 100%); /* IE10 */
        background: linear-gradient(to bottom,  #DE3427 23%,#6d0019 100%); /* W3C */
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#DE3427', endColorstr='#6d0019',GradientType=0 ); /* IE6-9 */

    }

    .login_errorMessage{
        list-style: none;
    }
    .login_errorMessage li{
        color: #DA3427;
        float: right;
        font-size: 16px;
        margin: 0 3px 15px 0 !important;
    }
    .login_errorMessage p{
        color: #DA3427;
    }
    .login_text_div{
        margin-left: auto !important;
        margin-right: auto !important;
        margin-top: 10px;
        width: 100% !important;
    }
    .login_text{
        font-family: tahoma,georgia,arial,serif;
        color: #000000 !important;
        width: 100% !important;
        height: 40px !important;
        background-color: #ADB2B5 !important;
        font-size: 15px !important;
        text-align: center;
        text-indent: 25px !important;
        border-radius: 5px !important;
        -o-border-radius: 5px !important;
        -moz-border-radius: 5px !important;
        -ms-border-radius: 5px !important;
        -webkit-border-radius: 5px !important;
    }
    .login_user_name_back_image{
        background: url('styles/layouts/tdsfront/image/user_name.png') no-repeat;
    }
    .login_password_back_image{
        background: url('styles/layouts/tdsfront/image/password.png') no-repeat;
    }
    .login_back_image_property{
        background-position:10px 10px;
        background-size: 18px;
    }
    .login_remember_me_div{
        clear: both;
        float: left;
        padding-top: 5px;
    }
    .login_reset_password{
        float: right;
        padding-top: 12px;
    }

    #login_frm fieldset input[type="checkbox"]{
        clear: both;
        display: block;
        float: left;
        height: 19px;
        margin-top: 10px;
        width: 19px;
    }
    /*
    #login_frm fieldset input[type="checkbox"]  label.login_checkbox_label{
        background-color: #fff;
        clear: both !important;
        cursor: pointer;
        float: left;
        height: 19px !important;
        margin-top: 10px;
        width: 19px !important;
    }
    #login_frm fieldset input[type="checkbox"]:checked  label.login_checkbox_label{
        background: url('styles/layouts/tdsfront/image/tick_mark.png') top no-repeat;
        background-color: #fff;
    }*/
    .login_checkbox_label_txt{
        color: #777;
        margin: 7px !important;
        cursor: pointer;
    }
    .login_horizontal-line{
        border-bottom: 1px solid #adb2b5;
        margin-left: auto;
        margin-right: auto;
        margin-top: 65px;
    }
    .login_sns-button-div{
        border: 8px solid #b9bec1;
        border-radius: 8px;
        -moz-border-radius: 8px;
        -ms-border-radius: 8px;
        -o-border-radius: 8px;
        -webkit-border-radius: 8px;
        height: 70px;
        margin-top: 18px;
    }
    .login_fb-button{
        background-color: #5A7099;
        color: #fff;
        font-size: 15px;
        text-align: center;
        vertical-align: central;
        float: left;
        width: 50%;
        height: 100%;
    }
    .sns-button-box
    {
        height: 100%;
        margin-left: auto;
        margin-right: auto;
        width: 35%;
    }
    .login_fb-button img{
        padding-top: 5px;
        width: 12px;
    }
    .login_google-button{
        background-color: #F7F7F7;
        text-align: center;
        font-size: 15px;
        vertical-align: central;
        float: right;
        /*        border-radius: 8px;
                 -ms-border-radius: 8px;
                -o-border-radius: 8px;
                -webkit-border-radius: 8px;*/
        width: 50%;
        height: 100%;
    }
    .login_google-button img{
        padding-top: 5px;
        width: 15px;
    }
    .login_circle{
        background-color: #b9bec1;
        border-radius: 1000px;
        -moz-border-radius: 1000px;
        -ms-border-radius: 1000px;
        -o-border-radius: 1000px;
        -webkit-border-radius: 1000px;
        float: left;
        height: 22px;
        margin: 18px 156px;
        position: absolute;
        width: 22px;
    }
    .login_circle_1{
        background-color: #f7f7f7;
        border-radius: 1000px;
        -moz-border-radius: 1000px;
        -ms-border-radius: 1000px;
        -o-border-radius: 1000px;
        -webkit-border-radius: 1000px;
        float: left;
        height: 12px;
        margin: 23px 161px;
        position: absolute;
        width: 12px;
    }
    /* Login Form Ends*/

    /* Registration Form Starts*/
    label {
        font-weight: normal !important;
    }

    .reg_logo{
        margin: 10px auto 5px;
        text-align: center;
        width: 100%;
    }
    .hell_box
    {
        margin: 10px 0 5px -25px;
        background-color: #ddd;
        width: 111%;
    }
    .plus-right {
        background-color: #fff !important;
        color: #de3427 !important;
        height: 15px !important;
        margin-left: 325px !important;
        margin-top: -23px;
        position: absolute;
        text-align: center;
        vertical-align: middle;
        width: 15px !important;
    }
    .plus-right div{
        color: #de3427 !important;
        height: 15px !important;
        margin: -9px 0 0 1px;
        position: relative;
        text-align: center;
        vertical-align: middle;
        width: 15px !important;
    }
    .reg_title{
        color: #000000;
        font-size: 35px;
        text-align: center;
    }

    fieldset {
        /*padding-left: 15px;*/
    }
    .user_type_div{
        padding: 0 0 0 50px;
    }
    .user_type_dialob_label {
        color: #000 !important;
        cursor: pointer;
        font-size: 18px;
    }
    .css-checkbox {
        clear: both;
        display: block;
        float: left;
        height: 19px;
        margin-right: 15px !important;
        margin-top: 0 !important;
        width: 19px;
        cursor: pointer;
    }
    label.css-label {
        float: left;
        background-color: #fff;
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
    fieldset select{
        width: 190px;
        overflow: hidden;
    }

    fieldset .drop-big select 
    {
        width: 270px;
        float: left;
        overflow: hidden;
        margin-top: 10px;
        height: 30;
    }

    ul.radio-holder{
        display: inline;
        float: left;
        list-style: none outside none;
        margin: 0 0 10px 0;
        padding: 0;

    }
    ul.radio-holder li{
        float: left;
        text-align: left !important;
        padding-left: 12px;
    }
    ul.radio-holder li:last-child {
        margin-right: 0px !important;
    }
    ul.radio-holder li:first-child {
        margin-left: 0;
        padding-left: 0;
    }
    .user_type_label {
        clear: none !important;
        color: #000 !important;
        cursor: pointer;
        float: left !important;
        font-size: 14px;
    }
    .user_type_radio{
        padding-top: 10px;
        margin: 0 !important;
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

    .file-upload{
        background-color: #DA3427;
        border-radius: 8px;
        color: #fff;
        padding: 3px 15px;
        width: auto !important;
        -o-border-radius: 8px;
        -moz-border-radius: 8px;
        -ms-border-radius: 8px;
        -webkit-border-radius: 8px;
        height: 35px;
        box-shadow: 2px 5px 10px 2px #bbb;
        -o-box-shadow: 2px 5px 10px 2px #bbb;
        -ms-box-shadow: 2px 5px 10px 2px #bbb;
        -moz-box-shadow: 2px 5px 10px 2px #bbb;
        -webkit-box-shadow: 2px 5px 10px 2px #bbb;
        cursor: pointer;
    }

    .red-gradient{
        background: #a90329; /* Old browsers */
        background: -moz-linear-gradient(top,  #a90329 23%, #6d0019 100%); /* FF3.6 */
        background: -webkit-gradient(linear, left top, left bottom, color-stop(23%,#a90329), color-stop(100%,#6d0019)); /* Chrome,Safari4 */
        background: -webkit-linear-gradient(top,  #a90329 23%,#6d0019 100%); /* Chrome10,Safari5.1 */
        background: -o-linear-gradient(top,  #a90329 23%,#6d0019 100%); /* Opera 11.10 */
        background: -ms-linear-gradient(top,  #a90329 23%,#6d0019 100%); /* IE10 */
        background: linear-gradient(to bottom,  #a90329 23%,#6d0019 100%); /* W3C */
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#a90329', endColorstr='#6d0019',GradientType=0 ); /* IE6-9 */

    }

    #div_cover_image{
        padding: 10px 0px;
    }
    #div_profile_image{
        padding: 10px 0px;
    }
    .errorMessage{
        list-style: none;
        margin: 0px 0px 20px 0px !important;
    }
    .errorMessage li{
        color: #DA3427;
        margin: 0px !important;
    }
    .errorMessage p{
        color: #DA3427;
    }
    .horizontal-line{
        border-bottom: 1px solid #adb2b5;
        margin: 15px 0 0;
    }
    .email_txt{
        font-family: tahoma,georgia,arial,serif;
        background-color: #adb2b5;
        border-radius: 0px;
        -moz-border-radius: 0px;
        -webkit-border-radius: 0px;
        -o-border-radius: 0px;
        -ms-border-radius: 0px;
        color: #000000 !important;
        font-size: 13px !important;
        height: 36px;
        width: 42%;
    }
    .center{
        margin-left: auto;
        margin-right: auto;
        padding-top: 15px;
        text-align: center;
        width: 100%;
    }
    .sns-button-div{
        height: 70px;
        margin-left: auto;
        margin-right: auto;
        margin-top: 15px;
        width: 50%;
        padding-left: 0px;
    }
    .fb-button{
        float: left;
        height: 50px;
        margin-right: 5px;
        width: 50px;
    }
    .fb-button button {
        background-color: transparent;
        background-image: url("styles/layouts/tdsfront/image/facebook.png");
        background-position: 0 0;
        background-repeat: no-repeat;
        background-size: 50px auto;
        border: medium none;
        color: #de3427;
        font-family: inherit;
        margin: 0;
        padding: 25px;
        position: relative;
    }
    .fb-button button:hover {
        background-image: url("styles/layouts/tdsfront/image/facebook_hover.png");
    }

    .google-button{
        float: left;
        height: 50px;
        width: 50px;
    }
    .google-button button {
        background-color: transparent;
        background-image: url("styles/layouts/tdsfront/image/google_plus.png");
        background-position: 0 0;
        background-repeat: no-repeat;
        background-size: 50px auto;
        border: medium none;
        color: #de3427;
        font-family: inherit;
        margin: 0;
        padding: 25px;
        position: relative;
    }
    .google-button button:hover {
        background-image: url("styles/layouts/tdsfront/image/google_plus_hover.png");
    }

    .circle{
        background-color: #b9bec1;
        border-radius: 1000px;
        -moz-border-radius: 1000px;
        -ms-border-radius: 1000px;
        -o-border-radius: 1000px;
        -webkit-border-radius: 1000px;
        float: left;
        height: 22px;
        /*        margin: 18px 256px;*/
        position: absolute;
        width: 22px;
    }
    .circle_1{
        background-color: #f7f7f7;
        border-radius: 1000px;
        -moz-border-radius: 1000px;
        -ms-border-radius: 1000px;
        -o-border-radius: 1000px;
        -webkit-border-radius: 1000px;
        float: left;
        height: 12px;
        margin: 23px 261px;
        position: absolute;
        width: 12px;
    }
    .sign_up_text{
        font-size: 22px;
        font-weight: bold;
        margin-top: 0;
        padding: 10px 0 3px 4px;
    }
    .name_text{
        font-family: tahoma,georgia,arial,serif;
        background-color: #adb2b5;
        border-color: #8f979a;
        color: #000000 !important;
        height: 51px !important;
        width: 135px;
    }
    .name_text_label {
        float: left;
        margin: 0 8px 0 0 !important;
        width: 146px !important;
    }
    .name_text_lable_div {
        background-color: #8f979a !important;
        float: left;
        height: 51px !important;
        padding: 15px 4px 0 !important;
        width: 29px !important;
    }
    .text_field {
        font-family: tahoma,georgia,arial,serif;
        background-color: #adb2b5;
        border-color: #8f979a !important;
        color: #000000 !important;
        height: 51px !important;
        margin-bottom: 20px;
        width: 100%;
        border-radius: 0px;
        -moz-border-radius: 0px;
        -ms-border-radius: 0px;
        -o-border-radius: 0px;
        -webkit-border-radius: 0px;
    }
    #district{
        background-color: #adb2b5;
        border-color: #8f979a;
        border-radius: 0;
        color: #000000;
        float: left;
        font-size: 15px;
        height: 51px;
        width: 240px;
    }
    .custom_dropdown {
        width: 250px;
    }
    .custom_dropdown_dob {
        width: 164px;
    }
    .custom_dropdown_dob .droppify_wrapper {
        background-position: 122px 11px;
    }
    .custom_dropdown_medium {
        width: 100%;
    }
    .custom_dropdown_medium .droppify_wrapper {
        background-position: 98.5% 11px;
        width: 100%;
    }
    .selectParent{
        overflow: hidden;
        width: 217px;
    }
    .selectParent select{
        -webkit-appearance: none;
        -moz-appearance: none;
        -o-appearance: none;
        -ms-appearance: none;
        appearance: none;
        background: url("styles/layouts/tdsfront/image/drop_down.png") no-repeat scroll 180px center / 28px auto #adb2b5;
        color: #000000;
        font-size: 15px;
        height: 51px;
        padding: 10px;
        width: 237px;
    }
    .selectDob{
        overflow: hidden;
        width: 138px;
    }
    .selectDob select{
        background: url("styles/layouts/tdsfront/image/drop_down.png") no-repeat scroll 100px center / 28px auto #adb2b5;
        color: #000000 !important;
        font-size: 15px;
        height: 51px !important;
        padding: 10px;
        width: 160px !important;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }
    .selectMedium{
        overflow: hidden;
        width: 100%;
    }
    .selectMedium select{
        background: url("styles/layouts/tdsfront/image/drop_down.png") no-repeat scroll 415px center / 28px auto #adb2b5;
        color: #000000;
        float: left;
        font-size: 15px;
        height: 51px;
        padding: 10px;
        width: 100%;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }
    .country_code{
        width: 50px;
    }
    .mobile_code{
        width: 50px;
    }
    .mobile_no{
        width: 115px;
    }
    .school_name
    {
        width: 400px;
    }
    .date_of_birth{
        float: left;
        width: 546px !important;
    }
    .gender_label{
        float: left;
        font-size: 20px;
        padding: 20px 0 0;
    }
    .gender_ul{
        font-size: 20px;
        padding: 26px 0 0 50px !important;
    }
    fieldset input[type="checkbox"].custom_checkbox {
        clear: both;
        cursor: pointer;
        display: block;
        float: left;
        height: 15px;
        margin-top: 8px !important;
        width: 15px;
    }
    .checkbox_label_txt{
        color: #000000;
        cursor: pointer;
        font-size: 14px;
        margin: 7px;
    }
    .grades_ul {
        background-color: #adb2b5 !important;
        margin: 10px 0 0 0px;
        width: 100%;
    }
    .select_grades {
        background-color: #fff;
        border: 6px solid #adb2b5;
        color: #7c8487;
        font-size: 18px;
        margin-top: 12px;
        text-align: center;
        width: 97%;
    }
    .select_grades span {
        color: #7c8487;
        font-size: 12px;
        font-weight: normal;
        vertical-align: text-top;
    }
    /* Registration Form Ends*/
    .settings-btn {
        background: url("/styles/layouts/tdsfront/image/down_arrow.png") no-repeat scroll center center / 25px auto transparent;
        background-size: 40%;        
        cursor: pointer;
        height: 25px;
        width: 35px;
    }
    .settings-btn:hover {
        background: url("/styles/layouts/tdsfront/image/down_arrow_hover.png") no-repeat scroll center center / 25px auto transparent;
        background-size: 40%;        
    }
    .settings-btn-active {
        background: url("/styles/layouts/tdsfront/image/down_arrow_hover.png") no-repeat scroll center center / 25px auto transparent;
        background-size: 40%;        
    }
    .custombox_profile_no_padding {
        padding: 0px !important;
    }
    #settings_div {
        cursor: pointer;
    }
    #settings_div:hover {
        background-color: #fff;
    }
    .tz_social li:first-child:after {
        content: '|';
        color:#ccc;
    }

    .tz_social li {
        display: inline-block;
        margin:0px;
    }
    .tz_social a{
        font-size: 14px;
        margin-right: 0;
        width: auto;
    }
    /* Search Box */
    .search_box_head
    {
        cursor: pointer;
        display: block;
        float: right;
    }
    .search-elm-holder-div {
        border: 1.75px solid #DC3434;

        box-shadow: 0 0 9px 0 #aaa;
        -moz-box-shadow: 0 0 9px 0 #aaa;
        -webkit-box-shadow: 0 0 9px 0 #aaa;
        -ms-box-shadow: 0 0 9px 0 #aaa;
        -o-box-shadow: 0 0 9px 0 #aaa;

        width:190px;
        position:absolute;
        right:85px;
        top:60px;
        background-color: #fff;
        z-index: 5000;
    }
    .search-elm-list-div ul{
        margin:0px;
    }
    .search-elm-list-div ul li {
        cursor: pointer;
        font-size: 11px;
        margin: 0;
        padding: 10px;
    }
    .search-elm-list-div ul li:hover{
        background-color: #CC161E;
        color: #ffffff;
        -webkit-transition: background-color 0.5s ease;
        -moz-transition: background-color 0.5s ease;
        -o-transition: background-color 0.5s ease;
        -ms-transition: background-color 0.5s ease;
        transition: background-color 0.5s ease;
    }
    .search-elm-list-div ul li a {
        color: #666;
    }
    .search-elm-list-div ul li:hover a {
        color: #ffffff;
    }
    .search-elm-list-div ul li label {
        cursor: pointer;
        display: inline;
    }
    .search_box_head .settings-elm-holder-div {
        position: search;
        right: 20px;
        width: 349px;
    }
    .search_box_head img
    {
        width: 75%;
    }
    .search_box_head .search-elm-holder-div {
        position: absolute;
        right: 20px;
        width: 349px;
    }
    .search_box_head .search-elm-holder-div .search-elm-list-div ul li{
        padding: 2px;
    }
    /* Search Box */
    .settings-elm-holder-div {
        border: 1.75px solid #DC3434;

        box-shadow: 0 0 9px 0 #aaa;
        -moz-box-shadow: 0 0 9px 0 #aaa;
        -webkit-box-shadow: 0 0 9px 0 #aaa;
        -ms-box-shadow: 0 0 9px 0 #aaa;
        -o-box-shadow: 0 0 9px 0 #aaa;

        width:190px;
        position:absolute;
        right:85px;
        top:60px;
        background-color: #fff;
        z-index: 5000;
    }
    .settings-elm-list-div ul{
        margin:0px;
    }
    .settings-elm-list-div ul li {
        cursor: pointer;
        font-size: 11px;
        margin: 0;
        padding: 10px;
    }
    .settings-elm-list-div ul li:hover{
        background-color: #CC161E;
        color: #ffffff;
        -webkit-transition: background-color 0.5s ease;
        -moz-transition: background-color 0.5s ease;
        -o-transition: background-color 0.5s ease;
        -ms-transition: background-color 0.5s ease;
        transition: background-color 0.5s ease;
    }
    .settings-elm-list-div ul li a {
        color: #666;
    }
    .settings-elm-list-div ul li:hover a {
        color: #ffffff;
    }
    .settings-elm-list-div ul li label {
        cursor: pointer;
        display: inline;
    }
    .settings-elm-list-profile{
        background: url("/styles/layouts/tdsfront/image/update_profile.png") no-repeat scroll center center / 22px auto transparent;
        float: left;
        padding: 18px 10px 0 30px;
    }
    .settings-elm-list-div ul li:hover .settings-elm-list-profile {
        background: url("/styles/layouts/tdsfront/image/update_profile_hover.png") no-repeat scroll center center / 22px auto transparent;
        -webkit-transition: background 0.5s ease;
        -moz-transition: background 0.5s ease;
        -o-transition: background 0.5s ease;
        -ms-transition: background 0.5s ease;
        transition: background 0.5s ease;
    }
    .settings-elm-list-profile-picture {
        background: url("/styles/layouts/tdsfront/image/change_picture.png") no-repeat scroll center center / 22px auto transparent;
        float: left;
        padding: 18px 10px 0 30px;
    }
    .settings-elm-list-div ul li:hover .settings-elm-list-profile-picture {
        background: url("/styles/layouts/tdsfront/image/change_picture_hover.png") no-repeat scroll center center / 22px auto transparent;
        -webkit-transition: background 0.5s ease;
        -moz-transition: background 0.5s ease;
        -o-transition: background 0.5s ease;
        -ms-transition: background 0.5s ease;
        transition: background 0.5s ease;
    }
    .settings-elm-list-pref {
        background: url("/styles/layouts/tdsfront/image/account_settings.png") no-repeat scroll center center / 22px auto transparent;
        float: left;
        padding: 18px 10px 0 30px;
    }
    .settings-elm-list-div ul li:hover .settings-elm-list-pref {
        background: url("/styles/layouts/tdsfront/image/account_settings_hover.png") no-repeat scroll center center / 22px auto transparent;
        -webkit-transition: background 0.5s ease;
        -moz-transition: background 0.5s ease;
        -o-transition: background 0.5s ease;
        -ms-transition: background 0.5s ease;
        transition: background 0.5s ease;
    }
    .settings-elm-list-logout {
        background: url("/styles/layouts/tdsfront/image/log_out.png") no-repeat scroll center center / 22px auto transparent;
        float: left;
        padding: 18px 10px 0 30px;
    }
    .settings-elm-list-div ul li:hover .settings-elm-list-logout {
        background: url("/styles/layouts/tdsfront/image/log_out_hover.png") no-repeat scroll center center / 22px auto transparent;
        -webkit-transition: background 0.5s ease;
        -moz-transition: background 0.5s ease;
        -o-transition: background 0.5s ease;
        -ms-transition: background 0.5s ease;
        transition: background 0.5s ease;
    }
    .daredevel-tree-label {
        color: #000000;
    }
    .not-signed-up {
        background-color: #ee1c25;
        color: #fff;
        text-align: center;
    }
    .profile-image{
        cursor: pointer;
        position: relative;
    }
    .profile-image img {
        border: 0 none;
        float: left;
        margin: 0;
        padding: 0;
        width: 100%;
    }
    .upload-icon {
        left: 12px;
        position: absolute;
        top: 7px;
        z-index: 1;
    }
    .upload-icon img {
        width: 60%;
    }
    .upload-msg {
        background-color: #000;
        color: #fff;
        left: 0;
        opacity: 0.5;
        padding: 10px 10px 10px 55px;
        position: absolute;
        top: 0;
    }

    .pref-tree-image {
        padding: 15px 0 0 10px;
    }
    .pref-tree-image img {
        width: 45px;
    }
    .pref-tree-list-wrap{
        background-color: #ffffff;
        padding-bottom: 15px;
    }
    .pref-tree-list-wrap label {
        color: #ED1B24;
        font-size: 14px;
        font-weight: bold !important;
    }
    .custom-checkbox {
        width: auto;
    }
    .pref_label {
        font-weight: normal !important;
        margin-left: 10px;
    }
    .close-btn-div{
        border: 3px solid #aaa;
        border-radius: 100px;
        border-radius: 100px;
        -moz-border-radius: 100px;
        -webkit-border-radius: 100px;
        -ms-border-radius: 100px;
        -o-border-radius: 100px;
        color: #aaaaaa;
        cursor: pointer;
        height: 25px;
        position: absolute;
        right: 10px;
        text-align: center;
        top: 10px;
        width: 25px;
    }
    .close-btn {
        font-size: 23px;
        font-weight: bold;
        margin-top: -8px;
        -webkit-transform: rotate(45deg);
        -moz-transform: rotate(45deg);
        -o-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
    }
    .close-btn-div:hover {
        background-color: #93989C;
        -webkit-transition: background-color 0.5s ease;
        -moz-transition: background-color 0.5s ease;
        -o-transition: background-color 0.5s ease;
        -ms-transition: background-color 0.5s ease;
        transition: background-color 0.5s ease;
        color: #ffffff;
    }
    #before-login-user-wrapper{
        border-bottom: 3.5px solid #db3434;
        height: auto;
        margin-left: auto;
        margin-right: auto;
        width: 450px;
    }
    .before-login-user-header{
        background-color: #242021;
        height: 45px;
        position: relative;
        width: 100%;
    }
    .before-login-user-header-label{
        color: #ffffff;
        font-size: 20px;
        margin-left: auto;
        margin-right: auto;
        padding: 5px;
        text-align: center;
        width: 50%;
    }
    .before-login-user-icon-wrapper{
        left: 15px;
        position: absolute;
        top: 15px;
    }
    .before-login-user-body{
        padding: 40px 20px 50px;
    }
    .login-user-btn-wrapper{
        margin-left: auto;
        margin-right: auto;
        padding: 20px 15px 28px;
        width: 280px;
    }
    .login-user-btn{
        width: 250px;
    }
    .not_registered, .sign_up_free{
        width: 100%;
    }
    .not_registered{
        font-size: 12px;
        letter-spacing: 0.7px;
        padding: 7px 0 0;
    }
    .sign_up_free{
        font-size: 14px;
        margin-bottom: 15px;
    }
    .sign_up_free label{
        color: #666;
        cursor: pointer;
        padding-right: 5px;
    }
    .sign_up_free span{
        color: #de3427;
        cursor: pointer;
    }

    ::-webkit-input-placeholder { /* WebKit browsers */
        color: #444444 !important;
    }
    input:-moz-placeholder { /* Mozilla Firefox 4 to 18 */
        color: #444444 !important;
        opacity: 1;
    }
    ::-moz-placeholder { /* Mozilla Firefox 19+ */
        color: #444444 !important;
        opacity: 1;
    }
    :-ms-input-placeholder { /* Internet Explorer 10+ */
        color: #444444 !important;
    }

    #divResult
    {
        background-color: white;
        border-color: #dedede;
        border-style: solid;
        border-width: 0 1px 1px;
        clear: both;
        display: none;
        margin-top: -1px;
        max-height: 400px;
        overflow-y: auto;
        position: absolute;
        right: 19px;
        top: 102px;
        width: 350px;
        z-index: 1000;
    }
    .display_box
    {
        padding:7px; border-top:solid 1px #dedede; 
        font-size:12px;
    }
    .display_box:hover
    {
        background:#3bb998;
        color:#FFFFFF;
        cursor:pointer;
    }
    #toPopup
    {
        z-index: 999 !important;
    }
    .lang-filter {
        cursor: pointer;
        float: left;
        font-size: 11px;
        padding-top: 12px;
        width: 8%;
    }
    .lang-filter div {
        background-color: #dddee0;
        border-radius: 6px;
        color: #ffffff;
        float: left;
        margin-left: 3px;
        padding: 5px;
        text-align: center;
        width: 47%;
    }
    .lang-filter div.active {
        background-color: #DC3434;
    }
</style>
