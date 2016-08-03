<script src="<?= base_url() ?>scripts/custom/customByline.js"></script>
<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section"><?php echo ($model->id) ? "Edit" : "Add"; ?> New Photo (In Pictures)</h2>

                        <?php
                        if ($_POST)
                            create_validation($model);
                        ?>
                        <?php echo form_open('', array('class' => 'validate_form')); ?>
                        <a href="../../../../../../SPELLNEW/application/libraries/Datatables.php"></a>


                        <fieldset class="label_side top">
                            <label for="required_field">Select Theme</label>
                            <div>
                                <?php
                                echo form_dropdown('theme_id', $theme_list, $model->theme_id);
                                ?>
                                <div class="required_tag"></div>

                            </div>
                        </fieldset>  

                        <fieldset class="label_side top">
                            <label for="required_field">Select Author</label>
                            <div>
                                <?php
                                echo form_dropdown('author_id', $author_list, $model->author_id);
                                ?>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>  

                        <fieldset class="label_side top">
                            <label for="required_field">Created Date</label>
                            <div>
                                <input id="date_taken" name="date_taken" class="datetimepicker_class" value="<?php echo $model->date_taken ?>"  type="text">
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
                                <textarea id="photo_caption" name="photo_caption"><?= $model->photo_caption ?></textarea>
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

