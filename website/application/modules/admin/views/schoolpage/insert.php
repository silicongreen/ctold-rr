<script src="<?= base_url() ?>ckeditor/ckeditor.js"></script>
<script src="<?= base_url() ?>scripts/custom/customSchoolPage.js"></script>
<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section"><?php echo ($model->id)?"Edit":"Add";?> School Page</h2>

                        <?php
                        if($_POST)
                        create_validation($model);
                        ?>
                        <?php echo form_open('',array('class' => 'validate_form'));?>
                           
                           
                        
                            <fieldset class="label_side top">
                                <label for="required_field">School</label>
                                <div>
                                    <?php
                                    
                                    echo form_dropdown('school_id', $schools_dropDown,$model->school_id);
                                    ?>
                                  
                                </div>
                            </fieldset>
                            <fieldset class="label_side top">
                                <label for="required_field">Select Menu</label>
                                <div>
                                    <?php
                                    
                                    echo form_dropdown('menu_id', $school_menu_dropDown,$model->menu_id);
                                    ?>
                                  
                                </div>
                            </fieldset>
                        
                            <fieldset class="label_side top">
                                <label for="required_field">Title</label>
                                <div>
                                    <input id="title" name="title" value="<?php echo  $model->title ?>" required="required"  type="text">
                                   
                                </div>
                            </fieldset>
                        
                           <fieldset class="top">
                                <label for="required_field">Content</label>
                                <div>
                                    <textarea class="ckeditor" id="content" name="content"><?= $model->content ?></textarea>
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                        
                            <fieldset class="top">
                                <label for="required_field">Mobile Content</label>
                                <div>
                                    <textarea class="ckeditor" id="mobile_content" name="mobile_content"><?= $model->mobile_content ?></textarea>
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                        
                            <div class="clearfix block" id="gallery_box">
                        <?php if (isset($related_gallery)) : ?>
                            <?php
                            foreach ($related_gallery as $value):
                                if (is_array(@getimagesize(base_url() . $value->material_url)))
                                    $title = '<img src="' . base_url() . $value->material_url . '" width="70">';
                                else
                                {
                                    $url_parem = explode("/", $value->material_url);

                                    $last = count($url_parem) - 1;

                                    $value_url = $url_parem[$last];

                                    $title = '<a href="' . base_url() . $value->material_url . '">' . $value_url . '</a>';
                                }
                                ?>
                                <div class="gallery_image">
                                    <fieldset class="label_side top">
                                        <label for="required_field"><?= $title ?></label>
                                        
                                    </fieldset>
                                    <input type="hidden" name="related_img[]" value="<?= $value->material_url ?>">
                                    <a class="text-remove"></a>
                                </div>

                            <?php endforeach; ?>

                        <?php endif; ?>
                    </div> 

                    <fieldset class="label_side top">
                        <label for="required_field">Add Gallery</label>
                        <div>
                            <button class="green" id="select_media"  type="button">
                                <span>Select Media</span>
                            </button>
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

