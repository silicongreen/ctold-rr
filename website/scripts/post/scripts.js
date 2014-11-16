function datepiker_calender(){
    var cur_date = format_date(new Date());
    
    $('#dp6').datepicker({
        startDate: '1990-01-01',
        endDate: cur_date,
        formatDate: 'yyyy-mm-dd',
    }).off('changeDate').on('changeDate', function(ev){
        var obj_date = new Date(ev.date);
        var str_date = format_date(obj_date);
        
        if(cur_date == str_date){
            ev.preventDefault;
            return false;
        }
        
        var base_url = $('#base_url').val();
        base_url = base_url.substr(0, base_url.length - 1);
        var url = base_url + '/newspaper?date=' + str_date;
        var win = window.open(url, '_blank');
        win.focus();
    });
}

function format_date(obj_date, yesterday){
    var month = obj_date.getMonth()+1;
    var day = (yesterday != undefined) ? (obj_date.getDate() - 1) : obj_date.getDate();
    
    var output = obj_date.getFullYear() + '-' + ((''+month).length < 2 ? '0' : '') + month + '-' + ((''+day).length < 2 ? '0' : '') + day;
    return output;
}



if($(".post").length>0)
{  
    var related_news_post_count = 0;
    var s_related_content = $.trim($("#related_news_1 .related_news-list").html());
    $(".related_news_on_post").each(function(){
        if ( s_related_content.length > 0 )
        {
            var style = $(this).attr("style");

            var div = document.createElement("div");
            div.setAttribute("class","related_news");
            div.setAttribute("style",style);

            $(this).before(div);
        }
        $(this).remove();
        related_news_post_count++;
    });
    //
    if ( related_news_post_count == 0 )
    {
        var s_related_content = $.trim($("#related_news_1 .related_news-list").html());

        if ( s_related_content.length > 0 )
        {
            $("#content").find("p").first().prepend('<div class="related_news" style="width: 150px; min-height: 100px; float: right;  border: 1px solid #ccc; margin: 10px;"></div>');   
        }
    }
    $(".related_news").wrapInner(function() {
        return $("#related_news_1").html();
    });

    if ( s_related_content.length > 0 )
    {
        if ( $(".related_news").css("float") == "left" )
        {
            $(".related_news").css("margin-right", "10px");
        }
        else if ( $(".related_news").css("float") == "right" )
        {
            $(".related_news").css("margin-left", "10px");
        }
        else
        {
            $(".related_news").css("float","right");
            $(".related_news").css("margin-left", "10px");    
        }
        var related_news_ul_height = $(".related_news ul").outerHeight();
        var related_news_div_height = $(".related_news").outerHeight();
        if ( related_news_div_height > related_news_ul_height )
        {
            //$(".related_news").height(related_news_ul_height + 40)
        }
        else
        {
            $(".related_news ul").height(related_news_div_height - 45);
        }
    }

    $(".post").find('img').addClass("toolbar");
   
    
   // $(".post").find('img').attr("alt",$("#headline").html());
    $(".post").find('img').css("cursor","pointer");

    $(".post").find('img').each(function(){
       if($(this).attr("alt")=="")
       {
          $(this).attr("alt",$("#headline").html());
       }    
       if ($(this).parent().prop("tagName").toLowerCase() == "a")
        {
            $(this).parent().attr("href","javascript:;");
            $(this).parent().attr("rel","");
        } 
    });

    var ci_key = $("#ci_key_main").val(); 
    var loader1=$('#loader2');
    if( $(".most-discuss .content .tab-0").html().length == 0 ) 
    {
        loader1.fadeIn();
        var extra_link = "";
        var post_id_link ="";
        
        if($("#post_id_value").length!=0)
            {
                post_id_link = "/"+$("#post_id_value").val();
            }
        $.get($("#base_url").val()+'front/ajax/getMoreNews/' + ci_key + "/" + post_id_link + "/" + extra_link, function(data){
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
          

           if($(this).index()==1)
           {
               ajaxurl = 'front/ajax/getMostRead/' + ci_key + "/" + extra_link;
           }
           else if($(this).index()==2)
           {
               ajaxurl = 'front/ajax/getMostDiscussed/' + ci_key + "/" + extra_link;
           }
           else
           {
               ajaxurl = 'front/ajax/getunAuthorizeAccess' + extra_link;
           }
           show_tab($(this),'tab',list,ajaxurl,loader1);
     });
     
     /* MENU HEADER */
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
            
            var margin_one = 9;
            var margin_two = 8;

            if(/chrom(e|ium)/.test(navigator.userAgent.toLowerCase()))
            {
                margin_one = 0;
                margin_two = 0;
            }


            
            if (margin_left > space_for_menu)
            {
                $(this).find('.sub-menu-news').css('margin-left', '-' + (submenu_width - menu_width + margin_one) + 'px');
            }
            else
            {
                $(this).find('.sub-menu-news').css('margin-left', '-' + (margin_left + margin_two) + 'px');
            }

        }

    });
}


 datepiker_calender();
     
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