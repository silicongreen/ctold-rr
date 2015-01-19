// Google API Details
var clientId = '650847745730-27tmea49dcp01iho24eun3tgol7gq772.apps.googleusercontent.com';
var apiKey = 'AIzaSyAEZOSmfxFmROGdV2u9aCxGO5fW2AuGDmM';
var scopes = 'https://www.googleapis.com/auth/plus.profile.emails.read';
var g_call_counter = 0;
var f_call_counter = 0;
// Google API Details
function sharebrowser(id)
{
    
    $.fancybox({
            'width'		        : 280,
            'height'                    : 115,
            'autoScale'                 : false,
            'autoSize'                  : false,
            'href'			: $("#base_url").val()+"front/ajax/sharepop/"+id,
            'title'                     : false,
            'transitionIn'		: 'none',
            'transitionOut'		: 'none',
            'type'		        : 'iframe'
                    
        });
}
function generate_result(current_page)
{
    var type = $(".results").attr("id").replace("results_", "");
    
    $.post($("#base_url").val() + 'front/ajax/getGKAnswer/',
        {type : type, current_page : current_page, tds_csrf: $('input[name$="tds_csrf"]').val()}, function(data){
            
            if (data != 0){
                $(".results").html(data);
                $('.header-previous-gk').css('background-color', $('#gk_layout_color').val());
            }
        }
    );
}

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
    
    var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
    var eventer = window[eventMethod];

    var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";

    eventer(messageEvent, function (e) {
        if ( e.data.indexOf("SHOW_POST") > -1 )
        {
            getPostData();
        }
    }, false);
    
//    $(document).on("contextmenu", function(e){
//        if(e.target.nodeName == "IMG"){
//            e.preventDefault();
//            return false;
//        }
//     });
    
    if ( $(".post").length != 0 )
    {
        $(".post").find('img').addClass("toolbar");
        $(".post").find('img').css("cursor","pointer");
        $(".ads-image").removeClass("toolbar");  
    }
    
    if ( $(".featured").length === 0 )
    {
        if ( $(".inner-news").length > 0 )
        {
            $("#article-title").hide();
//            $("#article-button").css("width", "95%");
        }
    }
    
    if ( $("#featured_1").length > 0 )
    {
        var i_max_height = 0;
        $("#featured_1").children("li").each(function(){
            if (  $(this).outerHeight() > i_max_height )
            {
                i_max_height = $(this).outerHeight();
            }
        });
        $("#featured_1").css("height", i_max_height + "px")
    }
    
    var header_height = new Number($(".champs-header").outerHeight());
    var title_height  = new Number($(".sports-inner-news").outerHeight());

    var height_to_count = header_height + title_height + 25 + 4;
    
    if ($(window).scrollTop() >= height_to_count )
    {
        $(".addthis_toolbox-float").css("display","block");
        $(".addthis_toolbox").hide();
    }
    
    $(window).scroll(function(){
        if ( $(".addthis_toolbox-float").length > 0 )
        {
            if ($(this).scrollTop() >= height_to_count )
            {
                $(".addthis_toolbox-float").css("display","block");
                $(".addthis_toolbox").hide();
            }
            else if ($(this).scrollTop() <= height_to_count )
            {
                $(".addthis_toolbox-float").css("display","none");
                $(".addthis_toolbox").show();
            }
        }
    });
    
    var current_page = 1;
    if ( $(".results").length > 0 )
    {
        generate_result(current_page);
    }
    
    $(document).on('click', '.datepicker', function(){ 
        if (!$(this).hasClass('hasDatepicker')) { 
            $(this).datepicker();$(this).datepicker('show'); 
        }
    }); 
    
    $(document).on("click",".next" , function(){
        current_page++;
        generate_result(current_page);
    });
    
    $(document).on("click",".previous" , function(){
        current_page--;
        generate_result(current_page);
    });
    
    $(document).on("change",".parent-chk" , function(){
        
        if($(this).is(':checked')){
            $(this).parent('div').parent('div').parent('div').find('input[type="checkbox"]').prop('checked', true);
        }else{
            $(this).parent('div').parent('div').parent('div').find('input[type="checkbox"]').prop('checked', false);
        }
        
        //console.log();
        //$(this).parent('div').parent('div').find('.user_pref_chk').prop('checked');
    });
    
    $(document).on("click",".gk_answers" , function(){
        var s_ids_selected = this.id.replace("answer_","");
        var ids = s_ids_selected.split("_");
        var post_id = ids[0];
        var answer = $.trim(this.innerHTML);
        var obj = this;
        $.post($("#base_url").val() + 'front/ajax/setGKAnswer/',
            {answer : answer, post_id: post_id, tds_csrf: $('input[name$="tds_csrf"]').val()}, function(data){
                if ( data == "0" )
                {
                    $(".login-user").trigger("click");

                }
                else
                {
                    var ar_data = data.split("+");
                    if ( ar_data[0] == "1" )
                    {
                        alert("Your Answer is Successfully Logged");
                        if ( ar_data[1] == "0" )
                        {
                            $(obj).css("background","#f00");
                        }
                        else
                        {
                            $(obj).css("background","#0A0");
                        }
                        $(".gk_answers").removeClass("gk_answers");
                        generate_result(current_page);
                    }
                    else if ( ar_data[0] == "0" )
                    {
                        $(".login-user").trigger("click");

                    }
                }
            }
        );
    });
     
    
//    $(document).on("mouseover",".post-boxes" , function(){
//        $(this).children(".action-box").show();
//    });
//    
//    $(document).on("mouseout",".post-boxes" , function(){
//        $(this).children(".action-box").hide();
//    });
    
    $(document).on("click",".normal .folder" , function(){
		if ( $(this).hasClass("add") )
        {
            $(".normal .done-folder").html("Add");
            $(".normal .remove-folder").html("Cancel");
            $(".normal .good-read-box-scroll").hide();
            $(".normal .good-read-box-new-folder").show();
        }
        else
        {
            $(".normal .selected-folder").addClass("folder");
            $(".normal .folder").removeClass("selected-folder");
            $(this).removeClass("folder");
            $(this).addClass("selected-folder");
        }
    });
	
	$(document).on("click",".add-folder" , function(){

		if ( $(this).hasClass("add") )
        {

            $(".good-read-box2").show();
           
        }
        else
        {
            $(".normal .selected-folder").addClass("folder");
            $(".normal .folder").removeClass("selected-folder");
            $(this).removeClass("folder");
            $(this).addClass("selected-folder");
        }
    });
	$(document).on("click",".good-read-box2 .remove-folder" , function(){
		$(".good-read-box2").fadeOut("normal");
	});
	$(document).on("click",".good-read-box2 .done-folder" , function(){
        if ( $(this).html() === "Done" )
        {	
            var selected_folder_length = $(".good-read-box2 .selected-folder").length;
            if ( selected_folder_length == 0 )
            {
                alert("You must select at least one folder before Add to Good Read");
            }
            else
            {
                var post_id = $("#post_id_value").val();
                var folder_id = 0;
                var folder_name = "";
                $(".good-read-box2 .selected-folder").each(function(){
                    folder_id = this.id.replace("folder_","");
                    folder_name = this.innerHTML.replace("<span>","").replace("</span>","");
                });
                
                $.post($("#base_url").val() + 'front/ajax/addPostToGoodRead/',
                    {folder_id : folder_id, post_id: post_id, is_read: 1}, function(data){
                        if ( data == "1" )
                        {
                            alert("The post is added to your selected folder");
                            $(".good-read-box2 .good-read-box2").hide();
                            
                        }
                        else
                        {
                            alert(data);
                        }
                    }
                );
            }
        }
        else
        {	
            var folder_name = $.trim($(".good-read-box2 #folder_name").val());
            if ( folder_name.length == 0 )
            {
                alert("You must enter a folder name to create");
            }
            else
            {
                $.post($("#base_url").val() + 'front/ajax/addUserGoodReadFolder/',
                    {title : folder_name}, function(data){
                        if ( data != "The Folder you try to create is already exists" )
                        {
                            alert("The folder has been created successfully");
                            $(".good-read-box2 .als-item:not(.add):last").after("<li class='als-item'><div id='folder_" + data + "' class='folder'><span>" + folder_name + "</span></span></div></li>");
                            
                            $(".good-read-box2 #folder_name").val('');
                            $(".good-read-box2").hide();
                        }
                        else
                        {
                            alert(data);
                        }
                    }
                );
            }
        }
    });
    
    $(document).on("click",".float-read .folder" , function(){
        if ( $(this).hasClass("add") )
        {
            $(".float-read .done-folder").html("Add");
            $(".float-read .remove-folder").html("Cancel");
            $(".float-read .good-read-box-scroll").hide();
            $(".float-read .good-read-box-new-folder").show();
        }
        else
        {
            $(".float-read .selected-folder").addClass("folder");
            $(".float-read .folder").removeClass("selected-folder");
            $(this).removeClass("folder");
            $(this).addClass("selected-folder");
        }
    });
    
    
    $(document).on("click",".read_later" , function(){
       var post_id = this.id.replace("read_later_","");
       var folder_id = 0;
       $.post($("#base_url").val() + 'front/ajax/addPostToGoodRead/',
            {folder_id : folder_id, post_id: post_id, is_read: 0}, function(data){
                if ( data != -1 )
                {
                    alert("Added to your Unread Post list");
                }
            }
        );
    });
    
    $(document).on("click",".float-read" , function(e){
        if ( $(".good-read-box").css("display") == "none" )
        {
            $(".good-read-box").show();
            $(".float-read").css("background-color","#93989C");
            $(".good-read-column").css("background-color","#93989C");
        }
        else
        {
            var ar_classes = ["good-read-box", "good-read-box-scroll", "good-read-box-new-folder", "good-read-box-action", "selected-folder", "folder", "done-folder", "remove-folder", "clear-folder", "title-folder", "highlighted-title", "folder_name", "label-folder", "br-folder", "title-span-folder", "add", "folder add"];
            
            if ( e.target.className.in_array(ar_classes) )
            {
                return false;
            }
            
            $(".normal .good-read-box").hide();
            $(".float-read .good-read-box").hide();
            $(".float-read").css("background-color","#FC3E30");
            $(".good-read-column").css("background-color","#FC3E30");
        }
    });
    
    $(document).on("click",".good-read-button" , function(e){
        if ( $(".good-read-box").css("display") == "none" )
        {
            $(".good-read-box").show();
            $(".good-read-column").css("background-color","#93989C");
            $(".float-read").css("background-color","#93989C");
            
        }
        else
        {
            var ar_classes = ["good-read-box", "good-read-box-scroll", "good-read-box-new-folder", "good-read-box-action", "selected-folder", "folder", "done-folder", "remove-folder", "clear-folder", "title-folder", "highlighted-title", "folder_name", "label-folder", "br-folder", "title-span-folder", "add", "folder add"];
            
            if ( e.target.className.in_array(ar_classes) )
            {
                return false;
            }
            
            $(".normal .good-read-box").hide();
            $(".float-read .good-read-box").hide();
            $(".good-read-column").css("background-color","");
            $(".float-read").css("background-color","#FC3E30");
        }
        
    });
    
    $(document).on("click",".normal .done-folder" , function(){
        if ( $(this).html() === "Done" )
        {
            var selected_folder_length = $(".normal .selected-folder").length;
            if ( selected_folder_length == 0 )
            {
                alert("You must select at least one folder before Add to Good Read");
            }
            else
            {
                var post_id = $("#post_id_value").val();
                var folder_id = 0;
                var folder_name = "";
                $(".normal .selected-folder").each(function(){
                    folder_id = this.id.replace("folder_","");
                    folder_name = this.innerHTML.replace("<span>","").replace("</span>","");
                });
                
                $.post($("#base_url").val() + 'front/ajax/addPostToGoodRead/',
                    {folder_id : folder_id, post_id: post_id, is_read: 1}, function(data){
                        if ( data == "1" )
                        {
                            alert("The post is added to your selected folder");
                            $(".normal .good-read-box").hide();
                            $(".float-read .good-read-box").hide();
                            $(".good-read-column").css("background-color","#93989C");
                        }
                        else
                        {
                            alert(data);
                        }
                    }
                );
            }
        }
        else
        {
            var folder_name = $.trim($(".normal #folder_name").val());
            if ( folder_name.length == 0 )
            {
                alert("You must enter a folder name to create");
            }
            else
            {
                $.post($("#base_url").val() + 'front/ajax/addUserGoodReadFolder/',
                    {title : folder_name}, function(data){
                        if ( data != "The Folder you try to create is already exists" )
                        {
                            alert("The folder has been created successfully");
                            $(".normal .folder:not(.add):last").after("<div id='folder_" + data + "' class='folder'><span>" + folder_name + "</span>");
                            $(".float-read .folder:not(.add):last").after("<div id='folder_" + data + "' class='folder'><span>" + folder_name + "</span>");
                            $(".normal #folder_name").val('');
                            $(".float-read #folder_name").val("");
                            $(".normal .done-folder").html("Done");
                            $(".normal .remove-folder").html("Remove");
                            $(".normal .good-read-box-scroll").show();
                            $(".normal .good-read-box-new-folder").hide();
                        }
                        else
                        {
                            alert(data);
                        }
                    }
                );
            }
        }
    });
    
    $(document).on("click",".float-read .done-folder" , function(){
        if ( $(this).html() === "Done" )
        {
            var selected_folder_length = $(".float-read .selected-folder").length;
            if ( selected_folder_length == 0 )
            {
                alert("You must select at least one folder before Add to Good Read");
            }
            else
            {
                var post_id = $("#post_id_value").val();
                var folder_id = 0;
                var folder_name = "";
                $(".float-read .selected-folder").each(function(){
                    folder_id = this.id.replace("folder_","");
                    folder_name = this.innerHTML.replace("<span>","").replace("</span>","");
                });
                
                $.post($("#base_url").val() + 'front/ajax/addPostToGoodRead/',
                    {folder_id : folder_id, post_id: post_id, is_read: 1}, function(data){
                        if ( data == "1" )
                        {
                            alert("The post is added to your selected folder");
                            $(".float-read .good-read-box").hide();
                            $(".normal .good-read-box").hide();
                            $(".good-read-column").css("background-color","#93989C");
                        }
                        else
                        {
                            alert(data);
                        }
                    }
                );
            }
        }
        else
        {
            var folder_name = $.trim($(".float-read #folder_name").val());
            if ( folder_name.length == 0 )
            {
                alert("You must enter a folder name to create");
            }
            else
            {
                $.post($("#base_url").val() + 'front/ajax/addUserGoodReadFolder/',
                    {title : folder_name}, function(data){
                        if ( data != "The Folder you try to create is already exists" )
                        {
                            alert("The folder has been created successfully");
                            $(".normal .folder:not(.add):last").after("<div id='folder_" + data + "' class='folder'><span>" + folder_name + "</span>");
                            $(".float-read .folder:not(.add):last").after("<div id='folder_" + data + "' class='folder'><span>" + folder_name + "</span>");
                            $(".normal #folder_name").val('');
                            $(".float-read #folder_name").val("");
                            $(".float-read .done-folder").html("Done");
                            $(".float-read .remove-folder").html("Remove");
                            $(".float-read .good-read-box-scroll").show();
                            $(".float-read .good-read-box-new-folder").hide();
                        }
                        else
                        {
                            alert(data);
                        }
                    }
                );
            }
        }
    });
    
    $(document).on("click",".float-read .remove-folder" , function(){
        if ( $(this).html() === "Remove" )
        {
            var selected_folder_length = $(".float-read .selected-folder").length;
            if ( selected_folder_length == 0 )
            {
                alert("You must select at least one folder before delete");
            }
            else
            {
                if ( confirm("Do you really want to remove this folder?") )
                {
                    var folder_id = 0;
                    $(".float-read .selected-folder").each(function(){
                        folder_id = this.id.replace("folder_","");
                    });
                    
                    $.post($("#base_url").val() + 'front/ajax/removeFolder/',
                        {folder_id : folder_id}, function(){
                            $(".float-read #folder_" + folder_id).remove();
                            $(".normal #folder_" + folder_id).remove();
                        }
                    );
                    
                    
                }
            }
        }
        else
        {
            $(".float-read .done-folder").html("Done");
            $(".float-read .remove-folder").html("Remove");
            $(".float-read .good-read-box-scroll").show();
            $(".float-read .good-read-box-new-folder").hide();
        }
    });
    
    $(document).on("click",".normal .remove-folder" , function(){
        if ( $(this).html() === "Remove" )
        {
            var selected_folder_length = $(".normal .selected-folder").length;
            if ( selected_folder_length == 0 )
            {
                alert("You must select at least one folder before delete");
            }
            else
            {
                if ( confirm("Do you really want to remove this folder?") )
                {
                    var folder_id = 0;
                    $(".normal .selected-folder").each(function(){
                        folder_id = this.id.replace("folder_","");
                    });
                    
                    $.post($("#base_url").val() + 'front/ajax/removeFolder/',
                        {folder_id : folder_id}, function(){
                            $(".float-read #folder_" + folder_id).remove();
                            $(".normal #folder_" + folder_id).remove();
                        }
                    );
                    
                    
                }
            }
        }
        else
        {
            $(".normal .done-folder").html("Done");
            $(".normal .remove-folder").html("Remove");
            $(".normal .good-read-box-scroll").show();
            $(".normal .good-read-box-new-folder").hide();
        }
    });
    
//    $(".lazy-load").lazyload({
//        effect : "fadeIn",
//        skip_invisible: false
//    });
//    $(".lazy-load-ad").lazyload({
//        event : "add_lazy"
//    });
//    
//    
//    $(".imgLiquidFill").imgLiquid({
//        fill:true,
//        verticalAlign: 'top'
//    });
    
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
    
    $(document).on('mouseover', '.profile-image', function() {
        $('.upload-msg').stop().fadeIn(100);
        $('.upload-icon img').stop().animate({width: "70%"}, 100);
    });
    
    $(document).stop().on('mouseout', '.profile-image', function() {
        $('.upload-msg').stop().fadeOut(100);
        $('.upload-icon img').stop().animate({width: "60%"}, 100);
    });
    
    $(document).on("mouseenter",".wow_class_single" , function(){
        var post_id = this.id.replace("wow_",""); 
        $("#wow_"+post_id+" .seen-image img").attr("src",$("#base_url").val()+"styles/layouts/tdsfront/images/social/wow-hover.png");
     });
     $(document).on("mouseleave",".wow_class_single" , function(){
        var post_id = this.id.replace("wow_",""); 
        $("#wow_"+post_id+" .seen-image img").attr("src",$("#base_url").val()+"styles/layouts/tdsfront/images/social/wow.png");
     }
    );
    
    $(document).on("click",".wow_class_single" , function(){
       var post_id = this.id.replace("wow_","");
       $.post($("#base_url").val() + 'front/ajax/addWow/',
            {post_id: post_id,single:true}, function(data){
                if ( data != 0  )
                {
                   $("#wow_"+post_id+" .seen h2").html(data);
                   //$("#wow_"+post_id).removeClass("wow_class");
                  // $("#wow_"+post_id+" .seen-image img").attr("src",$("#base_url").val()+"styles/layouts/tdsfront/images/social/wow-hover.png");
                }
                    
            }
        );
    });
    $(document).on("mouseenter",".share_class" , function(){
        var post_id = this.id.replace("share_",""); 
        $("#share_"+post_id+" .seen-image img").attr("src",$("#base_url").val()+"styles/layouts/tdsfront/images/social/share_minicon_tap.png");
     });
     $(document).on("mouseleave",".share_class" , function(){
        var post_id = this.id.replace("share_",""); 
        $("#share_"+post_id+" .seen-image img").attr("src",$("#base_url").val()+"styles/layouts/tdsfront/images/social/share_minicon_normal.png");
     }
    );
   
    $(document).on("mouseenter",".wow_class" , function(){
        var post_id = this.id.replace("wow_",""); 
        $("#wow_"+post_id+" .seen-image img").attr("src",$("#base_url").val()+"styles/layouts/tdsfront/images/social/wow-hover.png");
     });
     $(document).on("mouseleave",".wow_class" , function(){
        var post_id = this.id.replace("wow_",""); 
        $("#wow_"+post_id+" .seen-image img").attr("src",$("#base_url").val()+"styles/layouts/tdsfront/images/social/wow.png");
     }
    );
    
    $(document).on("click",".wow_class" , function(){
       var post_id = this.id.replace("wow_","");
       $.post($("#base_url").val() + 'front/ajax/addWow/',
            {post_id: post_id}, function(data){
                if ( data != 0  )
                {
                   $("#wow_"+post_id+" .seen span").html(data);
                   //$("#wow_"+post_id).removeClass("wow_class");
                  // $("#wow_"+post_id+" .seen-image img").attr("src",$("#base_url").val()+"styles/layouts/tdsfront/images/social/wow-hover.png");
                }
                    
            }
        );
    });
    
    
    
    $(document).on("click",'.before-login-user',function(){
        
        var pop_up_data = {
            'good_read' : {
                'icon' : 'good_read_red_icon.png',
                'header_label' : 'Good Read',
                'custom_message' : '<p>Save/collect articles that you like using "Good Read" feature.</p>'
            },
            'wow' : {
                'icon' : 'wow_red.png',
                'header_label' : 'WOW',
                'custom_message' : '<p>Use this feature to appreciate a content .</p>'
            },
            'candle' : {
                'icon' : 'candle_red_icon.png',
                'header_label' : 'Candle',
                'custom_message' : '<p>This is where you make your voice heard. Candle lets user to publish their articles and give feedback.</p>'
            },
            'read_later' : {
                'icon' : 'read_later_red_icon.png',
                'header_label' : 'Read Later',
                'custom_message' : '<p>We know how busy you are. Use "Read Later" to save interesting articles to read them later at a more convenient time.</p>'
            },
            'magic_mart' : {
                'icon' : 'magic_mart_red.png',
                'header_label' : 'Magic Mart',
                'custom_message' : 'Coming Soon.'
            },
            'assessment_save_score' : {
                'icon' : 'assessment_popup.png',
                'header_label' : 'Save Score',
                'custom_message' : ''
            },
            'school_join' : {
                'icon' : 'schools-new.png',
                'header_label' : 'Join To School',
                'custom_message' : "<p>Join your school's page today and stay connected!</p><p>Get updates on recent on goings and feeds from your school teachers.</p>"
            }
        };
        
        var key = $(this).attr('data');
        
        if(key == 'magic_mart'){
            $('.common_message').text('');
            $('.login-user-btn-wrapper').html('');
        }
        
        $('.before-login-user-header-label').html('');
        $('.before-login-user-header-label').html(pop_up_data[key].header_label);
        
        $('.before-login-user-icon-wrapper').html('');
        $('.before-login-user-icon-wrapper').html('<img src="/styles/layouts/tdsfront/image/' + pop_up_data[key].icon + '" width="75" />');
        
        $('.custom_message').html('');
        $('.custom_message').html(pop_up_data[key].custom_message);
        
        var html_before_login_popup = $('#before-login-user-fancy').html();

        $.fancybox({
            'content' : html_before_login_popup,
            'width': 450,
            'transitionIn': 'fade',
            'transitionOut': 'fade',
            'openEffect': 'elastic',
            'openSpeed' : 350,
            'fitToView' : true,
            'autoSize' : true,
            'padding': 0,
            'margin': 0
        });
    });
    
    $(document).on("click",'#free_user_profile_picture',function(){
        $('#profile_image_file').trigger('click');
    });
    
    $(document).on("click",'#logout_li',function(){
        window.location.href = $('#base_url').val() + "logout_user";
    });
    
    $(document).on("click",'.register-user',function(){
        
        $( "#reg_frm input:text, input:password").css( "border", "1px solid #d9dbdc" );
        
        var html_frm_reg = $('#frm_reg').html();
        
        $.fancybox({
            'content' : html_frm_reg,
            'width': 500,
            'height': 'auto',
            'transitionIn': 'fade',
            'transitionOut': 'fade',
            'openEffect': 'elastic',
            'openSpeed' : 350,
            'fitToView' : false,
            'autoSize' : false,
            'padding': 0,
            'margin': 0
        });
    });
    
    $(document).on("click", '.user_type_radio',function(){
        var user_type = $(this).find('input[type=radio]').val();
        var add_html_1 = '';
        var add_html_2 = '';
        
        $('#addition_row_1 div.center').html('');
        $('#addition_row_2 div.center').html('');
        
        if(user_type != 1){
            $('.grades_ul').slideDown(500);
            
            add_html_1 += '<input placeholder="' + (user_type == 4 ? "Student " : "") + 'Section" class="f5 email_txt" id="sections" name="sections" value="" type="text" maxlength="25" />';
            
            if(user_type == 2) {
                add_html_1 += '<input placeholder="Class Roll NO." class="f5 email_txt" id="roll_no" name="roll_no" value="" type="text" maxlength="15" />';
                add_html_2 += '<input placeholder="Activation Code" class="f5 email_txt large" id="admission_no" name="admission_no" value="" type="text" maxlength="100" />';
            }
            else if(user_type == 3) {
                add_html_1 += '<input placeholder="Employee ID." class="f5 email_txt" id="employee_id" name="employee_id" value="" type="text" maxlength="15" />';
                add_html_2 += '<input placeholder="Contact NO." class="f5 email_txt large" id="contact_no" name="contact_no" value="" type="text" maxlength="15" />';
            }
            else {
                add_html_1 += '<input placeholder="Student ID." class="f5 email_txt" id="roll_no" name="student_id" value="" type="text" maxlength="15" />';
                add_html_2 += '<input placeholder="Contact NO." class="f5 email_txt large" id="contact_no" name="contact_no" value="" type="text" maxlength="15" />';
            }
            
        }else{
            $('.grades_ul').slideUp(500);
            
            add_html_1 += '<input placeholder="Your Batch" class="f5 email_txt large" id="batch" name="batch" value="" type="text" maxlength="15" />';
            add_html_2 += '<input placeholder="Contact NO." class="f5 email_txt large" id="contact_no" name="contact_no" value="" type="text" maxlength="15" />';
        }
        
        $('#addition_row_1 div.center').html(add_html_1);
        $('#addition_row_2 div.center').html(add_html_2);
        
    });
    
    $(document).on("click",'.btn_user_join_school',function(){
        
        $str_school_ids = $(this).attr('id');
        $ar_school_ids = $str_school_ids.split('-');
        
        $('#school_id').attr('value', $ar_school_ids[0]);
        $('#paid_school_id').attr('value', $ar_school_ids[1]);
        $('#paid_school_code').attr('value', $ar_school_ids[2]);
        
        var html_frm_reg = $('#school_join_frm_wrapper').html();
        
        $.fancybox({
            'content' : html_frm_reg,
            'width': 500,
            'height': 'auto',
            'transitionIn': 'fade',
            'transitionOut': 'fade',
            'openEffect': 'elastic',
            'openSpeed' : 350,
            'fitToView' : false,
            'autoSize' : false,
            'padding': 0,
            'margin': 0
        });
        
    });
    
    $(document).on('submit', 'form#school_join_frm', function(event) {
        
        event.preventDefault();
        
        var formData = new FormData($(this)[0]);
        var school_id = $('#school_id').val();
        var paid_school_id = $('#paid_school_id').val();
        var paid_school_code = $('#paid_school_code').val();
        
        $.ajax({
            url: $('#base_url').val() + 'join_to_school',
            type: 'POST',
            data: formData,
            dataType: 'json',
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                
                if ( data.saved == true ){
                    
                    if(data.is_approved == 1) {
                        $('button#' + school_id + ' span').text('Leave');
                        $('button#' + school_id).removeClass('btn_user_join_school');
                        $('button#' + school_id).addClass('btn_leave_school');
                    } else {
                        $('button#' + school_id).removeClass('btn_user_join_school');
                        $('button#' + school_id).addClass('processing');
                        $('button#' + school_id + ' span').text('Processing');
                    }
                    
                    $('.fancybox-close').trigger('click');
                    
                    if(paid_school_id > 0) {
                        window.location.href = $('#base_url').val() + 'paid_regiser/' + data.activaiton_code + '/' + paid_school_code;
                    }
                    
                } else {
                    
                    var err_html = '<ul class="err-list">';
                    
                    $.each(data.errors, function(i, v) {
                        err_html += '<li>' + v + '</li>';
                        $( "form#school_join_frm #" + i ).css( "border", "1px solid #DE3427" );
                    });
                    err_html += '</ul>';
                    
                    $('.err-list-wrap').html('');
                    $('.err-list-wrap').html(err_html);
                    
                    $('.fancybox-wrap').css({
                        "opacity": 0.20,
                        "background-color": "#000000"
                    });
                    
                    $('#alert-errors').show();
                    $('#alert-errors').css({'opacity': 1, 'z-index': 8031});
                    
                    return false;
                }
                
            },
            error: function(e) {
                
            }
        });
        
        return false;
        
    });
    
    $(document).on('click', '.btn_leave_school', function(event) {
        
        var school_id = $(this).attr('id');
        
        $.ajax({
            
            url     : $('#base_url').val() + 'leave_school',
            type    : 'post',
            data    : {school_id: school_id},
            dataType: 'json',
            success : function(data) {
                
                if ( data.saved == true ){
                    
                    if(data.left == true) {
                        $('button#' + school_id + ' span').text('Join In');
                        $('button#' + school_id).removeClass('btn_leave_school');
                        $('button#' + school_id).addClass('btn_user_join_school');
                    }
                    
                    $('.fancybox-close').trigger('click');
                    
                } else {
                    
                    var err_html = '<ul class="err-list">';
                    
                    $.each(data.errors, function(i, v) {
                        err_html += '<li>' + v + '</li>';
                        $( "form#school_join_frm #" + i ).css( "border", "1px solid #DE3427" );
                    });
                    err_html += '</ul>';
                    
                    $('.err-list-wrap').html('');
                    $('.err-list-wrap').html(err_html);
                    
                    $('.fancybox-wrap').css({
                        "opacity": 0.20,
                        "background-color": "#000000"
                    });
                    
                    $('#alert-errors').show();
                    $('#alert-errors').css({'opacity': 1, 'z-index': 8031});
                    
                    return false;
                }
                
            },
            error: function(e) {
                
            }
        });
        
        return false;
        
    });
    
    $(document).on("click",'#free_user_profile',function(){
        
        var html_frm_reg = $('#frm_reg').html();
        
        $.fancybox({
            'content' : html_frm_reg,
            'width': 500,
            'height': 'auto',
            'transitionIn': 'fade',
            'transitionOut': 'fade',
            'openEffect': 'elastic',
            'openSpeed' : 350,
            'fitToView' : false,
            'autoSize' : false,
            'padding': 0,
            'margin': 0
        });
    });
    
    $(document).on("click",'#pref_li',function(){
        
        var html_frm_reg = $('#tree_div').html();
        
        $.fancybox({
            'content' : html_frm_reg,
            'width': '75%',
            'height': '100%',
            'transitionIn': 'fade',
            'transitionOut': 'fade',
            'openEffect': 'elastic',
            'openSpeed' : 350,
            'fitToView' : false,
            'autoSize' : false
        }); 
    });
    
    $(document).on("click",'.word-of-the-day-swf', function(e){
        
        e.preventDefault();
        
        $.fancybox({
            'type': 'iframe',
            'href': $(this).attr('href'),
            'transitionIn': 'fade',
            'transitionOut': 'fade',
            'openEffect': 'elastic',
            'openSpeed' : 350,
            'fitToView' : true,
            'autoSize' : true
        }); 
    });
    
    $(document).on("click",'.word-of-the-day-sound-btn', function(e){
        
        e.preventDefault();
        
        var spellingbee_url = $('.word-of-the-day-sound-link').attr('href');
        var ar_spellingbee_url = spellingbee_url.split('/');
        var word = ar_spellingbee_url[ar_spellingbee_url.length - 1].split('-')[0];
        word = word.trim();
        word = word.toLowerCase();
        word = word.replace(' ', '+');
        
        $.fancybox({
            'width': 300,
            'height': 100,
            'type': 'iframe',
            'href': 'http://translate.google.com/translate_tts?q=' + word + '&tl=en',
            'transitionIn': 'fade',
            'transitionOut': 'fade',
            'openEffect': 'elastic',
            'openSpeed' : 350,
        }); 
    });
    
    $(document).on('submit', 'form#pref_frm', function(event) {
        
        event.preventDefault();
        
        var formData = new FormData($(this)[0]);
        
        $.ajax({
            url: $('#base_url').val() + 'set_preference',
            type: 'POST',
            data: formData,
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                
                if(data == -1){
                    alert('Please Login to set Preference.');
                    window.location = $('#base_url').val();
                }
                
                if(data == 1){
                    window.location.reload();
                }
                
                if(data == 0){
                    alert('Preference could be saved at the moment.');
                    window.location.reload();
                }
                
            },
            error: function(e) {
                
            }
        });
        
        return false;
        
    });
    
    $('.profile_image').liteUploader({
        script: $("#base_url").val() + "home/upload_profile_image",
        params: {
            tds_csrf: $('input[name$="tds_csrf"]').val()
        }
    }).on('lu:success', function(e, response) {
        $("#prograssbar").hide("slow");
        if (response == 0)
        {
            alert("Invalid file type");
        }
        else
        {
            $("#profile_image_div").attr('style', '');
            $("#profile_image_div").attr('style', 'float: left; width: 46px; height: 44px; border-radius: 28px; -webkit-border-radius: 28px; -moz-border-radius: 28px; background: url(' + response + ') no-repeat; background-position: 50%;');
        }

    }).on('lu:progress', function(e, percentage) {
        $("#prograssbar").show();
        $("#prograssbar").css("width", percentage + "%");
        $("#prograssbar").html(percentage + "%");
    });
    
    $(document).on('submit', 'form#reg_frm', function(event) {
        
        event.preventDefault();
        
        var formData = new FormData($(this)[0]);
        
        $( "#reg_frm input:text, input:password").css( "border", "1px solid #d9dbdc" );
        
        $.ajax({
            url: $('#base_url').val() + 'register_user',
            type: 'POST',
            data: formData,
            dataType: 'json',
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                
                if( (data.registered == true && data.logged_in == true) ) {
                    window.location.reload();
                    first_register = 1;
                }else{
                    
                    var err_html = '<ul class="err-list">';
                    
                    $.each(data.errors, function(i, v) {
                        err_html += '<li>' + v + '</li>';
                        $( "#reg_frm #" + i ).css( "border", "1px solid #DE3427" );
                    });
                    err_html += '</ul>';
                    
                    $('.err-list-wrap').html('');
                    $('.err-list-wrap').html(err_html);
                    
                    $('.fancybox-wrap').css({
                        "opacity": 0.20,
                        "background-color": "#000000"
                    });
                    
                    $('#alert-errors').show();
                    $('#alert-errors').css({'opacity': 1, 'z-index': 8031});
                    
                    return false;
                }
            },
            error: function(e) {
                
            }
        });
        
        return false; 
    });
    
    $(document).on('submit', 'form#frm_contact_us', function(event) {
        
        event.preventDefault();
        
        var formData = new FormData($(this)[0]);
        
        $.ajax({
            url: $('#base_url').val() + 'contact-us',
            type: 'POST',
            data: formData,
            dataType: 'json',
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                
                var exclamation = 'Sorry!';
                
                if(data.saved == true) {
                    exclamation = 'Thank you for your time!';
                    $('#frm_contact_us')[0].reset();
                    $( "#frm_contact_us input:text, textarea").css( "border", "1px solid #d9dbdc" );
                }else{
                    $( "#frm_contact_us input:text, textarea").css( "border", "1px solid #DB3434" );
                }
                
                $('.alert-header').html('');
                $('.alert-header').html(exclamation);
                
                var err_html = '<ul class="err-list">';

                $.each(data.errors, function(i, v) {
                    err_html += '<li>' + v + '</li>';
                    $( "#reg_frm #" + i ).css( "border", "1px solid #DE3427" );
                });
                err_html += '</ul>';

                $('.err-list-wrap').html('');
                $('.err-list-wrap').html(err_html);
                
                $('#alert-errors').show();
                $('#alert-errors').css({'opacity': 1, 'z-index': 1});

                return false;
                
            },
            error: function(e) {
                console.log(e);
            }
        });
        
        return false; 
    });
    
    $(document).on('click', '.close_btn', function() {
        
        $('#alert-errors').hide();
        $('#alert-errors').css({'opacity': 0, 'z-index': 0});
        
        $('.fancybox-wrap').css({
            "opacity": 1,
            "background-color": "",
        });
        
    });
    
    $(document).on('submit', 'form#login_frm', function(event) {
        
        event.preventDefault();
        
        var formData = new FormData($(this)[0]);
        
        $( "#login_frm input:text, input:password").css( "border", "1px solid #d9dbdc" );
        
        $.ajax({
            url: $('#base_url').val() + 'login_user',
            type: 'POST',
            data: formData,
            dataType: 'json',
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                
                if( data.logged_in == true ) {
                    window.location.reload();
                }else{
                    
                    var err_html = '<ul class="err-list">';

                    $.each(data.errors, function(i, v) {
                        err_html += '<li>' + v + '</li>';
                        $("#login_frm input:text, input:password").css( "border", "1px solid #DE3427" );
                    });

                    err_html += '</ul>';

                    $('.err-list-wrap').html('');
                    $('.err-list-wrap').html(err_html);

                    $('.fancybox-wrap').css({
                        "opacity": 0.20,
                        "background-color": "#000000",
                    });

                    $('#alert-errors').show();
                    $('#alert-errors').css({'opacity': 1, 'z-index': 8031});

                    return false;
                }
                
            },
            error: function(e) {
                
            }
        });
        
        return false;
    });
    
    $(document).on('submit', 'form#update_profile_frm', function(event) {
        
        event.preventDefault();
        var formData = new FormData($(this)[0]);
        
        $.ajax({
            url: $('#base_url').val() + 'update_profile',
            type: 'POST',
            data: formData,
            dataType: 'json',
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                
                $.each(data, function(i, v) {
                    
                    if(i == 'errors') {
                        alert(v);
                    }
                    else if( (i == 'success') && (v == true) ) {
                        alert('Profile Seccessfully Updated.');
                        window.location.reload();
                    }else{
                        alert('Login or Register Please.');
                        window.location.reload();
                    }
                    
                });
                
            },
            error: function(e) {
                
            }
        });
        
        return false;
        
    });
    
    $(document).on("click",'.login-user',function(){
        
        var html_frm_login = $('#frm_login').html();
        
        $.fancybox({
            'content' : html_frm_login,
            'width': '30%',
            'height': "auto",
            'transitionIn': 'fade',
            'transitionOut': 'fade',
            'openEffect': 'elastic',
            'openSpeed' : 350,
            'fitToView' : false,
            'autoSize' : false
        });
        
    });
    
    $(document).on("click",'.google-reg-btn',function(){
        
        g_call_counter = 0;
        
        var params = {
            'clientid' : clientId,
            'cookiepolicy' : $("#base_url").val(),
            'callback' : 'googleSignInCallback',
            'scope' : scopes,
        };
        
        gapi.auth.signIn(params);
        return false;
    });
    
    $(document).on("click",'.google-login-btn',function(){
        
        g_call_counter = 0;
        
        var params = {
            'clientid' : clientId,
            'cookiepolicy' : $("#base_url").val(),
            'callback' : 'googleLoginCallback',
            'scope' : scopes,
        };
        
        gapi.auth.signIn(params);
        return false;
    });
    
    $(document).on("click",'.fb-reg-btn',function(){
        
        f_call_counter = 0;
        
        FB.login(function(response) {
            if (response.authResponse) {
                FB.api('/me', function(userInfo) {
                    FB.api(
                        "/me/picture",
                        {
                            "redirect": false,
                            "height": "200",
                            "type": "normal",
                            "width": "200"
                        },
                        function (profileImage) {
                            if (profileImage && !profileImage.error) {
                                processAndSaveFbInfo(userInfo, profileImage);
                            }
                        }
                    );
                    
                    //console.log('Good to see you, ' + userInfo.name + '.');
                });
            } else {
                console.log('User cancelled login or did not fully authorize.');
            }
        // handle the response
        },{scope: 'email, user_location, public_profile, user_birthday'});
        
        return false;
        
    });
    
    $(document).on("click",'.fb-login-btn',function(){
        
        f_call_counter = 0;
        
        FB.login(function(response) {
            if (response.authResponse) {
                FB.api('/me', function(userInfo) {
                    
                    FB.api(
                        "/me/picture",
                        {
                            "redirect": false,
                            "height": "200",
                            "type": "normal",
                            "width": "200"
                        },
                        function (profileImage) {
                            if (profileImage && !profileImage.error) {
                                processAndLoginFb(userInfo, profileImage);
                            }
                        }
                    );
                        
                    //console.log('Good to see you, ' + userInfo.name + '.');
                });
            } else {
                console.log('User cancelled login or did not fully authorize.');
            }
        // handle the response
        },{scope: 'email, public_profile'});
        
        return false;
        
    });
    
    $('#profile_image').change(function(){
        $('#frm_profile_image').submit();
    });

    $(document).on("click",'.toolbar',function(){
        if ( $(this).hasClass('ad') )
        {
            return ;
        }
        if ( $(this).hasClass('no_toolbar') )
        {
            return ;
        }
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
    
//    $(document).on("mouseenter",".imgLiquidFill",function(){
//        $(this).find(".tools-gallery").slideDown('slow');
//        $(this).find(".caption_img").slideDown('slow');
//    });
//    
//    $(document).on("mouseleave",".imgLiquidFill",function(){
//        $(this).find(".tools-gallery").slideUp('slow');
//        $(this).find(".caption_img").slideUp('slow');
//    });
//    
    var div_class = "";
    var height_array = {
                        'carrosel-news'                 : 390,
                        'main_news_div'                 : 400,
                        'main-news-overlay'             : 270,
                        'cat-topics'                    : 240,
                        'other-topics-part-one'         : 250, 
                        'other-topics-part-two'         : 160,
                        'topics_content'                : 270,
                        'inner-main-story'              : 544,
                        'inner-topics'                  : 540,
                        'inner-other-topics-container'  : 1000,
                        'more_contain'                  : 200
    };
    
    $(".contents-news").each(function(){
        div_class = $(this).parent().attr('class');
        var div_height = 0;
        if ( div_class.indexOf(" ") != -1 )
        {
            var ar_div_class = div_class.split(" ");    
            for( var i=0; i<ar_div_class.length; i++ )
            {
                div_height = ar_div_class[i].findIn(height_array);
                if ( div_height != false )
                {
                    div_class = ar_div_class[i];
                    break;
                }
            }
        }
        $("." + div_class + " img").addClass('toolbar');
        $("." + div_class + " img").css('cursor','pointer');
        $(".play_icon").removeClass("toolbar");
    });
    
    $(".news").each(function(){
        div_class = $(this).parent().attr('class');
        var div_height = 0;
        if ( div_class.indexOf(" ") != -1 )
        {
            var ar_div_class = div_class.split(" ");    
            for( var i=0; i<ar_div_class.length; i++ )
            {
                div_height = ar_div_class[i].findIn(height_array);
                if ( div_height != false )
                {
                    div_class = ar_div_class[i];
                    break;
                }
            }
        }
        $("." + div_class + " img").addClass('toolbar');
        $("." + div_class + " img").css('cursor','pointer');
        $(".play_icon").removeClass("toolbar");
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
    
    $("#nav").outerWidth($(".container").outerWidth());
    
    var timeout = setTimeout(function() {
        $("img.lazy-load-ad").trigger("add_lazy")
    }, 500);
    
    $(document).off("click","#settings_div").on("click","#settings_div", function(event){
        
        if( !$('.settings-elm-holder-div').is(':visible') ){
            $('.settings-elm-holder-div').slideDown(500);
            $('.settings-btn').addClass('settings-btn-active');
        }else{
            $('.settings-elm-holder-div').slideUp(500);
            $('.settings-btn').removeClass('settings-btn-active');
        }
        
    });
    
    $('html').click(function() {
        
        if( $('.settings-elm-holder-div').is(':visible') ){
            $('.settings-elm-holder-div').slideUp(500);
            $('.settings-btn').removeClass('settings-btn-active');
        }
    
    });
    
});

function googleSignInCallback(authResult){
    if (authResult['status']['signed_in']) {
    // Update the app to reflect a signed in user
    // Hide the sign-in button now that the user is authorized, for example:
        makeApiCall();
    } else {
        // Update the app to reflect a signed out user
        // Possible error values:
        //   "user_signed_out" - User is signed-out
        //   "access_denied" - User denied access to your app
        //   "immediate_failed" - Could not automatically log in the user
        console.log('Sign-in state: ' + authResult['error']);
    }
}

function makeApiCall() {
  gapi.client.load('plus', 'v1', function() {
      var request = gapi.client.plus.people.get({'userId': 'me'});
      request.execute(function(resp) {
          processAndSaveGoogleInfo(resp);
      });
    });
}

function processAndSaveGoogleInfo(resp){
    
    var primaryEmail;
    for (var i=0; i < resp.emails.length; i++) {
      if (resp.emails[i].type === 'account'){
          primaryEmail = resp.emails[i].value;
      }
    }
    
    var image_url = resp.image.url;
    image_url = image_url.substring(0, image_url.lastIndexOf("?")) + '?sz=250';
    
    var data = {
        id              : resp.id,
        email           : primaryEmail,
        nick_name       : resp.name.givenName,
        first_name      : resp.name.givenName,
        last_name       : resp.name.familyName,
        gender          : resp.gender,
        profile_url     : resp.url,
        profile_image   : image_url,
        source          : 'g'
    };
    
    if(g_call_counter < 1){
        $.ajax({
            url     : $('#base_url').val() + 'register_user',
            type    : 'post',
            data    : {data: data, tds_csrf: $('input[name$="tds_csrf"]').val()},
            dataType: 'json',
            success : function(data){
                
                g_call_counter = 1;
                
                if( (data.registered == true && data.logged_in == true) ) {
                    window.location.reload();
                }else{
                    
                    var err_html = '<ul class="err-list">';
                    
                    $.each(data.errors, function(i, v) {
                        err_html += '<li>' + v + '</li>';
                        $( "#reg_frm #" + i ).css( "border", "1px solid #DE3427" );
                    });
                    err_html += '</ul>';
                    
                    $('.err-list-wrap').html('');
                    $('.err-list-wrap').html(err_html);
                    
                    $('.fancybox-wrap').css({
                        "opacity": 0.20,
                        "background-color": "#000000"
                    });
                    
                    $('#alert-errors').show();
                    $('#alert-errors').css({'opacity': 1, 'z-index': 8031});
                    
                    return false;
                }
            },
            error   : function(e){
                console.log(e);
            }
        });
    }
    
}

function googleLoginCallback(authResult){
    if (authResult['status']['signed_in']) {
    // Update the app to reflect a signed in user
    // Hide the sign-in button now that the user is authorized, for example:
        makeApiCallLogin();
    } else {
        // Update the app to reflect a signed out user
        // Possible error values:
        //   "user_signed_out" - User is signed-out
        //   "access_denied" - User denied access to your app
        //   "immediate_failed" - Could not automatically log in the user
        console.log('Sign-in state: ' + authResult['error']);
    }
}

function makeApiCallLogin() {
  gapi.client.load('plus', 'v1', function() {
      var request = gapi.client.plus.people.get({'userId': 'me'});
      request.execute(function(resp) {
          processGoogleLogin(resp);
      });
    });
}

function processGoogleLogin(resp){
    
    var primaryEmail;
    for (var i=0; i < resp.emails.length; i++) {
      if (resp.emails[i].type === 'account'){
          primaryEmail = resp.emails[i].value;
      }
    }
    
    var data = {
        id              : resp.id,
        email           : primaryEmail,
        source          : 'g'
    };
    
    if(g_call_counter < 1){
        $.ajax({
            url     : $('#base_url').val() + 'login_user',
            type    : 'post',
            data    : {data: data, tds_csrf: $('input[name$="tds_csrf"]').val()},
            dataType: 'json',
            success : function(data){
                
                g_call_counter = 1;
                
                if( (data.logged_in == true) ) {
                    window.location.reload();
                }else{
                    
                    if(data.errors.login == 'unregistered') {
                        
                        g_call_counter = 0;
                        processAndSaveGoogleInfo(resp);
                        
                    }else {
                        var err_html = '<ul class="err-list">';
                        $.each(data.errors, function(i, v) {
                            err_html += '<li>' + v + '</li>';
                        });
                        err_html += '</ul>';

                        $('.err-list-wrap').html('');
                        $('.err-list-wrap').html(err_html);

                        $('.fancybox-wrap').css({
                            "opacity": 0.20,
                            "background-color": "#000000"
                        });

                        $('#alert-errors').show();
                        $('#alert-errors').css({'opacity': 1, 'z-index': 8031});

                        return false;
                    }
                    
                }
                
            },
            error   : function(e){
                console.log(e);
            }
        });
    }
}

function processAndSaveFbInfo(resp, profileImage){
    
    var str_location = '';
    var locations = '';
    var district = '';
    var country = '';
    
    if(resp.location != undefined){
        str_location = resp.location.name;
        locations = str_location.split(',');

        district = locations[0];
        country = locations[1];
    }
    
    var data = {
        id              : resp.id,
        email           : resp.email,
        nick_name       : resp.first_name,
        first_name      : resp.first_name,
        last_name       : resp.last_name,
        gender          : resp.gender,
        dob             : resp.birthday,
        profile_url     : resp.link,
        district        : district,
        country         : country,
        location        : str_location,
        profile_image   : profileImage.data.url,
        source          : 'f'
    };
    
    if(f_call_counter < 1){
        $.ajax({
            url     : $('#base_url').val() + 'register_user',
            type    : 'post',
            data    : {data: data, tds_csrf: $('input[name$="tds_csrf"]').val()},
            dataType: 'json',
            success : function(data){
                f_call_counter = 1;
                
                if( (data.registered == true && data.logged_in == true) ) {
                    window.location.reload();
                }else{
                    
                    var err_html = '<ul class="err-list">';
                    
                    $.each(data.errors, function(i, v) {
                        err_html += '<li>' + v + '</li>';
                        $( "#reg_frm #" + i ).css( "border", "1px solid #DE3427" );
                    });
                    err_html += '</ul>';
                    
                    $('.err-list-wrap').html('');
                    $('.err-list-wrap').html(err_html);
                    
                    $('.fancybox-wrap').css({
                        "opacity": 0.20,
                        "background-color": "#000000"
                    });
                    
                    $('#alert-errors').show();
                    $('#alert-errors').css({'opacity': 1, 'z-index': 8031});
                    
                    return false;
                }
                
            },
            error   : function(e){
                console.log(e);
            }
        });
    }    
}

function processAndLoginFb(resp, profileImage){
    
    var data = {
        id              : resp.id,
        email           : resp.email,
        source          : 'f'
    };
    
    if(f_call_counter < 1){
        $.ajax({
            url     : $('#base_url').val() + 'login_user',
            type    : 'post',
            data    : {data: data, tds_csrf: $('input[name$="tds_csrf"]').val()},
            dataType: 'json',
            success : function(data){
                f_call_counter = 1;
                
                if( (data.logged_in == true) ) {
                    window.location.reload();
                }else{
                    
                    if(data.errors.login == 'unregistered') {
                        
                        f_call_counter = 0;
                        
                        processAndSaveFbInfo(resp, profileImage);
                        
                    } else {
                        
                        var err_html = '<ul class="err-list">';
                        $.each(data.errors, function(i, v) {
                            err_html += '<li>' + v + '</li>';
                        });
                        err_html += '</ul>';

                        $('.err-list-wrap').html('');
                        $('.err-list-wrap').html(err_html);

                        $('.fancybox-wrap').css({
                            "opacity": 0.20,
                            "background-color": "#000000",
                        });

                        $('#alert-errors').show();
                        $('#alert-errors').css({'opacity': 1, 'z-index': 8031});

                        return false;
                    }
                    
                }
                
            },
            error   : function(e){
                console.log(e);
            }
        });
    }
}

function uploadProfilePicture(event) {
    
    var result = event.target.result;
    var fileName = document.getElementById('profile_image').files[0].name; //Should be 'picture.jpg'
    
    /*$.ajax({
            url     : $('#base_url').val() + 'upload_profile_image',
            type    : 'post',
            data    : {data: result, name: fileName, tds_csrf: $('input[name$="tds_csrf"]').val()},
            success : function(data){
                console.log(data);
            },
            error   : function(e){
                console.log(e);
            }
        }); */
    
    $.post($('#base_url').val() + 'upload_profile_image', {data: result, name: fileName, tds_csrf: $('input[name$="tds_csrf"]').val()}, function(data){
        console.log(data);
    });
}

function getPostData(){
    
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
                        url: $("#base_url").val() + 'front/ajax/getPosts/' + $("#category").val() + "/" + $("#target").val() + "/" + $("#page").val() + "/" + $("#page-limit").val() + "/" + page_to_load,
                        data: {
                            content_showed:content_showed, 
                            s: q
                        },
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
}
