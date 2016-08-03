<link rel="stylesheet" href="<?php echo base_url();?>styles/adminica/smoothness/jquery-ui-1.10.3.custom.css" />
<script src="<?php echo base_url();?>scripts/jquery/jquery-1.9.1.js"></script>
<script src="<?php echo base_url();?>scripts/jquery/jquery-ui-1.10.3.custom.min.js"></script>
 <style>
    .column { width: 500px; float: left; }
    .portlet { margin: 0 1em 1em 0; }
    .portlet-header { margin: 0.3em; padding-bottom: 4px; padding-left: 0.2em; }
    .portlet-header .ui-icon { float: right; cursor: pointer; }
    .portlet-content { padding: 0.4em; }
    .ui-sortable-placeholder { border: 1px dotted black; visibility: visible !important; height: 50px !important; }
    .ui-sortable-placeholder * { visibility: hidden; }
    #button_div { width: 500px; clear: both; float: left; padding-bottom: 100px; }
    #button_div input{ cursor: pointer; }
    .ui-dialog-titlebar-close{
        display: none;
    }
    .sortable_news { list-style-type: none; margin: 0; padding: 0; width: 100%; }
    .sorted_news { list-style-type: none; margin: 0; padding: 0; width: 100%; }
    .post_sort {margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: auto; cursor: move; }
    .sortable_news li span { position: absolute; margin-left: -1.3em; }
</style>
<script>
    $(function() {
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
                var id = this.id.replace("post_","");
                if ( id.length > 0 )
                {
                    post_ids += id + ",";
                }
            });
            
            $( ".sorted_news .post" ).each(function(){
                var id = this.id.replace("post_","");
                if ( id.length > 0 )
                {
                    post_ids += id + ",";
                }
            });
            post_ids = post_ids.substr(0, post_ids.length - 1);
            //I have my Ids now save
            $.post('<?php echo base_url();?>admin/news/save_priorities', {post_ids: post_ids,tds_csrf: $('input[name$="tds_csrf"]').val()}, function(data){
                   $("#dialog_post_arrange").dialog( "open" );
                   $("#btn_close").on("click", function(){
                        $("#dialog_post_arrange").dialog( "close" );
                    });
            });
        });
        
        $( ".sortable_news" ).sortable({
            connectWith: ".sortable_news",
            cursor: "move",
            placeholder: "ui-state-highlight",
            cancel: ".ui-state-disabled",
            receive: function( event, ui ) {
                var target_type = this.id.replace("sortable","");
                var item_id =  ui.item.attr('id').replace("post_","");
                var ids = item_id.split("_");
                var new_id = "post_" + ids[0] + "_" + target_type;
                $("#post_" + item_id).attr("id", new_id)
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
                }
            },
            remove: function( event, ui ) {
                var obj_id = this.id;
                $("#" + obj_id).addClass("sortable_news");
                $("#" + obj_id).removeClass("sorted_news");
            }
        });
        
        $( ".sorted_news" ).sortable({
            connectWith: ".sortable_news",
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
                }
            }
        });
        $( ".sortable_news" ).disableSelection();
        //$( ".column" ).disableSelection();
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
            <div id="button_div" style="position: fixed; right: 10px; width: 100px; top: 70px; text-align: right; ">
                <input type="button" name="arrange_post" id="arrange_post" value="Save Priority" />
            </div>
            <div class="flat_area grid_16">
                <input type="hidden" name="<?php echo $token_name;?>" value="<?php echo $token_val;?>" />
                <h2>News Arrangement</h2>
                <div class="column">
                    <?php if ($types) foreach($types as $ind => $type) : ?>
                    <?php if ( $ind == 5 ) : ?>
                    <?php $i=0; if ($categories) foreach($categories as $category) : ?>
                    <div class="portlet" id="portlet_<?php echo $ind; ?>">
                        <div class="portlet-header">
                            <span class='cat ui-icon ui-icon-minusthick'></span>
                            <?php echo $category->name; ?>
                        </div>
                        <div class="portlet-content">
                            <?php 
                                $posts = $obj_posts->get_posts_by_category($category->id, $issue_date_from, $issue_date_to);
                                $i_cnt = 0;
                                if ($posts) foreach($posts as $post){
                                    $i_cnt++; 
                                }
                            ?>
                            <ul id="sortable<?php echo $ind; ?>" class="<?php echo ($i_cnt >= $news_count[$ind] ) ? "sorted_news" : "sortable_news"; ?>">
                                <?php if ($posts) foreach($posts as $post) : ?>
                                <li class="post post_sort ui-state-default <?php echo ( in_array($post->id, $ar_posts_ids) ) ? "ui-state-disabled" : ""; ?>" id="post_<?php echo $post->id;?>_<?php echo $ind;?>_<?php echo $category->id;?>" style=" margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: auto; cursor: move; float: left; display: block; width: 90%; line-height: 23px;"><span class="ui-icon ui-icon-arrowthick-2-n-s" style="position: absolute; margin-left: -1.3em;"></span>
                                    <div style="width:80%; float: left;"><?php echo $post->headline; ?></div>
                                    <div style="width:20%; text-align: right; float: right">
                                        <?php if ( in_array($post->id, $ar_posts_ids) ) : ?>
                                            &nbsp;
                                        <?php else : ?>
                                            <a target="_blank" href="<?php echo base_url("admin/news/edit/" . $post->id); ?>">EDIT</a>
                                        <?php endif; ?>    
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <?php if ( $i == 1 ) break; ?>
                    <?php $i++; endforeach; ?>
                    </div><div class="column">
                    <?php endif; ?>
                    <div class="portlet" id="portlet_<?php echo $news_count[$ind]; ?>">
                        <?php if ( $ind == 5 ) : ?>
                        <div class="portlet-header" style="float: left; width: 95%; padding: 5px;">
                            <span class='cat ui-icon ui-icon-minusthick'></span>
                            <div style="float: left; width: 60%;"><?php echo $type; ?></div>
                            <div style="height: 20px; margin-top: -10px; padding: 0; width: 30%; text-align: right;">
                                <select name="category" id="category" >
                                <option value="">Select a category?</option>
                                <?php $i=0; if ($categories) foreach($categories as $category) : ?>
                                <option value="<?php echo $category->id;?>"><?php echo $category->name; ?></option>
                                <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div style="clear: both;"></div>
                        <?php else : ?>
                        <div class="portlet-header">
                            <span class='cat ui-icon ui-icon-minusthick'></span>
                            <?php echo $type; ?>
                        </div>    
                        <?php endif; ?>
                        
                        <div class="portlet-content">
                            <?php
                                $posts = $obj_posts->where("priority_type", $ind)->where_between("published_date", $issue_date_from, $issue_date_to)->order_by("priority","ASC")->get();
                                $i_cnt = 0;
                                if ($posts) foreach($posts as $post){
                                    $i_cnt++; 
                                }
                            ?>
                            <?php $s_class = ( $ind == 5 ) ? " other_box" : "" ; ?>
                            <ul id="sortable<?php echo $ind; ?>" class="<?php echo ($i_cnt >= $news_count[$ind] && $news_count[$ind] > 0 ) ? "sorted_news" : "sortable_news"; ?><?php echo $s_class; ?>" style="min-height: 100px;">
                                <?php if ($posts) foreach($posts as $post) : ?>
                                <li class="post post_sort ui-state-default" id="post_<?php echo $post->id;?>_<?php echo $ind;?>" style=" margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: auto; clear: both; cursor: move; "><span class="ui-icon ui-icon-arrowthick-2-n-s" style="position: absolute; margin-left: -1.3em;"></span>
                                    <?php echo $post->headline; ?>
                                    <span style="width:20%; text-align: right;"><a target="_blank" href="<?php echo base_url("admin/news/edit/" . $post->id); ?>">EDIT</a></span>
                                </li>
                                <?php array_push( $ar_posts_ids, $post->id );endforeach; ?>
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