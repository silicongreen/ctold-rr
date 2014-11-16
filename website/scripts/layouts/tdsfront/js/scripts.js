/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function(){
    var ci_key = $("#ci_key_main").val(); 
    
    var show_overlay = ( $("#show_overlay").length > 0 ) ? $("#show_overlay").val() : "1";
    
    $(".main_news_div > div.contents-news,.main_news_div > h1").mouseenter(function(e) 
    {
    
        
        if ( $(this).parent().find('div[class="main-news-overlay"]').css('display') == "none" ) 
        {
               
            var offset = $(this).parent().offset();
            var div_height = 100;

            var current_cursor_position =  e.pageY;
            var current_screen_height = $(window).height();

            var overlay_height = 150;
	
            var top = offset.top + (div_height / 2);
              

            var current_offset_position = current_screen_height - (top-$(window).scrollTop());
            $(this).parent().find('div[class="main-news-overlay"]').css({
                marginLeft:"300px"
            }); 
               
            if ( overlay_height > current_offset_position )
            {
                top = top - ( overlay_height - 40);
                $(this).parent().find('div[class="main-news-overlay"]').css({
                    top: top + "px"
                }); 
                $(this).parent().find('div[class="main-news-overlay"]').css("background","url('/styles/layouts/tdsfront/images/overly-box-rotate.png') no-repeat"); 
            }
            else
            {
                top = ( top - 10) ;
                $(this).parent().find('div[class="main-news-overlay"]').css({
                    top: top + "px"
                }); 
                $(this).parent().find('div[class="main-news-overlay"]').css("background","url('/styles/layouts/tdsfront/images/overly-box.png') no-repeat"); 
            }
              
            if ( show_overlay != "0" )
            {
                $(this).parent().find('div[class="main-news-overlay"]').stop(true, true).animate({
                    opacity: "show", 
                    marginLeft: "184px"
                }, "slow");         
                $(this).parent().find('div[class="main-news-overlay"]').css("display", "block");
            }
            $(this).parent().css({
                backgroundColor: "#fff"
            });
        }
    }

    );
    $(".main_news_div").mouseleave(function(){
              
        if ( show_overlay != "0" )
        {
            $(this).find('div[class="main-news-overlay"]').stop(true, true).animate({
                opacity: "hide", 
                marginLeft: "300px"
            }, "slow");
        }
        $(this).css({
            backgroundColor:"#F0F0F0"
        });
        if ( show_overlay != "0" )
        {
            $(this).find('.main-news-overlay').hide();
            $(this).find('.main-news-overlay').css("display", "none");
        }
    });
    
    ///MENU HEADER
    
    $('#nav').children('li').each(function() {
        
        if ($(this).children('ul').length > 0) {
            var width = 0;
            $(this).children('ul').find('span').each(function()
            {
                width += $(this).width();
            }

            );

            width = width + 10;
            $(this).find('.sub-menu-news').css('width', width + 'px');
            $(this).find('.sub-menu-news').css('margin-left', '-9px');
        }
    });

    var body_width = $(document).width();
    var wrapper_width = $('.ym-wrapper').width();
    var fixed_margin = Math.floor((body_width - wrapper_width) / 2);
    var first_menu_pos = Math.floor($('ul#nav > li:first').position().left);

    $('ul#nav > li').not("ul li ul").each(function(e) {
        var menu_pos_left = $(this).position().left;
        var menu_width = $(this).outerWidth();
        var margin_left = menu_pos_left - first_menu_pos;

        if ($(this).find('.sub-menu-news').length)
        {
            var submenu_width = $(this).find('.sub-menu-news').outerWidth();
            var space_for_menu = submenu_width - menu_width;
            if (margin_left > space_for_menu)
            {
                $(this).find('.sub-menu-news').css('margin-left', '-' + (submenu_width - menu_width + 9) + 'px');
            }
            else
            {
                $(this).find('.sub-menu-news').css('margin-left', '-' + (margin_left + 8) + 'px');
            }

        }

    });
    
    //MOST READ MORE READ
    
    var loader1=$('#loader2');
        if( $(".most-discuss .content .tab-0").html().length == 0 ) 
        {
            loader1.fadeIn();
            var extra_link   ="";
            var post_id_link ="";
            if ( $("#archieve").length != 0 )
            {
                extra_link = "?archieve=" + $("#archieve").val();
            }
            if($("#post_id_value").length!=0)
            {
                post_id_link = "/"+$("#post_id_value").val();
            }
            $.get($("#base_url").val()+'front/ajax/getMoreNews/' + ci_key + post_id_link + extra_link, function(data){
                    $(".tab-0").html(data);
                    loader1.hide(200);
            });
        }
        else
        {
            
        }
        
         $(".most-discuss .content .tab-list li").click(function()
         {
               
               var list = $(".most-discuss .content .tab-list  li");
               var loader1=$('#loader2');
               var ajaxurl;
               
               var extra_link = "";
               if ( $("#archieve").length != 0 )
               {
                    extra_link = "?archieve=" + $("#archieve").val();
               }
               
               if($(this).index()==1)
               {
                   ajaxurl = 'front/ajax/getMostRead/' + ci_key + extra_link;
               }
               else if($(this).index()==2)
               {
                   ajaxurl = 'front/ajax/getMostDiscussed/' + ci_key + extra_link;
               }
               else
               {
                   ajaxurl = 'front/ajax/getunAuthorizeAccess' + extra_link;
               }
               show_tab($(this),'tab',list,ajaxurl,loader1);
         });
         
         //TOP SEARCH
         
         $("#sb-search").click(function(){                
            if(!$(this).find(".sb-search-submit").hasClass("sb-submit-search"))
            {                
                if($(".search-box").width() == 30)
                {      
                    $(".search-box").css("min-width","228px");
                    $(this).addClass("sb-search-open");
                    $(this).find(".sb-search-submit").addClass("sb-submit-search");
                } 
            }                     
            return false;
        });
                
                
        $("#sb-search .sb-search-submit").on("click",function(){
            if($(this).hasClass("sb-submit-search") && $("#sb-search-input").val()=='Search ...')
            {
                $(".search-box").css("min-width","28px");
                $(this).removeClass("sb-submit-search");
                $(this).parent().parent().removeClass("sb-search-open");                        
            } 
            else
            {
                document.getElementById("serch-form").submit();
            }    
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
   
    if( $(".editorial .content .etab-0").length>0 && $(".editorial .content .etab-0").html().length == 0 ) 
    {    
        
        loader2.fadeIn();
        var extra_link = "";
        if ( $("#archieve").length != 0 )
        {
             extra_link = "?archieve=" + $("#archieve").val();
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
        if ( $("#archieve").length != 0 )
        {
             extra_link = "?archieve=" + $("#archieve").val();
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
        if ( $("#archieve").length != 0 )
        {
             extra_link = "?archieve=" + $("#archieve").val();
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
});

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