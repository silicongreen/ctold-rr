<script src="<?php echo  base_url() ?>scripts/custom/customVoice.js" type="text/javascript"></script><style type="text/css">
    .ui-autocomplete-loading {
        background: white url('<?php echo base_url();?>images/ajax-loader.gif') right center no-repeat;
    }
</style>
<div id="pjax">
    <?php echo form_open('',array('id'=>'voice_box_form','class' => 'validate_form','enctype'=>'multipart/form-data'));?>
    <?php
        if(isset($values) && !empty($values->id)){
            echo '<input type="hidden" id="Voice_id" name="Voice[id]" value="'.$values->id.'">';
        }
    ?>
    
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">
        
        <div id="main_container" class="main_container container_16 clearfix popup">
            
            <div class="box grid_16">
                <div class="block">
                    <h2 class="section"><?php echo ($edit) ? "Edit":"Add";?> Quote</h2>
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
                        <label for="personality_search">Personality</label>
                        <div>
                            <input type="text" id="personality_search" class="required" name="personality_search" value="<?php echo (isset($personality_search)) ? $personality_search:'';?>" />
                            <input type="hidden" id="<?php echo $model.'_'.$fields['personality_id'];?>" class="text" name="<?php echo $model.'['.$fields['personality_id'].']';?>" value="<?php echo (isset($values) && !empty($values->personality_id)) ? $values->personality_id : '';?>" />
                            <div class="required_tag"></div>
                        </div>
                    </fieldset>
                    
                    <fieldset class="label_side top">
                        <label for="<?php echo $model.'_'.$fields['voice'];?>">Voice</label>
                        <div>
                            <textarea id="<?php echo $model.'_'.$fields['voice'];?>" class="required" name="<?php echo $model.'['.$fields['voice'].']';?>"><?php echo (isset($values->voice)) ? $values->voice : '';?></textarea>
                            <div class="required_tag"></div>
                        </div>
                    </fieldset>
                    
                    <fieldset class="label_side top">
                        <label for="topic_search">Topic</label>
                        <div>
                            <input type="text" id="topic_search" name="topic_search" value="<?php echo (isset($topic_search)) ? $topic_search:'';?>" />
                            <input type="hidden" id="<?php echo $model.'_'.$fields['topic_id'];?>" class="text" name="<?php echo $model.'['.$fields['topic_id'].']';?>" value="<?php echo (isset($values) && !empty($values->topic_id)) ? $values->topic_id : '';?>" />
                        </div>
                    </fieldset>
                    
                    <fieldset class="label_side top">
                        <label for="<?php echo $model.'_'.$fields['published_date'];?>">Publish Date</label>
                        <div>
                            <input type="text" id="<?php echo $model.'_'.$fields['published_date'];?>" class="uniform datepicker required" name="<?php echo $model.'['.$fields['published_date'].']';?>" value="<?php echo (isset($values->published_date)) ? $values->published_date : '';?>" />
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