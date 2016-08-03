<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section"><?php echo ($model->id)?"Edit":"Add";?> Gallery</h2>

                        <?php
                        if($_POST)
                        create_validation($model);
                        ?>
                        <?php echo form_open('',array('class' => 'validate_form'));?>
                           
                            <fieldset class="label_side top">
                                <label for="required_field">Gallery Name<span>Unique field</span></label>
                                <div>
                                    <input id="gallery_name" name="gallery_name" value="<?php echo  $model->gallery_name ?>"  type="text" class="required" minlength="3"  required >
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                        
                            <fieldset class="label_side top">
                                <label for="required_field">Gallery Type</label>
                                <div>
                                    <?php
                                    if(!$model->gallery_type)
                                        $model->gallery_type = 0;
                                    echo form_dropdown('gallery_type', $type,$model->gallery_type);
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

