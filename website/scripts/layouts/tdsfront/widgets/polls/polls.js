$(function(){
    if($('#pollcontainer').length>0)
    {
        var loader=$('#loader');
        var pollcontainer=$('#pollcontainer');
        loader.fadeIn();
        //Load the poll form
        var extra_link = "";
        if ( $("#archive").length != 0 )
        {
             extra_link = "?archive=" + $("#archive").val();
        }
        $.get($("#base_url").val()+'front/poll/getpoll' + extra_link, '', function(data, status){
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
                            if ( right_height < left_height )
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
    
    function animateResults(data){
      
        $(data).find('.bar').hide().end().fadeIn('slow', function(){
            $(this).find('.bar').each(function(){
               
                var bar_width=$(this).width();
                
                
                
                //var width = $('#someElt').width();
                var parentWidth = $(this).parent().outerWidth();
                
                
                var percent = Math.round(100*bar_width/parentWidth);
              
                
                $(this).show();
                $(this).css('width', '0').animate({
                    width: percent+"%"
                }, 1000);
            });
        });
    }
	
});







//
//
//$(function(){
//    if($('#pollcontainer').length>0)
//    {
//        var loader=$('#loader');
//        var pollcontainer=$('#pollcontainer');
//        loader.fadeIn();
//        //Load the poll form
//        var extra_link = "";
//        if ( $("#archive").length != 0 )
//        {
//             extra_link = "?archive=" + $("#archive").val();
//        }
//        
//        $('#button_vote').click(function(){
//                var selected_val=$('#pollform').find('input[name=poll]:checked').val();
//            
//                if(selected_val!='' && typeof selected_val != 'undefined'){
//                    //post data only if a value is selected
//                    loader.fadeIn();
//                
//                    $.post($("#base_url").val()+'front/poll/getpoll', $('#pollform').serialize(), function(data, status){
//                        $('#formcontainer').fadeOut(100, function(){
//                            $('#pollform').html(data);
//                       
//                            animateResults(this);
//                            loader.fadeOut();
//                            
//                            var right_height = $(".category-news-two").outerHeight();
//                            var left_height = $(".category-news-one").outerHeight();
//                            if ( right_height < left_height )
//                            {
//                                var height_diff = left_height - (right_height + 1);
//                                $(".pool").height( $(".pool").height() + height_diff);
//                            }
//                        });
//                    });
//                }
//        });
//            
////        $.get($("#base_url").val()+'front/poll/getpoll' + extra_link, '', function(data, status){
////            pollcontainer.html(data);
////            animateResults(pollcontainer);
////            pollcontainer.find('#viewresult').click(function(){
////                //if user wants to see result
////                loader.fadeIn();
////                $.get('poll.php', 'result=1', function(data,status){
////                    pollcontainer.fadeOut(1000, function(){
////                    
////                        $(this).html(data);
////                        animateResults(this);
////                    });
////                    loader.fadeOut();
////                });
////                //prevent default behavior
////                return false;
////            }).end()
////            .find('#button_vote').click(function(){
////                var selected_val=$('#pollform').find('input[name=poll]:checked').val();
////            
////                if(selected_val!='' && typeof selected_val != 'undefined'){
////                    //post data only if a value is selected
////                    loader.fadeIn();
////                
////                    $.post($("#base_url").val()+'front/poll/getpoll', $('#pollform').serialize(), function(data, status){
////                        $('#formcontainer').fadeOut(100, function(){
////                            $('#pollform').html(data);
////                       
////                            animateResults(this);
////                            loader.fadeOut();
////                            
////                            var right_height = $(".category-news-two").outerHeight();
////                            var left_height = $(".category-news-one").outerHeight();
////                            if ( right_height < left_height )
////                            {
////                                var height_diff = left_height - (right_height + 1);
////                                $(".pool").height( $(".pool").height() + height_diff);
////                            }
////                        });
////                    });
////                }
////                //prevent form default behavior
////                return false;
////            });
////            loader.fadeOut();
////        });
//    }
//    
//    function animateResults(data){
//      
//        $(data).find('.bar').hide().end().fadeIn('slow', function(){
//            $(this).find('.bar').each(function(){
//               
//                var bar_width=$(this).width();
//                
//                
//                
//                //var width = $('#someElt').width();
//                var parentWidth = $(this).parent().outerWidth();
//                
//                
//                var percent = Math.round(100*bar_width/parentWidth);
//              
//                
//                $(this).show();
//                $(this).css('width', '0').animate({
//                    width: percent+"%"
//                }, 1000);
//            });
//        });
//    }
//	
//});