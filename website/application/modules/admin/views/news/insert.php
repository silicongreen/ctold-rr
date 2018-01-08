<script src="<?= base_url() ?>ckeditor/ckeditor.js"></script>
<script src="<?= base_url() ?>scripts/jquery/jquery.tree.js" type="text/javascript"></script>
<link href="<?= base_url() ?>scripts/tree/jquery.tree.css" rel="stylesheet" type="text/css" >
<script src="<?= base_url() ?>scripts/custom/customTree.js" type="text/javascript"></script>
<script src="<?= base_url() ?>scripts/jquery/jquery.cookie.js" type="text/javascript"></script>
<script src="<?= base_url() ?>scripts/jquery/sayt.jquery.js" type="text/javascript"></script>
<script src="<?= base_url() ?>scripts/custom/customNews.js"></script>
<script src="<?= base_url() ?>scripts/custom/jquery.liteuploader.js"></script>

<style>
    .ui-dialog-titlebar-close
    {
        display:none;
    }
    
</style>  
<script type="text/javascript">
		// select file function only for styling up input[type="file"]
		function select_file(){
			document.getElementById('attach_file').click();
			return false;
		}
	</script>

<div id="pjax">

    <?= form_open('', array('id' => 'valid_check_news', 'enctype' => 'multipart/form-data')); ?>
    <input type="hidden" name="id" value="<?= ($model->id) ? $model->id : 0; ?>" />
    <input type="hidden" id="current_date_for_publish" value="<?= $model->published_date ?>" />
    <?php if(!$model->id): ?>
         <input type="hidden" name="school_id" value="<?php echo $school_id; ?>" />
    <?php endif; ?>
    
    <div id="wrapper" data-adminica-nav-top="1" data-adminica-side-top="1">
        <?php
        $widget = new Widget;

        $widget->run('sidebar');
        ?>



        <div id="main_container" class="main_container container_16 clearfix">

            <div class="flat_area grid_16" >
                <div class="alert dismissible alert_green" id="updated_messege"  style="display:none;">
                    <img width="24" height="24" src="<?php echo base_url() ?>images/icons/small/white/speech_bubble_2.png">
                    <strong>News</strong>
                    Has been updated
                </div>
                <div id="validation_error">

                </div>
            </div>


            <div class="flat_area grid_16">
                <h2 class="section"><?= ($model->id) ? "Update" : "Add"; ?> Posts</h2>

            </div>


            <div class="box grid_12">
                <div class="block">


                    <fieldset class="label_side top"  style="display:none;">
                        <label for="required_field">Shoulder</label>
                        <div>
                            <input id="shoulder" name="shoulder" value="<?= $model->shoulder ?>"  type="text"  maxlength="60"  >  
                        </div>
                    </fieldset>

                    <?php if (isset($ref_headline)): ?>
                        <fieldset class="top">
                            <label for="required_field">Reference</span>

                            </label>
                            <div>
                                <?php echo $ref_headline ?>  
                            </div>
                        </fieldset>
                    <?php endif; ?>
                    <?php if (!$model->id): ?>
                        <fieldset class="top">
                            <label for="required_field">Reference Post<span>Leave empty if Main post</span>

                            </label>
                            <div>
                                <a href="javascript:void(0);" id="clear_reference">Clear</a> 
                                <input id="reference_filter" name="reference_filter"  type="text"    />    
                            </div>
                        </fieldset>
                        <input type="hidden" name="referance_id" id="referance_id" value="0" />
                    <?php endif; ?>
                    
                    <fieldset class="label_side top">
                        <label for="required_field">Send Notification</label>
                        <div class="clearfix">
                            <?php
                                $f_array = array(0 => "No", 1 => "Yes");
                                echo form_dropdown('send_notification', $f_array);
                            ?>
                        </div>
                    </fieldset>
                        
                    <fieldset class="label_side top">
                        <label for="required_field">Force Web View(Mobile)<span>Show web view on mobile. it's not depend on any type</span></label>
                        <div class="clearfix">
                            <?php
                                $f_array = array(0 => "No", 1 => "Yes");
                                echo form_dropdown('force_web_view_mobie', $f_array,$model->force_web_view_mobie);
                            ?>
                        </div>
                    </fieldset>

                    <fieldset class="label_side top">
                        <label for="required_field">Title</label>
                        <div>
                            <input id="headline" name="headline" value="<?= $model->headline ?>" class="required" required  type="text"  maxlength="100"  >  
                            <div class="required_tag"></div>
                        </div>
                    </fieldset>
                    <fieldset class="label_side top">
                        <label for="required_field">Subhead</label>
                        <div>
                            <input id="sub_head" name="sub_head" value="<?= $model->sub_head ?>"  type="text"  maxlength="255"  >  
                        </div>
                    </fieldset>
                    <fieldset class="label_side top">
                        <label for="required_field">Author</label>
                        <div>
                            <input id="byline_id" name="byline_id" value="<?= $model->byline_id ?>"  type="text"  maxlength="255"  >  

                        </div>
                    </fieldset>
                    <fieldset class="label_side top">
                        <label for="required_field">Mobile View Type</label>
                        <div class="clearfix">

                            <?php
                            $f_array = array(1 => "Web View", 2 => "Gallery View");


                            echo form_dropdown('mobile_view_type', $f_array, $model->mobile_view_type);
                            ?>
                            <div class="required_tag"></div>
                        </div>

                    </fieldset>
                    <fieldset class="label_side top">
                        <label for="required_field">Post Type</label>
                        <div class="clearfix">

                            <?php
                            $f_array = array(1 => "General", 2 => "Ad",3=>"Word Of The day");


                            echo form_dropdown('post_type', $f_array, $model->post_type);
                            ?>
                            <div class="required_tag"></div>
                        </div>

                    </fieldset>
                    <fieldset class="label_side top">
                        <label for="required_field">Spelling Bee<span>Select only if ad type post And use for home page spelling bee banner</span></label>
                        <div class="clearfix">

                           <?php
                            $f_array = array(0 => "No", 1 => "Yes");


                            echo form_dropdown('is_spelling_bee', $f_array, $model->is_spelling_bee);
                            ?>
                            
                        </div>

                    </fieldset>
                        
                    <fieldset class="label_side top">
                        <label for="required_field">Science Rocks<span>Select only if ad type post And use for home page Science Rocks banner</span></label>
                        <div class="clearfix">

                           <?php
                            $f_array = array(0 => "No", 1 => "Yes");


                            echo form_dropdown('is_science_rocks', $f_array, $model->is_science_rocks);
                            ?>
                            
                        </div>

                    </fieldset>
                        
                    <fieldset class="label_side top">
                        <label for="required_field">Ad Target<span>Select only if ad type post</span></label>
                        <div class="clearfix">

                           <?php
                            $f_array = array(1 => "New Window", 2 => "Same Window");


                            echo form_dropdown('ad_target', $f_array, $model->ad_target);
                            ?>
                            
                        </div>

                    </fieldset>
                    <fieldset class="label_side top">
                        <label for="required_field">For Category Type Ad<span>Select only if ad type post and its a category</span></label>
                        <div class="clearfix">

                            <?php
                            $js="id='category_id_for_subcategory'";
                            echo form_dropdown('category_id', $category_array, $model->category_id,$js);
                            ?>
                            
                        </div>

                    </fieldset>
                    <fieldset class="label_side top">
                        <label for="required_field">For Sub Category Type Ad<span>Select only if ad type category added</span></label>
                        <div class="clearfix" id="subcategory_id_div">

                            <?php
                            echo form_dropdown('subcategory_id_to_use', $subcategory_array, $model->subcategory_id_to_use);
                            ?>
                            
                        </div>

                    </fieldset>
                    <fieldset class="label_side top">
                        <label for="required_field">Assessment<span>Select only if post has assessment</span></label>
                        <div class="clearfix">

                            <?php
                            echo form_dropdown('assessment_id', $assessment_array, $model->assessment_id);
                            ?>
                            
                        </div>

                    </fieldset>
                    <fieldset class="top">
                        <label for="required_field">Lead Link<span>Leave Empty if not needed(Game Link, Ad Link etc)</span></label>
                        <div>
                            <input id="lead_link" name="lead_link" value="<?= $model->lead_link ?>"  type="text" />  

                        </div>
                    </fieldset>
                    <fieldset class="top">
                        <label for="required_field">Content</label>
                        <div>
                            <textarea class="ckeditor" id="content" name="content"><?= $model->content ?></textarea>
                            <div class="required_tag"></div>
                        </div>
                    </fieldset>
                    <fieldset class="top">
                        <label for="required_field">Mobile Content</label>
                        <div>
                            <textarea class="ckeditor" id="mobile_content" name="mobile_content"><?= $model->mobile_content ?></textarea>
                           
                        </div>
                    </fieldset>
                    <?php if (!$model->referance_id): ?>
                        <fieldset class="label_side top" id="publish_date_div_with_button">
                            <label for="required_field">Publish Date</label>
                            <div id="publish_date_div" style="display:none;">
                                <input  type="text" name="published_date" value="<?= $model->published_date ?>" id="published_date" class="datetimepicker_class required" required    >
                                <div class="required_tag"></div>
                            </div>
                            <div id="publish_date_button" >
                                <input  style="cursor:pointer; z-index:0;" type="button" value="<?= $model->published_date ?>" id="published_date_button" />
                            </div>    
                        </fieldset>
                    <?php endif; ?>
                    <fieldset class="label_side top">
                        <label for="required_field">Language</label>
                        <div id="language_div">
                            <?php
                            echo form_dropdown('language', $all_language, $model->language, "id='post_language'");
                            ?>

                        </div>
                    </fieldset> 
                    <fieldset class="label_side top">
                        <label for="required_field">Headline Color</label>
                        <div>
                            <input id="colorpicker_popup"  name="headline_color" value="<?= $model->headline_color ?>"  type="text"  maxlength="100"  > 

                            <?php if ($model->headline_color): ?>
                                <div id="div_headline_color" style="float:left; clear:both; width:20%; height:20px; margin-top:10px; background-color:#<?= $model->headline_color ?>" ></div>
                            <?php else : ?>
                                <div id="div_headline_color"  style="float:left; clear:both; width:20%; height:20px;margin-top:10px; background-color:#000" ></div>
                            <?php endif; ?>
                        </div>
                    </fieldset>
                        
                    <fieldset class="label_side top">
                        <label for="required_field">Post Layout</label>
                        <div class="clearfix">

                            <?php
                            $f_array = array(0 => "None", 1 => "Inside Image"
                                ,2=>"Bottom Gallery",3=>"Half Image Text",4=>"Left align post");


                            echo form_dropdown('post_layout', $f_array, $model->post_layout);
                            ?>
                        </div>
                    </fieldset> 
                        
                    <fieldset class="label_side top">
                        <label for="required_field">Inside Image <span>Use for inside post layout only</span></label>
                        <div>
                            <button class="green" id="select_inside_image"  type="button">
                                <span>Select Media</span>
                            </button>
                            <div  id="inside_image_box">
                                <?php
                                if ($model->inside_image):
                                    $title = '<img src="' . base_url() . $model->inside_image . '" width="70">';
                                    ?>
                                    <div><?= $title ?><input type="hidden" name="inside_image" value="<?= $model->inside_image ?>"><a class="text-remove"></a></div>
                                    <?php
                                endif;
                                ?>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="label_side top">
                        <label for="required_field">Short Title</label>
                        <div>
                            <input id="short_title" name="short_title" value="<?= $model->short_title ?>"  type="text"  maxlength="255"  >  
                        </div>
                    </fieldset>
                    <fieldset class="label_side top">
                        <label for="required_field">Sort Title Type</label>
                        <div class="clearfix">

                            <?php
                            $f_array = array(0 => "None", 1 => "Over Headline"
                                ,2=>"Below Headline",3=>"Left Image",4=>"Author Image",5=>"Over Image");


                            echo form_dropdown('sort_title_type', $f_array, $model->sort_title_type);
                            ?>
                        </div>
                    </fieldset> 
                   <fieldset class="label_side top">
                        <label for="required_field">Attach position</label>
                        <div class="clearfix">

                            <?php
                            $f_array = array(0 => "Below Content", 1 => "Above Content");


                            echo form_dropdown('pdf_top', $f_array, $model->pdf_top);
                            ?>
                        </div>
                    </fieldset>
                        
                    <fieldset class="label_side top">
                        <label for="required_field">Video <span>Support only MP4</span></label>
                        <div>


                            <input type="file" name="videoUpload" class="videoUpload" />
                            <div id="prograssbarvideo" style="display:none; margin-top:10px;background:#FFCCBA;width:0%; text-align: center; padding:10px 0px;">0%</div>
                            
                            <div style="padding:20px; float:left; width:95%; margin-top:10px; background: #fffacd"  id="video_div_box">
                                <?php
                                if($model->video_file!="")
                                {
                                   $title = '<video style="float:left; margin-bottom:5px;"  width="200px" controls><source src="'.base_url().$model->video_file.'" type="video/mp4">Browser unsuportad</video>';
                                ?>   
                                        <div style="float:left; padding:5px;clear:both; width:100%;"><?= $title ?>
                                            <input type="hidden" name="video_file" value="<?= $model->video_file ?>">
                                            <a style="float:right;position:relative;" class="text-remove"></a>
                                        </div>
                                 <?php   
                                  
                                }
                                ?>
                            </div>
                        </div>
                    </fieldset>
                        
                        
                    <fieldset class="label_side top">
                        <label for="required_field"> Attach<span>Support file pdf|doc|docx|docs</span></label>
                        <div>


                            <input type="file" name="fileUpload" class="fileUpload" />
                            <div id="prograssbar" style="display:none;margin-top:10px;background:#FFCCBA;width:0%; text-align: center; padding:10px 0px;">0%</div>
                            
                            <div style="padding:20px; float:left; width:95%; margin-top:10px; background: #fffacd"  id="file_div_box">
                                <?php
                                if(count($related_attach)>0)
                                {
                                   
                                   foreach($related_attach as $value)
                                   {
                                        $file_name_array = explode("/",$value->file_name);
                                        $file_name = $file_name_array[count($file_name_array)-1];
                                        $title = "<a style='float:left;margin-top:15px;vertical-align:middle' target='_blank'  href='" . base_url() . $value->file_name . "'>" . $file_name . "</a>";
                                        ?>
                                        <div style="float:left;height:30px; padding:5px;clear:both; width:100%;"><?= $title ?>
                                            <select style="float:left;margin-left:20px;margin-top:14px;" name="attach_checked[]"><option value="1">Show</option><option <?php if($value->show==0): ?>selected="selected"<?php endif; ?> value="0">Hide</option></select>
                                            <input style='float:left;margin-top:15px;margin-left:15px;' type='text' name='attach_caption[]' value="<?php echo $value->caption; ?>" size='40'>
                                            <input type="hidden" name="attach[]" value="<?= $value->file_name ?>"><a style="float:right;position:relative;" class="text-remove"></a></div>
                                        <?php   
                                   }
                                }
                                ?>
                            </div>
                        </div>  
                    </fieldset>    



                    <fieldset class="label_side top">
                        <label for="required_field">Lead Material <span>Support only image type</span></label>
                        <div>
                            <button class="green" id="select_lead_material"  type="button">
                                <span>Select Media</span>
                            </button>
                            <div  id="lead_material_box">
                                <?php
                                if ($model->lead_material):
                                    $title = '<a href="' . base_url() . $model->lead_material . '" target="_blank"><img src="' . base_url() . $model->lead_material . '" width="70"></a>';
                                    ?>
                                    <div><?= $title ?><input type="hidden" name="lead_material" value="<?= $model->lead_material ?>"><a class="text-remove"></a></div>
                                    <?php
                                endif;
                                ?>
                            </div>
                        </div>
                    </fieldset>



                    <fieldset class="label_side top">
                        <label for="required_field">Lead caption <span>If lead material added</span></label>
                        <div>
                            <textarea   id="lead_caption" name="lead_caption" ><?= $model->lead_caption ?></textarea>

                        </div>
                    </fieldset>
                    <fieldset class="label_side top">
                        <label for="required_field">Lead Source <span>If lead material added</span></label>
                        <div>
                            <input id="lead_source" name="lead_source" value="<?= $model->lead_source ?>"  type="text" />  

                        </div>
                    </fieldset>

                    <div class="clearfix block" id="gallery_box">
                        <?php if (isset($related_gallery)) : ?>
                            <?php
                            foreach ($related_gallery as $value):
                                if (is_array(@getimagesize(base_url() . $value->material_url)))
                                    $title = '<img src="' . base_url() . $value->material_url . '" width="70">';
                                else
                                {
                                    $url_parem = explode("/", $value->material_url);

                                    $last = count($url_parem) - 1;

                                    $value_url = $url_parem[$last];

                                    $title = '<a href="' . base_url() . $value->material_url . '">' . $value_url . '</a>';
                                }
                                ?>
                                <div class="gallery_image">
                                    <fieldset class="label_side top">
                                        <label for="required_field"><?= $title ?></label>
                                        <div class="noborder">
                                            <fieldset  class="top noborder">
                                                <label for="required_field">Caption</label>
                                                <div><input id="caption[]" name="caption[]" value="<?= $value->caption ?>" class="text"  type="text" /></div>
                                            </fieldset> 
                                            <fieldset class="top noborder">
                                                <label for="required_field">Source</label>
                                                <div><input id="source[]" name="source[]" value="<?= $value->source ?>" class="text"  type="text" /></div>
                                            </fieldset>  
                                        </div>
                                    </fieldset>
                                    <input type="hidden" name="related_img[]" value="<?= $value->material_url ?>">
                                    <a class="text-remove"></a>
                                </div>

                            <?php endforeach; ?>

                        <?php endif; ?>
                    </div> 

                    <fieldset class="label_side top">
                        <label for="required_field">Add Gallery</label>
                        <div>
                            <button class="green" id="select_media"  type="button">
                                <span>Select Media</span>
                            </button>
                        </div>
                    </fieldset>
                        
                        
                    <div class="clearfix block" id="gallery_box_mobile">
                        <?php if (isset($related_gallery_mobile)) : ?>
                            <?php
                            foreach ($related_gallery_mobile as $value):
                                if (is_array(@getimagesize(base_url() . $value->material_url)))
                                    $title = '<img src="' . base_url() . $value->material_url . '" width="70">';
                                else
                                {
                                    $url_parem = explode("/", $value->material_url);

                                    $last = count($url_parem) - 1;

                                    $value_url = $url_parem[$last];

                                    $title = '<a href="' . base_url() . $value->material_url . '">' . $value_url . '</a>';
                                }
                                ?>
                                <div class="gallery_image">
                                    <fieldset class="label_side top">
                                        <label for="required_field"><?= $title ?></label>
                                        <div class="noborder">
                                            <fieldset  class="top noborder">
                                                <label for="required_field">Caption<span>Use for mobile ad type post</span></label>
                                                <div><input id="caption_mobile[]" name="caption_mobile[]" value="<?= $value->caption ?>" class="text"  type="text" /></div>
                                            </fieldset> 
                                            <fieldset class="top noborder">
                                                <label for="required_field">Source<span>Use for mobile ad type post link. use http://</span></label>
                                                <div><input id="source_mobile[]" name="source_mobile[]" value="<?= $value->source ?>" class="text"  type="text" /></div>
                                            </fieldset>  
                                        </div>
                                    </fieldset>
                                    <input type="hidden" name="related_img_mobile[]" value="<?= $value->material_url ?>">
                                    <a class="text-remove"></a>
                                </div>

                            <?php endforeach; ?>

                        <?php endif; ?>
                    </div>
                       





                    <fieldset class="label_side top">
                        <label for="required_field">Add mobile images <span>(SIZE MUST BE 800*400 OTHER WISE NOT ADDED)</span></label>
                        <div>
                            <button class="green" id="select_media_mobile"  type="button">
                                <span>Select images for mobile</span>
                            </button>
                        </div>
                    </fieldset>    

                    <fieldset class="top">
                        <label for="required_field"> Summary ( Charecter count : <span style="font-weight:700;" id="charNum"><?php echo strlen($model->summary); ?></span> )</label>						
                        <div>
                            <textarea  name="summary" id="summary-text" onkeyup="countChar(this)"><?= $model->summary ?></textarea>							
                        </div>
                    </fieldset>
                    <fieldset class="top">
                        <label for="required_field">Country Filter<span>Leave empty if all country</span>

                        </label>
                        <div>
                            <a href="javascript:void(0);" id="clear_country">Clear</a> <input id="country_filter" name="country_filter" value="<?= $country_string ?>"  type="text"    />    
                        </div>
                    </fieldset>
                    <fieldset class="top">
                        <label for="required_field"> Embedded <span>Use for only special category</span></label>						
                        <div>
                            <textarea  name="embedded" id="embedded"><?= $model->embedded ?></textarea>							
                        </div>
                    </fieldset>
                    <?php if (!$model->referance_id): ?>
                        <fieldset class="label_side top" style="display: none">
                            <label for="required_field">Publish Type</label>
                            <div class="jqui_radios">
                                <?php
                                $is_breaking = ($model->is_breaking) ? 'checked="checked"' : "";
                                ?>
                                <input type="radio" name="type" value="Print"   id="type_print"   <?= ($model->type != "Online") ? 'checked="checked"' : ""; ?> /><label for="type_print">Print</label>
                                <input type="radio" name="type" value="Online"   id="type_online"  <?= ($model->type == "Online") ? 'checked="checked"' : ""; ?> /><label for="type_online">Online</label>
                                <div class="required_tag"></div>
                            </div>


                        </fieldset>
                    <?php endif; ?>
                    <fieldset class="label_side top" style="display: none">
                        <label for="required_field">Author</label>
                        <div class="clearfix">

                            <?php
                            $class_string = 'class="full_width"';
                            echo form_dropdown('user_id', $users, $user_id, $class_string);
                            ?>
                            <div class="required_tag"></div>
                        </div>

                    </fieldset>
                    <fieldset class="label_side top">
                        <label for="required_field">Two Column? </label>
                        <div class="jqui_radios">
                            <?php
                            $is_breaking = ($model->is_breaking) ? 'checked="checked"' : "";
                            ?>
                            <input type="radio" name="is_breaking" value="1" id="breaking_yes"  <?= $is_breaking ?> /><label for="breaking_yes">Yes</label>
                            <input type="radio" name="is_breaking" value="0" id="breaking_no"  <?= ($is_breaking) ? "" : 'checked="checked"'; ?>/><label for="breaking_no">No</label>
                        </div>


                    </fieldset>
                    <fieldset class="label_side top">
                        <label for="required_field">Two Column Expire</label>
                        <div>
                            <input id="breaking_expire" name="breaking_expire" class="datetimepicker_class"  value="<?= $model->breaking_expire ?>"  type="text" >  
                        </div>     
                    </fieldset>

                    <fieldset class="label_side top">
                        <label for="required_field">Three Column</label>
                        <div class="jqui_radios">
                            <?php
                            $is_exclusive = ($model->is_exclusive) ? 'checked="checked"' : "";
                            ?>
                            <input type="radio" name="is_exclusive" value="1"  id="exclusive_yes"  <?= $is_exclusive ?> /><label for="exclusive_yes">Yes</label>
                            <input type="radio" name="is_exclusive" value="0" id="exclusive_no" <?= ($is_exclusive) ? "" : 'checked="checked"'; ?>/><label for="exclusive_no">No</label>
                        </div>


                    </fieldset>
                    <fieldset class="label_side top">
                        <label for="required_field">Three Column Expire</label>
                        <div>
                            <input id="exclusive_expired" name="exclusive_expired" class="datetimepicker_class"  value="<?= $model->exclusive_expired ?>"  type="text" >  
                        </div>     
                    </fieldset>

                    <fieldset class="label_side top" style="display:none;">
                        <label for="required_field">Developing</label>
                        <div class="jqui_radios">
                            <?php
                            $is_developing = ($model->is_developing) ? 'checked="checked"' : "";
                            ?>
                            <input type="radio" name="is_developing" value="1" id="developing_yes" <?= $is_developing ?> /><label for="developing_yes">Yes</label>
                            <input type="radio" name="is_developing" value="0" id="developing_no" <?= ($is_developing) ? "" : 'checked="checked"'; ?>/><label for="developing_no">No</label>
                        </div>


                    </fieldset>
                    <?php if (!$model->referance_id): ?>
                        <fieldset class="label_side top"  id="featured_block_div">
                            <label for="required_field">Featured<span>Category Top post</span></label>
                            <div class="jqui_radios">
                                <?php
                                $is_featured = ($model->is_featured) ? 'checked="checked"' : "";
                                ?>
                                <input type="radio" name="is_featured" value="1" id="featured_yes" <?= $is_featured ?> /><label for="featured_yes">Yes</label>
                                <input type="radio" name="is_featured" value="0" id="featured_no" <?= ($is_featured) ? "" : 'checked="checked"'; ?>/><label for="featured_no">No</label>
                            </div>


                        </fieldset>

                        <fieldset class="label_side top"   id="featured_block_div_position">
                            <label for="required_field">Feature Position<span>Leave As it is if not featured</span></label>
                            <div class="clearfix">

                                <?php
                                $f_array = array(1 => "Top Most", 2 => "Top", 3 => "Bottom", 4 => "Bottom most");


                                echo form_dropdown('feature_position', $f_array, $model->feature_position);
                                ?>
                                <div class="required_tag"></div>
                            </div>

                        </fieldset>
                    <?php endif; ?>


                    <fieldset class="label_side top">
                        <label for="required_field">Can Comment</label>
                        <div class="jqui_radios">
                            <?php
                            $is_comment = ($model->can_comment == 0 || $model->can_comment == "") ? "" : 'checked="checked"';
                            ?>
                            <input type="radio" name="can_comment" value="1" id="comment_yes" <?= $is_comment ?> /><label for="comment_yes">Yes</label>
                            <input type="radio" name="can_comment" value="0" id="comment_no" <?= ($is_comment) ? "" : 'checked="checked"'; ?>/><label for="comment_no">No</label>
                        </div>


                    </fieldset>


                </div>
            </div>

            <div class="box grid_4">


                <div class="button_bar clearfix">

                    <?php if (access_check("news", "newsSave")): ?>         
                        <button class="green" id="Save"  type="button">
                            <span>Save</span>
                        </button>
                    <?php endif; ?>
                    <?php if (access_check("news", "publishNews")): ?> 
                        <button class="green"  id="publish"  type="button">
                            <span><?= ($model->status == 5) ? "Unpublish" : "Publish"; ?></span>
                        </button>
                    <?php endif; ?>
                    <?php if ($model->id): ?>
                    <button class="green"  id="preview" onclick="window.open('<?php echo base_url();?>news-admin-view-<?php echo $model->id; ?>')" type="button">
                        <span>Preview</span>
                    </button>
                    <?php endif; ?>

                </div> 
                <?php if ($model->id): ?>
                    <div class="button_bar clearfix">
                        <?php if (access_check("news", "delete")): ?>   
                            <button class="red"  id="trash"  type="button">
                                <span>Trash</span>
                            </button>
                        <?php endif; ?>


                    </div> 
                <?php endif; ?>



                <fieldset class="top"  id="show_in_block_div" <?php if ($model->id): ?> style="display:none;" <?php endif; ?>>
                    <label for="required_field">Show In Block</label>
                    <div class="jqui_radios">
                        <input type="radio" name="priority_type" value="1" <?= ($model->priority_type == 1) ? 'checked="checked"' : ""; ?> id="carousel_news"/><label for="carousel_news">Home Page</label>
                        <input type="radio" name="priority_type" value="5" <?= ($model->priority_type == 5) ? 'checked="checked"' : ""; ?> id="na_news"/><label for="na_news">N/A</label>
                    </div>
                </fieldset>
                
                

                <fieldset class="top">
                    <label for="required_field">Tags</label>
                    <div>
                        <input id="tags" name="tags" value="<?= $tag_string ?>"  type="text"    />    
                    </div>
                </fieldset>
                <fieldset class="top">
                    <label for="required_field">Keyword</label>
                    <div>
                        <input id="keywords" name="keywords" value="<?= $keyword_string ?>"  type="text"    />    
                    </div>
                </fieldset>

                <fieldset class="top">
                    <label for="required_field">Meta Description</label>
                    <div>
                        <textarea  name="meta_description"><?= $model->meta_description ?></textarea>
                    </div>
                </fieldset>
                <?php if (!$model->referance_id): ?>
                    <fieldset class="top"  id="category_div">
                        <label for="required_field">Category</label>
                        <div style="height:300px; overflow-y: scroll;">
                            <ul id="tree">
                                <?php echo $category_tree; ?>

                            </ul> 
                        </div>    
                    </fieldset>
                    <div id="game_category">

                        <fieldset class="top">
                            <label for="required_field">Game Type<span> Select A Type if Game Type Post</span></label>
                            <div>
                                <?php
                                $f_array = array(0 => 'NONE', 1 => "Web", 2 => "Mobile");


                                echo form_dropdown('game_type', $f_array, $model->game_type);
                                ?>


                            </div>
                        </fieldset>
                    </div>    



                    <fieldset class="top" id="grade_div">
                        <label for="required_field">For Grade
                            &nbsp;&nbsp;<input type="checkbox"  id="checkallgrade" value="1" /> Check all 
                        </label>
                        <div  id="tree2nd">

                            <?php echo $class_tree; ?>

                        </div>    
                    </fieldset>

                    <fieldset class="top" id="type_div">
                        <label for="required_field">For Type
                            &nbsp;&nbsp;<input type="checkbox"  id="checkalltype" value="1" /> Check all 
                        </label>
                        <div  id="tree3rd">
                            <?php echo $type_tree; ?>
                        </div>    
                    </fieldset>
                <?php endif; ?>

                <div class="clearfix block">
                    <h2 class="section">
                        Related  News Box
                    </h2>
                    <fieldset class="label_side top">
                        <label for="required_field">Related News Type<span>Change for Assessment</span></label>
                        <div class="clearfix">

                            <?php
                            $f_array = array(1 => "NEWS", 2 => "ASSESSMENT");
                            $js = "id='related_post_type'";

                            echo form_dropdown('related_post_type', $f_array, $model->related_post_type,$js);
                            ?>
                        </div>
                    </fieldset>
                    <fieldset class="top" id="related_news_box">
                        &nbsp; 
                        <?php if (isset($related_news)) : ?>
                            <?php foreach ($related_news as $value): ?>
                                <div class="text-button">
                                    <input type="hidden" name="related_title[]" value="<?= $value->title ?>">
                                    <input type="hidden" name="related_link[]" value="<?= $value->new_link ?>">
                                    <input type="hidden" name="related_published_date[]" value="<?= ($value->published_date ) ? $value->published_date : 0; ?>">
                                    <span class="text-label"><?= limit_string($value->title) ?></span>
                                    <a class="text-remove"></a>
                                </div>
                            <?php endforeach; ?>

                        <?php endif; ?>

                    </fieldset>

                </div>

                <div class="clearfix block">
                    <h2 class="section">
                        Related  News
                        <small>Search And Add news or add news by giving title and url</small>
                    </h2>
                    <fieldset class="top">
                        <label for="required_field">Search News</label>
                        <div>
                            <input id="releated_search"   type="text"    >    
                        </div>
                        <div class="clearfix">
                            <button class="green" id="add_releated" type="button">
                                <span>Add</span>
                            </button>
                        </div>    
                    </fieldset>


                </div>
                <?php
                if(!$model->editor_picks)
                {
                    $model->editor_picks = 10;
                }
                if(!$model->top_rated)
                {
                    $model->top_rated = 10;
                }
                if(!$model->most_popular)
                {
                    $model->most_popular = 10;
                }
                ?>
                 <div class="clearfix block">
                    <fieldset class="top">
                        <label for="required_field">Number Of Editor Picks to Show (Numeric Value)</label>
                        <div>
                            <input id="keywords" name="editor_picks" value="<?php echo $model->editor_picks  ?>"  type="text"    />    
                        </div>
                    </fieldset>
                    <fieldset class="top">
                        <label for="required_field">Number Of Top rated news to Show (Numeric Value)</label>
                        <div>
                            <input id="keywords" name="top_rated" value="<?php echo $model->top_rated   ?>"  type="text"    />    
                        </div>
                    </fieldset>
                    <fieldset class="top">
                        <label for="required_field">Number Of Most Popular news to Show (Numeric Value)</label>
                        <div>
                            <input id="keywords" name="most_popular" value="<?php echo $model->most_popular   ?>"  type="text"    />    
                        </div>
                    </fieldset>
                 </div>
                
                  <div class="clearfix block">

                        <fieldset class="top">
                            <label for="required_field">Post Visibility</label>
                            <div>
                                <?php
                                $f_array = array(1 => 'Common', 2 => "Only Web", 0 => "Only Mobile");


                                echo form_dropdown('website_only', $f_array, $model->website_only);
                                ?>


                            </div>
                        </fieldset>
                    </div>  

            </div>


        </div>    


        <?= form_close(); ?>  
    </div>


    <div class="display_none">						
        <div id="dialog_news_preview" class="dialog_content_preview dialog_content narrow" title="Preview">
            <div class="block">
                <div class="section">
                    <h3 id="shoulder_preview"></h3>
                    <h1 id="headline_preview"></h1>
                    <p id="subhead_preview"></p>
                    <p id="byline_preview"></p>
                    <p id="publish_preview"></p>

                    <div class="dashed_line"></div>	
                    <div id="content_preview"></div>
                    <div id="reletad_news_preview"></div>

                </div>
                <div class="button_bar clearfix">

                    <button class="light send_center close_dialog">
                        <div class="ui-icon ui-icon-closethick"></div>
                        <span>Ok</span>
                    </button>
                </div>


            </div>
        </div>
    </div>
    <script type="text/javascript">
        function countChar(val) {
            var len = val.value.length;
            $('#charNum').text(len);
        }
    </script>
    <style>

        #tree2nd ul 
        {
            list-style: none;
        }
        #tree2nd ul li 
        {
            float:left;
            margin: 3px 35px 6px 0;
        }
        #tree3rd ul 
        {
            list-style: none;
        }
        #tree3rd ul li 
        {
            float:left;
            margin: 3px 35px 6px 0;
        }
    </style>
