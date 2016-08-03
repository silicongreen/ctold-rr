<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            
            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section">Pin News Home And Other Category</h2>
                       

                        <?php echo form_open('',array('class' => 'validate_form','enctype'=>'multipart/form-data'));?>  
                            
                        <input type="hidden" name="post_id" value="<?php echo $model->id; ?>" />
                            <fieldset class="top">
                                    <label for="required_field"><?php echo $model->headline; ?></label>        
                            </fieldset>
                            <fieldset class="label_side top">
                                    <label for="required_field">Category</label>
                                    <div>
                                        <?php
                                             echo form_dropdown('category_id', $select_categoryMenu);
                                        ?>  
                                    </div>     
                            </fieldset>
                            <fieldset class="label_side top">
                                    <label for="required_field">Position</label>
                                    <div>
                                        <?php
                                             echo form_dropdown('position', $position);
                                        ?>  
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

