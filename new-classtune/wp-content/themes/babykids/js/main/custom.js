
//=================Login======================

//jQuery(document).ready(function(){
//    jQuery("#login-button").click(function(){
//        jQuery("#login-form").slideToggle("slow");
//    });
//});




//jQuery(document).ready(function(){
//    
//    var toggleClick = function(){
//
//        var divObj = jQuery("#login-form");
//        var nstyle = divObj.css("display");
//
//        if(nstyle === "none"){
//            divObj.slideDown(false,function(){
//                jQuery("html").bind("click",function(){
//                    divObj.slideUp();
//                    jQuery("html").unbind("click");
//                });
//            });
//        }
//    };
//
//    jQuery("#login-button").click(toggleClick);
//   
//});




jQuery("#login-form").hide().click(function(){
    return false;
});
jQuery("#login-button").show().click(function(){
    jQuery("#login-form").slideToggle();
    return false;
});
jQuery(document).click(function(){
    jQuery("#login-form").slideUp();
});

//=================Login End======================


