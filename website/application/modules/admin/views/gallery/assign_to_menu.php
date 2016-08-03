<link rel="stylesheet" href="<?php echo base_url();?>styles/adminica/smoothness/jquery-ui-1.10.3.custom.css" />
<script src="<?php echo base_url();?>scripts/jquery/jquery-1.9.1.js"></script>
<script src="<?php echo base_url();?>scripts/jquery/jquery-ui-1.10.3.custom.min.js"></script>
<script lang="" type="text/javascript" src="<?php echo base_url();?>scripts/gallery/gallery.js"></script>
<link href="<?php echo base_url();?>styles/adminca/gallery.css" media="screen" />
<style>
div.file {
    background: none repeat scroll 0 0 #FFFFFF;
    border: 1px solid #AAAAAA;
    border-radius: 4px 4px 4px 4px;
    margin: 3px;
    padding: 4px;
    margin-right: 10px;
}
div.file {
    cursor: default;
    float: left;
    text-align: center;
    white-space: nowrap;
    width: 100px;
}
div.file .thumb {
    background: none no-repeat scroll center center transparent;
    height: 100px;
    width: 100px;
}
div.file .name {
    font-weight: bold;
    height: auto;
    margin-top: 4px;
    overflow: hidden;
}
div.file .close {
    height: 30px;
    width: 30px;
    float: right;
    margin-top: -17px;
    margin-right: -17px;
    cursor: pointer;
}

</style>
<input type="hidden" name="<?php echo $token_name;?>" value="<?php echo $token_val;?>" />
<input type="hidden" name="base_url" id="base_url" value="<?php echo base_url();?>" />
<input type="hidden" name="type_data" id="type_data" value="<?php echo $type;?>" />
<input type="hidden" name="name" id="name" value="<?php echo $name;?>" />
<input type="hidden" name="material_type" id="material_type" value="<?php echo $type_id;?>" />
<div id="main_container" class="container_16 clearfix">
    <div class="box grid_16" >
        <div style="width: 400px; float: left; text-align: left; ">
            Select Menu: <br />
            <select name="menu" id="menu">
                <option value="">Select a menu</option>
                <?php if ($menus) foreach ($menus as $menu) : ?>
                    <option value="<?php echo $menu->id;?>"><?php echo $menu->title;?></option>
                <?php endforeach; ?>
            </select>    
        </div>
        <div style="width: 400px; float: right; text-align: left; ">
            Date: <br />
            <input id="issue_date" class="datepicker required" required name="issue_date" value="<?php echo date("Y-m-d"); ?>" readonly=""  type="text" >   
        </div>
        <div id="image_box" style="border: 1px solid #d5d5d5; clear: both; overflow: auto; height: 400px; width: 99%;">
            <br />
            <?php $i=0; if ( $images ) foreach( $images as $image) :?>
                <div class="file ui-state-default" id="imagediv_<?php echo $image['id'];?>">
                    <img id="close_<?php echo $image['id'];?>" src='<?php echo base_url(); ?>styles/layouts/tdsfront/images/close.png' class="close" />
                    <div style="background-image:url('<?php echo base_url() . "ckeditor/kcfinder/browse.php?type=image&lng=en&act=thumb&file=" . $image['filename'] . "&dir=" . urlencode($image['dir']);?>')" class="thumb"></div>
                    <div class="name"><?php echo $image['filename']; ?></div>
                </div>
            <?php $i++; endforeach; ?>
        </div>
    </div>
    <input type="hidden" name="cnt" id="cnt" value="<?php echo $i;?>" style="" />
    <div style="width: 100%; float: right; text-align: right; right: 20px; position: fixed; bottom: 0px;">
        <input type="button" name="assign_menu" id="assign_menu" value="Assign to Menu" />
    </div>
</div>

<div class="display_none">						
    <div id="dialog_gallery_arrange" class="dialog_content narrow" title="Arrange">
        <div class="block">
            <div class="section">
                <h1 style="font-size: 18px;">Assign to Menu</h1>
                <div class="dashed_line"></div>	
                <p style="font-size: 14px;">Images has been assign to menu successfully</p>
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

<script>
    $(document).ready(function(){
        $( ".datepicker" ).datepicker({
                dateFormat: 'yy-mm-dd',
                showOn:'focus'
        });
        $(document).on("click",".close",function(){
            var id = this.id.replace("close_","");
            $("#imagediv_" + id).css("display","none");
        });
        
        $(document).on("change","#menu",function(){
            $(".from_menu").remove();
            var ids = "";
            $(".file").each(function(){
                if ( $(this).css("display") != 'none' )
                {
                    ids += this.id.replace("imagediv_","") + ",";
                }
            });
            
            ids = ids.substr(0, ids.length - 1);
            $.ajax({
                type: 'POST',
                url: $("#base_url").val() + "admin/gallery/get_menu_gallery",
                data: {
                    images          : ids, 
                    menu            : $("#menu").val(), 
                    issue_data      : $("#issue_date").val(),
                    material_type   : $("#material_type").val(), 
                    name            : $("#name").val(),
                    tds_csrf        : $('input[name$="tds_csrf"]').val()
                },
                async: false,
                success: function(data) {
                    $("#image_box").append(data);
                },
                error: function() {
                    alert("Unknown error occur");
                }
            });
        });
        
        $(document).on("change","#issue_date",function(){
            $(".from_menu").remove();
            var ids = "";
            $(".file").each(function(){
                if ( $(this).css("display") != 'none' )
                {
                    ids += this.id.replace("imagediv_","") + ",";
                }
            });
            
            ids = ids.substr(0, ids.length - 1);
            $.ajax({
                type: 'POST',
                url: $("#base_url").val() + "admin/gallery/get_menu_gallery",
                data: {
                    images          : ids, 
                    menu            : $("#menu").val(), 
                    issue_data      : $("#issue_date").val(),
                    material_type   : $("#material_type").val(), 
                    name            : $("#name").val(),
                    tds_csrf        : $('input[name$="tds_csrf"]').val()
                },
                async: false,
                success: function(data) {
                    $("#image_box").append(data);
                },
                error: function() {
                    alert("Unknown error occur");
                }
            });
        });
        
        $(document).on("click","#assign_menu",function(){
            if ( $("#menu").val() == "" )
            {
                alert("You must select a menu");
                return ; 
            }
            var ids = "";
            $(".file").each(function(){
                if ( $(this).css("display") != 'none' )
                {
                    ids += this.id.replace("imagediv_","") + ",";
                }
            });
            
            ids = ids.substr(0, ids.length - 1);
            $.ajax({
                type: 'POST',
                url: $("#base_url").val() + "admin/gallery/assign_menu",
                data: {
                    images          : ids, 
                    menu            : $("#menu").val(), 
                    issue_data      : $("#issue_date").val(),
                    type_val        : $("#type_data").val(), 
                    name            : $("#name").val(), 
                    material_type   : $("#material_type").val(), 
                    tds_csrf        : $('input[name$="tds_csrf"]').val()
                },
                async: false,
                success: function(data) {
                    $("#dialog_gallery_arrange").dialog( "open" );
                       $("#btn_close").on("click", function(){
                            $("#dialog_gallery_arrange").dialog( "close" );
                            //hideDialog();
                    });
                },
                error: function() {
                    alert("Unknown error occur");
                }
            });
            
        });
        
        $( "#image_box" ).sortable({
            revert: true,
        });
    });
</script>