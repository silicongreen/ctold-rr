<script type="text/javascript" src="<?= base_url() ?>scripts/custom/ad/ad.js"></script>
<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section">Edit Ads</h2>
                        
                        <?php
                        $aError = array('type_id','url_link','name','html_code');
                        create_validation($aError);
                        ?>
                        <?=form_open('',array('class' => 'validate_form','enctype' => "multipart/form-data"));?>                            
                            <input type="hidden" name="id" id="id" value="<?= $ad_data->id ?>" />
                            <input type="hidden" name="type" id="type" value="<?= $ad_data->type ?>" />
                            <div class="form_title_bar"> Media </div>
                            <fieldset class="label_side top">
                                <label for="required_field">Banner Type</label>
                                <div>
                                    <?php echo (($ad_data->type == 1) ? "Image" : (($ad_data->type == 2) ? "Html Code" : 0));?>
                                    	
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            <?php  if($ad_data->type == 1){?>
                            <fieldset class="label_side top" id="js_ad_banner">
                                <label for="required_field">Banner Image<span>Upload a JPG, GIF or PNG file.</span></label>
                                <div>
                                     <img src="<?= base_url();?>upload/ads/<?=$ad_data->image_path;?>">
                                     Click
<a onclick="$('#js_ad_upload_banner').show(); $('#js_ad_banner').hide(); return false;" href="#">here</a>
to change this banner image. 
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            <fieldset class="label_side top" id="js_ad_upload_banner" style="display:none;">
                                <label for="required_field">Banner Image<span>Upload a JPG, GIF or PNG file.</span></label>
                                <div>
                                    <input type="file" size="30" name="image" required >                                    
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            <fieldset class="label_side top" id="js_type_image_link">
                                <label for="required_field">Banner Link</label>
                                <div>
                                    <input id="url_link" name="url_link" type="text" required class="required" value="<?php echo $ad_data->url_link; ?>"  minlength="4" />
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            <?php }?>
                            <?php if($ad_data->type == 2):?>
                            <fieldset class="label_side top" id="js_type_html">
                                <label for="required_field">HTML:</label>
                                <div>
                                    <textarea name="html_code" cols="60" rows="8" id="html_code" style="width:90%;" required ><?=$ad_data->html_code;?></textarea>
                                    <a onclick="$Core.popup('http://www.champs21.com//englishclub/?do=/ad/preview/', {scrollbars: 'yes', location: 'no', menubar: 'no', width: 900, height: 400, resizable: 'yes', center: true}); return false;" href="#">Preview This Ad</a>
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            <?php endif;?>
                            
                            
                            <div class="form_title_bar"> Campaign Details </div>
                            <fieldset class="label_side top">
                                <label for="required_field">Campaign Name:</label>
                                <div>                                    
                                    <input type="text" name="name" value="<?php echo $ad_data->name; ?>" id="name" class="required" required maxlength="150" minlength="3" />
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            <fieldset id="show_on_sub_menu" class="label_side top">
                                <label for="required_field">Campaign Link Location:</label>
                                <div>
                                    <?php
                                        $ad_link_location = array(NUll => 'Select', 'index' => 'Home', 'section' => 'Section', 'details' => 'Details');
                                        $current_type = 1;     
                                        $js = " id='link_location'";
                                        echo form_dropdown('menu_ci_key', $ad_link_location, $ad_data->menu_ci_key, $js);
                                    ?>
                                </div>
                            </fieldset>
                            <fieldset id="show_on_ad_plans" class="label_side top"   style="<?php if (!isset($ad_data->plan_id) || $ad_data->plan_id == null): ?>display:none<?php endif; ?>">
                               <label for="required_field">Campaign Plans:</label>
                               <div>
                                   <?php
                                        if (!isset($ad_data->plan_id))
                                            $plan_id = 0;
                                        else
                                            $plan_id = $ad_data->plan_id;
                                        
                                   echo form_dropdown('plan_id_home', $ad_plans['home'], $plan_id, " id='ad_home' style='display:".(($ad_data->menu_ci_key == "index")?"block":"none")."'" );
                                   echo form_dropdown('plan_id_section', $ad_plans['section'], $plan_id, " id='ad_section' style='display:".(($ad_data->menu_ci_key == "section")?"block":"none")."'" );
                                   echo form_dropdown('plan_id_details', $ad_plans['details'], $plan_id, " id='ad_details' style='display:".(($ad_data->menu_ci_key == "details")?"block":"none")."'" );
                                   ?>

                               </div>
                           </fieldset>
                            <fieldset class="label_side top">
                                <label for="required_field">Priority:</label>
                                <div>                                    
                                    <input type="text" name="priority" value="<?php echo $ad_data->priority; ?>" id="priority" class="required" required maxlength="3" minlength="1" />
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            <fieldset class="label_side top">
                                <label for="required_field">Start Date:</label>
                                <div>
                                    <input type="text" id="start_date" class="datepicker"  name="start_date" value="<?php echo date('Y-m-d', strtotime($ad_data->start_date));?>">
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>

                            <fieldset class="label_side top">
                                <label for="required_field">End Date:</label>
                                <div>
                                    <label><input type="radio" name="end_option" value="0" <?php echo (($ad_data->end_date > 0 ) ? "" : 'checked="checked"');?> class="v_middle end_option" /> Do not end this campaign.</label> <br />
                                    <label><input type="radio" name="end_option" value="1" <?php echo (($ad_data->end_date > 0 ) ? 'checked="checked"' : "");?> class="v_middle end_option" /> End on a specific date.</label>
                                    <input id="js_end_option" name="end_date"  type="text"  value="<?php echo ((strtotime($ad_data->end_date)!=null)?date('Y-m-d', strtotime($ad_data->end_date)):"");?>" class="datepicker" style="display:<?php echo (($ad_data->end_date > 0 ) ? "block" : "none");?>;">
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>

                            <!--<fieldset class="label_side top">
                                <label for="required_field">Total Views</label>
                                <div>
                                    <input type="text" name="total_view" value="<?php echo (($ad_data->total_view > 0 ) ? $ad_data->total_view : '');?>" id="total_view" class="disabled v_middle" size="10" <?php echo (($ad_data->total_view > 0 ) ? "" : 'disabled="disabled"');?> />
                                    <label><input type="checkbox" name="view_unlimited" id="view_unlimited" class="v_middle" <?php echo (($ad_data->total_view > 0 ) ? "" : 'checked="checked"');?> /> Unlimited</label>
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            
                            <fieldset class="label_side top" id="js_total_click" style="display:style="display:<?php echo (($ad_data->type_id == 1 ) ? "block" : "none");?>;";">
                                <label for="required_field">Total Clicks</label>
                                <div>
                                    <input type="text" name="total_click" value="<?php echo (($ad_data->total_click > 0 ) ? $ad_data->total_click : '');?>" id="total_click" class="disabled v_middle" size="10" <?php echo (($ad_data->total_click > 0 ) ? "" : 'disabled="disabled"');?> />
                                    <label><input type="checkbox" name="click_unlimited" id="click_unlimited" class="v_middle" <?php echo (($ad_data->total_click > 0 ) ? "" : 'checked="checked"');?> /> Unlimited</label>
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>-->
                            <fieldset class="label_side top">
                                <label for="required_field">Active</label>
                                <div>
                                    <label><input type="radio" name="is_active" id='is_active' value="1" <?php echo (($ad_data->is_active > 1 ) ? "" : 'checked="checked"');?> /> Yes</label>
                                    <label><input type="radio" name="is_active" id='is_active' value="0" <?php echo (($ad_data->is_active > 0 ) ? "" : 'checked="checked"');?> /> No</label>			
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            <fieldset class="label_side top">
                                <label for="required_field">For All</label>
                                <div>
                                    <label><input type="radio" name="for_all" id='for_all' value="1" <?php echo (($ad_data->for_all > 1 ) ? "" : 'checked="checked"');?> /> Yes</label>
                                    <label><input type="radio" name="for_all" id='for_all' value="0"  <?php echo (($ad_data->for_all > 0 ) ? "" : 'checked="checked"');?> /> No</label>			
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            




                            <div class="button_bar clearfix">
                                <button class="green" type="submit">
                                    <span>Submit</span>
                                </button>
                            </div>
                        <?=form_close();?>  
                    </div>
                </div>


            </div>

        </div>

<script  type="text/javascript">
$(document).ready(function(){    
    $(document).on('change','#link_location',function(){
        
        var value = $( "select#link_location option:selected").val();

        if(value == 'index')
        {
            $("#show_on_ad_plans").show();
            $("#ad_home").show();
            $("#ad_section").hide();                 
            $("#ad_details").hide();
            $('#ad_section option[selected]').removeAttr("selected");
            $('#ad_details option[selected]').removeAttr("selected");
        }
        if(value == 'section')
        {
            $("#show_on_ad_plans").show();
            $("#ad_home").hide();
            $("#ad_section").show();                 
            $("#ad_details").hide();  
            $('#ad_home option[selected]').removeAttr("selected");
            $('#ad_details option[selected]').removeAttr("selected");
        }
        if(value == 'details')
        {
            $("#show_on_ad_plans").show();
            $("#ad_home").hide();
            $("#ad_section").hide();                 
            $("#ad_details").show();
            $('#ad_section option[selected]').removeAttr("selected");
            $('#ad_home option[selected]').removeAttr("selected");
        }
    });
});

</script>