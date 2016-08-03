<script src="<?php echo  base_url() ?>scripts/custom/customCategory.js"></script>
<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section"><?php echo  ($model->id) ? "Edit" : "Add"; ?> Category</h2>
                        <?php
                        if ($_POST)
                            create_validation($model);
                        ?>

                        <?php echo  form_open('', array('class' => 'validate_form', 'enctype' => 'multipart/form-data')); ?>
                        <fieldset class="label_side top" style="display:none;">
                            <label for="required_field">Category Type</label>
                            <div>
                                <?php
                                echo form_dropdown('category_type_id', $typeCategory, $model->category_type_id);
                                ?>

                            </div>
                        </fieldset> 

                        <fieldset class="label_side top">
                            <label for="required_field">Parent Category</label>
                            <div>
                                <?php
                                echo form_dropdown('parent_id', $parentCategory, $model->parent_id);
                                ?>

                            </div>
                        </fieldset>                            

                        <fieldset class="label_side top">
                            <label for="required_field">Category Name<span>Unique field With Parent Category</span></label>
                            <div>
                                <input id="name" name="name" value="<?php echo  $model->name ?>"  type="text" class="required" minlength="4"  required uniqueCategory="uniqueCategory">
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                        <fieldset class="label_side top">
                            <label for="required_field">Display Name<span>Showed if given</span></label>
                            <div>
                                <input id="display_name" name="display_name" value="<?php echo $model->display_name ?>"  type="text"  minlength="4" >
                            
                            </div>
                        </fieldset>
                        <fieldset class="label_side top">
                            <label for="required_field">Details</label>
                            <div>
                                <textarea  name="description" id="description" ><?= $model->description ?></textarea>
                            </div>
                        </fieldset>
                        
                        <fieldset class="label_side top">
                            <label for="required_field">Responsible Person<span>Use for candle email notification</span></label>
                            <div>
                                <input id="responsible" name="responsible" value="<?php echo $model->responsible ?>"  type="text"  minlength="9" >
                            
                            </div>
                        </fieldset>
                        <fieldset class="label_side top">
                            <label for="required_field">Backup Person<span>Use for candle email notification</span></label>
                            <div>
                                <input id="backup" name="backup" value="<?php echo $model->backup ?>"  type="text"  minlength="9" >
                            
                            </div>
                        </fieldset>
                        
                        <fieldset class="label_side top">
                            <label for="required_field">Embedded</label>
                            <div>
                                <textarea  name="embedded" id="embedded" ><?= $model->embedded ?></textarea>
                            </div>
                        </fieldset>
                        <fieldset class="label_side top"  style="display:none;">
                        <label for="required_field">Background Color</label>
                            <div>
                                <input id="colorpicker_popup"  name="background_color" value="<?php echo $model->background_color; ?>"  type="text"  maxlength="100"  > 
                                
                                <?php if ($model->background_color): ?>
                                <div id="div_headline_color" style="float:left; clear:both; width:20%; height:20px; margin-top:10px; background-color:<?php echo "#".$model->background_color; ?>" ></div>
                                <?php else : ?>
                                    <div id="div_headline_color"  style="float:left; clear:both; width:20%; height:20px;margin-top:10px; background-color:#003773" ></div>
                                <?php endif; ?>
                            </div>
                        </fieldset>
                        <fieldset class="label_side top">
                            <label for="required_field">Category Icon</label>
                            <div>
                                <button class="green" id="select_icon"  type="button">
                                    <span>Select Image</span>
                                </button>
                                <div  id="select_icon_box">
                                    <?php
                                    if ($model->icon):
                                        $title = '<img src="' . base_url() . $model->icon . '" width="50">';
                                        ?>
                                        <div><?php echo  $title ?><input type="hidden" name="icon" value="<?php echo  $model->icon ?>"><a class="text-remove"></a></div>
                                        <?php
                                    endif;
                                    ?>
                                </div>
                            </div>
                        </fieldset>
                        
                        <fieldset class="label_side top">
                            <label for="required_field">Category Cover</label>
                            <div>
                                <button class="green" id="select_cover"  type="button">
                                    <span>Select Image</span>
                                </button>
                                <div  id="select_cover_box">
                                    <?php
                                    if ($model->cover):
                                        $title = '<img src="' . base_url() . $model->cover . '" width="50">';
                                        ?>
                                        <div><?php echo  $title ?><input type="hidden" name="cover" value="<?php echo  $model->cover ?>"><a class="text-remove"></a></div>
                                        <?php
                                    endif;
                                    ?>
                                </div>
                            </div>
                        </fieldset>
                        
                        <fieldset class="label_side top">
                            <label for="required_field">Category Menu Icon</label>
                            <div>
                                <button class="green" id="select_menu_icon"  type="button">
                                    <span>Select Image</span>
                                </button>
                                <div  id="select_menu_icon_box">
                                    <?php
                                    if ($model->menu_icon):
                                        $title = '<img src="' . base_url() . $model->menu_icon . '" width="50">';
                                        ?>
                                        <div><?php echo  $title ?><input type="hidden" name="menu_icon" value="<?php echo  $model->menu_icon ?>"><a class="text-remove"></a></div>
                                        <?php
                                    endif;
                                    ?>
                                </div>
                            </div>
                        </fieldset>

                        

                        <fieldset class="label_side top">
                            <label for="required_field">Status</label>
                            <div>
                                <?php
                                $allow_from_all = array('0' => 'Inactive', '1' => 'Active');

                                if (!$model->status)
                                    $model->status = 1;
                                echo form_dropdown('status', $allow_from_all, $model->status);
                                ?>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>

                        <div class="button_bar clearfix">
                            <button class="green" type="submit">
                                <span>Submit</span>
                            </button>
                        </div>
                        <?php echo  form_close(); ?>  
                    </div>
                </div>


            </div>

        </div>

