<link rel="stylesheet" href="<?php echo base_url();?>styles/adminica/smoothness/jquery-ui-1.10.3.custom.css" />
<script src="<?php echo base_url();?>scripts/jquery/jquery-1.9.1.js"></script>
<script src="<?php echo base_url();?>scripts/jquery/jquery-ui-1.10.3.custom.min.js"></script>
<script src="<?php echo base_url();?>scripts/jquery/jquery.tree.js" type="text/javascript"></script>
<link href="<?php echo base_url();?>scripts/tree/jquery.tree.css" rel="stylesheet" type="text/css" />
<script src="<?php echo  base_url() ?>scripts/custom/customTree.js" type="text/javascript"></script>
<script src="<?php echo  base_url() ?>scripts/custom/customwidget.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>ckeditor/ckeditor.js"></script>
<style type="text/css">
    .ui-autocomplete-loading {
        background: white url('<?php echo base_url();?>images/ajax-loader.gif') right center no-repeat;
    }
</style>
<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section"><?php echo ($model->id)?"Edit":"Add";?> Widget</h2>

                        <?php
                        if($_POST)
                        create_validation($model);
                        ?>
                        <?php echo form_open('',array('class' => 'validate_form'));?>
                           
                            <fieldset class="label_side top">
                                <label for="required_field">Name<span>Unique field</span></label>
                                <div>
                                    <input id="name" name="name" value="<?php echo  $model->name ?>"  type="text" class="required" minlength="3"  required >
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                        
                            <fieldset class="label_side top">
                                <label for="required_field">Widget Type</label>
                                <div>
                                    <?php
                                    if(!$model->type)
                                        $model->type = "";
                                    echo form_dropdown('type', $widget_type,$model->type);
                                    ?>
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>

                            <fieldset class="label_side top category_tree_main field" style="display: none;">
                                <label for="">Category</label>
                                <div>
                                    <ul id="tree" style="height:300px; overflow-y: scroll;">
                                        <li id="tree_all_category"><input id="all" type="checkbox" value="all" <?php echo ( $model->is_all  ? "checked='checked'" : ''); ?> name="all" /><label for="all">All Categories</label>
                                        <?php foreach ($category_tree as $value): ?>
                                            <li><input id="<?php echo $value['id'];?>" type="checkbox" value="<?php echo $value['id'];?>" <?php echo (isset($values->category_id) ? ($value['id'] == $values->category_id) ? "checked='checked'" : '' : ''); ?> name=<?php echo $model.'['. $model->category_id .']';?>" /><label for="<?php echo $value['id'];?>"><?php echo $value['title'];?></label>
                                                <?php if (count($value['children']) > 0): ?>
                                                    <ul>
                                                        <?php foreach ($value['children'] as $childrens): ?>
                                                            <li>
                                                                <input id="<?php echo $childrens['id'];?>" type="checkbox" <?php echo (isset($values->category_id) ? ($childrens['id'] == $values->category_id) ? "checked='checked'" : '' : '');?> value="<?php echo $childrens['id'];?>" name="<?php echo $model.'['. $model->category_id .']';?>" /><label for="<?php echo $childrens['id'];?>"><?php echo $childrens['title'];?></label>
                                                            </li>
                                                            <?php endforeach; ?>
                                                    </ul>    
                                                <?php endif; ?> 

                                        <?php endforeach; ?>
                                    </ul>
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            
                            <fieldset class="label_side top widget_default field" style="display: none;">
                                <label for="required_field">Widget Default Layout</label>
                                <div class="jqui_radios">
                                    <?php $is_include_default_layout = ($model->is_include_default_layout) ? 'checked="checked"' : "";
                                    ?>
                                    <input type="radio" name="is_include_default_layout" value="1" id="default_layout" <?php echo  $is_include_default_layout ?> /><label for="default_layout">Yes</label>
                                    <input type="radio" name="is_include_default_layout" value="0" id="no_default_layout" <?php echo  ($is_include_default_layout) ? "" : 'checked="checked"'; ?>/><label for="no_default_layout">No</label>
                                </div>
                            </fieldset>
                        
                            <fieldset class="label_side top news_count field" style="display: none;">
                                <label for="required_field">News Count</label>
                                <div>
                                    <input id="news_count" name="news_count" value="<?php echo  $model->news_count ?>"  type="text"  maxlength="60"  >  
                                </div>
                            </fieldset>
                        
                            <fieldset class="label_side top tab_count field" style="display: none;">
                                <label for="required_field">Tab Count</label>
                                <div>
                                    <input id="tab_count" name="tab_count" value=""  type="text"  maxlength="60"  />  
                                </div>
                            </fieldset>
                        
                            <fieldset class="label_side top widget_tab field" style="display: none;">
                                <label for="required_field">Tab data</label>
                                <div id="tab"></div>
                            </fieldset>
                            
                        
                            <fieldset class="top widget_text field" style="display: none;">
                                <label for="required_field">Widget Text</label>
                                <div>
                                    <textarea class="ckeditor" id="widget_text" name="widget_text"><?php echo  $model->widget_text ?></textarea>
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                        
                            <fieldset class="label_side top">
                                <label for="required_field">Has ad to Widget</label>
                                <div class="jqui_radios">
                                    <?php $has_ad = ($model->has_ad) ? 'checked="checked"' : "";
                                    ?>
                                    <input type="radio" name="has_ad" value="1" id="yes_ad" <?php echo  $has_ad ?> /><label for="yes_ad">Yes</label>
                                    <input type="radio" name="has_ad" value="0" id="no_ad" <?php echo  ($has_ad) ? "" : 'checked="checked"'; ?>/><label for="no_ad">No</label>
                                </div>
                            </fieldset>
                        
                            <fieldset class="label_side top">
                                <label for="required_field">Ad Position</label>
                                <div>
                                    <?php
                                    if(!$model->ad_position)
                                        $model->ad_position = "";
                                    echo form_dropdown('ad_position', $ad_position,$model->ad_position);
                                    ?>
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>

                            <div class="button_bar clearfix">
                                <button class="green" type="submit">
                                    <span>Submit</span>
                                </button>
                            </div>
                       <?php echo form_close();?>  
                    </div>
                </div>


            </div>

        </div>


        <div class="tab_data field" style="display: none;">
            <fieldset class="label_side top" style="border-left: 1px solid #ddd; border-right: 1px solid #ddd;">
                <label for="required_field">Tab Title</label>
                <div>
                    <input id="tab_name" name="tab_name" value=""  type="text" class="required" minlength="3"  required />
                    <div class="required_tag"></div>
                </div>
            </fieldset>

            <fieldset class="label_side top" style="border-left: 1px solid #ddd; border-right: 1px solid #ddd;">
                <label for="required_field">Tab Type</label>
                <div>
                    <?php echo form_dropdown('tab_type', $tab_type);?><br />
                    <input type="checkbox" name="tab_title_from_type" id="tab_title_from_type" value="" style="display: none;" />
                    <span id="tab_title_from_type_span" style="display: none;">Use this as Tab Title</span>
                    <div class="required_tag"></div>
                </div>
            </fieldset>

            <fieldset class="label_side top tab_category tab_fields" style="display: none;border-left: 1px solid #ddd; border-right: 1px solid #ddd;">
                <label for="">Category</label>
                <div>
                    <ul class="tab_tree" style="height:300px; overflow-y: scroll;">
                        <li id="tab_all_category"><input id="tab_all" type="checkbox" value="all" <?php echo ( $model->is_all  ? "checked='checked'" : ''); ?> name="all" /><label for="all">All Categories</label>
                        <?php foreach ($category_tree as $value): ?>
                            <li><input id="tab_<?php echo $value['id'];?>" type="checkbox" value="<?php echo $value['id'];?>" <?php echo (isset($values->category_id) ? ($value['id'] == $values->category_id) ? "checked='checked'" : '' : ''); ?> name=<?php echo $model.'['. $model->category_id .']';?>" /><label for="<?php echo $value['id'];?>"><?php echo $value['title'];?></label>
                                <?php if (count($value['children']) > 0): ?>
                                    <ul>
                                        <?php foreach ($value['children'] as $childrens): ?>
                                            <li>
                                                <input id="tab_<?php echo $childrens['id'];?>" type="checkbox" <?php echo (isset($values->category_id) ? ($childrens['id'] == $values->category_id) ? "checked='checked'" : '' : '');?> value="<?php echo $childrens['id'];?>" name="<?php echo $model.'['. $model->category_id .']';?>" /><label for="<?php echo $childrens['id'];?>"><?php echo $childrens['title'];?></label>
                                            </li>
                                            <?php endforeach; ?>
                                    </ul>    
                                <?php endif; ?> 

                        <?php endforeach; ?>
                    </ul><br />
                    <input type="checkbox" name="tab_title_from_category" id="tab_title_from_category" value="" style="display: none;" />
                    <span id="tab_title_from_category_span" style="display: none;">Use this as Tab Title</span>
                    <div class="required_tag"></div>
                </div>
            </fieldset>
            
            <fieldset class="label_side top tab_news_count_fields tab_fields" style="display: none;border-left: 1px solid #ddd; border-right: 1px solid #ddd;">
                <label for="required_field">News Count</label>
                <div>
                    <input id="tab_news_count" name="tab_news_count" value=""  type="text"  maxlength="60"  />  
                </div>
            </fieldset>
            
            <fieldset class="top tab_text_fields tab_fields" style="display: none;border-left: 1px solid #ddd; border-right: 1px solid #ddd;">
                <label for="required_field">Tab Text</label>
                <div>
                    <textarea class="ckeditor" id="tab_text" name="tab_text"></textarea>
                    <div class="required_tag"></div>
                </div>
            </fieldset>
            
            <fieldset class="label_side top tab_cartoon tab_fields" style="display: none;border-left: 1px solid #ddd; border-right: 1px solid #ddd;">
                <label for="required_field">Cartoon</label>
                <div>
                    <select name="cartoon_gallery" id="cartoon_gallery">
                        <option value="">Select a cartoon for your Tab</option>
                        <?php if ( $cartoon_gallery ) foreach ($cartoon_gallery as $cartoon) : ?>
                            <option value="<?php echo $cartoon->id;?>"><?php echo $cartoon->gallery_name; ?></option>
                        <?php endforeach;?>
                    </select><br />
                    <input type="checkbox" name="tab_title_from_cartoon" id="tab_title_from_cartoon" value="" style="display: none;" />
                    <span id="tab_title_from_cartoon_span" style="display: none;">Use this as Tab Title</span>
                    <div class="required_tag"></div>
                </div>
            </fieldset>
        </div>