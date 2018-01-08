/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var img_height_to_deduct = 0;
String.prototype.findIn = function (multi) {
    multi = multi || '';
    var val = this.valueOf();
    if(typeof multi == 'object' || typeof multi == 'array')
    {
        if(val in multi)
        {
            return multi[val];
        }
        else
        {
            for(var x in multi)
            {
                var found = this.findIn(multi[x]);
                if(found != false)
                {
                    return found;
                }
            }
        }
    }
    return false;
};

String.prototype.in_array = function (haystack, argStrict) 
{
    var needle = this.valueOf();
    var key = '',
    strict = !! argStrict;

  if (strict) {
    for (key in haystack) {
      if (haystack[key] === needle) {
        return true;
      }
    }
  } else {
    for (key in haystack) {
      if (haystack[key] == needle) {
        return true;
      }
    }
  }

  return false;
}


$(document).ready(function(){
    $(".lazy-load").lazyload({
        effect : "fadeIn",
        skip_invisible: false
    });
    $(".lazy-load-ad").lazyload({
        event : "add_lazy"
    });
    
    
    $(".imgLiquidFill").imgLiquid({
        fill:true,
        verticalAlign: 'top'
    });
    
    if ( $(window).scrollTop() > 77 )
    {
        if ( $(".navigation_fixed").length == 0  )
        {
            $(".navigation").addClass("navigation_fixed");
            $(".navigation").removeClass("navigation");
        }
    }
    else
    {
        if ( $(".navigation").length == 0  )
        {
            $(".navigation_fixed").addClass("navigation");
            $(".navigation_fixed").removeClass("navigation_fixed");
        }
    }
    
    $(window).on("scroll",function(){
        if ( $(window).scrollTop() > 77 )
        {
            if ( $(".navigation_fixed").length == 0  )
            {
                $(".navigation").addClass("navigation_fixed");
                $(".navigation").removeClass("navigation");
            }
        }
        else
        {
            if ( $(".navigation").length == 0  )
            {
                $(".navigation_fixed").addClass("navigation");
                $(".navigation_fixed").removeClass("navigation_fixed");
            }
        }
    });
    
    $(document).on("click",'.toolbar',function(){
        if ( $(this).parent().find("h1").children("a").attr("href") != undefined )
        {
            location.href = $(this).parent().find("h1").children("a").attr("href");
        }
        
        else if ( $(this).parent().find("h2").children("a").attr("href") != undefined )
        {
            location.href = $(this).parent().find("h2").children("a").attr("href");
        }
        else if ( $(this).parent().parent().find("h2").children("a").attr("href") != undefined )
        {
            location.href = $(this).parent().parent().find("h2").children("a").attr("href");
        }
        else if ( $(this).parent().parent().find("h1").children("a").attr("href") != undefined )
        {
            location.href = $(this).parent().parent().find("h1").children("a").attr("href");
        }
        else
        {
            
        var alt = $(this).attr("alt");
        var src = $(this).attr("src");
        src = src.replace("main/","");
        src = src.replace("carrousel/","");
        src = src.replace("thumbs/","");
        src = src.replace("otherRightFirst/","");
        src = src.replace("otherSixBottom/","");
        src = src.replace("weekly/","");
        src = src.replace("magazineHome/","");
        src = src.replace("magazine/","");
        $.fancybox(src,{
           // API options
           padding : 10,
           title : alt,
           helpers: {
               title : {
                 type : 'over'
               }
             },
            afterShow : function() {
               $(".fancybox-title").hide();
               var imageWidth = $(".fancybox-image").width();
               $(".fancybox-title-over-wrap").css({
                   "width": imageWidth - 5,
                   "paddingLeft": 5,
                   "paddingRight": 0,
                   "textAlign": "center"
               });
               $(".fancybox-wrap").hover(function() {
                 $(".fancybox-title").stop(true,true).slideDown(200);
                }, function() {
                 $(".fancybox-title").stop(true,true).slideUp(200);
               });
              },
           openEffect: 'elastic',
           openSpeed : 900
       });
        }
    });
    
    $(document).on("mouseenter",".imgLiquidFill",function(){
        $(this).find(".tools-gallery").slideDown('slow');
        $(this).find(".caption_img").slideDown('slow');
    });
    
    $(document).on("mouseleave",".imgLiquidFill",function(){
        $(this).find(".tools-gallery").slideUp('slow');
        $(this).find(".caption_img").slideUp('slow');
    });
    
    //Get Title Height
    var div_class = "";
    var child_class_height = 0;
    var shoulder_height = 0;
    var headline_height = 0;
    var subhead_height = 0;
    var image_height = 0;
    
    
    //Get The total Height
    var height_array = {
                        'carrosel-news'                 : 390,
                        'main_news_div'                 : 400,
                        'main-news-overlay'             : 150,
                        'cat-topics'                    : 240,
                        'other-topics-part-one'         : 250, 
                        'other-topics-part-two'         : 160,
                        'topics_content'                : 270,
                        'inner-main-story'              : 544,
                        'inner-topics'                  : 540,
                        'inner-other-topics-container'  : 1000,
                        'more_contain'                  : 280
    };
    
    var child_classes = {
                        'carrosel-news'                 : "shoulder,headline,subhead,news-images",
                        'main_news_div'                 : "headline",
                        'main-news-overlay'             : "headline",
                        'cat-topics'                    : "title",
                        'other-topics-part-one'         : "title",
                        'other-topics-part-two'         : "title,othernews-images",
                        'topics_content'                : "title,othernews-images",
                        'inner-main-story'              : "inner-top-h1,imgLiquidFill",
                        'inner-topics'                  : "inner-topics-h1",
                        'inner-other-topics-container'  : "other-shoulder,other-headline",
                        'more_contain'                  : "other-shoulder,other-headline"
    };
    var dec_height = {
                        'carrosel-news'                 : "0,0",
                        'main_news_div'                 : "0,30",
                        'main-news-overlay'             : "0,70",
                        'cat-topics'                    : "-10,0",
                        'other-topics-part-one'         : "-10,0",
                        'other-topics-part-two'         : "-10,0",
                        'topics_content'                : "-10,0",
                        'inner-main-story'              : "0,30",
                        'inner-topics'                  : "5,30",
                        'inner-other-topics-container'  : "0,20",
                        'more_contain'                  : "0,80"
    };
    
    $(".contents-news").each(function(){
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
        $("." + div_class + " img").addClass('toolbar');
        $("." + div_class + " img").css('cursor','pointer');
        $(".play_icon").removeClass("toolbar");
    });
    
    $(".news").each(function(){
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
        $("." + div_class + " img").addClass('toolbar');
        $("." + div_class + " img").css('cursor','pointer');
        $(".play_icon").removeClass("toolbar");
    });
    
    $(".contents-news").each(function(){
        
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
            }

            var ar_dec = dec.split(",");
            
            var height = (div_height / cnt) - (child_class_height - ar_dec[0]);
            
            
            if ( found )
            {
                height = (div_height / cnt);
                $(this).parent().css('height', ( height - 25) + 'px');
            }
            height = (div_height / cnt) - (child_class_height - ar_dec[0]);
            if ( height < 20  )
            {
                if ( height < 0 )
                {
                    height = 0;
                }

                $(".imgLiquidFill", $(this).parent()).height($(".imgLiquidFill", $(this).parent()).height() - 45);
                height = height + 45;
            }
            
            
            $(this).height((height < 0 ) ? 0 : (height - ar_dec[1]));
        }
    });
    
    //Get The total Height
    var max_height_array = {
                        'carrosel-news'                 : 370,
                        'main_news_div'                 : 400,
                        'main-news-overlay'             : 150,
                        'cat-topics'                    : 240,
                        'other-topics-part-one'         : 250, 
                        'other-topics-part-two'         : 160,
                        'topics_content'                : 270,
                        'inner-main-story'              : 544,
                        'inner-topics'                  : 540,
                        'inner-other-topics-container'  : 1000,
                        'more_contain'                  : 280
    };
    
    var best_height = 0;
    $(".contents-news").each(function(){
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
        
        var height = $("." + div_class).parent().outerHeight();
        if ( $(this).parent().css('float') == 'left' || $(this).parent().css('float') == 'right' )
        {
           
             $(this).parent().css("height", $(this).parent().parent().outerHeight());
             //$(this).css("height", $(this).parent().outerHeight());
        }
        if ($("#" + div_class + "_count").length == 0)
        {
            if ( $(this).parent().outerHeight() > 0 )
                 max_height_array[div_class] = $(this).parent().outerHeight();
        }
        else if ($("#" + div_class + "_count").length != 0 && $("#" + div_class + "_count").val() == 1)
        {
            max_height_array[div_class] = $(this).parent().outerHeight();    
        }
        
        
            //
       // $("." + div_class).css("border","1px solid #f00000");
    });
    
    //$( ".other-topics-part-three .other-topics-part-two").css("height", $(".other-topics-part-three").height());
    
    $(".cap").each(function(){
        var div_height = 0;
        if ( $(this).parent().parent().children().last().attr('class') == "contents-news" )
        {
            $(this).parent().parent().children("img").first().attr("alt",this.innerHTML);
        }
        else if ( $(this).parent().parent().parent().children().last().attr('class') == "contents-news" )
        {
            $(this).parent().parent().parent().children("img").first().attr("alt",this.innerHTML);
        }
    });
    
    $(".contents-news").each(function(){
        
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
                div_height = ar_div_class[i].findIn(max_height_array);
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
            div_height = div_class.findIn(max_height_array);
            child_cls = div_class.findIn(child_classes);
            dec = div_class.findIn(dec_height);
        }
        if ( $(this).parent().parent().css('display') == "none" )
             $(this).parent().parent().show();
        if ( $(this).parent().css('display') == "none" )
            $(this).parent().show();
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
            }
            
            if ( div_class == "topics_content" )
            {
                 var topics_height = $(this).parent().parent().parent().outerHeight() - 21;
//                alert($(".other-news").outerHeight());
//                alert($(".other-topics").outerHeight());
//                alert(topics_height);
//alert($(".other-topics").outerHeight());
          
                div_height = topics_height / 2;
                
              
                //$(this).parent().css("border","1px solid #f00");
            }
            
            var ar_dec = dec.split(",");
//            if ( $(".tds_my_sub_cat_title").length > 0 )
//            {
//                 div_height += $(".tds_my_sub_cat_title").outerHeight() + 80;   
//            }
            var height = (div_height / cnt) - (child_class_height - ar_dec[0]);
            if ( found )
            {
                height = (div_height / cnt);
                if ( $(this).parent().attr('class') == "inner-main-story" )
                {
                    $(this).parent().css('height', ( height - 35) + 'px');
                }
                else
                {
                    $(this).parent().css('height', ( height - 25) + 'px');
                }
            }
            height = (div_height / cnt) - (child_class_height - ar_dec[0]);
           
            if ( height < 20  )
            {
                if ( height < 0 )
                {
                    height = 0;
                }

                $(".imgLiquidFill", $(this).parent()).height($(".imgLiquidFill", $(this).parent()).height() - 45);
                height = height + 45;
            }
            
            if ( $(this).parent().parent().parent().attr("id") == "inner-top-common-topics" )
            {
                var right_height = $("#cover_image").outerHeight();
                height = right_height;
                var shoulder_height = $("#inner-top-common-topics .other-shoulder").outerHeight();
                var headline_height = $("#inner-top-common-topics .other-headline").outerHeight();
                var image_height    = $("#inner-top-common-topics .common_images").outerHeight();
                $(this).height(right_height - (  shoulder_height + headline_height + image_height + 10 ));
            }
            else
            {
                var ar_skip_class = ['other-topics-part-one', 'cat-topics','topics_content'];
                if ( ! div_class.in_array( ar_skip_class ) )
                {
                    if ( $(this).parent().children("img").length == 1 )
                    {
                        if ( $(this).parent().children("img").hasClass("floatRight") || $(this).parent().children("img").hasClass("floatLeft") )
                        {
                            var img_height = $(this).parent().children("img").outerHeight();
                            height = img_height + (height - child_class_height);
                        }
                        
                    }
                }
                else if ( div_class == "topics_content" )
                {
                    if ( $(this).parent().children("img").length == 1 )
                    {
                        var img_height = $(this).parent().children("img").outerHeight();
                        height = img_height + child_class_height + 50;
                    }
                }
                else
                {
                    if ( $(this).parent().children("img").length == 1 )
                    {
                        $(this).parent().children("img").css("margin-bottom","5px");
                        var img_height = $(this).parent().children("img").outerHeight();
                        if ( height > img_height )
                        {
                            height = img_height + child_class_height + 40;
                        }
                        
                    }
                }
                 //if (  div_class == "other-topics-part-one" )
                //alert(height + "  "  + child_class_height);
                $(this).height((height < 0 ) ? 0 : (parseInt(height) - ( parseInt(ar_dec[1]) + parseInt(10))));
            }
            
            $(this).height( $(this).height( ) - 5 );
            
            //I have the height, let set the actual height for the div
            
            if ( div_class == "main-news-overlay" )
                $(this).parent().css("display","block");            
            
            var ar_excludes_div_class = ['cat-topics','inner-other-topics-container'];
            
            var excludes_div_class = ['carrosel-news','main-news-overlay', 'main_news_div'];
            
            if ( ! div_class.in_array( excludes_div_class ) )
            {
                $("." + div_class).css("position","relative");
                $(this).after($("#bottom-bar").html());
            }
            //$("." + div_class + " .news-bottom-bar").css("width",$("." + div_class).css("width"));
            if ( ! div_class.in_array( ar_excludes_div_class ) )
            {
                $(this).show();
                $(this).dotdotdot({
                    after: 'a.more',
                    height : $(this).height() - 5
                });
                
                //$(this).after('<div style="background: #fff; bottom: 0; position: absolute; width: 100%; height: 10px; font-size:10px; font-family: vardana;">More Print</div>');
            }

            if ( div_class == "main-news-overlay" )
                $(this).parent().css("display","none");   
        }
    });
   
    $(".contents-news").removeClass("overflow");
    
    $(".news").each(function(){
        
        child_class_height = 0;
        div_class = $(this).parent().attr('class');
        
        var div_height = 0;
        if ( div_class.indexOf(" ") != -1 )
        {
            var ar_div_class = div_class.split(" ");    
            for( var i=0; i<ar_div_class.length; i++ )
            {
                div_height = ar_div_class[i].findIn(max_height_array);
                if ( div_height != false )
                {
                    div_class = ar_div_class[i];
                    break;
                }
            }
        }
        
        var excludes_div_class = ['carrosel-news','main-news-overlay', 'main_news_div','topics-main'];
        if ( ! div_class.in_array( excludes_div_class ) )
        {
            $("." + div_class).css("position","relative");
            $(this).after($("#bottom-bar").html());
        }
    });
    
    $(".print_online").each(function(){
        
        if ( $(this).parent().parent().children().last().attr('class') == "news-bottom-bar" )
        {
            
            if ( $(this).parent().parent().attr("class") != "carrosel-news" && $(this).parent().parent().attr("class") != "main-news-overlay" && $(this).parent().parent().attr("class") != "topics_main main_news_div")
            {
               
                $(this).parent().parent().children().last().prepend(this.innerHTML);
            }
        }
        else if ( $(this).parent().parent().parent().children().last().attr('class') == "news-bottom-bar" )
        {
            
            $(this).parent().parent().parent().children().last().prepend(this.innerHTML);
        }
        $(this).html("");
    });
    
    if ( $("#cover_image").length == 1 )
    {
        var left_height = $("#inner-top-common-topics").outerHeight();
        var right_height = $("#cover_image").outerHeight();
        $("#inner-top-common-topics .news-bottom-bar").css("left","-6px");
        $("#inner-top-common-topics .news-bottom-bar").css("bottom","-6px");
        $("#cover_image").css("background","#fff");
        if ( left_height > right_height )
        {
            $("#cover_image").css("height",left_height - 21);
        }
        else if ( left_height < right_height )
        {
            $("#inner-top-common-topics .more_container").css("border-bottom","none");
            var dif = (right_height - left_height) + 7;
            $("#inner-top-common-topics").css("height",right_height - 11);
            $("#cover_image").css("background","#fff");
            $("#inner-top-common-topics .news-bottom-bar").css("bottom","-" + dif + "px");
        }
    }
    
    var timeComment;
    if ( $("#zero_comment_show").length == 1  )
    {
        timeComment = setInterval(function comment_track()
        {
            $(".comment_count").each(function(){
                if ( $(this).html() == "Comments" )
                {
                    return ;
                }
                else
                {
                    var dt = $(this).html();
                    var ar_dt = dt.split(" ");
                    if ( ar_dt.length > 1 )
                    {
                        clearInterval(timeComment);
                        $(".comment_count").each(function()
                        {
                            var dt = $(this).html();
                            var ar_dt = dt.split(" ");
                            if ( parseInt(ar_dt[0]) == 0 && $("#zero_comment_show").val() == "1" )
                            {
                                $(this).show();
                            }
                            else if ( parseInt(ar_dt[0]) > 0 && $("#zero_comment_show").val() == "0" )
                            {
                                $(this).show();
                            }
                        });
                        return ;
                    }
                }
            });
        }, 500);
    }
    
    var twitter_check = setInterval(function comment_track(){
        var obj = $($('#twitter-widget-0').contents().find('body #twitter-widget-0 .timeline-header'));
        var html = $($('#twitter-widget-0').contents().find('body #twitter-widget-0 .timeline-header')).html();
        if ( $.trim(html).length > 0 )
        {
            obj.css('background','url("' + $("#base_url").val() + '/styles/layouts/tdsfront/images/h-bg.gif") repeat-x scroll 0 0 rgba(0, 0, 0, 0)');
            obj.css('padding','8.5px');
            $($('#twitter-widget-0').contents().find('body #twitter-widget-0 .timeline-header iframe')).css("margin-top","-4px");
            clearInterval(twitter_check);
        }
    }, 500);
    
    
    $("#nav").outerWidth($(".container").outerWidth());
    
    var width = 0;
    var pos = 0;
    
//    var logoLoad = setInterval(function(){
//        $("#logo").load( function(){
//            clearInterval(logoLoad);
//            $(".top-header-nav li").each(function(){
//                if ( pos == 2 )
//                {
//                    var main_width = $(".top-header-nav").outerWidth();
//                    $(this).width( main_width - ( width + 30) );    
//                }
//                else
//                {
//                    width += $(this).outerWidth();
//                }
//                pos++;
//            });
//            $(".adds-header img").width(($(".adds-header").parent().outerWidth() / 2) - 5 );
//            $(".adds-header").show();
//        });    
//    }, 1000);
    
    
    var timeout = setTimeout(function() {
        $("img.lazy-load-ad").trigger("add_lazy")
    }, 500);
    
    
    
});
