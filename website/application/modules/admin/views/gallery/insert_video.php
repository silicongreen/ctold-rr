<script lang="" type="text/javascript" src="<?php echo base_url();?>scripts/gallery/gallery.js"></script>
<style>
 .close_video {
     background-image: url("<?php echo base_url();?>images/plugins/fancybox/fancybox_sprite.png");
 }
 .close_video {
    cursor: pointer;
    height: 36px;
    position: relative;
    right: -18px;
    top: 16px;
    width: 36px;
    text-align: right;
    float: right;
    z-index: 8040;
}
</style>
<div id="pjax">
    <div style="padding-top:5px;" data-adminica-nav-top="1" data-adminica-side-top="1">

        <div id="main_container" class="main_container container_16 clearfix popup">


            <div class="flat_area grid_16">

                <div class="box grid_16">
                    <div class="block">
                        <?php echo form_open('',array('class' => 'validate_form'));?>
                            <input type="hidden" name="gallery_id" value="<?php echo ( isset($gallery_id) ) ? $gallery_id : 0; ?>" id="gallery_id" />
                            <fieldset class="label_side top">
                                <label for="required_field">Video URL</label>
                                <div>
                                    <input id="video_url" name="video_url" value=""  type="text" class="required" minlength="3"  required style="width: 200px;" >
                                    
                                    <div class="button_bar clearfix">
                                        <button id="play" class="green" type="button">
                                            <span>Play Video</span>
                                        </button>
                                        <div id="play_div" style="text-align: center; display: none;">
                                            
                                        </div>
                                    </div>
                                    
                                    <div class="required_tag"></div>
                                </div>
                            </fieldset>

                            <div class="button_bar clearfix">
                                <button class="green" type="button" id="save_video">
                                    <span>Submit</span>
                                </button>
                            </div>
                       <?php echo form_close();?>  
                    </div>
                </div>


            </div>

        </div>


        <div class="display_none">						
            <div id="dialog_gallery_arrange" class="dialog_content narrow" title="Arrange">
                <div class="block">
                    <div class="section">
                        <h1 style="font-size: 18px;">Video Upload</h1>
                        <div class="dashed_line"></div>	
                        <p style="font-size: 14px;">Video has been Uploaded successfully</p>
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