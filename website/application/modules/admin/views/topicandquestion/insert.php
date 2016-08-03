<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">

            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section"><?php echo ($model->id)? "Edit": "Add";?> level</h2>

                        <?php
                        if($_POST)
                            create_validation($model);
                        ?>
                        <?php echo form_open('',array('class' => 'validate_form')); ?>
                           
                            <fieldset class="label_side top">
                                <label for="required_field">Name</label>
                                <div>
                                    <input id="name" name="name" value="<?php echo $model->name; ?>" type="text" class="required" minlength="2" required>
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                        
                            <fieldset class="label_side top">
                                <label for="required_field">English Name</label>
                                <div>
                                    <input id="en_name" name="en_name" value="<?php echo $model->en_name; ?>" type="text" class="required" minlength="2" required>
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                        
                            <fieldset class="label_side top">
                                <label for="required_field">Topic</label>
                                <div>
                                    <?php
                                    echo form_dropdown('category_id', $category, $model->category_id);
                                    ?>

                                </div>
                            </fieldset> 
                            
                            
                            
                            <fieldset class="label_side top">
                                <label for="required_field">Default Time</label>
                                <div>
                                    <input id="title" name="time" value="<?php echo $model->time ?>" type="text" class="required" required>
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                        
                            <fieldset class="label_side top">
                                <label for="required_field">Default Mark</label>
                                <div>
                                    <input id="mark" name="mark" value="<?php echo $model->mark ?>" type="text" class="required" required>
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
