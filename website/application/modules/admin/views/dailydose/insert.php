<script src="<?= base_url() ?>ckeditor/ckeditor.js"></script>
<script src="<?php echo  base_url() ?>scripts/custom/customdailydose.js"></script>
<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section"><?php echo ($model->id) ? "Edit" : "Add"; ?> Daily Dose</h2>

                        <?php
                        if ($_POST)
                            create_validation($model);
                        ?>
                        <?php echo form_open('', array('class' => 'validate_form')); ?>






                        <fieldset class="label_side top" style="display:none;">
                            <label for="required_field">Title</label>
                            <div>
                                <input id="title" name="title" value="<?php echo $model->title ?>" required="required"  type="text">

                            </div>
                        </fieldset>

                        <fieldset class="top">
                            <label for="required_field">Content</label>
                            <div>
                                <textarea class="ckeditor" id="content"  required="required"  name="content"><?= $model->content ?></textarea>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                        <fieldset class="top">
                            <label for="required_field">Share Content</label>
                            <div>
                                <textarea class="ckeditor" id="share_content"  required="required"  name="share_content"><?= $model->share_content ?></textarea>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                        <fieldset class="top">
                            <label for="required_field">Summary</label>
                            <div>
                                <textarea  id="summary" name="summary"><?= $model->summary ?></textarea>
                               
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

