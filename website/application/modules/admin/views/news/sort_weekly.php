<link rel="stylesheet" href="<?php echo base_url();?>styles/adminica/smoothness/jquery-ui-1.10.3.custom.css" />
<script src="<?php echo base_url();?>scripts/jquery/jquery-1.9.1.js"></script>
<script src="<?php echo base_url();?>scripts/jquery/jquery-ui-1.10.3.custom.min.js"></script>
 <style>
    .column { width: 470px; float: left; }
    .portlet { margin: 0 1em 1em 0; }
    .portlet-header { margin: 0.3em; padding-bottom: 4px; padding-left: 0.2em; }
    .portlet-header .ui-icon { float: right; cursor: pointer; }
    .portlet-content { padding: 0.4em; }
    .ui-sortable-placeholder { border: 1px dotted black; visibility: visible !important; height: 50px !important; }
    .ui-sortable-placeholder * { visibility: hidden; }
    #button_div { width: 470px; clear: both; float: left; padding-bottom: 100px; }
    #button_div input{ cursor: pointer; }
    .ui-dialog-titlebar-close{
        display: none;
    }
</style>
<script>
    $(function() {
        $("#dialog_category_arrange").dialog({
            autoOpen: false,
            modal: false,
            show: {
                effect: "blind",
                duration: 1000
            },
            hide: {
                effect: "explode",
                duration: 1000
            }
        });
        
        $( ".column" ).sortable({
            connectWith: ".column"
        });

        $( ".portlet" ).addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" )
                       .find( ".portlet-header" )
                       .addClass( "ui-widget-header ui-corner-all" )
                       .end()
                       .find( ".portlet-content" )
                       .css("display","none");
        
        $("#arrange_category").on("click", function(){
            var category_ids = "";
            $( ".portlet-header .cat" ).each(function(){
                var id = this.id.replace("category_","");
                if ( id.length > 0 )
                {
                    category_ids += id + ",";
                }
            });
            
            category_ids = category_ids.substr(0, category_ids.length - 1);
            
            //I have my Ids now save
            $.post('<?php echo base_url();?>admin/news/save_weekly_priorities', {category_ids: category_ids,tds_csrf: $('input[name$="tds_csrf"]').val()}, function(data){
                   $("#dialog_category_arrange").dialog( "open" );
                   $("#btn_close").on("click", function(){
                        $("#dialog_category_arrange").dialog( "close" );
                    });
            });
        });
        
        $( ".column" ).disableSelection();
    });
</script>
<div id="pjax">
    <div id="wrapper" data-adminica-nav-top="1" data-adminica-side-top="1">
        <?php
            $widget = new Widget();
            $widget->run('sidebar');
        ?>
        <div id="main_container" class="main_container container_16 clearfix">
            
                <input type="button" style="cursor: pointer;position: fixed;-webkit-transform: translateZ(0); right: 200px;  top: 110px; text-align: right; " name="arrange_category" id="arrange_category" class="arrange_category" value="Save Priority" />
            
            <div class="flat_area grid_16">
                <input type="hidden" name="<?php echo $token_name;?>" value="<?php echo $token_val;?>" />
                <h2>Weekly Arrangement</h2>
                
                <div class="flat_area grid_16" >
                    <div class="alert alert_green" id="updated_messege"  style="display:block;">
                        <strong>Only First Two (2) Catgory will be displayed at Homepage.</strong>
                    </div>
                </div>
                
                <div class="column">
                    <?php if ($categories) foreach($categories as $categorie) : ?>
                    <div class="portlet">
                        <div class="portlet-header" id="category_<?php echo $categorie->id;?>">
                            <span class='cat' id="category_<?php echo $categorie->id;?>"></span>
                            <?php echo $categorie->name; ?>
                        </div>
                        <div class="portlet-content"></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="display_none">						
    <div id="dialog_category_arrange" class="dialog_content narrow" title="Arrange">
        <div class="block">
            <div class="section">
                <h1>Weekly Arrangement</h1>
                <div class="dashed_line"></div>	
                <p>Weekly has been Successfully Arranged </p>
                <p>Press The Ok button for continue</p>
            </div>
            <div class="button_bar clearfix">
                <button id="btn_close" class="dark blue no_margin_bottom link_button" data-link="<?= base_url() ?>admin/news/weekly_arrangement">
                    <div class="ui-icon ui-icon-check"></div>
                    <span>Ok</span>
                </button>
            </div>
        </div>
    </div>
</div>