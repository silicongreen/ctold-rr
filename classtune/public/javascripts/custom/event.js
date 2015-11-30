var jqss = jQuery.noConflict();
jqss(document).ready(function() {
    
    jqss(document).on('click', '.tabbed-span', function() {
        var this_tab = jqss(this);
//        $("#table-filters>ul>li.active").removeClass("active");
        this_tab.siblings().removeClass('span-active');
        this_tab.addClass('span-active');
    });
    
    jqss('.tabs-wrapper').find('span:eq(0)').trigger('click');
    jqss('.tabs-wrapper').find('span:eq(0) a').trigger('click');
    
});
