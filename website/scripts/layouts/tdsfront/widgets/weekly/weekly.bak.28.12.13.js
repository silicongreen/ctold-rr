$(function(){
    
    if($("#weekly").length>0)
    {
        $.get($("#base_url").val()+'front/weekly/getWeekly', '', function(data, status){
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
                    if ( right_height > left_height )
                    {
                        rearrangeRightHeight(right_height + 10, left_height);
                    }
                    else if ( right_height < left_height )
                    {
                        var height_diff = left_height - (right_height + 1);
                        $(".pool").height( $(".pool").height() + height_diff);
                        rearrangeRightHeight(left_height, left_height);
                    }
                    var right_height = $(".category-news-two").outerHeight();
                    var left_height = $(".category-news-one").outerHeight();

                    if ( right_height > left_height )
                    {
                        rearrangeRightHeight(right_height + 1, left_height);
                    }
                    else if ( right_height < left_height )
                    {
                        var height_diff = left_height - (right_height + 1);
                        $(".pool").height( $(".pool").height() + height_diff);
                        rearrangeRightHeight(left_height, left_height);
                    }
                    
//                    var left_height = $(".ym-col1").outerHeight();
//                    var right_height = $(".container > .ym-column > .ym-gr").outerHeight();
//                    
//                    alert(left_height+" "+right_height);
//                    if ( left_height > right_height )
//                    {
//                       // $(".right-content").height(left_height - 17);
//                    }
//                    else
//                    {
//                        
//                        //$(".container .left").height(right_height);
//                    }
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
     
    function rearrangeRightHeight(rheight, lheight)
    {
        var h;
        var height_array = {
            'cat-topics': 260
        };
        if ( rheight > lheight )
        {
            h = (rheight - lheight) / 2;
            height_array = {
                'cat-topics': 260 + h
            };
        }
        if ( rheight < lheight )
        {
            h = (lheight - rheight) / 2;
        
            height_array = {
                'cat-topics': 260 - h
            };
        }
        $(".category-news-one .contents-news").each(function(){
            
            var title_height = $(".title", $(this).parent()).outerHeight();
            var image_height = $(".cat-images", $(this).parent()).outerHeight() + 60;
            
            var div_class = $(this).parent().attr('class');
            
            var div_height = div_class.findIn(height_array);
            
            var cat_height = div_height - title_height;
           
            if ( cat_height != image_height )
            {
                cat_height = ( image_height + 10 ) - title_height;
            }
            
            $(this).height(cat_height ); 
        });
        
        var right_height = $(".category-news-two").outerHeight();
        var left_height = $(".category-news-one").outerHeight();
        if ( right_height > left_height )
        {
            h = (right_height - left_height);
            var i = 0;
            $(".category-news-one .contents-news").each(function(){
                if ( i == 0 )
                    $(this).height( $(this).height() + h ); 
                i++;
            });
        //rearrangeRightHeight(right_height + 1, left_height);
        }
        else if ( right_height < left_height )
        {
            var height_diff = left_height - (right_height + 1);
            $(".pool").height( $(".pool").height() + height_diff);
        }
        
        $(".category-news-one .contents-news").each(function(){
            $(this).dotdotdot({
                after: 'a.more'
            });
        });
    }
	
});