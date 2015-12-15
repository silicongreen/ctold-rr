var jq = jQuery.noConflict();
jq(document).ready(function () {
    
    jq('.nav-vertical li#employee_category a').addClass('active');

    jq(".nav-vertical li").click(function () {
        jq('.nav-vertical li a').removeClass('active');
        jq(this).find('a').addClass('active');
    });

    jq(".nav-vertical li a").click(function () {
        jq('.nav-vertical li').removeClass('active');
        jq(this).parent('li').addClass('active');
    });
});