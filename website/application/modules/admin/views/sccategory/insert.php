<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section"><?php echo  ($model->id) ? "Edit" : "Add"; ?> Science Rocks Topic</h2>
                        <?php
                        if ($_POST)
                            create_validation($model);
                        ?>

                        <?php echo  form_open('', array('class' => 'validate_form', 'enctype' => 'multipart/form-data')); ?>
                        

                                                  

                        <fieldset class="label_side top">
                            <label for="required_field">Category Name<span>Unique field</span></label>
                            <div>
                                <input id="name" name="name" value="<?php echo  $model->name ?>"  type="text" class="required" minlength="4"  required>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                        <fieldset class="label_side top">
                            <label for="required_field">Category Name<span>English</span></label>
                            <div>
                                <input id="en_name" name="en_name" value="<?php echo  $model->en_name ?>"  type="text" class="required" minlength="4"  required>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                        
                        <fieldset class="label_side top">
                            <label for="required_field">Details</label>
                            <div>
                                <textarea  name="details" id="details" ><?= $model->details ?></textarea>
                            </div>
                        </fieldset>
                        
                        
                        

                        

                        <fieldset class="label_side top">
                            <label for="required_field">Status</label>
                            <div>
                                <?php
                                $allow_from_all = array('0' => 'Inactive', '1' => 'Active');

                                if (!$model->status)
                                    $model->status = 1;
                                echo form_dropdown('status', $allow_from_all, $model->status);
                                ?>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>

                        <div class="button_bar clearfix">
                            <button class="green" type="submit">
                                <span>Submit</span>
                            </button>
                        </div>
                        <?php echo  form_close(); ?>  
                    </div>
                </div>


            </div>

        </div>

