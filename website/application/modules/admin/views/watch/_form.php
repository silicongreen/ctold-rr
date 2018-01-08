<script src="<?php echo base_url();?>scripts/jquery/jquery.tree.js" type="text/javascript"></script>
<link href="<?php echo base_url();?>scripts/tree/jquery.tree.css" rel="stylesheet" type="text/css" />
<script src="<?php echo  base_url() ?>scripts/custom/customTree.js" type="text/javascript"></script>
<script src="<?php echo  base_url() ?>scripts/custom/customWatch.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>ckeditor/ckeditor.js"></script>
<style type="text/css">
    .ui-autocomplete-loading {
        background: white url('<?php echo base_url();?>images/ajax-loader.gif') right center no-repeat;
    }
</style>
<div id="pjax">
    <?php echo form_open('',array('id'=>'watch_form','class' => 'validate_form','enctype'=>'multipart/form-data'));?>
    <?php
        if(isset($values) && !empty($values->id)){
            echo '<input type="hidden" id="WhatsOn_id" name="WhatsOn[id]" value="'.$values->id.'">';
        }
    ?>
    
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">
        
        <div id="main_container" class="main_container container_16 clearfix popup">
            
            <div class="box grid_16">
                <div class="block">
                    <h2 class="section"><?php echo ($edit) ? "Edit":"Add";?> TV Program</h2>
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
                    <fieldset class="label_side top">
                        <label for="channel_search">Channel Name</label>
                        <div>
                            <input type="text" id="channel_search" name="channel_search" value="<?php echo (isset($channel_search)) ? $channel_search:'';?>" />
                            <input type="hidden" id="<?php echo $model.'_'.$fields['channel_id'];?>" class="text " name="<?php echo $model.'['.$fields['channel_id'].']';?>" value="<?php echo (isset($values) && !empty($values->channel_id)) ? $values->channel_id : '';?>" />
                            <div class="required_tag"></div>
                        </div>
                    </fieldset>
                    
                    <fieldset class="label_side top">
                        <label for="">Category</label>
                        <div style="height:300px; overflow-y: scroll;">
                            <ul id="tree">
                                <?php foreach ($category_tree as $value): ?>
                                    <li><input id="<?php echo $value['id'];?>" type="checkbox" value="<?php echo $value['id'];?>" <?php echo (isset($values->category_id) ? ($value['id'] == $values->category_id) ? "checked='checked'" : '' : ''); ?> name=<?php echo $model.'['.$fields['category_id'].']';?>" /><label for="<?php echo $value['id'];?>"><?php echo $value['title'];?></label>
                                        <?php if (count($value['children']) > 0): ?>
                                            <ul>
                                                <?php foreach ($value['children'] as $childrens): ?>
                                                    <li>
                                                        <input id="<?php echo $childrens['id'];?>" type="checkbox" <?php echo (isset($values->category_id) ? ($childrens['id'] == $values->category_id) ? "checked='checked'" : '' : '');?> value="<?php echo $childrens['id'];?>" name="<?php echo $model.'['.$fields['category_id'].']';?>" /><label for="<?php echo $childrens['id'];?>"><?php echo $childrens['title'];?></label>
                                                    </li>
                                                    <?php endforeach; ?>
                                            </ul>    
                                        <?php endif; ?> 

                                <?php endforeach; ?>
                            </ul>
                            <div class="required_tag"></div>
                        </div>
                    </fieldset>
                    
                    <fieldset class="label_side top">
                        <label>Program Type</label>
                        <div>
                            <div class="jqui_radios">
                                <input type="radio" id="tv_program" name="<?php echo $model.'['.$fields['program_type'].']';?>" value="1"
                                <?php echo (!isset($values->program_type) || $values->program_type == 1) ? 'checked="checked"' : '';?>
                                 />
                                <label for="tv_program">TV Program</label>
                                
                                <input type="radio" id="friday_program" name="<?php echo $model.'['.$fields['program_type'].']';?>" value="3"
                                <?php echo (isset($values->program_type) && $values->program_type == 3) ? 'checked="checked"' : '';?>
                                 />
                                <label for="friday_program">Thank God It's Friday</label>
                                
                                <input type="radio" id="event_program" name="<?php echo $model.'['.$fields['program_type'].']';?>" value="4"
                                <?php echo (isset($values->program_type) && $values->program_type == 4) ? 'checked="checked"' : '';?>
                                 />
                                <label for="event_program">Showbiz Events</label>
                                
                                <input type="radio" id="other_program" name="<?php echo $model.'['.$fields['program_type'].']';?>" value="2"
                                <?php echo (isset($values->program_type) && $values->program_type == 2) ? 'checked="checked"' : '';?>
                                 />
                                <label for="other_program">Other Program</label>
                            </div>
                        </div>
                    </fieldset>
                    
                    <fieldset class="label_side top">
                        <label for="<?php echo $model.'_'.$fields['program_details'];?>">Program Details</label>
                        <div>
                            <textarea id="<?php echo $model.'_'.$fields['program_details'];?>" class="ckeditor required" name="<?php echo $model.'['.$fields['program_details'].']';?>"><?php echo (isset($values->program_details)) ? $values->program_details : '';?></textarea>
                            <div class="required_tag"></div>
                        </div>
                    </fieldset>
                    
                    <fieldset class="label_side top">
                        <label for="<?php echo $model.'_'.$fields['show_date'];?>">Show Date</label>
                        <div>
                            <input type="text" id="<?php echo $model.'_'.$fields['show_date'];?>" class="uniform datepicker required" name="<?php echo $model.'['.$fields['show_date'].']';?>" value="<?php echo (isset($values->show_date)) ? $values->show_date : '';?>" />
                            <div class="required_tag"></div>
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