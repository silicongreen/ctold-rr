<br /><br /><br /><br /><br /><br /><div id="pjax">
    <?php echo form_open('',array('id'=>'settings_form','class' => 'validate_form','enctype'=>'multipart/form-data'));?>
    <?php
        if(isset($values) && !empty($values->id)){
            echo '<input type="hidden" id="Settings_id" name="Settings[id]" value="'.$values->id.'">';
            
        }
    ?>
        <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">
            <?php
                $widget = new Widget;
        
                $widget->run('sidebar');                
            ?>
            <?php if(!isset($values->id) && empty($values->id)): ?>
            <div id="main_container" class="main_container container_16 clearfix">
                    <div class="box grid_16">
                        <div class="block">
                            <h2 class="section">Manual Issue Date is currently disabled, rather it's using current datetime.To use manual Issue Date Please Enable it From Config file.</h2>
                        </div>
                    </div>
            </div>
            
            
            <?php else:?>
           
            <div id="main_container" class="main_container container_16 clearfix">
                    <div class="box grid_16">
                        <div class="block">
                            <h2 class="section"><?php echo ($values->id) ? "Update":"Add";?> Issue Date</h2>
                        </div>
                    </div>
                    
                    <?php if(isset($success) && !empty($success)){?>
                        <div class="alert dismissible alert_green">
                            <strong><?php echo $success;?></strong>
                        </div>
                    <?php }?>
                        
                    <?php if(isset($errors) && !empty($errors)){ ?>
                        <div class="alert dismissible alert_red">
                            <strong><?php echo $errors;?></strong>
                        </div>
                    <?php } ?>
                    
                    <div class="box grid_16">
                        <div class="block">
                            <fieldset class="label_side top" id="f_set_<?php echo $model.'_'.$fields['value'];?>">
                                <label for="<?php echo $model.'_'.$fields['value'];?>">Issue Date</label>
                                <div>
                                    <input type="text" id="<?php echo $model.'_'.$fields['value'];?>" class="datepicker" name="<?php echo $model.'['.$fields['value'].']';?>" value="<?php echo (isset($values)) ? $values->value : '';?>" readonly="readonly" />
                                </div>
                            </fieldset>
                            
                            <div class="button_bar clearfix">
                                <button class="green" type="submit"><span>Submit</span></button>
                            </div>
                        </div>
                    </div>
                    
            </div>
             <?php endif;?>
        </div>
    <?php echo form_close();?>
</div>