<script src="<?php echo base_url() ?>ckeditor/ckeditor.js"></script>
<div id="pjax">
    <?php echo form_open('',array('id'=>'menus_form','class' => 'validate_form','enctype'=>'multipart/form-data'));?>
    <?php
        if(isset($values) && !empty($values['id'])){
            echo '<input type="hidden" id="menus_id" name="menus[id]" value="'.$values['id'].'">';
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
                            <fieldset class="label_side top">
                                <label for="required_field">Location</label>
                                <div>
                                    <div class="jqui_radios">
                                        <input type="radio" id="<?php echo $model.'_'.$fields['position'].'_1';?>" name="<?php echo $model.'['.$fields['position'].']';?>" value="1"
                                            <?php echo (!isset($values['position']) || (isset($values['position']) && $values['position'] == '1')) ? 'checked="checked"' : '';?>
                                         />
                                        <label for="<?php echo $model.'_'.$fields['position'].'_1';?>" id="lbl_<?php echo $model.'_'.$fields['position'].'_1';?>">Header</label>
                                        
                                        <input type="radio" id="<?php echo $model.'_'.$fields['position'].'_2';?>" name="<?php echo $model.'['.$fields['position'].']';?>" value="2"
                                            <?php echo (isset($values['position']) && $values['position'] == '2') ? 'checked="checked"' : ''; ?>
                                         />
                                        <label for="<?php echo $model.'_'.$fields['position'].'_2';?>" id="lbl_<?php echo $model.'_'.$fields['position'].'_2';?>">Footer</label>
                                    </div>
                                </div>
                            </fieldset>
                            
                            <fieldset class="label_side top" id="f_set_menu_types">
                                <label for="<?php echo $model.'_'.$fields['type'];?>">Menu Types</label>
                                <div>
                                    <?php echo form_dropdown($model.'['.$fields['type'].']', $ar_menu_types, (isset($values)) ? $values['type'] : '', 'id='.$model.'_'.$fields['type'].' class="full_width required"');?>
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            
<!--                            <fieldset class="label_side top" id="f_set_categories_or_text" <?php echo (isset($values) && !empty($values['type']) && ($values['type'] == 2)) ? 'style="display: block;"' : 'style="display: none;"';?>>
                                <label for="chk_categories_or_text">Category ?</label>
                                <div>
                                    <input type="checkbox" id="chk_categories_or_text" name="chk_categories_or_text" value="1" <?php echo (isset($values) && !empty($values['type']) && ($values['type'] == 2 && empty($values['link_text']))) ? 'checked="checked"' : '';?> />
                                </div>
                            </fieldset>-->
                            
                            <fieldset class="label_side top" id="f_set_drp_categories" <?php echo (isset($values) && !empty($values['type']) && ($values['type'] == 1 || $values['type'] == 4)) ? 'style="display: block;"' : 'style="display: none;"';?>>
                                <label for="drp_categories">Categories</label>
                                <div id="div_drp_categories">
                                    <select id="drp_categories" class="full_width" name="menus[category_id]"></select>
                                </div>
                            </fieldset>
                            
                            <fieldset class="label_side top" id="f_set_sub_categories" style="display: none;">
                                <label>Sub Categories</label>
                                <div id="div_sub_categories"></div>
                            </fieldset>
                            
                            <fieldset class="label_side top" id="f_set_news_num" style="display: none;">
                                <label for="news_num">Number of News</label>
                                <div>
                                    <?php echo form_dropdown('news_num',array(null=>'Select','1'=>1,'2'=>2,'3'=>3,'4'=>4,'5'=>5), (isset($values)) ? $values['news_num'] : '', 'id="news_num" class="full_width"');?>
                                </div>
                            </fieldset>
                            
                            <fieldset class="label_side top" id="f_set_news_list" style="display: none;">
                                <label>News</label>
                                <div id="div_news_list"></div>
                            </fieldset>
                            
                            <!--<fieldset class="label_side top" id="f_set_footer_groups" style="display: none;">
                                <label for="required_field">Footer Groups</label>
                                <div>
                                    <?php //echo form_dropdown($model.'['.$fields['footer_group'].']',$ar_footer_groups,'', 'id='.$model.'_'.$fields['footer_group'].' class="uniform full_width"');?>
                                </div>
                            </fieldset>-->
                            
                            <fieldset class="label_side top" id="f_set_<?php echo $model.'_'.$fields['title'];?>" style="display: block;">
                                <label for="<?php echo $model.'_'.$fields['title'];?>">Menu Title</label>
                                <div>
                                    <input type="text" id="<?php echo $model.'_'.$fields['title'];?>" class="text required" name="<?php echo $model.'['.$fields['title'].']';?>" value="<?php echo (isset($values)) ? $values['title'] : '';?>" />
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            
                            <!--Text Block Starts-->
                            <!--Also for icon-->
                            
                            <fieldset class="label_side top" id="f_set_<?php echo $model.'_'.$fields['ci_key'];?>" <?php  echo (isset($values) && !empty($values['ci_key'])) ? 'style="display: block;"' : 'style="display: block;"';?>>
                                <label for="<?php echo $model.'_'.$fields['ci_key'];?>">Key</label>
                                <div>
                                    <input type="text" id="<?php echo $model.'_'.$fields['ci_key'];?>" class="text" name="<?php echo $model.'['.$fields['ci_key'].']';?>" value="<?php echo (isset($values)) ? $values['ci_key'] : '';?>" />
                                </div>
                            </fieldset>
                            <fieldset class="label_side top">
                                <label for="<?php echo $model.'_'.$fields['ci_key'];?>">External Link</label>
                                <div>
                                    <input type="text"  class="text" name="<?php echo $model.'['.$fields['permalink'].']';?>" value="<?php echo (isset($values)) ? $values['permalink'] : '';?>" />
                                </div>
                            </fieldset>
                            
                            <fieldset class="label_side top" id="f_set_<?php echo $model.'_'.$fields['link_type'];?>" <?php  echo (isset($values) && !empty($values['link_type'])) ? 'style="display: block;"' : 'style="display: block;"';?>>
                                <label>Open</label>
                                <div>
                                    <div class="jqui_radios">
                                        <input type="radio" id="<?php echo $model.'_'.$fields['link_type'];?>__blank" name="<?php echo $model.'['.$fields['link_type'].']';?>" value="_blank"
                                            <?php echo (isset($values['link_type']) && $values['link_type'] == '_blank') ? 'checked="checked"' : ''; ?>
                                         />
                                        <label for="<?php echo $model.'_'.$fields['link_type'];?>__blank" id="lbl_<?php echo $model.'_'.$fields['link_type'];?>__blank">_Blank</label>
                                        <input type="radio" id="<?php echo $model.'_'.$fields['link_type'];?>__self" name="<?php echo $model.'['.$fields['link_type'].']';?>" value="_self" 
                                            <?php echo (!isset($values['link_type']) || (isset($values['link_type']) && $values['link_type'] == '_self')) ? 'checked="checked"' : '';?> 
                                         />
                                        <label for="<?php echo $model.'_'.$fields['link_type'];?>__self" id="lbl_<?php echo $model.'_'.$fields['link_type'];?>__self">_Self</label>
                                    </div>
                                </div>
                            </fieldset>
                            <!--Also for icon ends-->
                            
                            <fieldset class="label_side top" id="f_set_<?php echo $model.'_'.$fields['link_text'];?>" <?php echo (isset($values) && !empty($values['link_text'])) ? 'style="display: block;"' : 'style="display: block;"';?>>
                                <label>Text</label>
                                <div>
                                    <textarea id="<?php echo $model.'_'.$fields['link_text'];?>" class="ckeditor" name="<?php echo $model.'['.$fields['link_text'].']';?>"><?php echo (isset($values)) ? $values['link_text'] : '';?></textarea>
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            <!--Text Block Starts-->
                            
                            <fieldset class="label_side top" id="f_set_<?php echo $model.'_'.$fields['icon_name'];?>" <?php echo (isset($values) && !empty($values['icon_name'])) ? 'style="display: block;"' : 'style="display: none;"';?>>
                                <label for="<?php echo $model.'_'.$fields['icon_name'];?>">Icon</label>
                                <div>
                                    <input type="file" id="<?php echo $model.'_'.$fields['icon_name'];?>" class="uniform" name="<?php echo $model.'['.$fields['icon_name'].']';?>" />
                                    <?php
                                     echo (isset($values)) ? '<img src="'.base_url().UPLOADPATH.'menu_icons/'.$values['icon_name'].'">' : '';?>
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            
                            <fieldset class="label_side top" id="f_set_<?php echo $model.'_'.$fields['startdate'];?>">
                                <label for="<?php echo $model.'_'.$fields['startdate'];?>">Start Date</label>
                                <div>
                                    <input type="text" id="<?php echo $model.'_'.$fields['startdate'];?>" class="uniform datepicker" name="<?php echo $model.'['.$fields['startdate'].']';?>" value="<?php echo (isset($values)) ? $values['startdate'] : '';?>" />
                                </div>
                            </fieldset>
                            
                            <fieldset class="label_side top" id="f_set_<?php echo $model.'_'.$fields['expired'];?>">
                                <label for="<?php echo $model.'_'.$fields['expired'];?>">Expire Date</label>
                                <div>
                                    <input type="text" id="<?php echo $model.'_'.$fields['expired'];?>" class="uniform datepicker" name="<?php echo $model.'['.$fields['expired'].']';?>" value="<?php echo (isset($values)) ? $values['expired'] : '';?>" />
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
<script src="<?php echo base_url()?>scripts/custom/customMenu.js" type="text/javascript"></script>