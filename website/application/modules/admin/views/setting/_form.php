<div id="pjax">
    <?php echo form_open('',array('id'=>'settings_form','class' => 'validate_form','enctype'=>'multipart/form-data'));?>
    <?php
        if(isset($values) && !empty($values->id)){
            echo '<input type="hidden" id="Settings_id" name="Settings[id]" value="'.$values->id.'">';
        }
    ?>
        <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">
            <div id="main_container" class="main_container container_16 clearfix popup">
                    <div class="box grid_16">
                        <div class="block">
                            <h2 class="section"><?php //echo ($model->id) ? "Edit":"Add";?> Menu</h2>
                        </div>
                    </div>
                    
                    <?php
                        if($_POST)
                        create_validation($fields);
                    ?>
                    
                    <div class="box grid_16">
                        <div class="block">
                            
                            <fieldset class="label_side top" id="f_set_<?php echo $model.'_'.$fields['key'];?>" style="display: block;">
                                <label for="<?php echo $model.'_'.$fields['key'];?>">Settings Key</label>
                                <div>
                                    <input type="text" id="<?php echo $model.'_'.$fields['key'];?>" class="text" name="<?php echo $model.'['.$fields['key'].']';?>" value="<?php echo (isset($values)) ? $values->key : '';?>" />
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            
                            <!--Text Block Starts-->
                            <!--Also for icon-->
                            
                            <fieldset class="label_side top" id="f_set_<?php echo $model.'_'.$fields['value'];?>">
                                <label for="<?php echo $model.'_'.$fields['value'];?>">Settings Value</label>
                                <div>
                                    <input type="text" id="<?php echo $model.'_'.$fields['value'];?>" class="text" name="<?php echo $model.'['.$fields['value'].']';?>" value="<?php echo (isset($values)) ? $values->value : '';?>" />
                                </div>
                            </fieldset>
                            
                            
                            <fieldset class="label_side top" id="f_set_<?php echo $model.'_'.$fields['description'];?>">
                                <label for="<?php echo $model.'_'.$fields['description'];?>">Description</label>
                                <div>
                                    <textarea id="<?php echo $model.'_'.$fields['description'];?>" class="" name="<?php echo $model.'['.$fields['description'].']';?>"><?php echo (isset($values)) ? $values->description : '';?></textarea>
                                </div>
                            </fieldset>
                            
                            <fieldset class="label_side top" id="f_set_<?php echo $model.'_'.$fields['is_active'];?>" style="display: block;">
                                <label for="<?php echo $model.'_'.$fields['is_active'];?>">Status</label>
                                <div>
                                    <?php echo form_dropdown($model.'['.$fields['is_active'].']',array(null=>'Select', '0' => 'Inactive', '1' => 'Active'), (isset($values)) ? $values->is_active : '', 'id="'.$model.'_'.$fields['is_active'].'" class="full_width"');?>
                                </div>
                            </fieldset>
                            
                            <div class="button_bar clearfix">
                                <button class="green" type="submit"><span>Submit</span></button>
                            </div>
                        </div>
                    </div>
                    
            </div>
        </div>
    <?php echo form_close();?>
</div>