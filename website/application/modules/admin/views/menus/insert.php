<script src="<?php echo base_url() ?>ckeditor/ckeditor.js"></script>
<script src="<?= base_url() ?>scripts/custom/customByline.js"></script>
<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section"><?php echo ($model->id) ? "Edit" : "Add"; ?> Menu</h2>

                        <?php
                        if ($_POST)
                            create_validation($model);
                        ?>
                        <?php echo form_open('', array('class' => 'validate_form', 'enctype' => 'multipart/form-data')); ?>

                        <fieldset class="label_side top">
                            <label>Location</label>
                            <div>
                                <div class="jqui_radios">
                                    <input type="radio" id="position_1" name="position" value="1"
                                    <?php echo (empty($model->position) || $model->position == 1) ? 'checked="checked"' : ''; ?>
                                           />
                                    <label for="position_1" >Header</label>
                                    <input type="radio" id="position_2" name="position" value="2" 
                                    <?php echo ($model->position == 2) ? 'checked="checked"' : ''; ?> 
                                           />
                                    <label for="position_2" >Footer</label>

                                </div>
                            </div>
                        </fieldset>                           

                        <fieldset class="label_side top" id="f_set_menu_types">
                            <label for="">Menu Types</label>
                            <div>

                                <select id="menu_type" name="type" class="full_width required">
                                    <?php foreach ($ar_menu_types as $key => $val): ?>
                                        <?php if (empty($model->type)): ?>

                                            <option  value="<?php echo $key; ?>"><?php echo $val; ?> </option>
                                        <?php else : ?>
                                            <option <?php echo ($model->type == $key) ? 'selected' : ''; ?> value="<?php echo $key; ?>"><?php echo $val; ?> </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>    
                                </select>     

                                <div class="required_tag"></div>
                            </div>
                        </fieldset>


                        <fieldset class="label_side top"  id="sub_menu" style="<?php if ($model->type == 3 || $model->type == 5): ?>display:none<?php endif; ?>">
                            <label for="required_field">Type</label>
                            <div>
                                <?php
                                $menu_types = array('1' => 'Parent Menu', '2' => 'Sub Menu');

                                $current_type = 2;
                                if (!isset($model->parent_menu_id) || $model->parent_menu_id == null)
                                    $current_type = 1;

                                $js = " id='menu_types'";
                                echo form_dropdown('menu_types', $menu_types, $current_type, $js);
                                ?>

                            </div>
                        </fieldset>

                        <fieldset id="show_on_sub_menu" class="label_side top"  style="<?php if (!isset($model->parent_menu_id) || $model->parent_menu_id == null || $model->type == 2): ?>display:none<?php endif; ?>">
                            <label for="required_field">Parent Menu</label>
                            <div>
                                <?php
                                if (!isset($model->parent_menu_id))
                                    $parent_menu_id = 0;
                                else
                                    $parent_menu_id = $model->parent_menu_id;

                                echo form_dropdown('parent_menu_id_header', $parent_menu, $parent_menu_id);
                                ?>

                            </div>
                        </fieldset>


                        <fieldset id="show_on_sub_menu_footer" class="label_side top"  style="<?php if (!isset($model->parent_menu_id) || $model->parent_menu_id == null || $model->type == 1): ?>display:none<?php endif; ?>">
                            <label for="required_field">Parent Menu</label>
                            <div>
                                <?php
                                if (!isset($model->parent_menu_id))
                                    $parent_menu_id = 0;
                                else
                                    $parent_menu_id = $model->parent_menu_id;

                                echo form_dropdown('parent_menu_id_footer', $parent_menu_footer, $parent_menu_id);
                                ?>

                            </div>
                        </fieldset>


                        <fieldset class="label_side top">
                            <label for="required_field">Menu Title<span>Unique field</span></label>
                            <div>
                                <input id="title" name="title" value="<?php echo $model->title ?>"  type="text" class="required" minlength="3"  required >
                                <div class="required_tag"></div>
                            </div>

                            <input type="hidden" name="ci_key" value="<?php echo $model->ci_key ?>" />
                        </fieldset>

                        <fieldset class="label_side top" id="permalink_fieldset" <?php echo ($model->type == 2) ? 'style="display: block;"' : 'style="display: none;"'; ?>>
                            <label for="required_field">External Link </label>
                            <div>
                                <input id="permalink" name="permalink" value="<?php echo $model->permalink ?>"  type="text"  minlength="3">

                            </div>
                        </fieldset>


                        <fieldset class="label_side top">
                            <label>Open</label>
                            <div>
                                <div class="jqui_radios">
                                    <input type="radio" id="link_type_1" name="link_type" value="_blank"
                                    <?php echo (isset($model->link_type) && $model->link_type == '_blank') ? 'checked="checked"' : ''; ?>
                                           />
                                    <label for="link_type_1" id="">_Blank</label>
                                    <input type="radio" id="link_type_2" name="link_type" value="_self" 
                                    <?php echo (!isset($model->link_type) || (isset($model->link_type) && $model->link_type == '_self')) ? 'checked="checked"' : ''; ?> 
                                           />
                                    <label for="link_type_2">_Self</label>

                                </div>
                            </div>
                        </fieldset>  


                        <fieldset class="label_side top">
                            <label>Active                                      
                            </label>
                            <div class="jqui_radios">

                                <?php
                                if ($model->is_active == "") {
                                    $model->is_active = 1;
                                }
                                ?>
                                <input type="radio" id="is_active_1" name="is_active" value="1"
                                <?php echo (($model->is_active != 0)) ? 'checked="checked"' : ''; ?>
                                       />
                                <label for="is_active_1" id="">Active</label>
                                <input type="radio" id="is_active_2" name="is_active" value="0" 
                                <?php echo ($model->is_active == 0) ? 'checked="checked"' : ''; ?> 
                                       />
                                <label for="is_active_2">Inactive</label>

                            </div>
                   
                    </fieldset>    
                    <fieldset class="label_side top">
                        <label>Has Right                                      
                        </label>
                        <div class="jqui_radios">

                            <?php
                            if ($model->has_right == "") {
                                $model->has_right = 1;
                            }
                            ?>
                            <input type="radio" id="has_right_1" name="has_right" value="1"
                            <?php echo (($model->has_right != 0)) ? 'checked="checked"' : ''; ?>
                                   />
                            <label for="has_right_1" id="">Yes</label>
                            <input type="radio" id="has_right_2" name="has_right" value="0" 
<?php echo ($model->has_right == 0) ? 'checked="checked"' : ''; ?> 
                                   />
                            <label for="has_right_2">No</label>

                        </div>
                    
                    </fieldset>
                        
                    <fieldset class="label_side top">
                        <label>Full Custom <span>Use only for text type menu</span>                                      
                        </label>
                        <div class="jqui_radios">

                            <?php
                            if ($model->full_custom == "") {
                                $model->full_custom = 0;
                            }
                            ?>
                            <input type="radio" id="full_custom_1" name="full_custom" value="1"
                            <?php echo (($model->full_custom == 1)) ? 'checked="checked"' : ''; ?>
                                   />
                            <label for="full_custom_1" id="">Yes</label>
                            <input type="radio" id="full_custom_2" name="full_custom" value="0" 
<?php echo ($model->full_custom != 1) ? 'checked="checked"' : ''; ?>  />
                            <label for="full_custom_2">No</label>

                        </div>
                    
                    </fieldset>
                        
                    <fieldset class="label_side top">
                        <label for="required_field">Menu Header Image<span>Use only for text type menu</span> </label>
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
                        </div>
                  </fieldset>


                <fieldset class="label_side top" id="text_fieldset" <?php echo ($model->type == 2) ? 'style="display: block;"' : 'style="display: none;"'; ?>>
                    <label>Text</label>
                    <div>
                        <textarea id="link_text" class="ckeditor" name="link_text"><?php echo (isset($model->link_text)) ? $model->link_text : ''; ?></textarea>

                    </div>
                </fieldset>

                <fieldset class="label_side top" id="icon_fieldset" <?php echo ($model->type == 3) ? 'style="display: block;"' : 'style="display: none;"'; ?>>
                    <label for="required_field">Icon </label>
                    <div>
                        <input id="icon_name" class="uniform" name="icon_name" value="<?php echo $model->icon_name ?>"  type="file"  minlength="3">

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

<script src="<?php echo base_url() ?>scripts/custom/customMenu.js" type="text/javascript"></script>