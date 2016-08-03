$(function(){
    
    if($('#innermiddlenewsblock').length>0)
    { 
        $.get($("#base_url").val()+'front/ajax/get_innermiddle_news_block/'+$('#ci_key').val(), '', function(data, status){
       
            $('#innermiddlenewsblock').html(data);
            $("#loader_image_middlecontent").hide();
            var ad = $("#adsense123").html();            
            $("#googleadgoeshere").html(ad);
            
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
        
        $('.bxslider2').bxSlider({
            auto: true,
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
});