$(function(){
    
    if($("#weekly").length>0)
    {
        var extra_link = "";
        if ( $("#archive").length != 0 )
        {
             extra_link = "?archive=" + $("#archive").val();
        }
        $.get($("#base_url").val()+'front/weekly/getWeekly'+ "/" + extra_link, '', function(data, status){
            $('#weekly').html(data);
            $('#weekly').children().hide();
            var imagesLoaded = 0; 
            var imageCount = $(".weekly_images").length;
            
            //alert(imageCount);
            var all_image_shown = false;
        
            var cntInt = 0;
        
            $(".weekly_images").each(function(){
                $(this).load(function(){
                    $(this).parent().parent().show();
                    ++imagesLoaded;
                });
            });
            $(".weekly_images").each(function(){
                $(this).error(function(){
                    ++imagesLoaded;
                });
            });
            
            var imgLoad = setInterval(function(){  
            
                if (imagesLoaded >= imageCount)
                {
                    clearInterval(imgLoad);
                    $("#loader_image_tech").hide();
                    var right_height = $(".category-news-two").outerHeight();
                    var left_height = $(".category-news-one").outerHeight();
                    if ( right_height < left_height )
                    {
                        var height_diff = left_height - (right_height + 1);
                        $(".pool").height( $(".pool").height() + height_diff);
                    }
                }
            },100);   
        });
        $(document).on("mouseenter", ".tech", function()
        { 
            $(this).find(".caption").slideDown('slow');
        });
     
        $(document).on("mouseleave", ".tech", function()
        {      
            $(this).find(".caption").slideUp('slow');       
    
        });
    }
	
});