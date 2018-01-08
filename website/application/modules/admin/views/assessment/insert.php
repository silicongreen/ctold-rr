<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">

            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section"><?php echo ($model->id)? "Edit": "Add";?> Assessment</h2>

                        <?php
                        if($_POST)
                            create_validation($model);
                        ?>
                        <?php echo form_open('',array('class' => 'validate_form')); ?>
                           
                            <fieldset class="label_side top">
                                <label for="required_field">Title</label>
                                <div>
                                    <input id="title" name="title" value="<?php echo $model->title; ?>" type="text" class="required" minlength="3" required>
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            
                            <fieldset class="label_side top">
                                <label for="required_field">Type</label>
                                <div>
                                    <?php echo form_dropdown('type', $assessment_types, $model->type); ?>
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            
                            <fieldset class="label_side top">
                                <label for="required_field">Topic</label>
                                <div>
                                    <input id="title" name="topic" value="<?php echo $model->topic ?>" type="text" class="required" minlength="2" required>
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            
                            <fieldset class="label_side top">
                                <label for="required_field">Play Time (Minute)</label>
                                <div>
                                    <input id="title" name="time" value="<?php echo $model->time ?>" type="text" class="required" minlength="2" required>
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            
                            <div class="button_bar clearfix">
                                <button class="green" type="submit">
                                    <span>Submit</span>
                                </button>
                            </div>
                       <?php echo form_close();?>  
                    </div>
                </div>


            </div>

        </div>
    </div>
</div>
