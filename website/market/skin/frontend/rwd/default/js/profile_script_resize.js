
function resizefunction(sidebar, menu){

    jQuery(window).resize(function() {

        // Stuff in here happens on ready and resize.

        var win_width = jQuery('body').width();
        var win_height = jQuery(window).height();
        jQuery('#tz-main .tz-content-wrap').css({
            'min-height': win_height + 'px'
        });
        var sibar_left = jQuery('#sidebar-left').hasClass('left-sidebar');
        var sibar_right = jQuery('#sidebar-right').hasClass('right-sidebar');

        var sidebar_width = sidebar;

        var menu_width = menu;

        var left_width = parseInt(sidebar_width) + parseInt(menu_width);

        var page_width = jQuery('.tz-main-body .container-fluid').width();
        if (sibar_left == false || sibar_right == false) {
            var content_width = page_width - sidebar_width - menu_width;
        }
        if (sibar_left == true && sibar_right == true) {
            var content_width = page_width - sidebar_width - menu_width - sidebar_width;
        }
        var left_position = parseInt(sidebar_width) + parseInt(menu_width) + parseInt(content_width);
        if (sibar_right == true) {
            jQuery('#tz-main #sidebar-right').css({
                'width': sidebar_width + 'px'
            });
        }
        jQuery('#tz-content').css({
            'margin-left': left_width + 'px',
            'width': content_width + 'px'
        });
        if (sibar_left == true) {
            jQuery('#tz-main #sidebar-left').css({
                'margin-left': -left_position + 'px',
                'width': sidebar_width + 'px'
            });
        }

        jQuery('#tz_mainmenu').css({
            'top': 0,
            'margin-left': 0 + 'px',
            'width': menu_width + 'px'
        });

        jQuery('#tz_mainmenu').tinyscrollbar();

        if (win_width < 767) {
            jQuery('span.mobile-open').click(function () {

                jQuery('#sidebar-left').slideDown();
                jQuery(this).hide();
                jQuery('span.mobile-close').show();
                jQuery('.mobile-header').addClass('mobile-active');
                jQuery('#tz-main #tz_mainmenu nav.plazart-mainnav .navbar-inner .btn-navbar i').css('color', '#fff');
            });
            jQuery('span.mobile-close').click(function () {
                jQuery('#sidebar-left').slideUp();
                jQuery(this).hide();
                jQuery('span.mobile-open').show();
                jQuery('.mobile-header').removeClass('mobile-active');
                jQuery('#tz-main #tz_mainmenu nav.plazart-mainnav .navbar-inner .btn-navbar i').css('color', '#414952');
            });
        }

        if (win_width > 767 && win_width <=1024) {
            var left_html = jQuery('#sidebar-left').html();
            jQuery('body').append('<section class="left-sidebar  tablet-sidebar" id="sidebar-left">' +  left_html + '</section>');
            jQuery('#sidebar-left').remove();
            var menu_width = menu;
            var content_width = win_width - menu_width;
            jQuery('#tz-main #tz-content, #tz-main #sidebar-right ').css({
                'margin-left': menu_width + 'px',
                'width': content_width + 'px'
            });

            jQuery('#tz-main #tz_mainmenu').css({
                'margin-left': 0
            });

            jQuery('html').addClass('body_open');
            jQuery('.btn_open_sidebar').click(function(){

                jQuery('body > *').css({
                    '-webkit-transform': 'translateX(210px)',
                    '-moz-transform': 'translateX(210px)',
                    '-o-transform': 'translateX(210px)',
                    'transform': 'translateX(210px)'
                });

                jQuery(this).hide();
                jQuery('.btn_close_sidebar').fadeIn();
            });
            jQuery('.btn_close_sidebar').click(function(){


                jQuery('body > *').css({
                    '-webkit-transform': 'none',
                    '-moz-transform': 'none',
                    '-o-transform': 'none',
                    'transform': 'none'
                });

                jQuery(this).hide();
                jQuery('.btn_open_sidebar').fadeIn();
            });


        }


    });

}