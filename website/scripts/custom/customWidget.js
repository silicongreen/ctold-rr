var tab_created = false;
$(document).ready(function(){
    $(document).on('change','#tree input:checkbox', function(){
        $('#tree').find('input:checkbox').prop('checked', false);
        $(this).prop('checked',true);
    });
    
    $(document).on('change','.tab_tree input:checkbox', function(){
        var parent_id = $( this ).parents("div[id^='tab-']").attr('id');
        var tab_no = parent_id.replace("tab-", "");
        $('#' + parent_id + ' .tab_tree').find('input:checkbox').prop('checked', false);
        $(this).prop('checked',true);
        
        var is_checked = $('#' + parent_id + ' #tab_title_from_category').prop("checked");
        if ( is_checked )
        {
            var checked_id = "";
            var tab_name = "";
            $('#' + parent_id + ' .tab_tree').find('input:checkbox').each(function(){
                if ( this.checked )
                {
                    checked_id = this.id.replace("tab_","");
                    tab_name = $('label[for="' + checked_id + '"]').html();
                    
                }
            });
            $("#taba-" + tab_no).html(tab_name.ucfirst());
            $("#" + parent_id + " #tab_name").val(tab_name.ucfirst());
        }
    });
    
    $(document).on('change','select[name="type"]', function(){
        var val = this.value;
        $(".field").hide();
        if ( val == 'news' )
        {
            $("#tree_all_category").hide();
            $(".category_tree_main").show();
            $(".news_count").show();
        }
        else if ( val == 'tab' )
        {
            $(".tab_count").show();
        }
    });
    
    $(document).on('blur','input[name="tab_count"]', function(){
        var val = this.value;
        if ( parseInt(val) > 0 && parseInt(val) < 4 )
        {
             if ( tab_created )
             {
                 $( "#tab" ).tabs("destroy");
                 $( "#tab" ).html("");
                 tab_created = false;
             }
             
             var ul = document.createElement("UL");
             var j = 1;
             for( var i=0; i<parseInt(val); i++ )
             {
                 var li = document.createElement("LI");
                 
                 var a = document.createElement("A");
                 a.setAttribute("href", "#tab-" + j);
                 a.setAttribute("class", "tab_titles");
                 a.setAttribute("id", "taba-" + j);
                 var tab_title = document.createTextNode("Tab " + j );
                 a.appendChild(tab_title);
                 li.appendChild(a);
                 ul.appendChild(li);
                 j++;
             }
             $("#tab").append(ul);
             
             
             j = 1;
             for( var i=0; i<parseInt(val); i++ )
             {
                 var div = document.createElement("DIV");
                 
                 div.setAttribute("id", "tab-" + j);
                 $("#tab").append(div);
                 $("#tab-" + j).html($(".tab_data").html());
                 j++;
             }
             $( "#tab" ).tabs();
             
             tab_functions();
             
             $(".widget_tab").show();
             tab_created = true;
        }
        else if ( parseInt(val) > 0 && parseInt(val) > 3 )
        {
            if ( tab_created )
            {
                 $( "#tab" ).tabs("destroy");
                 $( "#tab" ).html("");
                 $(".widget_tab").hide();
                 tab_created = false;
            }
            alert("You can created Maximum 3 Tab content");
        }
        else
        {
            if ( tab_created )
            {
                 $( "#tab" ).tabs("destroy");
                 $( "#tab" ).html("");
                 $(".widget_tab").hide();
                 tab_created = false;
            }
            alert("Please enter valid Tab count");
        }
    });
})

var tab_functions = function(){
    $(document).on('change','select[name="tab_type"]', function(){
        var parent_id = $( this ).parents("div[id^='tab-']").attr("id");
        var tab_no = parent_id.replace("tab-", "");
        $("#taba-" + tab_no).html("Tab " + tab_no);
        $("#" + parent_id + " #tab_name").val("");
        var val = this.value;
        $("#" + parent_id + " .tab_fields").hide();
        $("#" + parent_id + " #tab_title_from_type").hide();
        $("#" + parent_id + " #tab_title_from_type_span").hide();
        $("#" + parent_id + " #tab_title_from_category").hide();
        $("#" + parent_id + " #tab_title_from_category_span").hide();
        $("#" + parent_id + " #tab_title_from_cartoon").hide();
        $("#" + parent_id + " #tab_title_from_cartoon_span").hide();
        
        if ( val == 'news' )
        {
            $("#" + parent_id + " #tab_title_from_category").show();
            $("#" + parent_id + " #tab_title_from_category_span").show();
            $("#" + parent_id + " #tab_all_category").hide();
            $("#" + parent_id + " .tab_category").show();
            $("#" + parent_id + " .tab_news_count_fields").show();
        }
        else if ( val == 'text' )
        {
            $("#" + parent_id + " .tab_text_fields").show();
        }
        else if ( val == 'more' )
        {
            $("#" + parent_id + " #tab_title_from_type").show();
            $("#" + parent_id + " #tab_title_from_type_span").show();
            $("#" + parent_id + " #tab_all_category").show();
            $("#" + parent_id + " .tab_category").show();
            $("#" + parent_id + " .tab_news_count_fields").show();
            
            if ( $("#" + parent_id + " #tab_title_from_type").prop("checked") )
            {
                 $("#taba-" + tab_no).html(val.replace("_", " ").ucwords());
                 $("#" + parent_id + " #tab_name").val(val.replace("_", " ").ucwords());
            }
        }
        else if ( val == 'most_viewed' )
        {
            $("#" + parent_id + " #tab_title_from_type").show();
            $("#" + parent_id + " #tab_title_from_type_span").show();
            $("#" + parent_id + " #tab_all_category").show();
            $("#" + parent_id + " .tab_category").show();
            $("#" + parent_id + " .tab_news_count_fields").show();
            if ( $("#" + parent_id + " #tab_title_from_type").prop("checked") )
            {
                 $("#taba-" + tab_no).html(val.replace("_", " ").ucwords());
                 $("#" + parent_id + " #tab_name").val(val.replace("_", " ").ucwords());
            }
        }
        else if ( val == 'most_discussed' )
        {
            $("#" + parent_id + " #tab_title_from_type").show();
            $("#" + parent_id + " #tab_title_from_type_span").show();
            $("#" + parent_id + " #tab_all_category").show();
            $("#" + parent_id + " .tab_category").show();
            $("#" + parent_id + " .tab_news_count_fields").show();
            if ( $("#" + parent_id + " #tab_title_from_type").prop("checked") )
            {
                 $("#taba-" + tab_no).html(val.replace("_", " ").ucwords());
                 $("#" + parent_id + " #tab_name").val(val.replace("_", " ").ucwords());
            }
        }
        else if ( val == 'cartoon' )
        {
            $("#" + parent_id + " #tab_title_from_cartoon").show();
            $("#" + parent_id + " #tab_title_from_cartoon_span").show();
            $("#" + parent_id + " .tab_cartoon").show();
        }
    });
    
    $(document).on('change','input[name^="tab_title_from_"]', function(){
        var parent_id = $( this ).parents("div[id^='tab-']").attr("id");
        var tab_no = parent_id.replace("tab-", "");
        var id = this.id;
        
        if ( $(this).prop("checked") )
        {
            if ( id.indexOf("type") != -1 )
            {
                var val = $('#' + parent_id + ' select[name="tab_type"]').val().replace("_", " ").ucwords();
                $("#taba-" + tab_no).html(val);
                $("#" + parent_id + " #tab_name").val(val);
            }
            else if ( id.indexOf("category") != -1 )
            {
                var checked_id = "";
                var tab_name = "";
                $('#' + parent_id + ' .tab_tree').find('input:checkbox').each(function(){
                    if ( this.checked )
                    {
                        checked_id = this.id.replace("tab_","");
                        tab_name = $('label[for="' + checked_id + '"]').html();

                    }
                });
                $("#taba-" + tab_no).html(tab_name.ucfirst());
                $("#" + parent_id + " #tab_name").val(tab_name.ucfirst());
            }
            else if ( id.indexOf("cartoon") != -1 )
            {
                if ( $.trim($('#' + parent_id + ' #cartoon_gallery').val()) > 0 )
                {
                    $("#taba-" + tab_no).html($('#' + parent_id + ' #cartoon_gallery option[value="' + $('#' + parent_id + ' #cartoon_gallery').val() + '"]').html().ucfirst());
                    $("#" + parent_id + " #tab_name").val($('#' + parent_id + ' #cartoon_gallery option[value="' + $('#' + parent_id + ' #cartoon_gallery').val() + '"]').html().ucfirst());
                }
            }
        }
        else
        {
            $("#taba-" + tab_no).html("Tab " + tab_no);
            $("#" + parent_id + " #tab_name").val("");
        }
    });
    
    $(document).on('change','select[name="cartoon_gallery"]', function(){
        var parent_id = $( this ).parents("div[id^='tab-']").attr("id");
        var tab_no = parent_id.replace("tab-", "");
        if ( $("#" + parent_id + " #tab_title_from_cartoon").prop("checked") )
        {
            if ( $.trim(this.value) > 0 )
            {
                $("#" + parent_id).val($('#' + parent_id + ' #cartoon_gallery option[value="' + this.value + '"]').html().ucfirst());    
                $("#" + parent_id + " #tab_name").val($('#' + parent_id + ' #cartoon_gallery option[value="' + this.value + '"]').html().ucfirst());
            }
        }
    });
 
}

String.prototype.ucwords = function () {
    var val = this.valueOf();
    return (val + '').replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function ($1) {
        return $1.toUpperCase();
    });
};

String.prototype.ucfirst = function () {
    var val = this.valueOf();
    val += '';
    var f = val.charAt(0).toUpperCase();
    return f + val.substr(1);
};