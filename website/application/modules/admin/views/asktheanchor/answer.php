<script src="<?= base_url() ?>ckeditor/ckeditor.js"></script>
<script src="<?= base_url() ?>scripts/custom/customSchoolPage.js"></script>
<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section">Answer</h2>

                        <?php
                        if ($_POST)
                            create_validation($model);
                        ?>
                        <?php echo form_open('', array('class' => 'validate_form')); ?>

                        <fieldset class="top">
                            <label for="required_field">Question</label>
                            <div><b><?= $model->question ?> ? </b></div>
                        </fieldset>
                        <fieldset class="top">
                            <label for="required_field">Answer</label>
                            <div>
                                <textarea  id="answer" name="answer"><?= $model->answer ?></textarea>
                               
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

