<script src="<?= base_url() ?>scripts/custom/customByline.js"></script>
<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section"><?php echo ($model->id) ? "Edit" : "Add"; ?> New Photo (Gallery)</h2>

                        <?php
                        if ($_POST)
                            create_validation($model);
                        ?>
                        <?php echo form_open('', array('class' => 'validate_form')); ?>
               


                        <fieldset class="label_side top">
                            <label for="required_field">Select Gallery</label>
                            <div>
                                <?php
                                echo form_dropdown('gallery_id', $gallery_list, $model->gallery_id);
                                ?>
                                <div class="required_tag"></div>

                            </div>
                        </fieldset>  

                        

                        <fieldset class="label_side top">
                            <label for="required_field">Select Photo</label>
                            <div>
                                <button class="green" id="select_icon"  type="button">
                                    <span>Select Image</span>
                                </button>
                                <div  id="select_icon_box">
                                    <?php
                                    if ($model->image):
                                        $title = '<img src="' . base_url() . $model->image . '" width="50">';
                                        ?>
                                        <div><?= $title ?><input type="hidden" name="image" value="<?= $model->image ?>"><a class="text-remove"></a></div>
                                        <?php
                                    endif;
                                    ?>
                                </div>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>

                      <fieldset class="top">
                            <label for="required_field">Caption</label>
                            <div>
                                <textarea id="caption" name="caption"><?= $model->caption ?></textarea>
                                 <div class="required_tag"></div>
                            </div> 
                        </fieldset>
                        <fieldset class="top">
                            <label for="required_field">Source</label>
                            <div>
                                <textarea id="source" name="source"><?= $model->source ?></textarea>
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

