$(function(){
    if($('#pollcontainer').length>0)
    {
        var loader=$('#loader');
        var pollcontainer=$('#pollcontainer');
        loader.fadeIn();
        //Load the poll form
        $.get($("#base_url").val()+'front/poll/getpoll', '', function(data, status){
            pollcontainer.html(data);
            animateResults(pollcontainer);
            pollcontainer.find('#viewresult').click(function(){
                //if user wants to see result
                loader.fadeIn();
                $.get('poll.php', 'result=1', function(data,status){
                    pollcontainer.fadeOut(1000, function(){
                    
                        $(this).html(data);
                        animateResults(this);
                    });
                    loader.fadeOut();
                });
                //prevent default behavior
                return false;
            }).end()
            .find('#button_vote').click(function(){
                var selected_val=$('#pollform').find('input[name=poll]:checked').val();
            
                if(selected_val!='' && typeof selected_val != 'undefined'){
                    //post data only if a value is selected
                    loader.fadeIn();
                
                    $.post($("#base_url").val()+'front/poll/getpoll', $('#pollform').serialize(), function(data, status){
                        $('#formcontainer').fadeOut(100, function(){
                            $('#pollform').html(data);
                       
                            animateResults(this);
                            loader.fadeOut();
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
                            }
                        });
                    });
                }
                //prevent form default behavior
                return false;
            });
            loader.fadeOut();
        });
    }
    
    function rearrangeRightHeight(rheight, lheight)
    {
        
        var h;
        var height_array;
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
           
            if ( cat_height < image_height )
            {
                cat_height = image_height + 60;
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
            rearrangeRightHeight(left_height, left_height);
        }
        
        $(".category-news-one .contents-news").each(function(){
            $(this).dotdotdot({
                after: 'a.more'
            });
        });
    }

    function animateResults(data){
      
        $(data).find('.bar').hide().end().fadeIn('slow', function(){
            $(this).find('.bar').each(function(){
               
                var bar_width=$(this).width();
                //var width = $('#someElt').width();
                var parentWidth = $(this).offsetParent().width();
                var percent = Math.round(100*bar_width/parentWidth);
                
                $(this).show();
                $(this).css('width', '0').animate({
                    width: percent+"%"
                }, 1000);
            });
        });
    }
	
});