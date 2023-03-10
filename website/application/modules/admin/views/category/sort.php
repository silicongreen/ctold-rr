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
                       
        $( ".portlet-header .ui-icon" ).click(function() 
        {
            if ( $(this).hasClass('opened-once') )
            {
                var obj = this;
                $( obj ).toggleClass( "ui-icon-minusthick" ).toggleClass( "ui-icon-plusthick" );
                $( obj ).parents( ".portlet:first" ).find( ".portlet-content" ).toggle();
            }
            else
            {
                var id = this.id.replace("category_","");
                var obj = this;
                $( this ).toggleClass( "ui-icon-minusthick" ).toggleClass( "ui-icon-plusthick" );
                $.post('<?php echo base_url();?>admin/categories/sub_categories', {category_id: id,tds_csrf: $('input[name$="tds_csrf"]').val()}, function(data){
                    $( obj ).parents( ".portlet:first" ).find( ".portlet-content" ).html(data);
                    $( obj ).parents( ".portlet:first" ).find( ".portlet-content" ).toggle();
                    $( obj ).addClass("opened-once");
                    $( ".sortable" ).sortable({
                         placeholder: "ui-state-highlight"
                    });
                    $( ".sortable" ).disableSelection();
                });
            }
        });
        
        $("#arrange_category").on("click", function(){
            var category_ids = "";
            $( ".portlet-header .ui-icon" ).each(function(){
                var id = this.id.replace("category_","");
                if ( id.length > 0 )
                {
                    category_ids += id + ",";
                }
            });
        
            $( ".sortable .sub_cat" ).each(function(){
                var id = this.id.replace("category_","");
                if ( id.length > 0 )
                {
                    category_ids += id + ",";
                }
            });
            category_ids = category_ids.substr(0, category_ids.length - 1);
            
            //I have my Ids now save
            $.post('<?php echo base_url();?>admin/categories/save_priorities', {category_ids: category_ids,tds_csrf: $('input[name$="tds_csrf"]').val()}, function(data){
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
          
                <input style="cursor: pointer;position: fixed;-webkit-transform: translateZ(0); right: 30px;  top: 70px; text-align: right; " type="button" name="arrange_category" id="arrange_category" class="arrange_category" value="Save Priority" />
        
            <div class="flat_area grid_16">
                <input type="hidden" name="<?php echo $token_name;?>" value="<?php echo $token_val;?>" />
                <h2>Category Arrangement</h2>
                <div class="column">
                    <?php if ($categories) foreach($categories as $category) : ?>
                    <div class="portlet">
                        <div class="portlet-header" id="category_<?php echo $category->id;?>">
                            <span class='cat ui-icon ui-icon-plusthick' id="category_<?php echo $category->id;?>"></span>
                            <?php echo $category->name; ?>
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
                <h1>Category Arrangement</h1>
                <div class="dashed_line"></div>	
                <p>Category has been Successfully Arranged </p>
                <p>Press The Ok button for continue</p>
            </div>
            <div class="button_bar clearfix">
                <button id="btn_close" class="dark blue no_margin_bottom link_button" data-link="<?php echo  base_url() ?>admin/categories/sort_categories/">
                    <div class="ui-icon ui-icon-check"></div>
                    <span>Ok</span>
                </button>
            </div>
        </div>
    </div>
</div>