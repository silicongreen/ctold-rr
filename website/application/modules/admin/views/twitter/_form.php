<div id="pjax">
    <?php echo form_open('',array('id'=>'twitter_form','class' => 'validate_form','enctype'=>'multipart/form-data'));?>
        <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">
            <div id="main_container" class="main_container container_16 clearfix popup">
                    <div class="box grid_16">
                        <div class="block">
                            <h2 class="section"><?php echo ($edit === true) ? "Edit":"Add";?> Twitter Widget</h2>
                        </div>
                    </div>
                    
                    <?php
                        if(isset($errors) && !empty($errors)){
                            foreach($errors as $err_key => $err_val){
                    ?>
                        <div class="alert dismissible alert_red">
                            <?php foreach($err_val as $error){?>
                            <strong>
                                <?php echo $error;?>
                            </strong><br />
                            <?php }?>
                        </div>
                    <?php } }?>
                    
                    <div class="box grid_16">
                        <div class="block">
                            
                            <fieldset class="label_side top" id="f_set_<?php echo $model.'_'.$fields['id'];?>" style="display: block;">
                                <label for="<?php echo $model.'_'.$fields['id'];?>">Menu</label>
                                <div>
                                    <?php echo form_dropdown($model.'['.$fields['id'].']',$menus, (isset($values)) ? $values->id : '', 'id="'.$model.'_'.$fields['id'].'" class="full_width required number"');?>
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            
                            <fieldset class="label_side top" id="f_set_<?php echo $model.'_'.$fields['twitter_name'];?>">
                                <label for="<?php echo $model.'_'.$fields['twitter_name'];?>">Twitter Username</label>
                                <div>
                                    <input type="text" id="<?php echo $model.'_'.$fields['twitter_name'];?>" class="text required" name="<?php echo $model.'['.$fields['twitter_name'].']';?>" value="<?php echo (isset($values)) ? $values->twitter_name : '';?>" />
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            
                            
                            <fieldset class="label_side top" id="f_set_<?php echo $model.'_'.$fields['widget_id'];?>">
                                <label for="<?php echo $model.'_'.$fields['widget_id'];?>">Twitter Widget ID</label>
                                <div>
                                    <input type="text" id="<?php echo $model.'_'.$fields['widget_id'];?>" class="text required number" name="<?php echo $model.'['.$fields['widget_id'].']';?>" value="<?php echo (isset($values)) ? $values->widget_id : '';?>" />
                                    <div class="required_tag"></div>
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