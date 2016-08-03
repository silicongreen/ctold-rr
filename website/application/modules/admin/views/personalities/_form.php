<div id="pjax">
    <?php echo form_open('',array('id'=>'personalitiy_form','class' => 'validate_form','enctype'=>'multipart/form-data'));?>
    <?php
        if(isset($values) && !empty($values->id)){
            echo '<input type="hidden" id="Personalitiy_id" name="Personalitiy[id]" value="'.$values->id.'">';
        }
    ?>
    
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">
        
        <div id="main_container" class="main_container container_16 clearfix popup">
            
            <div class="box grid_16">
                <div class="block">
                    <h2 class="section"><?php echo ($edit) ? "Edit":"Add";?> Personalitiy</h2>
                </div>
            </div>
            
            <?php if(isset($errors) && !empty($errors)){ ?>
                <div class="alert dismissible alert_red">
                    <strong><?php echo $errors;?></strong>
                </div>
            <?php } ?>
            
            <div class="box grid_16">
                <div class="block">
                    <fieldset class="label_side top">
                        <label for="required_field">Name</label>
                        <div>
                            <input type="text" id="<?php echo $model.'_'.$fields['name'];?>" class="text required" name="<?php echo $model.'['.$fields['name'].']';?>" value="<?php echo (isset($values) && !empty($values->name)) ? $values->name : '';?>" />
                            <div class="required_tag"></div>
                        </div>
                    </fieldset>
                    
                    <fieldset class="label_side top">
                        <label for="required_field">Description</label>
                        <div>
                            <input type="text" id="<?php echo $model.'_'.$fields['description'];?>" class="text" name="<?php echo $model.'['.$fields['description'].']';?>" value="<?php echo (isset($values) && !empty($values->description)) ? $values->description : '';?>" />
                        </div>
                    </fieldset>
                    
                    <?php if($edit){ ?>
                        <fieldset class="label_side top">
                            <label for="<?php echo $model.'_'.$fields['is_active'];?>">Channel Name</label>
                            <div>
                                <?php echo form_dropdown($model.'['.$fields['is_active'].']',array( null=> 'Select','0'=>'Inactive','1'=>'Active'), (isset($values)) ? $values->is_active : '', 'id="'.$model.'_'.$fields['is_active'].'" class="full_width"');?>
                                <div class="required_tag"></div>
                            </div>
                        </fieldset>
                    <?php } ?>
                    
                    <div class="button_bar clearfix">
                        <button class="green" type="submit"><span>Submit</span></button>
                    </div>
                    
                </div>
                
                
            </div>
            
            
        </div>
        
    </div>
    
</div>