$(document).ready(function(){
  
 
	$("#stars-list li").mouseover(function(event){
	  
	  $(this).find('.star-bio').show();
	});
	
	$("#stars-list li").mouseout(function(event){
		$(this).find('.star-bio').hide();
	})
	
	$("#stories-list li").mouseover(function(event){
	  
	  $(this).find('.fs-excerpt').show();
	});
	
	$("#stories-list li").mouseout(function(event){
		$(this).find('.fs-excerpt').hide();
	})	
	
	
	
	//$(window).animate({ scrollTop: 0 }, 'slow');
	$('.sscrollToTop').click(function(){
        $("html, body").animate({ scrollTop: 0 }, 600);
        return false;
        });
 });


