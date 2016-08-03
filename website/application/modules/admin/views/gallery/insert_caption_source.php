<link rel="stylesheet" href="<?php echo base_url();?>styles/adminica/smoothness/jquery-ui-1.10.3.custom.css" />
<script src="<?php echo base_url();?>scripts/jquery/jquery-1.9.1.js"></script>
<script src="<?php echo base_url();?>scripts/jquery/jquery-ui-1.10.3.custom.min.js"></script>
<script lang="" type="text/javascript" src="<?php echo base_url();?>scripts/gallery/gallery.js"></script>
<link href="<?php echo base_url();?>styles/adminca/gallery.css" media="screen" />

<input type="hidden" name="<?php echo $token_name;?>" value="<?php echo $token_val;?>" />
<input type="hidden" name="base_url" id="base_url" value="<?php echo base_url();?>" />
<input type="hidden" name="pos" id="pos" value="<?php echo base_url();?>" />
<div id="main_container" class="container_16 clearfix">
    <div class="box grid_16" >
        <?php $i=0; if ( $images ) foreach( $images as $image) :?>
        <div id="image_container_<?php echo $i;?>" class="img_container" style="position: fixed; top: 60px; <?php echo ($i > 0) ? "display:none;" : "";?>">
            <div style="height: 120px; width: 80px; float: left; text-align: left; margin-top: 80px;">
                <img id="pre_<?php echo $i;?>" style="display: none; cursor: pointer;" class="prev" src="<?php echo base_url();?>styles/layouts/tdsfront/images/prev.png" />    
            </div>
            <div style="height: 200px; width: 155px; margin-left: 100px; float: left; border: 1px solid #fff; padding: 10px; background: #fff;">
                <img src="<?php echo base_url() . $image['material_url'];?>" width="150" />
            </div>
            <div style="height: 120px; width: 215px; float: left; padding: 10px; margin-left: 10px; margin-top: 40px;">
                <div style="width: 145px; float: left; padding: 10px; margin-left: 10px;">
                    Caption: <br /><input type="text" name="caption_<?php echo $image['id'];?>" id="caption_<?php echo $image['id'];?>" class="cap" value="" />
                </div>
                <div style="width: 145px; float: left; padding: 10px; margin-left: 10px;">
                    Source: <br /><input type="text" name="source_<?php echo $image['id'];?>" id="source_<?php echo $image['id'];?>" value="" />
                </div>
            </div>
            <div style="height: 120px; width: 70px; float: right; text-align: right; margin-top: 80px;<?php echo (count($images) == 1) ? "display:none;": "display: block"; ?> ">
                <img id="next_<?php echo $i;?>" style="cursor: pointer;" class="next" src="<?php echo base_url();?>styles/layouts/tdsfront/images/next.png" />    
            </div>
        </div>
        <div style="clear: both; height: 20px;"></div>
        <?php $i++; endforeach; ?>
    </div>
    <input type="hidden" name="cnt" id="cnt" value="<?php echo $i;?>" style="" />
    <div style="width: 100%; float: right; text-align: right; right: 20px; position: fixed; bottom: 0px;">
        <input type="button" name="save_caption" id="save_caption" value="Save Caption" />
    </div>
</div>

<div class="display_none">						
    <div id="dialog_gallery_arrange" class="dialog_content narrow" title="Arrange">
        <div class="block">
            <div class="section">
                <h1 style="font-size: 18px;">Gallery Caption Arrangement</h1>
                <div class="dashed_line"></div>	
                <p style="font-size: 14px;">Caption and Source has been Updated successfully</p>
                <p style="font-size: 13px;">Press The Ok to close the Dialog and continue</p>
            </div>
            <div class="button_bar clearfix">
                <button id="btn_close" class="dark blue no_margin_bottom link_button" data-link="<?php echo  base_url() ?>admin/categories/sort_categories/">
                    <div class="ui-icon ui-icon-check"></div>
                    <span style="font-size: 10px;">Ok</span>
                </button>
            </div>
        </div>
    </div>
</div>