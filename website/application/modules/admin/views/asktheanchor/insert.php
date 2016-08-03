<script src="<?= base_url() ?>ckeditor/ckeditor.js"></script>
<script src="<?= base_url() ?>scripts/custom/customSchoolPage.js"></script>
<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section"><?php echo ($model->id) ? "Edit" : "Add"; ?> question Answer</h2>

                        <?php
                        if ($_POST)
                            create_validation($model);
                        ?>
                        <?php echo form_open('', array('class' => 'validate_form')); ?>






                        <fieldset class="label_side top">
                            <label for="required_field">Name</label>
                            <div>
                                <input id="name" name="name" value="<?php echo $model->name ?>" required="required"  type="text">

                            </div>
                        </fieldset>

                        <fieldset class="top">
                            <label for="required_field">Question</label>
                            <div>
                                <textarea id="question"  required="question"  name="question"><?= $model->question ?></textarea>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                        <fieldset class="top">
                            <label for="required_field">Answer</label>
                            <div>
                                <textarea  id="answer" name="answer"><?= $model->answer ?></textarea>
                               
                            </div>
                        </fieldset>




                        <?php
                        if(!$model->date)
                        {
                            $model->date = date("Y-m-d");
                        }
                        
                        ?>
                        <fieldset class="label_side top">
                            <label for="required_field">Date</label>
                            <div id="publish_date_div" >
                                <input  type="text" name="date" value="<?= $model->date ?>" id="date" class="datepicker required" required    >
                                <div class="required_tag"></div>
                            </div>

                        </fieldset>
                        
                        <fieldset class="label_side top">
                                <label for="required_field">Status</label>
                                <div>
                                    <?php
                                    $a_status = array('0'=>'Inactive','1'=>'Active');
                                    
                                    if(!$model->status)
                                        $model->status = 0;
                                    echo form_dropdown('status', $a_status,$model->status);
                                    ?>
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

