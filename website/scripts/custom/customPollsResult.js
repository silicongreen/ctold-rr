/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function() {
    
    var loader=$('#loader');
    var pollcontainer=$('#pollcontainer');
    loader.fadeIn();
    
    $.post($("#base_url").val()+"admin/polls/showresults/"+$("#id").val(), {
        tds_csrf: $('input[name$="tds_csrf"]').val()
        })
    .done(function(data) {
        pollcontainer.fadeOut(1000, function(){
            $("#pollcontainer").html(data);
            animateResults("#pollcontainer");
        });
        loader.fadeOut();
    });
    
    function animateResults(data)
    {
        $(data).find('.bar').hide().end().fadeIn('slow', function(){
            $(this).find('.bar').each(function(){
               
                var bar_width=$(this).css('width');
                $(this).show();
                $(this).css('width', '0').animate({
                    width: bar_width
                }, 1000);
            });
        });
    }
   
 
});
