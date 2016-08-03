<script src="<?= base_url() ?>scripts/custom/customSchool.js"></script>
<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section"><?php echo ($model->id) ? "Edit" : "Add"; ?> School</h2>

                        <?php
                        if ($_POST)
                            create_validation($model);
                        ?>
                        <?php echo form_open('', array('class' => 'validate_form', 'id' => 'school_create_form')); ?>

                        <?php if (!$edit) { ?>

                            <fieldset class="label_side top">
                                <label for="required_field">Schools</label>
                                <div>
                                    <?php echo form_dropdown('user_schools', $user_schools, array(), 'id="user_schools"'); ?>
                                </div>
                            </fieldset>

                        <?php } ?>

                        <fieldset class="label_side top">
                            <label for="required_field">Name<span>Unique field</span></label>
                            <div>
                                <input id="name" name="name" value="<?php echo $model->name ?>"  type="text" class="required" minlength="3"  required >
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>

                        <fieldset class="label_side top">
                            <label for="required_field">Location</label>
                            <div>
                                <input id="location" name="location" value="<?php echo $model->location ?>"  type="text">

                            </div>
                        </fieldset>
                        <fieldset class="label_side top">
                            <label for="required_field">District</label>
                            <div>
                                <input id="district" name="district" value="<?php echo $model->district ?>"  type="text">

                            </div>
                        </fieldset>
                        <fieldset class="label_side top">
                            <label for="required_field">Girls</label>
                            <div>
                                <input id="girls" name="girls" value="<?php echo $model->girls ?>"  type="text">

                            </div>
                        </fieldset>
                        <fieldset class="label_side top">
                            <label for="required_field">Boys</label>
                            <div>
                                <input id="boys" name="boys" value="<?php echo $model->boys ?>"  type="text">

                            </div>
                        </fieldset>

                        <fieldset class="label_side top">
                            <label for="required_field">Gender</label>
                            <div>
                                <?php
                                $a_gender = array(0 => 'All', 1 => 'Boys', 2 => 'Girls');

                                if (!$model->gender)
                                    $model->gender = 0;
                                echo form_dropdown('gender', $a_gender, $model->gender, 'id="gender"');
                                ?>
                            </div>
                        </fieldset>

                        <fieldset class="label_side top">
                            <label for="required_field">Medium </label>
                            <div>
                                <?php
                                $medium = array("Bangla & English" => "Bangla & English", "Bangla" => "Bangla", "English" => "English");

                                if (!$model->medium)
                                    $model->medium = "Bangla & English";
                                echo form_dropdown('medium', $medium, $model->medium, 'id="medium"');
                                ?>

                            </div>
                        </fieldset>

                        <fieldset class="label_side top">
                            <label for="required_field">Level</label>
                            <div>
                                <input id="level" name="level" value="<?php echo $model->level ?>"  type="text">

                            </div>
                        </fieldset>
                        <fieldset class="label_side top">
                            <label for="required_field">Shift</label>
                            <div>
                                <input id="shift" name="shift" value="<?php echo $model->shift ?>"  type="text">

                            </div>
                        </fieldset>


                        <fieldset class="label_side top">
                            <label for="required_field">Cover Image<span>Size must be 954px×310px</span></label>
                            <div>
                                <button class="green" id="select_cover"  type="button">
                                    <span>Select Cover</span>
                                </button>
                                <div  id="select_cover_box">
                                    <?php
                                    if ($model->cover):
                                        $title = '<img src="' . base_url() . $model->cover . '" width="50">';
                                        ?>
                                        <div><?= $title ?><input id="cover" type="hidden" name="cover" value="<?= $model->cover ?>"><a class="text-remove"></a></div>
                                        <?php
                                    endif;
                                    ?>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="label_side top">
                            <label for="required_field">Picture<span>Size must be 300px×200px(USE FOR SEARCH RESULT)</span></label>
                            <div>
                                <button class="green" id="select_picture"  type="button">
                                    <span>Select Picture</span>
                                </button>
                                <div  id="select_picture_box">
                                    <?php
                                    if ($model->picture):
                                        $title = '<img src="' . base_url() . $model->picture . '" width="50">';
                                        ?>
                                        <div><?= $title ?><input id="picture" type="hidden" name="picture" value="<?= $model->picture ?>"><a class="text-remove"></a></div>
                                        <?php
                                    endif;
                                    ?>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="label_side top">
                            <label for="required_field">Logo<span>Size must be 225px×225px</span></label>
                            <div>
                                <button class="green" id="select_logo"  type="button">
                                    <span>Select Logo</span>
                                </button>
                                <div id="select_logo_box">
                                    <?php
                                    if ($model->logo):
                                        $title = '<img src="' . base_url() . $model->logo . '" width="50">';
                                        ?>
                                        <div><?= $title ?><input id="logo" type="hidden" name="logo" value="<?= $model->logo ?>"><a class="text-remove"></a></div>
                                        <?php
                                    endif;
                                    ?>
                                </div>
                            </div>
                        </fieldset>
                        
                         <fieldset class="label_side top">
                            <label for="required_field">Is Visible</label>
                            <div>
                                <?php
                                $a_visible = array(1 => 'Yes', 2 => 'No');

                                if (!$model->is_visible)
                                    $model->is_visible = 1;
                                echo form_dropdown('is_visible', $a_visible, $model->is_visible);
                                ?>
                            </div>
                        </fieldset>




                        <div class="button_bar clearfix">
                            <button class="green" type="submit">
                                <span>Submit</span>
                            </button>
                        </div>
                        <?php echo form_close(); ?>  
                    </div>
                </div>


            </div>

        </div>

        <script type="text/javascript">
            $(document).ready(function(){
                $(document).on('change','#user_schools', function() {
                    
                    $.ajax({
                        url : $('#base_url').val() + 'admin/school/get_user_school_data',
                        type : 'post',
                        dataType : 'json',
                        data : {
                            school_id : $(this).val()
                        },
                        success : function(response) {
                            
                            $('#school_create_form').append('<input id="user_created_school_id" type="hidden" name="user_created_school_id" value="' + response.id + '">');
                            $('#school_create_form #name').val(response.school_name);
                            $('#school_create_form #location').val(response.address);
                            
                            $('#school_create_form #select_logo_box').html('');
                            $('#school_create_form #select_logo_box').append('<img src="' + $('#base_url').val() + response.logo + '" width="50">');
                            $('#school_create_form #select_logo_box').append('<input id="logo" type="hidden" name="logo" value="' + response.logo + '">');
                            
                            $('#school_create_form #picture').val(response.picture);
                            
                        },
                        error : function() {}
                    });
                    
                });
            });
        </script>