 var sent_request = false;
 var current_page = 0;
 var msnroy;
 var pageSizeDefault = 9;
 
function setCookie() {
    var exdays = 1;
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toGMTString();
    document.cookie = "tds_welcome_banners=viewed;" + "expires=" + d.toGMTString() + ';' + "path=/";
}

function getCookie(cookie_name) {
    var cookies = document.cookie.split('; ');
    for (i = 0; i < cookies.length; i++) {
        var cookie = cookies[i].split('=');
        if (cookie[0] == cookie_name) {
            return cookie[1];
        }
    }
    return "";
}

function datepiker_calender() {
    var cur_date = format_date(new Date());

    $('#dp6').datepicker({
        startDate: '1990-01-01',
        endDate: cur_date,
        formatDate: 'yyyy-mm-dd',
    }).off('changeDate').on('changeDate', function(ev) {
        var obj_date = new Date(ev.date);
        var str_date = format_date(obj_date);

        if (cur_date == str_date) {
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

function format_date(obj_date, yesterday) {
    var month = obj_date.getMonth() + 1;
    var day = (yesterday != undefined) ? (obj_date.getDate() - 1) : obj_date.getDate();

    var output = obj_date.getFullYear() + '-' + (('' + month).length < 2 ? '0' : '') + month + '-' + (('' + day).length < 2 ? '0' : '') + day;
    return output;
}

(function() {
    addthis.init() 
    if ($(".mygallery").length > 0)
    {
        var tn1 = $('.mygallery').tn3({
            skinDir: "http://www.thedailystar.net/in_picture/skins/",
            imageClick: "fullscreen",
            image: {
                maxZoom: 1.5,
                crop: true,
                clickEvent: "dblclick",
                transitions: [{
                        type: "blinds"
                    }, {
                        type: "grid"
                    }, {
                        type: "grid",
                        duration: 460,
                        easing: "easeInQuad",
                        gridX: 1,
                        gridY: 8,
                        // flat, diagonal, circle, random
                        sort: "random",
                        sortReverse: false,
                        diagonalStart: "bl",
                        // fade, scale
                        method: "scale",
                        partDuration: 360,
                        partEasing: "easeOutSine",
                        partDirection: "left"
                    }]
            }
        });
    }

    /*var banner_cookie_status = getCookie('tds_welcome_banners');*/
    var banner_cookie_status = "no";

    if (banner_cookie_status != 'no') {
        var id = '#dialog';

        var maskHeight = $(document).height();
        var maskWidth = $(window).width();

        $('#mask').css({'width': maskWidth, 'height': maskHeight});

        $('#mask').fadeIn(1000);
        $('#mask').fadeTo("slow", 0.8);

        var winH = $(window).height();
        var winW = $(window).width();

        $(id).css('top', winH / 2 - $(id).height() / 2);
        $(id).css('left', winW / 2 - $(id).width() / 2);

        $(id).fadeIn(1000);
        setCookie();

        setTimeout(function() {
            $('.window').hide();
            $('#mask').hide();
        }, 10000);

        $('.window .close').click(function(e) {
            $('#mask').hide();
            $('.window').hide();

            e.preventDefault();
            return false;
        });
    }

    datepiker_calender();

    /* if($('#disable_active_datepicker').length > 0 && $('#disable_active_datepicker').val() == 'disable_active_datepicker'){
     if($('.datepicker-inline').length > 0){
     var active_day = $('.datepicker-inline').find('td.active');
     active_day.removeClass('active');
     }
     } */

    var ci_key = $("#ci_key_main").val();

    var show_overlay = ($("#show_overlay").length > 0) ? $("#show_overlay").val() : "1";
    $("#carrosel-news li").each(function() {
        $(this).show();

    });

//    $("#carrosel-news").carouFredSel({
//        pagination: "#pager",
//        circular: true,
//        direction: "left",
//        auto: {
//            pauseOnHover: 'resume',
//            progress: '#timer1',
//            timeoutDuration: 46000
//        },
//        mousewheel: true,
//        swipe: {
//            onMouse: true,
//            onTouch: true
//        }
//    },
//    {
//        transition: false
//    });

    $(".close-carrosel").click(function(){
        $(this).parent().hide();
        if ( $(window).scrollTop() == 0 )
        {
            window.scrollTo(0,1);
        }
    });
    
    
    
    $( window ).scroll(function() {
        
        var screen_height = $(document).innerHeight() - 400;
        
        var scroll_top = $(this).scrollTop();
        
        var licount = 0;
        $('#grid li').each(function(el,i){
                    
            if( !$(this).hasClass( 'shown' ) && !$(this).hasClass( 'animate' ))
            {
                licount++;
            } 
            
        });
        
        setTimeout(function(){
        if ( (($("#content-wrapper").height()-$(window).height()) - scroll_top) <= 100 && licount == 0 )
        {
            if ( $(".loading-box").length != 0 && sent_request == false )
            {
                var total_post = new Number( $("#total_data").val() );
                var page_size = new Number( $("#page-size").val() );
                var page_limit = new Number( $("#page-limit").val() );
                var q = $("#q").val();
                var callcount = 0;
                var lang = readCookie('local');
                
                if(lang !== null) {
                    lang = lang;
                } else {
                    lang = '';
                }
                
                current_page = new Number( $("#current-page").val());
                //console.log(current_page);
                var page_to_load = current_page + 1;
                
                sent_request = true;
                $(".loading-box").show();
                runScrool = false;
                var content_showed = "";
                if($(".container ul#grid").length>0)
                {
                    $(".container ul#grid li.post-content-showed").each(function()
                    {
                        if(this.id)
                        {
                            var post_id = this.id;
                            var id_array = post_id.split("-");
                            content_showed = content_showed+id_array[1]+"|";
                        }
                    });
                }    
                
                $.ajax({
                    type: "GET",
                    url: $("#base_url").val() + 'front/ajax/getPosts/' + $("#category").val() + "/" + $("#target").val() + "/" + $("#page").val() + "/" + $("#page-limit").val() + "/" + page_to_load + "/" + lang,
                    data: { content_showed:content_showed, s: q},
                    async: true,
                    success: function(data) {
			runScrool = true;			
			callcount += 1;
                        page_size += pageSizeDefault;
                        $("#page-size").val(page_size);
                        if ( page_size >= total_post )
                        {
                            $(".loading-box").remove();
                        }
                        //$(".posts-" + current_page).append("<div class='clear-box-" + current_page + "' style='clear:both;'></div>");
                        //$("#grid").append(data);
                        $("#grid").append(data);
                        
                        addthis.toolbox('.addthis_toolbox');
                        
//                        if(callcount > 1)
//                        {
//                                alert(1);
//                                var dataad1 = "<aside class='widget_contact_info' style='margin-bottom:20px;'><center><img src='/upload/ads/right-ad-07.png' ></center></aside>"
//                                                                +"<aside class='widget_contact_info' style='margin-bottom:20px;'><center><img src='/upload/ads/right-ad-08.png' ></center></aside>";
//                                $("div.sidebar-level1").append(dataad1);
//                        }
//                        if(callcount > 2)
//                        {
//                                alert(2);
//                                var dataad2 = "<aside class='widget_contact_info' style='margin-bottom:20px;'><center><img src='/upload/ads/right-ad-09.png' ></center></aside>"
//                                                                +"<aside class='widget_contact_info' style='margin-bottom:20px;'><center><img src='/upload/ads/right-ad-10.png' ></center></aside>";
//                                $("div.sidebar-level1").append(dataad2);
//                        }
						
						
                        current_page += 1;
                        $("#current-page").val(current_page);
                        
                        
                        
                        setTimeout(function(){
                           
                            var $container = jQuery("[id=grid]");  
                            $container.imagesLoaded(function(){
                                $("#grid li").removeClass("ajax-hide");
                                
                                jQuery('.flex-wrapper .flexslider').flexslider( {
                                            slideshow : false,
                                            animation : 'fade',
                                            pauseOnHover: true,
                                            animationSpeed : 400,
                                            smoothHeight : false,
                                            directionNav: true,
                                            controlNav: false,
                                            after: function(){
                                               $("#grid").masonry('reload');
                                                jQuery('#tz_mainmenu').tinyscrollbar();
                                            }

                                });
                                if($(".flex-wrapper_news").length>0)
                                {   
                                    jQuery('.flex-wrapper_news .flexslider_news').flexslider( {
                                              slideshow : false,
                                              animation : 'fade',
                                              pauseOnHover: true,
                                              animationSpeed : 400,
                                              smoothHeight : false,
                                              directionNav: false,
                                              selector: ".slides_news > li.news_slides",
                                              after: function(){
                                                    $("#grid").masonry('reload');
                                                    jQuery('#tz_mainmenu').tinyscrollbar();
                                              }
                                      });
                                }
                                
                                $("#grid").masonry('reload');
                                scrollPage();
                                if($("#triangle-bottomright").length>0)
                                {
                                    $("#triangle-bottomright").css("border-left-width", $("#post-image").width() + "px");
                                }

                                
                                setTimeout(function(){
                                    
                                    sent_request = false;
                                    $(".loading-box").hide();
                                   
                                    
                                   
                                    
                                }, 500);
                            
                            });
                            
                            
                        }, 200);
                        
                        
                        
                        

                    }
                });
            }
        }
       }, 200);
    });
    
    $(".main_news_div > div.contents-news,.main_news_div > h1").mouseenter(function(e)
    {


        if ($(this).parent().find('div[class="main-news-overlay"]').css('display') == "none")
        {

            var offset = $(this).parent().offset();
            var div_height = 100;

            var current_cursor_position = e.pageY;
            var current_screen_height = $(window).height();

            var overlay_height = 150;

            var top = offset.top + (div_height / 2);


            var current_offset_position = current_screen_height - (top - $(window).scrollTop());
            $(this).parent().find('div[class="main-news-overlay"]').css({
                marginLeft: "300px"
            });

            if (overlay_height > current_offset_position)
            {
                top = top - (overlay_height - 40);
                $(this).parent().find('div[class="main-news-overlay"]').css({
                    top: top + "px"
                });
                $(this).parent().find('div[class="main-news-overlay"]').css("background", "url('/styles/layouts/tdsfront/images/overly-box-rotate.png') no-repeat");
            }
            else
            {
                top = (top - 10);
                $(this).parent().find('div[class="main-news-overlay"]').css({
                    top: top + "px"
                });
                $(this).parent().find('div[class="main-news-overlay"]').css("background", "url('/styles/layouts/tdsfront/images/overly-box.png') no-repeat");
            }

            if (show_overlay != "0")
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
    $(".main_news_div").mouseleave(function() {

        if (show_overlay != "0")
        {
            $(this).find('div[class="main-news-overlay"]').stop(true, true).animate({
                opacity: "hide",
                marginLeft: "300px"
            }, "slow");
        }
        $(this).css({
            backgroundColor: "#FFF"
        });
        if (show_overlay != "0")
        {
            $(this).find('.main-news-overlay').hide();
            $(this).find('.main-news-overlay').css("display", "none");
        }
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

            if (/chrom(e|ium)/.test(navigator.userAgent.toLowerCase()))
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

    
})();