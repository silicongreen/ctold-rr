jQuery(document).ready( function() {
    jQuery('.tzalert-icon').click(function(){
        jQuery(this).parent().slideUp();
    });

    jQuery('.tzalert2-icon').click(function(){
        jQuery(this).parent().slideUp();
    });


    //acconrdion
    var tz = jQuery.noConflict();
    tz('.tz_news .tz_accordion:first-child h3').addClass('open');
    tz('.tz_news .tz_accordion:first-child').find('.info_accordion').css('display','block');

    tz('.tz_accordion h3').click(function(){
        tz(this).parent().find('.info_accordion').slideToggle(200);
        tz(this).toggleClass('open');
    });

    // tabs

        jQuery('.Shortcode_myTab a:first').tab('show') ;


    jQuery('.Shortcode_myTab a').click(function (e) {
        e.preventDefault();
        jQuery(this).tab('show');
    });



} ) ;