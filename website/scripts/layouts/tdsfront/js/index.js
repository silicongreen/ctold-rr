/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


var bDivShow = false; 
$(document).ready(function(){
 
   $(".image_div_main").each(function(){
       $(this).css("width",$(this).find("img").width());
   });
    
    
    $(document).on("mouseenter",".image_div_main",function(event)
    {
        $(this).css("width",$(this).find("img").width());
        $(this).find(".tools-gallery").slideDown('slow');
        $(this).find(".caption_img").slideDown('slow');
        event.preventDefault();
        return false;
    });
    
    $(document).on("mouseleave",".image_div_main",function()
    {
        $(this).find(".tools-gallery").slideUp('slow');
        $(this).find(".caption_img").slideUp('slow');
       
        event.preventDefault();
        return false;
    });
   
    $(".editorial-list li").each(function() {
        var i = $(this).index();
        i++;
        $(this).find(".number").html(i);

    });
    

    $(".cartoon-content > div").hide();
    $(".cartoon-content > div.ctab-0").show();
       
    $(document).on('click','#cartoon-list li',function(){
        $("#cartoon-list li").removeClass('active');
        $(this).addClass('active');
        $(".cartoon-content > div").hide();
        $(".ctab-"+$(this).index()).stop(true, true).animate({
            opacity: "show"
        }, "slow");
              
              
    }); 
    

    
    var ci_key = $("#ci_key_main").val(); 
    
   
    var loader2=$('#loader3');
    if( $(".bxslider3").length>0 )
    {
        $('.bxslider3').bxSlider({
               auto: false,
               minSlides: 1,
               maxSlides: 1,
               slideWidth: 600,
               slideMargin: 20,
               controls: true,
               onSliderLoad: function(){
                   $('.bx-controls-direction').hide();
                   $('.bx-wrapper').hover(
                       function(){$('.bx-controls-direction').fadeIn(300);},
                       function(){$('.bx-controls-direction').fadeOut(300);}
                   );
               }
           });
    }
    
    if( $(".bxslider4").length>0 )
    {
        $('.bxslider4').bxSlider({
               auto: false,
               minSlides: 1,
               maxSlides: 1,
               slideWidth: 600,
               slideMargin: 20,
               controls: true,
               onSliderLoad: function(){
                   $('.bx-controls-direction').hide();
                   $('.bx-wrapper').hover(
                       function(){$('.bx-controls-direction').fadeIn(300);},
                       function(){$('.bx-controls-direction').fadeOut(300);}
                   );
               }
           });
    }
   
    if( $(".editorial .content .etab-0").length>0 && $(".editorial .content .etab-0").html().length == 0 ) 
    {    
        
        loader2.fadeIn();
        var extra_link = "";
        if ( $("#archive").length != 0 )
        {
             extra_link = "?archive=" + $("#archive").val();
        }
        $.get($("#base_url").val()+'front/ajax/getEditorialNews/' + ci_key + extra_link, function(data){  
            
            $(".etab-0").html(data);
            loader2.hide(200);                   
        });
      
    }
    else
    {
              
    }
        
    $(".editorial .content .tab-list li").click(function(){
        var list = $(".editorial .content .tab-list  li");
        var loader2=$('#loader3');
        var ajaxurl;
        
        var extra_link = "";
        if ( $("#archive").length != 0 )
        {
             extra_link = "?archive=" + $("#archive").val();
        }
            
        if($(this).index()==1)
        {
            ajaxurl = 'front/ajax/getOped/' + ci_key + extra_link;
        }
        else if($(this).index()==2)
        {
            ajaxurl = 'front/ajax/getLetterToEditor/' + ci_key + extra_link;
        }
        else
        {
            ajaxurl = 'front/ajax/getunAuthorizeAccess' + extra_link;
        }
        show_tab($(this),'etab',list,ajaxurl,loader2);
    });
   
 
    if($('#columinsts').length>0)
    {
        var extra_link = "";
        if ( $("#archive").length != 0 )
        {
             extra_link = "?archive=" + $("#archive").val();
        }
        $.get($("#base_url").val()+'front/ajax/get_columinst' + extra_link, "", function(data, status)
        {
            $('#columinsts').html(data);
            $('#columinsts li a').css("color","#000");
            $('#columinsts li a').css("font-size","12px");
        });
    }
    $(document).on('click','#media-list li',function()
    {
        $("#media-list li").removeClass('active');
        $(this).addClass('active');
        $(".media-content > div").hide();
        $(".mtab-"+$(this).index()).stop(true, true).animate({
            opacity: "show"
        }, "slow");
              
              
    }); 
 
    
  
        
        $('.category > div > ul > li').mouseenter(function()
        {
            
             
                if($(this).parent().find('.image_div').length>0)
                {
                     if(!$(this).find('.image_div').is(':visible') )
                     {
                        $(this).parent().find('h2').removeClass("bold_title");
                        
                        $(this).find('h2').addClass("bold_title");
                        $(this).parent().find('.image_div').stop().slideUp("slow");

                        $(this).find('.image_div').stop().slideDown("slow");  
                     }

                }
              
                
            });
        
        var pre_height = 0;
        $(".cat-desc").each(function(){
            var height = $(this).outerHeight();
            if ( height > pre_height )
            {
                pre_height = height;
            }
        });
        
        var height = $(".cat_name").outerHeight();
        pre_height = pre_height + height;
        
        $(".category").each(function(){
            $(this).height(pre_height);
        });
        
        /*$('.bxslider2').bxSlider({
            mode: 'fade',
            minSlides: 2,
            maxSlides: 2,
            pager:true,
            controls: false
        });*/
        
        if($("#size_of_what_to_watch").length>0)
        {
            var auto_load = false; 
            if($("#size_of_what_to_watch").val()>5)
                auto_load = true;
       
            $('.bxslider').bxSlider({
                auto: auto_load,
                minSlides: 3,
                maxSlides: 3,
                slideWidth: 600,
                slideMargin: 20,
                controls: true,
                onSliderLoad: function(){
                    $('.bx-controls-direction').hide();
                    $('.bx-wrapper').hover(
                        function(){$('.bx-controls-direction').fadeIn(300);},
                        function(){$('.bx-controls-direction').fadeOut(300);}
                    );
                }    
            });
        }

    
}); 
$(document).ready(function(){
    if($(".galleryrightwidget").length>0)
    {
        jQuery(".galleryrightwidget").html5gallery();
    }
//    if($(".media-gallery"))
//    {
//        var extra_link = "";
//        if ( $("#archive").length != 0 )
//        {
//             extra_link = "?archive=" + $("#archive").val();
//        }
//        $.get( $("#base_url").val() + "front/ajax/getmedia" + extra_link, 
//        {
//            ci_key: $("#ci_key_media").val(),
//            gallery_name: $("#gallery_name").val(),
//            ad_plan_id: $("#ad_plan_id").val(),
//            s_date: $("#s_date").val(),
//            show_ad: $("#show_ad").val()
//        }, function(data){
//            $(".media-gallery").html(data);
//            $(".media-content > div").hide();
//            $(".media-content > div.mtab-0").show();
//            jQuery(".mediahtml5gallery").html5gallery()
//        });
//    }
});

function onSlideChange(data) {
    try {
        $('.content-news-body').hide();
        $("div.content-news-body#"+data[0]).show();
    } catch (error){
        
    }
}
function onThumbOver(data) {
    
    try {
        $('.content-news-body').hide();
        $("div.content-news-body#"+data[0]).show();
    } catch (error){
        
    }
}
function onThumbOut(data) {
    
    try {
        $('.content-news-body').hide();
        $("div.content-news-body#"+data[0]).show();
    } catch (error){
        
    }
}

function show_tab(tab, tabname, ul_list, ajaxurl, loader)
{
    if ($("." + tabname + '-' + tab.index()).html().length == 0)
    {
        loader.fadeIn();
        $.get($("#base_url").val() + ajaxurl, function(data) {
            $("." + tabname + '-' + tab.index()).html(data);
            loader.hide(200);
        });
    }
    else
    {
        loader.hide();

    }
    tab.addClass('active');
    $("." + tabname + '-' + tab.index()).show();
    ul_list.each(function() {
        if ($(this).index() != tab.index())
        {
            $("." + tabname + '-' + $(this).index()).hide();
            $(this).removeClass('active');
        }
    });



}
