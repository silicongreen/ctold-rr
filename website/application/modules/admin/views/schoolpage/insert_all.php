<script src="<?= base_url() ?>ckeditor/ckeditor.js"></script>
<div id="pjax">
    <div id="wrapper" data-adminica-nav-top="1" data-adminica-side-top="1">
        <?php
            $widget = new Widget;

            $widget->run('sidebar');
        ?>
        <div id="main_container" class="main_container container_16 clearfix">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section">Add School Pages</h2>

                        <?php echo form_open('',array('class' => 'validate_form'));?>
                           
                           
                        
                            <fieldset class="label_side top">
                                <label for="required_field">School</label>
                                <div>
                                    <?php
                                    
                                    echo form_dropdown('school_id', $schools_dropDown,$model->school_id);
                                    ?>
                                  
                                </div>
                            </fieldset>
                            
                            <?php foreach($school_menu_dropDown as $key=>$value): ?>
                            <fieldset class="label top"><label><h2><?php echo $value; ?></h2></label></fieldset>
                            <fieldset class="label_side top">
                                <label for="required_field">Title</label>
                                <div>
                                    <input id="title_<?php echo $key;?>" name="title_<?php echo $key;?>"   type="text">
                                   
                                </div>
                            </fieldset>
                        
                           <fieldset class="top">
                                <label for="required_field">Content</label>
                                <div>
                                    <textarea class="ckeditor" id="content_<?php echo $key;?>" name="content_<?php echo $key;?>"></textarea>
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                        
                            <fieldset class="top">
                                <label for="required_field">Mobile Content</label>
                                <div>
                                    <textarea class="ckeditor" id="mobile_content_<?php echo $key;?>" name="mobile_content_<?php echo $key;?>"></textarea>
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            <?php endforeach; ?>

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

