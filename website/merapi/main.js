var docElem = window.document.documentElement;
var runScrool = true;
(function () {

    if ($('.suggested-post-container #grid').length > 0) {
        $('.suggested-post-container #grid').parent('div').addClass('als-viewport');
        $('.suggested-post-container #grid').parent('div').parent('div').addClass('als-container');

        $('.suggested-post-container #grid').addClass('als-wrapper');
        $('.suggested-post-container #grid li').addClass('als-item');
        $('.suggested-post-container #grid').removeAttr('id');
        $('.suggested-post-container ul').attr('id', 'grid_1');
    }

    $(document).on('click', '#solution-button', function () {
        $('#solution-text').show('slow');
        $('#solution-p').remove();
    });
    jQuery('.flex-wrapper .flexslider').flexslider({
        slideshow: false,
        animation: 'fade',
        pauseOnHover: true,
        animationSpeed: 400,
        smoothHeight: false,
        directionNav: true,
        controlNav: false,
        after: function () {
            $("#grid").masonry('reload');
            jQuery('#tz_mainmenu').tinyscrollbar();
        }

    });
    if ($(".flex-wrapper_news").length > 0)
    {
        jQuery('.flex-wrapper_news .flexslider_news').flexslider({
            slideshow: false,
            animation: 'fade',
            pauseOnHover: true,
            animationSpeed: 400,
            smoothHeight: false,
            directionNav: false,
            selector: ".slides_news > li.news_slides",
            after: function () {
                $("#grid").masonry('reload');
                jQuery('#tz_mainmenu').tinyscrollbar();
            }
        });
    }




    window.addEventListener('scroll', function () {
        scrollPage();
    }, false);
    var $container = jQuery("[id=grid]");

    $container.imagesLoaded(function () {

        msnroy = $container.masonry({
            itemSelector: 'li.post',
            isAnimated: false,
            transitionDuration: 0,
            columnWidth: function (containerWidth) {
                return containerWidth / 6;
            },
            layoutPriorities: {
                upperPosition: 27,
                shelfOrder: 27
            }
        });

        scrollPage();


        var content_height = $(".site-main").outerHeight();


        if ($("#tz_mainmenu").length > 0)
        {
            $("#main").children("aside").children("div").height(content_height);
        }

    });
})();

function scrollPage() {

    if (runScrool)
    {
        $('#grid li').each(function (el, i) {

            if (!$(this).hasClass('shown') && !$(this).hasClass('animate') && inViewport($(this), 0)) {
                var $obj = $(this);
                setTimeout(function () {

                    var perspY = scrollY() + getViewportH() / 2;
                    var randDuration = '0.6s';
                    $obj.css({
                        WebkitAnimationDuration: randDuration,
                        MozAnimationDuration: randDuration,
                        animationDuration: randDuration
                    });
                    $obj.addClass('animate');
                    $("#grid").masonry('reload');
                }, 25);
            }
        });
    }

}

function getViewportH() {
    var client = docElem['clientHeight'],
            inner = window['innerHeight'];

    if (client < inner)
        return inner;
    else
        return client;
}

function scrollY() {
    return window.pageYOffset || docElem.scrollTop;
}



function inViewport(el, h) {
    var elH = el.height(),
            scrolled = scrollY(),
            viewed = scrolled + getViewportH(),
            elTop = el.offset().top,
            elBottom = elTop + elH,
            // if 0, the element is considered in the viewport as soon as it enters.
            // if 1, the element is considered in the viewport only when it's fully inside
            // value in percentage (1 >= h >= 0)
            h = h || 0;

    return (elTop + elH * h) <= viewed && (elBottom - elH * h) >= scrolled;
}

