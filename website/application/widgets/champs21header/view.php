<header class="champs-header col-xs-12 clearfix">

    <div class="col-xs-12">
        <?php if (array_key_exists($ci_key_for_cover, $this->config->config['cover']) && $this->config->config['cover'][$ci_key_for_cover] ) : ?>
        <img src="<?php echo base_url($this->config->config['cover-image'][$ci_key_for_cover]); ?>" width="100%" class="image-logo" alt="logo">
        <?php elseif ((array_key_exists($ci_key_for_cover, $this->config->config['LOGO']) && $this->config->config['LOGO'][$ci_key_for_cover]) ||
                (array_key_exists("allpage", $this->config->config['LOGO']) && $this->config->config['LOGO']["allpage"])) : ?>
        <div class="header-new" style="background: #fff; width: 77%;margin: 0px auto; height: 80px; padding: 18px 5px; ">
            <div class="logo-div">
                <a href="<?php echo base_url(); ?>" ><img  src="<?php echo base_url('styles/layouts/tdsfront/images/logo-new.png'); ?>" class="image-logo" alt="logo"></a>
            </div>
            
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
                <div class="login_reg_div_box">
                    
                    <?php if( !free_user_logged_in() ){ ?>
                    
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
                                    background-position: 50%;" >
                           
                        </div>
                        <div style="float: left; padding: 0 0 0 10px;" >
                            <h3 class="f2" style="font-size:15px;margin: 10px 0px;"><?php echo $user_data['nick_name']; ?></h3>
                        </div>

                        <div id="settings_div" style="float: right; float: right; margin: 10px 0 0; padding: 0px; background-color: #fff;">
                            <div style="" class="settings-btn"></div>
                        </div>

                        <div class="settings-elm-holder-div" style="display: none;">
                            <div class="settings-elm-list-div">
                                <ul>
                                    <li>
                                        <span><img width="20" src="<?php echo base_url('styles/layouts/tdsfront/image/account_settings.png'); ?>" /></span>
                                        <span id="free_user_profile">Update Profile</span>
                                    </li>
                                    <li>
                                        <span><img width="20" src="<?php echo base_url('styles/layouts/tdsfront/image/account_settings.png'); ?>" /></span>
                                        <span id="free_user_profile_picture"><label for="profile_image_file">Update Profile Picture</label></span>
                                    </li>
                                    <li id="pref_li">
                                        <span><img width="20" src="<?php echo base_url('styles/layouts/tdsfront/image/privacy.png'); ?>" /></span>
                                        <span>Preference Settings</span>
                                    </li>
                                    <li>
                                        <span><img width="20" src="<?php echo base_url('styles/layouts/tdsfront/image/logout.png'); ?>" /></span>
<!--                                            <span><a href="<?php echo base_url('logout_user'); ?>">Log Out</a></span>-->
                                        <span><a href="<?php echo base_url('logout_user');?>" class="logout_free">Log Out</a></span>
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
                ?>

                <?php echo form_open($action, array('class' => 'validate_form', 'id' => $frm_id, 'enctype' => 'multipart/form-data', 'autocomplete' => 'off')); ?>

                    <div class="clearfix" style="margin-left: auto; margin-right: auto; width: 550px; margin-top: 0px; ">

                        <div>
                            
                            <fieldset class="reg_logo">
                                <div>
                                    <img src="styles/layouts/tdsfront/image/register.png" width="60px" alt="Chmaps21.com" />
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
                            <div style="text-align: center; font-size: 20px; padding: 15px 0 10px 30px;">Mark Your Preferred Name</div>
                            <?php }?>
                            
                            <?php if ( !$edit || empty($model->user_type) ) { ?>
                                <fieldset style="margin: 5px 0;">
                                    <div class="user_type_div">
                                        <label class="user_type_dialob_label">I am a... </label>
                                        <ul class="radio-holder">
                                            <?php $i = 0; foreach ($free_user_types as $key => $value) { ?>
                                                <li class="user_type_radio" <?php echo ($i > 0) ? 'style="padding-left: 60px !important;"' : '' ?>>
                                                    <input class="css-checkbox" id="<?php echo $value; ?>" name="user_type" value="<?php echo $key; ?>" type="radio" 
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

                                <div class="clearfix horizontal-line" <?php echo ($edit) ? 'style="margin-bottom: 30px"' : ''; ?>></div>
                            <?php } ?>

                            <?php if ( !$edit ) { ?>

        <!--                        <div class="sign_up_text">
                                    Sign up using email address
                                </div>-->

                                <fieldset style="margin: 5px 0;">
                                    <div>
                                        <input placeholder="Enter Email Address" class="f5 email_txt" id="email" name="email" value="<?php echo $model->email; ?>" type="text" maxlength="60" <?php echo ($edit) ? 'readonly="readonly"' : ''; ?>>
                                        <input placeholder="Re-enter Email Address" class="f5 email_txt" id="cnf_email" name="cnf_email" value="<?php echo $model->email; ?>" type="text" maxlength="60" <?php echo ($edit) ? 'readonly="readonly"' : ''; ?>>
                                    </div>
                                </fieldset>

                                <fieldset style="margin: 5px 0;">
                                    <div>
                                        <input placeholder="Enter Password (Minimum 6 Charecters)" class="f5 email_txt" id="password" name="password" value="" type="password" maxlength="60" />
                                        <input placeholder="Re-enter Password" class="f5 email_txt" id="cnf_password" name="cnf_password" value="" type="password" maxlength="60" />
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
                                                <input class="f5 name_text" placeholder="First Name" id="first_name" name="first_name" value="<?php echo $model->first_name; ?>" type="text" maxlength="60">
                                            </label>
                                        </li>
                                        <li>
                                            <div class="name_text_lable_div">
                                                <input class="css-checkbox" id="middle_name" name="nick_name" value="2" type="radio" <?php echo ($model->nick_name == '2') ? 'checked="checked"' : ''; ?>>
                                                <label for="middle_name" class="css-label"></label>
                                            </div>
                                            <label for="middle_name" class="user_type_label">
                                                <input class="f5 name_text" placeholder="Middle Name" id="middle_name" name="middle_name" value="<?php echo $model->middle_name; ?>" type="text" maxlength="60" >
                                            </label>
                                        </li>
                                        <li>
                                            <div class="name_text_lable_div">
                                                <input class="css-checkbox" id="last_name" name="nick_name" value="3" type="radio" <?php echo ($model->nick_name == '3') ? 'checked="checked"' : ''; ?>>
                                                <label for="last_name" class="css-label"></label>
                                            </div>
                                            <label for="last_name" class="user_type_label">
                                                <input class="f5 name_text" placeholder="Last Name" id="last_name" name="last_name" value="<?php echo $model->last_name; ?>" type="text" maxlength="60">
                                            </label>
                                        </li>
                                    </ul>

                                </fieldset>

                                <fieldset>

                                    <ul class="radio-holder">    
                                        <li>
                                            <div class="f5 selectParent">
                                                <?php
                                                //$class_string = '';
                                                $class_string = 'id="tds_country_id" class="f5"';
                                                echo form_dropdown('tds_country_id', $country, $country['id'], $class_string);
                                                ?>
                                            </div>
                                        </li>
                                        <li>
                                            <input class="f5" placeholder="District" id="district" name="district" value="<?php echo $model->district; ?>" type="text" maxlength="60">
                                        </li>
                                    </ul>

                                </fieldset>

                                <fieldset>

                                    <ul class="radio-holder">    
                                        <li>
                                            <input placeholder="880" class="f5 name_text country_code" id="country_code" name="country_code" value="" type="text" maxlength="6">
                                        </li>
                                        <li>
                                            <input placeholder="Mobile Number" class="f5 name_text mobile_no" id="mobile_no" name="mobile_no" value="<?php echo $model->mobile_no; ?>" type="text" maxlength="60">
                                        </li>
                                    </ul>
                                </fieldset>

                                <fieldset>

                                    <ul class="radio-holder">    

                                        <li>
                                            <div class="f5 selectDob">
                                                <?php

                                                $dob_day = NULL;
                                                $dob_month = NULL;
                                                $dob_year = NULL;

                                                if ( isset($model->dob) && !empty($model->dob) && ($model->dob != '0000-00-00') ) {
                                                    $ar_dob = explode('-', $model->dob);

                                                    $dob_day = $ar_dob[2];
                                                    $dob_month = $ar_dob[1];
                                                    $dob_year = $ar_dob[0];

                                                }

                                                $days[NULL] = 'Select Day';
                                                for($i = 1; $i <= 31; $i++){
                                                    if(strlen($i) < 2){
                                                        $i = '0'.$i;
                                                    }
                                                    $days[$i] = $i;
                                                }

                                                $class_string = 'id="dob_day" class="f5"';
                                                echo form_dropdown('dob_day', $days, $dob_day, $class_string);
                                                ?>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="f5 selectDob">
                                                <?php

                                                $months[NULL] = 'Select Month';
                                                for($i = 1; $i <= 12; $i++){
                                                    if(strlen($i) < 2){
                                                        $i = '0'.$i;
                                                    }
                                                    $months[$i] = $i;
                                                }

                                                $class_string = 'id="dob_month" class="f5"';
                                                echo form_dropdown('dob_month', $months, $dob_month, $class_string);
                                                ?>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="f5 selectDob">
                                                <?php

                                                $year = date('Y');

                                                $years[NULL] = 'Select Year';

                                                $num = $year - 70;

                                                for($i = $year; $i >= $num; $i--){
                                                    if(strlen($i) < 2){
                                                        $i = '0'.$i;
                                                    }
                                                    $years[$i] = $i;
                                                }

                                                $class_string = 'id="dob_year" class="f5"';
                                                echo form_dropdown('dob_year', $years, $dob_year, $class_string);
                                                ?>
                                            </div>
                                        </li>

                                    </ul>

                                </fieldset>

                                <?php if( ($user_data['type'] == 1) || ($user_data['type'] == 4) ) { ?>
                                <fieldset>
                                    <div class="selectMedium">
                                        <?php
                                        $ar_input_data =array(
                                            'class' => 'f5 text_field',
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
                                    <div class="selectMedium">
                                        <?php
                                        $ar_input_data =array(
                                            'class' => 'f5 text_field',
                                            'placeholder' => 'School Name',
                                            'maxlength' => '255',
                                            'size' => '560',
                                            'name' => 'school_name',
                                            'value' => $model->school_name,
                                        );
                                        echo form_input($ar_input_data);
                                        ?>
                                    </div>
                                </fieldset>

                                <?php if( $user_data['type'] == 3 ) { ?>
                                <fieldset>
                                    <div class="selectMedium">
                                        <?php
                                        $ar_input_data_tf =array(
                                            'class' => 'f5 text_field',
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
                                    <div class="selectMedium">
                                        <?php
                                        $class_string = 'id="medium" class="f5"';
                                        echo form_dropdown('medium', $medium, NULL, $class_string);
                                        ?>
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
                                            <li class="user_type_radio" style="padding-top: 0px;">
                                                <input class="css-checkbox" id="male" name="gender" value="1" type="radio" <?php echo ($model->gender == '1') ? 'checked="checked"' : ''; ?>>
                                                <label for="male" class="user_type_label">Male</label>
                                            </li>
                                            <li class="user_type_radio" style="padding-top: 0px;">
                                                <input class="css-checkbox" id="female" name="gender" value="0" type="radio" <?php echo ($model->gender == '0') ? 'checked="checked"' : ''; ?>>
                                                <label for="female" class="user_type_label">Female</label>
                                            </li>
                                        </ul>
                                    </div>
                                </fieldset>

                            <?php } ?>

                        </div>

                        <div class="clearfix" style="margin-left: auto; margin-right: auto; margin-top: 20px; text-align: center; margin-bottom: 30px;">
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
                            <div style="height: 100%; margin-left: auto; margin-right: auto; width: 55%;">
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

                        <div class="clearfix" style="margin-left: auto; margin-right: auto; width: 380px; margin-top: 10px; ">

                            <div>
                                <fieldset class="login_logo">
                                    <div>
                                        <img src="styles/layouts/tdsfront/image/Login.png" width="60px" alt="Chmaps21.com" />
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
                                        <input class="f5 login_text login_user_name_back_image login_back_image_property" id="email" name="email" value="" type="text" maxlength="60" placeholder="Email" size="60">
                                    </div>
                                </fieldset>

                                <fieldset>
                                    <div class="login_text_div">
                                        <input class="f5 login_text login_password_back_image login_back_image_property" id="password" name="password" value="" type="password" maxlength="60" placeholder="Password"  size="60">
                                    </div>
                                </fieldset>

                                <fieldset>

                                    <div>
                                        <div class="login_remember_me_div">
                                            <input id="remember_me_chk" name="remember_me" value="1" type="checkbox" checked="checked">
                                            <!--<label class="login_checkbox_label" for="remember_me"></label>-->
                                            <label for="remember_me_chk" class="f5 login_checkbox_label_txt">Remember Me</label>
                                        </div>

                                        <div class="f5 login_reset_password">
                                            <a href="javascript:void(0);">Reset Password</a>
                                        </div>
                                    </div>

                                </fieldset>

                            </div>

                            <div class="clearfix" style="margin-left: auto; margin-right: auto; width:350px;">
                                <button class="login_red" type="submit">
                                    <span class="clearfix f2">
                                        Sign in
                                    </span>
                                    <div class="login_arrow-right"></div>
                                </button>
                            </div>

                            <div class="clearfix center" style="padding-top: 60px;"><strong>Or Login with </strong></div>

                            <div class="sns-button-div" style="width: 100%">
                                <div style="height: 100%; margin-left: auto; margin-right: auto; width: 40%;">
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
                
                <div style="width: 10%; float: right;">
                    <?php if( free_user_logged_in() ){ ?>
                    <ul class="ch-grid-header">
                        <li>
                                <?php if ( get_notification() ) : ?>
                                    <div class="circle"><?php echo get_notification(); ?></div>
                                <?php endif; ?>
                                <div class="ch-item-header">				
                                        <div class="ch-info-wrap-header">
                                                <div class="ch-info-header">
                                                        <div class="ch-info-front-header ch-img-1-header"></div>
                                                        <div class="ch-info-back-header">
                                                            <img class="ch-img-1-header-hover " src="<?php echo base_url('merapi/images/icon/notification_hover.png');?>">
                                                        </div>	
                                                </div>
                                        </div>
                                        <!--<span>Notification</span>-->
                                </div>

                        </li>
                    </ul>
                    <?php }?>
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
        <div class="fixed-menu">
            <ul class="fixed-menu-ul">
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
                <li data="magic_mart" class="before-login-user">
                <!-- onclick="location.href='//<?php echo base_url('/market'); ?>'" -->
                    <div class="icon-magic-mart">&nbsp;</div> 
                </li>
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

                    <p class="custom_message"></p>
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
        <!--   Good Read, Candle, Magic Mart and Read Later Pop up     -->
        
                <?php
                        $widget = new Widget;
                        $widget->run('champs21candle', $ci_key);
                ?>
                <?php
                        //$widget = new Widget;
                        //$widget->run('champs21schoolsearch', $ci_key);
                ?>
        <div class="category-fixed-menu">
            <div class="f2 category-fixed-menu-box">            
                <label class="category-fixed-menu-title" for="menu-toggle">Menu</label>
         
                <input type="checkbox" id="menu-toggle"/>
                <div class="category-fixed-menu-list" id="category-fixed-menu-list">
                    <?php
                    $widget = new Widget;
                    $widget->run('champs21slidemenu');
                    ?>  
                </div><!--end class plazart-megamenu-->            
            </div>            
        </div>
        
        
        
        
        
        
        
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
                           <img src="styles/layouts/tdsfront/image/champs_logo.png" width="250px" alt="Chmaps21.com">
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
          Opps!
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
<style>
.logo-div
{
	float: left; 
	width: 40%; 
	padding-left: 20px;        
}

.login_reg_div
{
	float: right;
	width: 27%;
	padding-right: 15px;
}
.login_reg_div_box
{
    width: 90%;
    float: left;
}
@media all and (min-width: 600px) and (max-width: 799px) {

	.login_reg_div {
            float: right;
            padding-right: 15px;
            width: 35%;
        }
        
}
@media all and (min-width: 450px) and (max-width: 599px) {
  .logo-div
	{		
		float:none;                
                width: 40%;
                margin:0px auto;
		padding-left: 0px;
	}

	.login_reg_div
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
        }
        
}
@media all and (max-width: 449px) {
  .logo-div
	{		
		float:none;                
                width: 80%;
                margin:0px auto;
		padding-left: 0px;
	}

	.login_reg_div
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
        .category-fixed-menu
        {
            top:115px;            
        }
        .fixed-menu
        {
            top:115px !important;            
        }
        .icon-good-read
        {
            width:60px !important;
            height:41px !important;
        }
        .icon-candle
        {
            width:60px !important;
            height:41px !important;
        }
        .icon-magic-mart
        {
           width:60px !important;
            height:41px !important;
        }
        .category-fixed-menu-box label {
            margin-bottom:0px;
            float:right;
            width:100%;
            border-bottom : 2px solid #6c5754;
            padding:10px !important;
            font-size:8px;
          }
} 

.category-fixed-menu
{
    padding:0;
    position:fixed;
    right:0px;
    z-index:1000;
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
    padding:0;
    position:fixed;
    left:0px;
	top:170px;
}
.category-fixed-menu-list{
    padding: 21px;
    display: block;
    background:#414952;
    float:right;
    clear:both;
    width:210px;
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
.icon-magic-mart
{
    background: url(<?php echo base_url('styles/layouts/tdsfront/image/magicmart_red.png'); ?>) no-repeat;
    background-size:70%;
    width:118px;
    height:79px;
}
.icon-magic-mart:hover
{
    background: url(<?php echo base_url('styles/layouts/tdsfront/image/magicmart_black.png'); ?>) no-repeat;
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
        width: 400px !important;
    }
    .login_title{
        color: #000000;
        font-size: 35px;
        height: 48px;
        margin: 10px auto 0;
        text-align: center;
        width: 400px !important;
    }
    
    #login_frm fieldset{
        clear: both;
        height: 48px;
        margin: 1px auto 0;
        width: 350px !important;
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
        width: 350px;
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
        width: 350px !important;
    }
    .login_text{
        color: #000000 !important;
        width: 350px !important;
        height: 40px !important;
        background-color: #ADB2B5 !important;
        font-size: 15px !important;
        text-align: center;
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
        /*        border-radius: 8px;
                 -ms-border-radius: 8px;
                -o-border-radius: 8px;
                -webkit-border-radius: 8px;*/
        width: 50%;
        height: 100%;
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
        margin: 10px auto;
        text-align: center;
        width: 400px !important;
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
        margin: 10px auto 0;
        text-align: center;
        width: 550px !important;
    }
    
    fieldset {
        clear: both;
        margin: -10px 0px 0px;
        text-align: center !important;
    }
    
    .user_type_dialob_label {
        background-color: #dddddd;
        color: #b0b8bb;
        font-size: 20px;
        height: 35px;
        padding-top: 0;
        text-align: center;
        width: 550px !important;
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

/*    input[type=radio].css-checkbox  label.css-label {
        border: 3px solid #a7a8aa;
        border-radius: 1000px;
        -o-border-radius: 1000px;
        -moz-border-radius: 1000px;
        -ms-border-radius: 1000px;
        -webkit-border-radius: 1000px;
        cursor: pointer;
        display: inline-block;
        float: left;
        height: 20px !important;
        margin-right: 10px;
        width: 20px !important;
    }

    input[type=radio].css-checkbox:checked  label.css-label {
        background: url('styles/layouts/tdsfront/image/radio-checked.png') top no-repeat;
        background-color: #fff;
        background-position: 2.7px center !important;
        background-size: 9px auto !important;
    }*/
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

    ul.radio-holder{
        display: inline;
        float: left;
        list-style: none outside none;
        margin: 0 0 10px 0;
        padding: 0;
        radio-holder
    }
    ul.radio-holder li{
        float: left;
        text-align: left !important;
        padding-left: 20px !important;
    }
    ul.radio-holder li:last-child {
        margin-right: 0px !important;
    }
    ul.radio-holder li:first-child {
        margin-left: 0px !important;
        padding-left: 0 !important;
    }
    .user_type_label {
        clear: none !important;
        color: #000 !important;
        cursor: pointer;
        float: left !important;
        font-size: 18px;
    }
    .user_type_radio{
        padding-top: 10px;
        margin: 0 !important;
    }
    .red{
        background-color: #DE3427;
        border: none;
        font-size: 20px;
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
        background-color: #adb2b5;
        border-radius: 0px;
        -moz-border-radius: 0px;
        -webkit-border-radius: 0px;
        -o-border-radius: 0px;
        -ms-border-radius: 0px;
        color: #000000 !important;
        font-size: 15px;
        height: 36px;
        width: 49%;
    }
    .center{
        margin-left: auto;
        margin-right: auto;
        padding-top: 15px;
        text-align: center;
        width: 50%;
    }
    .sns-button-div{
        height: 70px;
        margin-left: auto;
        margin-right: auto;
        margin-top: 15px;
        width: 50%;
    }
    .fb-button{
        float: left;
        height: 75px;
        /*margin-left: 30px;*/
        width: 75px;
    }
    .fb-button button {
        background: url("styles/layouts/tdsfront/image/facebook.png") no-repeat scroll 0 0 / 65px auto transparent;
        border: medium none;
        color: #de3427;
        font-family: inherit;
        margin: 0;
        padding: 40px;
        position: relative;
    }
    .fb-button button:hover {
        background: url("styles/layouts/tdsfront/image/facebook_hover.png") no-repeat scroll 0 0 / 65px auto transparent;
    }
    
    .google-button{
        float: left;
        height: 75px;
        /*margin-left: 15px;*/
        width: 75px;
    }
    .google-button button {
        background: url("styles/layouts/tdsfront/image/google_plus.png") no-repeat scroll 0 0 / 65px auto transparent;
        border: medium none;
        color: #de3427;
        font-family: inherit;
        margin: 0;
        padding: 40px;
        position: relative;
    }
    .google-button button:hover {
        background: url("styles/layouts/tdsfront/image/google_plus_hover.png") no-repeat scroll 0 0 / 65px auto transparent;
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
        background-color: #adb2b5;
        border-color: #8f979a !important;
        border-radius: 0px;
        -moz-border-radius: 0px;
        -ms-border-radius: 0px;
       -o-border-radius: 0px;
       -webkit-border-radius: 0px;
        color: #000000 !important;
        font-size: 18px !important;
        height: 51px !important;
        width: 140px !important;
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
        background-color: #adb2b5;
        border-color: #8f979a !important;
        border-radius: 0px;
        -moz-border-radius: 0px;
        -ms-border-radius: 0px;
       -o-border-radius: 0px;
       -webkit-border-radius: 0px;
        color: #000000 !important;
        font-size: 18px !important;
        height: 51px !important;
        margin-bottom: 20px !important;
        width: 546px;
    }
    #district{
        background-color: #adb2b5;
        border-color: #8f979a !important;
        border-radius: 0px;
        -moz-border-radius: 0px;
        -ms-border-radius: 0px;
       -o-border-radius: 0px;
       -webkit-border-radius: 0px;
        color: #000000 !important;
        float: left;
        font-size: 18px !important;
        height: 51px !important;
        width: 263px;
    }
    .selectParent{
        width: 263px;
        overflow:hidden;
    }
    .selectParent select{
        background: url("styles/layouts/tdsfront/image/drop_down.png") no-repeat scroll 225px center / 28px auto #adb2b5;
        color: #000000 !important;
        float: left;
        font-size: 18px !important;
        height: 51px !important;
        padding: 10px;
        width: 285px !important;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }
    .selectDob{
        width: 169px;
        overflow:hidden;
    }
    .selectDob select{
        background: url("styles/layouts/tdsfront/image/drop_down.png") no-repeat scroll 131px center / 28px auto #adb2b5;
        color: #000000 !important;
        float: left;
        font-size: 18px !important;
        height: 51px !important;
        padding: 10px;
        width: 190px !important;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }
    .selectMedium{
        /*padding-top: 21px;*/
        overflow: hidden;
        width: 546px;
    }
    .selectMedium select{
        background: url("styles/layouts/tdsfront/image/drop_down.png") no-repeat scroll 507px center / 28px auto #adb2b5;
        color: #000000 !important;
        float: left;
        font-size: 18px !important;
        height: 51px !important;
        padding: 10px;
        width: 568px !important;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }
    .country_code{
        width: 100px !important;
    }
    .mobile_no{
        width: 426px !important;
    }
    .date_of_birth{
        float: left;
        width: 546px !important;
    }
    .gender_label{
        float: left;
        font-size: 24px;
        padding: 25px 0 0;
        text-align: left;
        width: 150px;
    }
    .gender_ul{
        float: left !important;
        font-size: 24px;
        padding: 36px 0 0 25px !important;
    }
    fieldset input[type="checkbox"].custom_checkbox {
        clear: both;
        display: block;
        float: left;
        height: 19px;
        margin-right: 5px !important;
        margin-top: 8px !important;
        width: 19px;
        cursor: pointer;
    }
/*    
    fieldset input[type="checkbox"]  label.checkbox_label{
        background-color: #fff;
        clear: both !important;
        cursor: pointer;
        float: left;
        height: 19px !important;
        margin-top: 10px;
        width: 19px !important;
        border-radius: 4px;
        -moz-border-radius: 4px;
        -webkit-border-radius: 4px;
        -ms-border-radius: 4px;
        -o-border-radius: 4px;
    }
    fieldset input[type="checkbox"]:checked  label.checkbox_label{
        background: url('styles/layouts/tdsfront/image/tick_mark.png') top no-repeat;
        background-color: #fff;
    }*/

    .checkbox_label_txt{
        color: #000000;
        margin: 7px !important;
        cursor: pointer;
    }
    .grades_ul {
        background-color: #adb2b5 !important;
        color: #000000 !important;
        margin-top: 10px;
        width: 546px;
    }
    .select_grades {
        background-color: #fff;
        border: 6px solid #adb2b5;
        color: #7c8487;
        font-size: 18px;
        font-weight: bold;
        margin-top: 12px;
        padding: 0 0 0 15px;
        text-align: left;
        width: 80%;
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
    }
    .tz_social a{
        font-size: 14px;
        margin-right: 0;
        width: auto;
    }
    .settings-elm-holder-div {
        width:13%;
        padding:15px 0px;
        position:absolute;
        right:201px;
        top:60px;
        background-color: gray;
        z-index: 5000;
    }
    .settings-elm-list-div ul{
        margin:0px;
    }
    .settings-elm-list-div ul li {
        color: #ffffff;
        cursor: pointer;
        font-size: 11px;
    }
    .settings-elm-list-div ul li:hover {
        background-color: #CC161E;
        -webkit-transition: background-color 0.5s ease;
        -moz-transition: background-color 0.5s ease;
        -o-transition: background-color 0.5s ease;
        -ms-transition: background-color 0.5s ease;
        transition: background-color 0.5s ease;
    }
    .settings-elm-list-div ul li a {
        color: #ffffff !important;
    }
    .settings-elm-list-div ul li span {
        padding-left: 7px;
    }
    .settings-elm-list-div ul li span label {
        cursor: pointer;
    }
    .daredevel-tree-label {
        color: #000000;
    }
/*    .login-user-btn{
        background: none repeat scroll 0 0 #72bf43 !important;
        border: 1px solid #72bf43;
        font-size: 14px !important;
        position: relative;
        text-align: center;
        text-decoration: none;
        transition: all 0.1s ease 0s;
        padding: 0 7px;
        
        -webkit-transition: all 0.1s;
        -ms-transition: all 0.1s;
        -o-transition: all 0.1s;
        -moz-transition: all 0.1s;
        transition: all 0.1s;
        
        -webkit-box-shadow: 0 5px 0 #4b9106;
        -ms-box-shadow: 0 5px 0 #4b9106;
        -o-box-shadow: 0 5px 0 #4b9106;
        -moz-box-shadow: 0 5px 0 #4b9106;
        box-shadow: 0 5px 0 #4b9106;
    }
    .login-user-btn:hover {
        background-color: #4b9106 !important;
        border: 1px solid #4b9106 !important;
        -webkit-transition: background-color 0.5s ease !important;
        -moz-transition: background-color 0.5s ease !important;
        -o-transition: background-color 0.5s ease !important;
        -ms-transition: background-color 0.5s ease !important;
        transition: background-color 0.5s ease !important;
    }
    .login-user-btn:active{
        -webkit-box-shadow: 0px 2px 0px #72bf43;
        -ms-box-shadow: 0px 2px 0px #72bf43;
        -o-box-shadow: 0px 2px 0px #72bf43;
        -moz-box-shadow: 0px 2px 0px #72bf43;
        box-shadow: 0px 2px 0px #72bf43;
        position:relative;
        top:7px;
    }
    
    .register-user-btn {
        background: none repeat scroll 0 0 #f4f929 !important;
        border: 1px solid #f4f929;
        font-size: 14px !important;
        position: relative;
        text-align: center;
        text-decoration: none;
        transition: all 0.1s ease 0s;
        padding: 0 7px;
        
        -webkit-transition: all 0.1s;
        -ms-transition: all 0.1s;
        -o-transition: all 0.1s;
        -moz-transition: all 0.1s;
        transition: all 0.1s;
        
        -webkit-box-shadow: 0 5px 0 #c2c444;
        -ms-box-shadow: 0 5px 0 #c2c444;
        -o-box-shadow: 0 5px 0 #c2c444;
        -moz-box-shadow: 0 5px 0 #c2c444;
        box-shadow: 0 5px 0 #c2c444;
    }
    .register-user-btn:hover {
        background-color: #c2c444 !important;
        border: 1px solid #c2c444 !important;
        -webkit-transition: background-color 0.5s ease !important;
        -moz-transition: background-color 0.5s ease !important;
        -o-transition: background-color 0.5s ease !important;
        -ms-transition: background-color 0.5s ease !important;
        transition: background-color 0.5s ease !important;
    }
    .register-user-btn:active{
        -webkit-box-shadow: 0px 2px 0px #f4f929;
        -ms-box-shadow: 0px 2px 0px #f4f929;
        -o-box-shadow: 0px 2px 0px #f4f929;
        -moz-box-shadow: 0px 2px 0px #f4f929;
        box-shadow: 0px 2px 0px #f4f929;
        position:relative;
        top:7px;
    }*/
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
</style>