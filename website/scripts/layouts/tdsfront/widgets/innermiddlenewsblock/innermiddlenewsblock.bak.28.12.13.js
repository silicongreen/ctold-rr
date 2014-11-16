$(function(){
    
    if($('#innermiddlenewsblock').length>0)
    { 
        $.get($("#base_url").val()+'front/ajax/get_innermiddle_news_block/'+$('#ci_key').val(), '', function(data, status){
       
            $('#innermiddlenewsblock').html(data);
            setTimeout(rearrange_size, 5000);
            $("#loader_image_middlecontent").hide();
        });     
        $(document).on("mouseenter", "#innermiddlenewsblock .ym-grid", function()
        { 
            if($(this).find(".caption").length>0)
                $(this).find(".caption").slideDown('slow');
        });
     
        $(document).on("mouseleave", "#innermiddlenewsblock .ym-grid", function()
        { 
            if($(this).find(".caption").length>0)
                $(this).find(".caption").slideUp('slow');       
    
        });
    }
});

function rearrange_size()
{
    var right_height = $('#innermiddlenewsblock').outerHeight();
    var left_height  = $('.inner-other-topics').outerHeight();
    size = right_height;
    if ( $(".whatson").length > 0 )
    {
        size += 410;
    }
    var count_header = 0;
    var count_height = 0;
    $(".inner-other-topics .tds_my_sub_cat_title").each(function(){
        count_header++;
        count_height += $(this).outerHeight() - 0.5;
    });
    
    var height_array = {
        'inner-other-topics-container': (size + 100) - ( 16 + count_height)
    };
    
    var child_classes = {
        'inner-other-topics-container'  : "other-shoulder,other-headline"
    };
    var dec_height = {
        'inner-other-topics-container'  : "0,0"
    };
    
    var div_class = "";
    var child_class_height = 0;
    var shoulder_height = 0;
    var headline_height = 0;
    var subhead_height = 0;
    var image_height = 0;
    $(".inner-other-topics .contents-news").each(function(){
        
        child_class_height = 0;
        div_class = $(this).parent().attr('class');
        
        var found = false;
        var div_height = 0;
        var child_cls = "";
        var dec = "";
        if ( div_class.indexOf(" ") != -1 )
        {
            var ar_div_class = div_class.split(" ");    
            for( var i=0; i<ar_div_class.length; i++ )
            {
                div_height = ar_div_class[i].findIn(height_array);
                child_cls = ar_div_class[i].findIn(child_classes);
                dec = ar_div_class[i].findIn(dec_height);
                if ( div_height != false )
                {
                    div_class = ar_div_class[i];
                    break;
                }
            }
        }
        else
        {
            div_height = div_class.findIn(height_array);
            child_cls = div_class.findIn(child_classes);
            dec = div_class.findIn(dec_height);
        }
        
        if (child_cls != false)
        {
            var ar_child_classes = child_cls.split(",");
            for( var i=0; i<ar_child_classes.length; i++ )
            {
                if ( $("." + ar_child_classes[i], $(this).parent()).css("float") == "none"  )
                    child_class_height += $("." + ar_child_classes[i], $(this).parent()).outerHeight();
            }
            var cnt = 1;


            if ($("#" + div_class + "_count").length != 0 && $("#" + div_class + "_count").val() > 1)
            {
                found = true;
                var cnt = $("#" + div_class + "_count").val();
                cnt = cnt - count_header;
            }
            
            var ar_dec = dec.split(",");
            var height = (div_height / cnt) - (child_class_height - ar_dec[0]);
            
            if ( cnt == 1 )
            {
                div_height += 4;
            }
            
            //alert($("." + div_class + " .middle-images").length);
            if ( $(".middle-images").length == 1 )
            {
                var image_height = $(".middle-images").outerHeight();
            //  alert(image_height);
            }
            
            height = (div_height / cnt);
            if ( $(this).parent().children("img").length == 1 )
            {
                if ( $(this).parent().children("img").hasClass("floatRight") || $(this).parent().children("img").hasClass("floatLeft") )
                {
                    var img_height = $(this).parent().children("img").outerHeight();
                    height = img_height + (height - child_class_height);
                }

            }
            
            $(this).parent().height(height - 36);
            
            //            alert(height + "  " + div_height);
            height = height - (child_class_height - ar_dec[0]);
            
            if ( height < 20  )
            {
                if ( height < 0 )
                {
                    height = 0;
                }

                $(".imgLiquidFill", $(this).parent()).height($(".imgLiquidFill", $(this).parent()).height() - 45);
                height = height + 45;
            }
            $(this).height((height < 0 ) ? 0 : (parseInt(height) - ( parseInt(ar_dec[1]) + parseInt(20))));
            
            
            $(this).height( $(this).height( ) - 5 );
            //I have the height, let set the actual height for the div
            
            var ar_excludes_div_class = ['inner-other-topics-container'];
            
            if ( div_class.in_array( ar_excludes_div_class ) )
            {    
                $(this).show();
                $(this).dotdotdot({
                    after: 'a.more',
                    height : $(this).height() - 15
                });
            }
        }
    });
    
    //OOPS Left Height Now can be increased, lets update the right height now based on left height
    var right_height = $('#innermiddlenewsblock').outerHeight();
    var left_height  = $('.inner-other-topics').outerHeight();
    if ( left_height > right_height )
    {
        //Take the Difference
        var dif_height = left_height - (right_height + 5);
        if ( $(".whatson").length > 0 )
        {
            dif_height = dif_height - 410;
        }
        //Take the last child for the right panel, NOT THE WHATS ON DIV
        var last_child = $('#innermiddlenewsblock').children().last().css("height", $('#innermiddlenewsblock').children().last().outerHeight() + dif_height);
    }
}