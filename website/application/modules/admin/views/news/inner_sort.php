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
        
        $( ".sorted_category_news" ).sortable({
            cursor: "move",
            placeholder: "ui-state-highlight",
            cancel: ".ui-state-disabled",
            remove: function( event, ui ) {
                var obj_id = this.id;
                $("#" + obj_id).addClass("sortable_news");
                $("#" + obj_id).removeClass("sorted_news");
            },
            receive: function( event, ui ) {
                var target_type = this.id.replace("sortable","");
                var item_id =  ui.item.attr('id').replace("post_","");
                var ids = item_id.split("_");
                var new_id = "post_" + ids[0] + "_" + target_type;
                $("#post_" + item_id).attr("id", new_id);
                var news_count = ui.item.parent().parent().parent().attr('id').replace("portlet_","");
                if ( news_count > 0 )
                {
                    var cnt = 0;
                    var obj_id = ui.item.parent().attr("id");
                    var obj_class = ui.item.parent().attr("class");
                    $("#" + obj_id + " li").each(function(){
                        cnt++;
                    });
                    if ( cnt >= news_count )
                    {
                        $("#" + obj_id).removeClass("sortable_news");
                        $("#" + obj_id).addClass("sorted_news");
                    }        
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    return false;
                }        
            }
        });

        $( ".portlet" ).addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" )
                       .find( ".portlet-header" )
                       .addClass( "ui-widget-header ui-corner-all" )
                       .end()
                       .find( ".portlet-content" )
                       .css("display","none");
                       
        $("#arrange_category").on("click", function(){
            
            var cate_id = $('#category_data').val();
            
            var post_ids = $("#sortable_" + cate_id + " li").map(function(i,n) {
                var id =  $(n).attr('id').split("_")[1];
                return id;
            }).get().join(',');
            
            
            var published_dates = $("#sortable_" + cate_id + " li").map(function(i,n) {
                var publish_date =  $(n).attr('id').split("_")[3];
                return publish_date;
            }).get().join(',');
            
            //I have my Ids now save
            $.post('<?php echo base_url();?>admin/news/save_innerpage_priority', {category_id: cate_id, post_ids: post_ids, published_dates: published_dates, tds_csrf: $('input[name$="tds_csrf"]').val()}, function(data){
                   $("#dialog_category_arrange").dialog( "open" );
                   $("#btn_close").on("click", function(){
                        $("#dialog_category_arrange").dialog( "close" );
                    });
            });
        });
        
        $( ".column" ).disableSelection();
        
        $('#category_data').change(function(e){
            $(".portlet").hide();
            var cate_id = $('#category_data').val();
            $("#portletcate_" + cate_id).show();
            $("#portletcate_" + cate_id + " > .portlet-content").show();
        });
    });
</script>
<div id="pjax">
    <div id="wrapper" data-adminica-nav-top="1" data-adminica-side-top="1">
        <?php
            $widget = new Widget();
            $widget->run('sidebar');
        ?>
        <div id="main_container" class="main_container container_16 clearfix">
        
                <input style="cursor: pointer;position: fixed;-webkit-transform: translateZ(0); right: 100px;  top: 70px; text-align: right; " type="button" name="arrange_category" id="arrange_category" class="arrange_category" value="Save Priority" />
           
            
            <div class="flat_area grid_16">
                <input type="hidden" name="<?php echo $token_name;?>" value="<?php echo $token_val;?>" />
                <h2>Innerpage News Arrangement</h2>
                
                <select name="category_data" id="category_data" class="category"  style="width: 166px;">
                    <option value="">Select a category?</option>
                    <?php $j=0; foreach($categories as $category) : ?>
                        <?php echo $category->name;?>
                        <option value="<?php echo $category->id; ?>"><?php echo $category->name; ?></option>
                    <?php $j++;endforeach; ?>
                </select>
                
                <div class="column">
                    
                    <?php if ($categories) foreach($categories as $category) : ?>
                        <div class="portlet" id="portletcate_<?php echo $category->id; ?>" style="display: none;">
                        
                            <div class="portlet-header" id="category_<?php echo $category->id;?>">
                                <span class="cat" id="category_<?php echo $category->id;?>"></span>
                                <?php echo $category->name; ?>
                            </div>
                            
                            <?php
                                
                                $this->load->config('tds');
                            
                                $obj_menu = new Menu();
                                $obj_menu = $obj_menu->get_ci_key_by_category_id((int)$category->id);
                                
                                $ci_key = (($obj_menu)) ? $obj_menu->ci_key : NULL;
                                
                                if(empty($ci_key) || strlen($ci_key) < 0 ){
                                    $obj_category = new Category();
                                    $obj_category = $obj_category->get_category_name_by_id((int)$category->id);
                                    $ci_key = sanitize($obj_category->name);
                                }
                                $i_limit = (isset($this->config->config[$ci_key]) && array_key_exists('total_news', $this->config->config[$ci_key])) ? $this->config->config[$ci_key]['total_news'] : 10;
                                
                                $posts = $obj_post_model->get_posts(array('inner_sorting', 'news'), NULL, NULL, $category->category_type_id, $category->id, 'between', 'post.priority, asc', $i_limit, TRUE, "", "", 0, $category->category_type_id, FALSE);
                                //var_dump($posts[0]);
//                                exit;
                            ?>
                            
                            <div class="portlet-content" style="display: block;">
                                <ul id="sortable_<?php echo $category->id; ?>" class="sorted_category_news">
                                    <?php if ($posts) foreach($posts as $post): ?>
                                    <li class="post post_sort ui-state-default" id="post_<?php echo $post->id;?>_<?php echo $category->id; ?>_<?php echo $post->published_date_only; ?>" style="list-style: none; margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: auto; cursor: move; line-height: 23px;"><span class="ui-icon ui-icon-arrowthick-2-n-s" style="position: absolute; margin-left: -1.3em;"></span>
                                        <?php echo $post->headline; ?>
                                        &nbsp;-&nbsp;<?php echo $post->published_date_only; ?>
                                        <?php if($post->show): ?>
                                        <a target="_blank" href="<?php echo base_url("admin/news/edit/" . $post->id); ?>">EDIT</a>
                                        <label class="del_post" style="font-size: 14px; font-weight: bold; color: #555; cursor: pointer;">DELETE</label>
                                        <?php endif; ?>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div> 
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
                <h1>Innerpage News Arrangement</h1>
                <div class="dashed_line"></div>	
                <p>Innerpage News has been Successfully Arranged </p>
                <p>Press The Ok button for continue</p>
            </div>
            <div class="button_bar clearfix">
                <button id="btn_close" class="dark blue no_margin_bottom link_button" data-link="<?php echo  base_url() ?>admin/news/save_innerpage_priority/">
                    <div class="ui-icon ui-icon-check"></div>
                    <span>Ok</span>
                </button>
            </div>
        </div>
    </div>
</div>