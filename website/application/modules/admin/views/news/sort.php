<link rel="stylesheet" href="<?php echo base_url();?>styles/adminica/smoothness/jquery-ui-1.10.3.custom.css" />
<script src="<?php echo base_url();?>scripts/jquery/jquery-1.9.1.js"></script>
<script src="<?php echo base_url();?>scripts/jquery/jquery-ui-1.10.3.custom.min.js"></script>
 <style>
    .column { width: 500px; float: left; }
    .portlet { margin: 0 1em 1em 0; }
    .portlet-header { margin: 0.3em; padding-bottom: 4px; padding-left: 0.2em; }
    .portlet-header .ui-icon { float: right; cursor: pointer; }
    .portlet-content { padding: 0.4em; }
    .ui-state-highlight{ height: 40px;  }
    .ui-sortable-placeholder { border: 1px dotted black; visibility: visible !important; height: 50px !important; }
    .ui-sortable-placeholder * { visibility: hidden; }
    #button_div { width: 500px; clear: both; float: left; padding-bottom: 100px; }
    #button_div input{ cursor: pointer; }
    .ui-dialog-titlebar-close{
        display: none;
    }
    .sortable_news { list-style-type: none; margin: 0; padding: 0; width: 100%; }
    .sorted_news { list-style-type: none; margin: 0; padding: 0; width: 100%; }
    .sorted_category_news{ list-style-type: none; margin: 0; padding: 0; width: 100%; }
    .post_sort {margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: auto; cursor: move; }
    .sortable_news li span { position: absolute; margin-left: -1.3em; }
    .cate-posts{ display: none; }
    a{
        font-size: 14px !important;
    }
</style>
<script>
    $(function() {
        $(window).load(function(){
            $(".main_cate").val("");
        });
        $("#dialog_post_arrange").dialog({
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
        
        $( ".portlet" ).addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" )
                       .find( ".portlet-header" )
                       .addClass( "ui-widget-header ui-corner-all" )
                       .end()
                       .find( ".portlet-content" );
                       
        $( ".portlet-header .ui-icon" ).click(function() 
        {
            var obj = this;
            $( obj ).toggleClass( "ui-icon-minusthick" ).toggleClass( "ui-icon-plusthick" );
            $( obj ).parents( ".portlet:first" ).find( ".portlet-content" ).toggle();
        });
        
        $("#arrange_post").on("click", function(){
            var post_ids = "";
            $( ".sortable_news .post" ).each(function(){
                if ( $(this).parent().parent().parent().css("display") == "block" )
                {
		    if ( $(this).parent().parent().parent().attr("id") != "portlet_other_box" )
		    { 	
                    	var id = this.id.replace("post_","");
                    	if ( id.length > 0 )
                    	{
                        	post_ids += id + ",";
                    	}
		    }	
                }
            });
            
            
            post_ids = post_ids.substr(0, post_ids.length - 1);
            //I have my Ids now save
            $.post('<?php echo base_url();?>admin/news/save_priorities', {
                post_ids: post_ids,
                date:$('#change_date').val(),
                post_type: $("#post_type").val(),
                tds_csrf: $('input[name$="tds_csrf"]').val()}, function(data){
                   $("#dialog_post_arrange").dialog( "open" );
                   $("#btn_close").on("click", function(){
                        $("#dialog_post_arrange").dialog( "close" );
                    });
            });
        });
        
        $(document).on("change", ".category", function(){
            var cate_id = this.value;
            
            if ( cate_id == "" )
            {
                   $("#portlet_other_box").css("display","block");
                   $(".cate_portlet").css("display","none");
            }
            else
            {
                $(".cate_portlet").css("display","none");
                $("#portlet_other_box").css("display","none");
                $("#portletcate_" + cate_id).css("display","block");
            }
        });
        
        $( ".sortable_news" ).sortable({
            connectWith: ".sortable_news",
           
        });
        
        
        $('.del_post').on('click',function(){
            if(confirm("Are you sure to delete this from home page?")){
                var ar_post = $(this).parent().attr('id').split('_');
                var s_post_id = ar_post[1];
                var date_running = $("#change_date").val();
                var post_type = $("#post_type").val();
                var objparent = $(this).parent();
                $.post('<?php echo base_url();?>admin/news/delete_home_page/', {
                    primary_id: s_post_id,
                    date_running: date_running,
                    post_type: post_type,
                    tds_csrf: $('input[name$="tds_csrf"]').val(), 
                    user_agent: navigator.userAgent,}, function(data){
                    if(data == 1){
                        objparent.remove();
                    }else{
                        alert('Data cannot be deleted at the moment.');
                    }
                });
            }
            return false;
        });
        
        $('.change_filter').change(function(e){
            $.post('<?php echo base_url();?>admin/news/get_news_search/', {
                date: $("#change_date").val(), 
                post_type: $("#post_type").val(),
                tds_csrf: $('input[name$="tds_csrf"]').val(), 
                user_agent: navigator.userAgent
            }, function(data){
                 $("#homepage_sort_content").html(data);  
                 $('.del_post').on('click',function(){
                        if(confirm("Are you sure to delete this from home page?")){
                            var ar_post = $(this).parent().attr('id').split('_');
                            var s_post_id = ar_post[1];
                            var date_running = $("#change_date").val();
                            var post_type = $("#post_type").val();
                            var objparent = $(this).parent();
                            $.post('<?php echo base_url();?>admin/news/delete_home_page/', {
                                primary_id: s_post_id,
                                date_running: date_running, 
                                post_type: post_type,
                                tds_csrf: $('input[name$="tds_csrf"]').val(), 
                                user_agent: navigator.userAgent,}, function(data){
                                if(data == 1){
                                    objparent.remove();
                                }else{
                                    alert('Data cannot be deleted at the moment.');
                                }
                            });
                        }
                        return false;
                    });
                 $( ".sortable_news" ).sortable({
                            connectWith: ".sortable_news",
           
                 });
            });
        });
        
    });
</script>
<?php 
    $ar_posts_ids = array();
?>
<div id="pjax">
    <div id="wrapper" data-adminica-nav-top="1" data-adminica-side-top="1">
        <?php
            $widget = new Widget();
            $widget->run('sidebar');
        ?>
        <div id="main_container" class="main_container container_16 clearfix">
            
                <input style="cursor: pointer;position: fixed;-webkit-transform: translateZ(0); right: 30px; top: 70px; text-align: right; " type="button" name="arrange_post" id="arrange_post" value="Save Priority" />
                
                <div class="flat_area grid_16">
                <input type="hidden" name="<?php echo $token_name;?>" value="<?php echo $token_val;?>" />
                <h2>News Arrangement</h2>
                <div class="column">
                    <div style="float:left; clear:both; width:100%; margin-bottom:10px;">
                        <input type="text" readonly="readonly" id="change_date" class="datepicker filter_datepicker change_filter" style="width:200px;" value="<?php echo $date_running?>">
                    </div>
                    <div style="float:left; clear:both; width:100%; margin-bottom:10px;">
                    <?php
                        $js = "class='change_filter' id='post_type'";
                        echo form_dropdown('post_type', $type_array, 1,$js);
                    ?>
                    </div>
                    <div style="float:left; clear:both; width:100%" id="portlet_home" class="portlet ui-widget ui-widget-content ui-helper-clearfix ui-corner-all">
                        <div class="portlet-header ui-widget-header ui-corner-all">
                            <span class="cat ui-icon ui-icon-minusthick"></span>
                             Home Page News  
                        </div>
                        

                        <div class="portlet-content" id="homepage_sort_content">
                            <ul id="sortable" class="sortable_news" style="min-height: 100px;">
                                <?php foreach($home_page_post as $post): ?>
                                <li class="post post_sort ui-state-default" id="post_<?php echo $post->id;?>" style=" margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: auto; clear: both; cursor: move; line-height: 24px;"><span class="ui-icon ui-icon-arrowthick-2-n-s" style="position: absolute; margin-left: -1.3em;"></span>
                                    <?php echo $post->headline; ?>
                                    <?php if($post->show): ?>
                                     - <a target="_blank" href="<?php echo base_url("admin/news/edit/" . $post->id); ?>">EDIT</a>
                                    <label class="del_post" style="font-size: 14px; font-weight: bold; color: #555; cursor: pointer;">DELETE</label>
                                    <?php endif; ?>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                    
                        </div>
                    </div>
                   </div> 
                    <input type="hidden" name="<?php echo $token_name;?>" value="<?php echo $token_val;?>" />
                    
                    
                    
                           
                </div>
            </div>
        </div>
    </div>
</div>


<div class="display_none">						
    <div id="dialog_post_arrange" class="dialog_content narrow" title="Arrange">
        <div class="block">
            <div class="section">
                <h1>Post Arrangement</h1>
                <div class="dashed_line"></div>	
                <p>Post has been Successfully Arranged </p>
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