<div id="pjax">

    <div id="wrapper">
           
        <div class="isolate">
            <div class="center narrow">
                <div class="main_container full_size container_16 clearfix">
                    <div class="box">
                        <div class="block">
                            <div class="section">
                                <div style="width: 185px; margin: 5px auto;">
                                    <img  src="<?php echo  base_url() ?>images/champs_logo.jpg">
                                </div> 
                                <div class="alert dismissible alert_light">
                                    <img width="24" height="24" src="<?php echo  base_url() ?>images/icons/small/grey/locked.png">
                                    <strong>Welcome to champs21 Admin.</strong> Please enter your details to login.
                                </div>
                            </div>
                            
                            <?php
                                if($_POST)
                                create_validation($model);
                            ?>
                            
                            <?php echo form_open('',array('class'=>'validate_form'));?>
                                <fieldset class="label_side top">
                                    <label for="admin_name">Username</label>
                                    <div>
                                        <input type="text" id="username" name="username"  required />
                                    </div>
                                </fieldset>
                                
                                <fieldset class="label_side bottom">
                                    <label for="password">Password</label>
                                    <div>
                                        <input type="password" id="password" name="password" required />
                                    </div>
                                </fieldset>
                                
                                <?php if ( $has_captcha ) : ?>
                                <fieldset class="label_side top">
                                    <div>
                                        <?php echo $captcha;?>
                                    </div>
                                </fieldset>
                                
                                <fieldset class="label_side bottom">
                                    <div>
                                        <label for="captcha">Input the displayed capthca</label>
                                        <input type="text" id="captcha" name="captcha" required />
                                    </div>
                                </fieldset>
                                <?php endif; ?>
                            
                                <div class="button_bar clearfix">
                                    <button class="wide" type="submit">
                                        <img src="<?php echo  base_url();?>images/icons/small/white/key_2.png">
                                        <span>Login</span>
                                    </button>
                                </div>
                            <?php echo form_close();?>  
                        </div>
                    </div>
                </div>


            </div>
        </div>
