<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        
                         <h2 class="section"><?= ($model->id) ? "Edit" : "Add"; ?> User</h2>
                        <?php
                        if($_POST)
                        create_validation($model);
                        ?>
                        <?=form_open('',array('class' => 'validate_form'));?>
                            
                            <fieldset class="label_side top">
                                <label for="required_field">User Name<span>Unique field</span></label>
                                <div>
                                    <input id="username" name="username" value="<?= $model->username ?>" type="text" class="required" minlength="4" maxlength="20">
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            <fieldset class="label_side top">
                                <label for="required_field">Password<span>Min (6 char)</span></label>
                                <div>
                                    <input type="password" id="password"  name="password" lengthPassword="lengthPassword" >

                                </div>
                            </fieldset>

                            <fieldset class="label_side top">
                                <label for="required_field">Confirm Password<span>Match Password</span></label>
                                <div>
                                    <input type="password" id="confirm_password"  name="confirm_password"  equalTo ="#password" >

                                </div>
                            </fieldset>

                            <fieldset class="label_side top">
                                <label for="required_field">Email</label>
                                <div>
                                    <input id="admin_email" name="email" value="<?= $model->email ?>"  type="text" class="required email" required >
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>

                            <fieldset class="label_side top">
                                <label for="required_field">Full Name</label>
                                <div>
                                    <input id="name" name="name" value="<?= $model->name ?>"  type="text" class="required" required >
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>

                            <fieldset class="label_side top">
                                <label for="required_field">Group</label>
                                <div>
                                    <?php
                                    echo form_dropdown('group_id', $group, $model->group_id);
                                    ?>
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>




                            <div class="button_bar clearfix">
                                <button class="green" type="submit">
                                    <span>Submit</span>
                                </button>
                            </div>
                        <?=form_close();?>  
                    </div>
                </div>


            </div>

        </div>

