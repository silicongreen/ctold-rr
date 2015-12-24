jQuery(document).ready(function($){
    
        $('.newsletter-submit').parent().addClass('subscribe_btn');
    // for scroll top
    
        $(window).scroll(function() {
            if ($(this).scrollTop() > 200) {
                $('#go-top').fadeIn();
            } else {
                $('#go-top').fadeOut();
            }
        });
    
        // scroll body to 0px on click
        $('#go-top').click(function() {
            $('body,html').animate({
                scrollTop: 0
            }, 800);
            return false;
        });
    
    
    $('.ap_toggle_title').click(function(){
        if($(this).parent().hasClass('open')){
            $(this).next().slideUp();
            $(this).parent().removeClass('open');
            $(this).parent().addClass('close');
        }else{        
       $('.ap_toggle_content').slideUp();
       $('.ap_toggle').removeClass('open');
       $('.ap_toggle').addClass('close');
       $(this).next().slideDown();
       $(this).parent().removeClass('close');
       $(this).parent().addClass('open');
       }
    });


	$('.fa-search').toggle(function(){
    if ($(window).width()<=540){
      $('.aglee-search').css({'right': '45px', 'opacity': '100', 'z-index': '9999'});
      //$('.boxed-layout .aglee-search').css({'right': '80px', 'opacity': '100', 'z-index': '9999'});
    }
    else if( ($(window).width() >= 540) && ($(window).width() <= 640) ){ // bteween 540 - 640
       $('.aglee-search').css({'right': '45px', 'opacity': '100', 'z-index': '9999'});
      //$('.boxed-layout .aglee-search').css({'right': '80px', 'opacity': '100', 'z-index': '9999'}); 
    }
    
    else if( ($(window).width() >= 641) && ($(window).width() <= 768) ){ // between 641 - 768
        $('.aglee-search').css({'right': '45px', 'opacity': '100', 'z-index': '9999'});
      //$('.boxed-layout .aglee-search').css({'right': '80px', 'opacity': '100', 'z-index': '9999'}); 
    }
    else if( ($(window).width() >= 769) && ($(window).width() <= 800) ){ // between 769 - 1024
        $('.aglee-search').css({'right': '45px', 'opacity': '100', 'z-index': '9999'});
      //$('.boxed-layout .aglee-search').css({'right': '80px', 'opacity': '100', 'z-index': '9999'}); 
    }
    else if( ($(window).width() >= 801) && ($(window).width() <= 980) ){ // between 769 - 1024
        $('.aglee-search').css({'right': '45px', 'opacity': '100', 'z-index': '9999'});
      //$('.boxed-layout .aglee-search').css({'right': '80px', 'opacity': '100', 'z-index': '9999'}); 
    }
    else if( ($(window).width() >= 981) && ($(window).width() <= 1024) ){ // between 769 - 1024
        $('.aglee-search').css({'right': '90px', 'opacity': '100', 'z-index': '9999'});
      //$('.boxed-layout .aglee-search').css({'right': '80px', 'opacity': '100', 'z-index': '9999'}); 
    }
    else if( ($(window).width() >= 1025) && ($(window).width() <= 1280) ){ // between 1025 - 1280
        $('.aglee-search').css({'right': '105px', 'opacity': '100', 'z-index': '9999'});
      //$('.boxed-layout .aglee-search').css({'right': '80px', 'opacity': '100', 'z-index': '9999'}); 
    }
    else if( ($(window).width() >= 1281) && ($(window).width() <= 1349) ){ // between 1025 - 1280
        $('.aglee-search').css({'right': '130px', 'opacity': '100', 'z-index': '9999'});
        $('.boxed-layout .aglee-search').css({'right': '85px', 'opacity': '100', 'z-index': '9999'}); 
    }
    else if( ($(window).width() >= 1350) && ($(window).width() <= 1900) ){ // between 1025 - 1280
        $('.aglee-search').css({'right': '240px', 'opacity': '100', 'z-index': '9999'});
        $('.boxed-layout .aglee-search').css({'right': '85px', 'opacity': '100', 'z-index': '9999'}); 
    }
    else{ //( $(window).width() > 1280 ){
      $('.aglee-search').css({'right': '405px', 'opacity': '100', 'z-index': '9999'});
      $('.boxed-layout .aglee-search').css({'right': '85px', 'opacity': '100', 'z-index': '9999'});  
    }	
	},function(){
		$('.aglee-search').css({'right': '0px', 'opacity': '0', 'z-index': '-9999'});
	});


/*MENU TOGGLE*/

  $('.menu-toggle').click(function(){
    $('.menu').slideToggle(800);
  });
});