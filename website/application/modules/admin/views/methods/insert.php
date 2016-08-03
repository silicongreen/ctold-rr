<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section"><?php echo ($model->id)?"Edit":"Add";?> Methods</h2>
                        <?php 
                        if($_POST)
                        create_validation($model);
                        ?>
                        <?php echo form_open('',array('class' => 'validate_form'));?>
                            
                            
                            <fieldset class="label_side top">
                                <label for="required_field">Name<span>Unique field</span></label>
                                <div>
                                    <input id="name" name="name" value="<?php echo  $model->name ?>"  type="text" class="required"  required >
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            
                            <fieldset class="label_side top">
                                <label for="required_field">Method<span>Unique field</span></label>
                                <div>
                                    <input id="function" name="function" value="<?php echo  $model->function ?>"  type="text" class="required"  required >
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            <fieldset class="label_side top">
                                <label for="required_field">Allow From All</label>
                                <div>
                                    <?php
                                    $allow_from_all = array('No','Yes');
                                    echo form_dropdown('allow_from_all', $allow_from_all,$model->allow_from_all);
                                    ?>
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                        
                            <fieldset class="label_side top">
                                <label for="required_field">Controller</label>
                                <div>
                                    <?php
                                    echo form_dropdown('controller_id', $controller, $model->controller_id);
                                    ?>
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

