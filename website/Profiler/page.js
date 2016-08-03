jQuery(window).load(function() {
    jQuery('div.loadering').remove();
	jQuery(document).find('body').addClass('loaded');
   jQuery('#timeline, #container, #portfolio').animate({
     opacity:1
   });
});
