<script type="text/javascript" src="<?= base_url() ?>scripts/custom/ad/ad.js"></script>
<input type="hidden" id="base_url" value="<?= base_url() ?>" />
<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <h2 class="section">New Ad</h2>
                        <?php
                        //echo date('d M y');
                       // echo $tomorrow  = mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"));
                        $aError = array('type_id','url_link','name','html_code');
                        create_validation($aError);
                        ?>
                        <?=form_open('',array('class' => 'validate_form','enctype' => "multipart/form-data"));?>
                       
                            <input type="hidden" id="admin_id" value="0" />
                            <div class="form_title_bar"> Media </div>
                            <fieldset class="label_side top">
                                <label for="required_field">Banner Type</label>
                                <div>
                                    <select name="type" id="type_id" required >
					<option value="">Select:</option>
					<option value="1">Image</option>
					<option value="2">Html/SWF</option>
                                    </select>	
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            <fieldset class="label_side top" id="js_type_image" style="display:none;">
                                <label for="required_field">Banner Image<span>Upload a JPG, GIF or PNG file.</span></label>
                                <div>
                                    <input id="image" type="file" size="30" name="image" required >                                    
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            <fieldset class="label_side top" id="js_type_image_link" style="display:none;">
                                <label for="required_field">Banner Link</label>
                                <div>
                                    <input id="url_link" name="url_link"  type="text" required class="required" value="<?php echo set_value('url_link'); ?>"  minlength="4" />
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            <fieldset class="label_side top" id="js_type_html" style="display:none;">
                                <label for="required_field">HTML:</label>
                                <div>
                                    <div id="upload">Upload File</div><span id="status" ></span>
                                    <div style="margin-bottom:10px;"><ul id="files"></ul><ul id="files_url"></ul></div>
                                    <!--<input type="file" name="images" id="images">-->                                    
                                    <div id="response"></div>
                                    
                                    <textarea name="html_code" cols="60" rows="8" id="html_code" style="width:90%;" required ></textarea>
                                    <!--<a onclick="$Core.popup('http://www.champs21.com//englishclub/?do=/ad/preview/', {scrollbars: 'yes', location: 'no', menubar: 'no', width: 900, height: 400, resizable: 'yes', center: true}); return false;" href="#">Preview This Ad</a>-->
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            
                            
                            <div class="form_title_bar"> Campaign Details </div>
                            <fieldset class="label_side top">
                                <label for="required_field">Campaign Name:</label>
                                <div>                                    
                                    <input type="text" name="name" value="" id="name" class="required" required maxlength="150" minlength="3" />
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
                                        echo form_dropdown('menu_ci_key', $ad_link_location, $current_type, $js);
                                    ?>
                                </div>
                            </fieldset>
                            
                            <fieldset id="show_on_ad_plans" class="label_side top"   style="display:none;">
                                <label for="required_field">Campaign Plans:</label>
                                <div>
                                    <?php
                                    echo form_dropdown('plan_id_home', $ad_plans['home'], '', " id='ad_home' style='display:none;'");
                                    echo form_dropdown('plan_id_section', $ad_plans['section'], '', " id='ad_section' style='display:none;'");
                                    echo form_dropdown('plan_id_details', $ad_plans['details'], '', " id='ad_details' style='display:none;'");
                                    ?>

                                </div>
                            </fieldset>
                            <fieldset class="label_side top">
                                <label for="required_field">Priority:</label>
                                <div>                                    
                                    <input type="text" name="priority" value="200" id="priority" class="required" required maxlength="3" minlength="1" />
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            <fieldset class="label_side top">
                                <label for="required_field">Start Date</label>
                                <div>
                                    <input type="text" id="start_date" class="datepicker"  name="start_date">
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>

                            <fieldset class="label_side top">
                                <label for="required_field">End Date</label>
                                <div>
                                    <label><input type="radio" name="end_option" value="0" checked="checked" class="v_middle end_option" /> Do not end this campaign.</label> <br />
                                    <label><input type="radio" name="end_option" value="1" class="v_middle end_option" /> End on a specific date.</label>
                                    <input id="js_end_option" name="end_date"  type="text" class="datepicker" style="display:none;">
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>

                            <!--<fieldset class="label_side top">
                                <label for="required_field">Total Views</label>
                                <div>
                                    <input type="text" name="total_view" value="" id="total_view" class="disabled v_middle" size="10" disabled="disabled" />
                                    <label><input type="checkbox" name="view_unlimited" id="view_unlimited" class="v_middle" checked="checked" /> Unlimited</label>
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            
                            <fieldset class="label_side top" id="js_total_click" style="display:none;">
                                <label for="required_field">Total Clicks</label>
                                <div>
                                    <input type="text" name="total_click" value="" id="total_click" class="disabled v_middle" size="10" disabled="disabled" />
                                    <label><input type="checkbox" name="click_unlimited" id="click_unlimited" class="v_middle" checked="checked" /> Unlimited</label>
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>-->
                            <fieldset class="label_side top">
                                <label for="required_field">Active</label>
                                <div>
                                    <label><input type="radio" name="is_active" id='is_active' value="1" checked="checked" /> Yes</label>
                                    <label><input type="radio" name="is_active" id='is_active' value="0" /> No</label>			
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>
                            <fieldset class="label_side top">
                                <label for="required_field">For All</label>
                                <div>
                                    <label><input type="radio" name="for_all" id='for_all' value="1" /> Yes</label>
                                    <label><input type="radio" name="for_all" id='for_all' value="0" checked="checked" /> No</label>			
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
<style>
    #upload{
        padding:15px;
	font-weight:bold; font-size:12px;
	font-family:Verdana, Arial, Helvetica, sans-serif;
	text-align:center;
	background:#9ED3DC;
	color:#000000;
	border:1px solid #ccc;
	width:100px;
	cursor:pointer;
	-webkit-border-radius:5px;
	-moz-border-radius: 5px;
	border-radius: 5px;
}
.darkbg{
	background:#ddd !important;
}
#status{
	font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; color:#FF0000;
}
ul#files{ float:left;list-style:none; margin:10px 0px;display:none; }
ul#files_url{ float:left;list-style:none; margin:10px 0px;display:none; }
ul#files_url li{ padding:10px;  float:left;  font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px;}
ul#files li{ padding:10px;   width:200px; float:left;  font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px;}
ul#files li img{ max-width:180px; max-height:150px; }
.success{ background:#9ED3DC; border:1px solid #1A3265; }
.error{ background:#FF0000; border:1px solid #005B8E; }
</style>
<script  type="text/javascript">
$(document).ready(function(){
    $(document).on('change','#type_id',function(){
        var value = $( "select#type_id option:selected").val();
        if(value == 1)
        {
            $("#js_type_image").show();
            $("#js_type_image_link").show();
            $("#js_type_html").hide();                 
        }
        if(value == 2)
        {
            $("#js_type_image").hide();
            $("#js_type_image_link").hide();
            $("#js_type_html").show();
        }
    });
    $(document).on('change','#link_location',function(){
        var value = $( "select#link_location option:selected").val();
        if(value == 'index')
        {
            $("#show_on_ad_plans").show();
            $("#ad_home").show();
            $("#ad_section").hide();                 
            $("#ad_details").hide();
        }
        if(value == 'section')
        {
            $("#show_on_ad_plans").show();
            $("#ad_home").hide();
            $("#ad_section").show();                 
            $("#ad_details").hide();  
        }
        if(value == 'details')
        {
            $("#show_on_ad_plans").show();
            $("#ad_home").hide();
            $("#ad_section").hide();                 
            $("#ad_details").show();
        }
    });
});

</script>
<script type="text/javascript" src="<?= base_url() ?>scripts/custom/ad/ajaxupload.js"></script>
<script type="text/javascript" >
$(function(){

	var btnUpload=$('#upload');
	var status=$('#status');
	new AjaxUpload(btnUpload, {
		action: $('#base_url').val()+'/admin/ad/ajaxupload',
		name: 'filename',
		onSubmit: function(file, ext){
			 if (! (ext && /^(jpg|png|jpeg|gif|swf)$/.test(ext))){ 
				// other file extensions are not allowed 
				status.text('Only JPG, PNG or GIF files are allowed');
				return false;
			}
			status.text('Uploading...');
		},
		onComplete: function(file, response){
			//On completion clear the status
			status.text('');
			//Add uploaded file to list
			if(response==="error"){
                            $('<li></li>').appendTo('#files').text(file).addClass('error');                                
			} else{
                            $('#files').html('<li>'+response+'</li>').addClass('success');
                            //$('<li></li>').appendTo('#files_url').html( $('#base_url').val() +response ).addClass('success');
                            //$('#files_url').show();
                            $('#html_code').text(response);
                        }
                        $('#files').show();
		},
                tds_csrf : $('input[name$="tds_csrf"]').val()
	});

});
</script>