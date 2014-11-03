<script src="<?= base_url() ?>ckeditor/ckeditor.js"></script>
<script src="<?= base_url() ?>scripts/custom/customByline.js"></script>
<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section"><?php echo ($model->id) ? "Edit" : "Add"; ?> In Picture Theme</h2>

                        <?php
                        if ($_POST)
                            create_validation($model);
                        ?>
                        <?php echo form_open('', array('class' => 'validate_form')); ?>

                        <fieldset class="label_side top">
                            <label for="required_field">Name<span>Theme Title</span></label>
                            <div>
                                <input id="name" name="name" value="<?php echo $model->name ?>"  type="text" class="required" minlength="3"  required >
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>

                        <fieldset class="label_side top">
                            <label for="required_field">Date Published</label>
                            <div>
                                <input id="publish_date" name="publish_date" class="datetimepicker_class"  value="<?= $model->publish_date ?>"  type="text" >  
                            </div>     
                        </fieldset>


                        <fieldset class="label_side top">
                            <label for="required_field">Is Current</label>
                            <div>
                                <?php
                                $is_current = array('0' => 'No', '1' => 'Yes');

                                if (!$model->is_current)
                                    $model->is_current = 0;
                                echo form_dropdown('is_current', $is_current, $model->is_current);
                                ?>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>

                        <fieldset class="label_side top">
                            <label for="required_field">Is Active</label>
                            <div>
                                <?php
                                $is_active = array('0' => 'No', '1' => 'Yes');

                                if (!$model->is_active)
                                    $model->is_active = 0;
                                echo form_dropdown('is_active', $is_active, $model->is_active);
                                ?>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>



                        <fieldset class="label_side top">
                            <label for="required_field">Theme Banner</label>
                            <div>
                                <button class="green" id="select_icon"  type="button">
                                    <span>Select Image</span>
                                </button>
                                <div  id="select_icon_box">
                                    <?php
                                    if ($model->image):
                                        $title = '<img src="' . base_url() . $model->image . '" width="50">';
                                    ?>
                                        <div><?php echo $title; ?><input type="hidden" name="image" value="<?= $model->image ?>"><a class="text-remove"></a></div>
                                        <?php
                                    endif;
                                    ?>
                                </div>
                            </div>
                        </fieldset>


                        <fieldset class="top">
                            <label for="required_field">Description</label>
                            <div>
                                <textarea class="ckeditor" id="description" name="description"><?= $model->description ?></textarea>
                                <div class="required_tag"></div>
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

