/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var oTable;
var oTableTrash;
var sortindex = 0;
var sorttype = "asc";
var param = 0;

$(document).ready(function(){
    var curpos = 0;
    
    if ( $(".validate_form_gallery").length > 0 )
    {
        $(".validate_form_gallery").validate();

        if($("#sortIndex").length>0) 
        {
            sortindex = $("#sortIndex").val();
        }
        if($("#sorttype").length>0) 
        {
            sorttype = $("#sorttype").val();
        }
        oTable = $('.mytable_gallery').dataTable( {
            "bJQueryUI": true,
            "sScrollX": "",
            "sScrollY": "100px",
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": $("#base_url").val()+"admin/"+$("#controllername").val()+"/datatable/",
            "bSortClasses": false,
            "aaSorting": [[sortindex,sorttype]],
            "bAutoWidth": true,
            "bInfo": true,
            "bScrollCollapse": true,
            "sPaginationType": "full_numbers",
            "bRetrieve": true,
            "fnInitComplete": function () {

                $(".mytable_gallery .dataTables_length > label > select").uniform();
                $(".mytable_gallery .dataTables_filter input[type=text]").addClass("text");
                $(".mytable_gallery").css("visibility","visible");
                this.fnAdjustColumnSizing(true);

            },
            'fnServerData': function(sSource, aoData, fnCallback)
            {
                $.ajax
                ({
                    'dataType': 'json',
                    'type'    : 'POST',
                    'url'     : sSource,
                    'data'    : aoData,
                    'success' : fnCallback
                });
            } 
        });

        $(document).on("click", "button.model", function(){


            $.fancybox({
                'width'		        : $("#modelwidth").val(),
                'height'                    : $("#modelheight").val(),
                'autoScale'                 : true,
                'href'			: this.id,
                'title'                     : false,
                'transitionIn'		: 'none',
                'transitionOut'		: 'none',
                'type'		        : 'iframe'

            });

        });

        $(document).on("click", "button.ajax", function(){ 

            var $confirm_messege = "Do you really want to delete?";

            if($(this).html()=="Change status")
                $confirm_messege = "Do you really want to change status?";

            if(confirm($confirm_messege))
            {
                var id = this.id;
                $.post($("#base_url").val()+"admin/"+$("#controllername").val()+"/"+$(this).attr('executeFunction')+"/", {
                    primary_id:this.id, 
                    user_agent: navigator.userAgent,
                    tds_csrf: $('input[name$="tds_csrf"]').val()
                    })
                .done(function(data) {
                    if ( data == "image_exists_on_gallery" )
                    {
                        var c = confirm("Image exists on Gallery please delete those images\nbefore you want to delete this gallery\n\nDo you want to delete this Gallery anyway?");
                        if ( c )
                        {
                            $.post($("#base_url").val()+"admin/"+$("#controllername").val()+"/deleteall/", {
                                primary_id:id,
                                tds_csrf: $('input[name$="tds_csrf"]').val()
                                })
                            .done(function(data) {
                                oTable.fnClearTable(true);
                            });
                        }
                    }
                    else
                    {
                        oTable.fnClearTable(true);
                    }
                });
            }

        });

        $(document).on("blur", "input.filter", function(){ 

            var idFilter = this.id;
            var $filterIdArray =  idFilter.split("_");
            oTable.fnFilter($(this).val(),$filterIdArray[1]);

        });

        $(document).on("change", "select.filter", function(){ 
            var idFilter = this.id;
            var $filterIdArray =  idFilter.split("_");
            oTable.fnFilter($(this).val(),$filterIdArray[1],'eq');


        });
        $(document).on("change", "select.group_concate", function(){ 
            var idFilter = this.id;
            var $filterIdArray =  idFilter.split("_");
            oTable.fnFilter($(this).val(),$filterIdArray[1],'group_concate');


        });

        $(document).on("change", "input.filter_datepicker", function(){ 
            var idFilter = this.id;
            var $filterIdArray =  idFilter.split("_");
            oTable.fnFilter($(this).val(),$filterIdArray[1]);


        });
        $(document).on("click", "button.ajax_restore", function(){ 

            if(confirm("Do you really want to restore this news?"))
            {

                $.post($("#base_url").val()+"admin/"+$("#controllername").val()+"/"+$(this).attr('executeFunction')+"/", {
                    primary_id:this.id, 
                    user_agent: navigator.userAgent,
                    tds_csrf: $('input[name$="tds_csrf"]').val()
                    })
                .done(function(data) {
                    oTableTrash.fnClearTable(true);
                });
            }

        });
    }
    
    $("#dialog_gallery_arrange").dialog({
            autoOpen: false,
            show: {
                effect: "blind",
                duration: 1000
            },
            hide: {
                effect: "explode",
                duration: 1000
            }
    });
    
    $(".next").click(function(){
        var id = this.id.replace("next_","");
        curpos++;
        $("#next_" + curpos).show();
        if ( curpos == ( $("#cnt").val() - 1) )
        {
            $("#next_" + curpos).hide();
        }
        $("#pre_" + curpos).show();
        $(".img_container").hide();
        $("#image_container_" + curpos).show();
    });

    $(".prev").click(function(){
        var id = this.id.replace("next_","");
        curpos--;
        $("#pre_" + curpos).show();
        $("#next_" + curpos).show();
        if ( curpos <= 0 )
        {
            $("#pre_" + curpos).hide();
        }

        $(".img_container").hide();
        $("#image_container_" + curpos).show();
    });
    
    $("#save_caption").on("click", function(){
        var image_data;
        var image_array = new Array();
        var i = 0;
        var empty_count_caption = 0;
        var empty_count_source = 0;
        $(".cap").each(function(){
            image_data = new Object();
            var id = this.id.replace("caption_","");
            image_data.material_id = id;
            image_data.caption = this.value;
            if ( this.value.length == 0 )
            {
                empty_count_caption++;
            }
            image_data.source = $("#source_" + id).val();
            if ( image_data.source.length == 0 )
            {
                empty_count_source++;
            }
            image_array[i] = image_data;
            i++;
        });
        
        if ( empty_count_caption == empty_count_source && empty_count_caption == i )
        {
            alert("You must enter at least One Caption or One Source to Continue");
            return ;
        }
        
        $.ajax({
            type: 'POST',
            url: $("#base_url").val() + "/admin/gallery/save_caption_data",
            data: {images : image_array,tds_csrf: $('input[name$="tds_csrf"]').val()},
            async: false,
            success: function(data) {
                $("#dialog_gallery_arrange").dialog( "open" );
                   $("#btn_close").on("click", function(){
                        $("#dialog_gallery_arrange").dialog( "close" );
                        hideDialog();
                });
            },
            error: function() {
                alert("Unknown error occur");
            }
        });
    });
    
    $(document).on("click", ".close_video", function(){
        $("#play_div").html("");
        $("#play_div").hide();
    });
    
    $(document).on("click","#play", function(){
        var urlPattern = /(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;:/~\+#]*[\w\-\@?^=%&amp;/~\+#])?/;
        if ( ! $("#video_url").val().match(urlPattern) )
        {
            alert("Not a valid URL");
            return;
        }
        $.ajax({
            type: 'POST',
            url: $("#base_url").val() + "/admin/gallery/check_video",
            data: {video_url : $("#video_url").val(),tds_csrf: $('input[name$="tds_csrf"]').val()},
            async: false,
            success: function(data) {
                if ( data == "exists" )
                {
                    var video_url = getEmbeddedPlayer($("#video_url").val(), 270, 425);
                    $("#play_div").html('<div class="close_video" title="Close"></div>\n\n' + video_url);
                    $("#play_div").show();
                }
                else if ( data == "not_supported" )
                {
                    alert("Un Supported video");
                }
                else
                {
                    alert("video not exists");
                }
            },
            error: function() {
                alert("Unknown error occur");
            }
        });
    });
    
    $(document).on("click","#save_video", function(){
        var urlPattern = /(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;:/~\+#]*[\w\-\@?^=%&amp;/~\+#])?/;
        if ( ! $("#video_url").val().match(urlPattern) )
        {
            alert("Not a valid URL");
            return;
        }
        $.ajax({
            type: 'POST',
            url: $("#base_url").val() + "/admin/gallery/add_video_data",
            data: {video_url : $("#video_url").val(), gallery_id: $("#gallery_id").val(),tds_csrf: $('input[name$="tds_csrf"]').val()},
            async: false,
            success: function(data) {
                if ( data == "saved" )
                {
                    $("#dialog_gallery_arrange").dialog( "open" );
                        $("#btn_close").on("click", function(){
                             $("#dialog_gallery_arrange").dialog( "close" );
                             hideDialog();
                             //alert($(".current").html());
                    });
                }
                else if ( data == "not_supported" )
                {
                    alert("Un Supported video");
                }
                else
                {
                    alert("video not exists");
                }
            },
            error: function() {
                alert("Unknown error occur");
            }
        });
    });

});

function getEmbeddedPlayer(url, height, width){
	var output = '';
	var youtubeUrl = url.match(/watch\?v=([a-zA-Z0-9\-_]+)/);
	var vimeoUrl = url.match(/^http:\/\/(www\.)?vimeo\.com\/(clip\:)?(\d+).*$/);
	if( youtubeUrl )
        {
		output = '<iframe src="http://www.youtube.com/embed/'+youtubeUrl[1]+'?rel=0&wmode=transparent" height="' + height + '" width="' + width + '" allowfullscreen="" frameborder="0"></iframe>'
        }
        else if( vimeoUrl )
        {
            console.log(vimeoUrl);
            output = '<iframe src="//player.vimeo.com/video/' + vimeoUrl[3] + '" width="' + width + '" height="' + height + '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
        }
        return output;
}

var hideDialog = function() {
    unshadow();
    parent.document.getElementById("dialog").style.display = "none";
    parent.document.getElementById("dialog").innerHTML = "";
};

var unshadow = function() {
    if ( $("#main_shadow").length > 0 )
    {
         parent.document.getElementById("main_shadow").style.display = "none";
    }
    if ( $("#shadow").length > 0 )
         parent.document.getElementById("shadow").style.display = "none";
};

