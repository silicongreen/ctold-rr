/**
 * Resize function without multiple trigger
 * 
 * Usage:
 * $(window).smartresize(function(){  
 *     // code here
 * });
 */
var alreadyFullScreen = false, lockSidebar = false;
var jq = jQuery.noConflict();

function locksidebar(obj)
{
    if ( ! lockSidebar )  
    {
        lockSidebar = true;
        jq(obj).addClass("selected_panel");
    }
    else
    {
        lockSidebar = false;
        jq(obj).removeClass("selected_panel");
    }
}

function launchIntoFullscreen(element) 
{
    if ( ! alreadyFullScreen )  
    {
        alreadyFullScreen = true;
        if(element.requestFullscreen) 
        {
            element.requestFullscreen();
        } 
        else if(element.mozRequestFullScreen) 
        {
            element.mozRequestFullScreen();
        } 
        else if(element.webkitRequestFullscreen) 
        {
            element.webkitRequestFullscreen();
        } 
        else if(element.msRequestFullscreen) 
        {
            element.msRequestFullscreen();
        }
    }
    else
    {
        alreadyFullScreen = false;
        if(document.exitFullscreen) 
        {
            document.exitFullscreen();
        } 
        else if(document.mozCancelFullScreen) 
        {
            document.mozCancelFullScreen();
        } 
        else if(document.webkitExitFullscreen) 
        {
            document.webkitExitFullscreen();
        }
    }
}

(function($,sr){
    // debouncing function from John Hann
    // http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
    var debounce = function (func, threshold, execAsap) {
      var timeout;

        return function debounced () {
            var obj = this, args = arguments;
            function delayed () {
                if (!execAsap)
                    func.apply(obj, args); 
                timeout = null; 
            }

            if (timeout)
                clearTimeout(timeout);
            else if (execAsap)
                func.apply(obj, args);

            timeout = setTimeout(delayed, threshold || 100); 
        };
    };

    // smartresize 
    jQuery.fn[sr] = function(fn){  return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr); };

})(jQuery,'smartresize');
/**
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var CURRENT_URL = window.location.href.split('?')[0];
if (jq(".progress .progress-bar").length > 0) {
    jq('.progress .progress-bar').progressbar();
}

var m1 = setInterval(function(){
    jq(".alert-warning").html("");
},10000);

// Sidebar

jq(document).ready(function() {
 
    // TODO: This is some kind of easy fix, maybe we can improve this
    var setContentHeight = function () {
        // reset height
        jq('.left_col').css('min-height', jq(window).height());

        var bodyHeight = jq('body').outerHeight(),
            footerHeight = jq('body').hasClass('footer_fixed') ? -10 : jq('footer').height(),
            leftColHeight = jq('.left_col').eq(1).height() + jq('.sidebar-footer').height(),
            contentHeight = bodyHeight < leftColHeight ? leftColHeight : bodyHeight;

        // normalize content
        contentHeight -= jq('.nav_menu').height() + footerHeight;

        jq('.left_col').css('min-height', contentHeight);
    };
    
    jq('.close').on('click', function(ev) {
        var data_dismiss = jq(this).data("dismiss");
        if ( typeof(data_dismiss) != 'undefined' )
        {
            if ( jq('.' + data_dismiss).length > 0 )
            {
                jq('.' + data_dismiss).remove();
            }
        }
    });
    
    jq('#sidebar-menu').find('a').on('click', function(ev) {
        var li = jq(this).parent();

        if (li.is('.active')) {
            li.removeClass('active active-sm');
            jq('ul:first', li).slideUp(function() {
                setContentHeight();
            });
        } else {
            // prevent closing menu if we are on child menu
            if (!li.parent().is('.child_menu')) {
                jq('#sidebar-menu').find('li').removeClass('active active-sm');
                jq('#sidebar-menu').find('li ul').slideUp();
            }
            
            li.addClass('active');

            jq('ul:first', li).slideDown(function() {
                setContentHeight();
            });
        }
    });

    // toggle small or large menu
    jq('#menu_toggle').on('click', function() {
        if ( ! lockSidebar )
        {
            if (jq('body').hasClass('nav-md')) {
                jq('#sidebar-menu').find('li.active ul').hide();
                jq('#sidebar-menu').find('li.active').addClass('active-sm').removeClass('active');
            } else {
                jq('#sidebar-menu').find('li.active-sm ul').show();
                jq('#sidebar-menu').find('li.active-sm').addClass('active').removeClass('active-sm');
            }

            jq('body').toggleClass('nav-md nav-sm');

            setContentHeight();
        }
    });

    // check active menu
    jq('#sidebar-menu').find('a[href="' + CURRENT_URL + '"]').parent('li').addClass('current-page');

    jq('#sidebar-menu').find('a').filter(function () {
        return this.href == CURRENT_URL;
    }).parent('li').addClass('current-page').parents('ul').slideDown(function() {
        setContentHeight();
    }).parent().addClass('active');

    // recompute content when resizing
    jq(window).smartresize(function(){  
        setContentHeight();
    });

    setContentHeight();

    // fixed sidebar
    if (jq.fn.mCustomScrollbar) {
        jq('.menu_fixed').mCustomScrollbar({
            autoHideScrollbar: true,
            theme: 'minimal',
            mouseWheel:{ preventDefault: true }
        });
    }
});
// /Sidebar

jq(document).ready(function() {
    if (jq("input.flat")[0]) {
        jq(document).ready(function () {
            jq('input.flat').iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'
            });
        }).on('ifChanged', function(e) {
            var isChecked = e.target.checked;
            
            if (isChecked == true) {
                var classname = e.target.className;
                if (classname.indexOf('select_package') > -1)
                {
                    var id = e.target.id;
                    jq(".university_modules").removeAttr('checked');
                    var modules_id = id.replace('university_','');
                    var modules_name = jq("#" + modules_id + "_modules").val();
                    
                    var menu_name = jq("#" + modules_id + "_menu").val();
                    var ar_modules_name = modules_name.split(",");
                    
                    jq("#menus").val(menu_name);
                    for( var i=0; i< ar_modules_name.length; i++)
                    {
                      jq("#university_" + ar_modules_name[i]).prop("checked",true);
                    }
                }
            }
            else
            {
                var classname = e.target.className;
                if (classname.indexOf('select_package') > -1)
                {
                    var id = e.target.id;
                    jq(".university_modules").removeAttr('checked');
                    var modules_id = id.replace('university_','');
                    var modules_name = jq("#" + modules_id + "_modules").val();
                    
                    var menu_name = jq("#" + modules_id + "_menu").val();
                    var ar_modules_name = modules_name.split(",");
                    jq("#menus").val("");
                    for( var i=0; i< ar_modules_name.length; i++)
                    {
                        jq("#university_" + ar_modules_name[i]).removeAttr("checked");
                    }
                }
            }
        });
    }
});

// Panel toolbox
jq(document).ready(function() {
    jq('.collapse-link').on('click', function() {
        var BOX_PANEL = jq(this).closest('.x_panel'),
            ICON = jq(this).find('i'),
            BOX_CONTENT = BOX_PANEL.find('.x_content');
        
        // fix for some div with hardcoded fix class
        if (BOX_PANEL.attr('style')) {
            BOX_CONTENT.slideToggle(200, function(){
                BOX_PANEL.removeAttr('style');
            });
        } else {
            BOX_CONTENT.slideToggle(200); 
            BOX_PANEL.css('height', 'auto');  
        }

        ICON.toggleClass('fa-chevron-up fa-chevron-down');
    });

    jq('.close-link').click(function () {
        var BOX_PANEL = jq(this).closest('.x_panel');

        BOX_PANEL.remove();
    });
});
// /Panel toolbox

// Tooltip
jq(document).ready(function() {
    jq('[data-toggle="tooltip"]').tooltip({
        container: 'body'
    });
});
// /Tooltip

// Progressbar
if (jq(".progress .progress-bar")[0]) {
    jq('.progress .progress-bar').progressbar();
}
// /Progressbar

// Switchery
jq(document).ready(function() {
    if (jq(".js-switch")[0]) {
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        elems.forEach(function (html) {
            var switchery = new Switchery(html, {
                color: '#26B99A'
            });
        });
    }
});
// /Switchery

// iCheck
jq(document).ready(function() {
    if (jq("input.flat")[0]) {
        jq(document).ready(function () {
            jq('input.flat').iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'
            });
        });
    }
});

jq(document).ready(function() {
    if (jq("input.flatpackage")[0]) {
        jq(document).ready(function () {
            jq('input.flatpackage').iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'
            });
        });
    }
});
// /iCheck

// Table
jq('table input').on('ifChecked', function () {
    checkState = '';
    jq(this).parent().parent().parent().addClass('selected');
    countChecked();
});
jq('table input').on('ifUnchecked', function () {
    checkState = '';
    jq(this).parent().parent().parent().removeClass('selected');
    countChecked();
});

var checkState = '';

jq('.bulk_action input').on('ifChecked', function () {
    checkState = '';
    jq(this).parent().parent().parent().addClass('selected');
    countChecked();
});
jq('.bulk_action input').on('ifUnchecked', function () {
    checkState = '';
    jq(this).parent().parent().parent().removeClass('selected');
    countChecked();
});
jq('.bulk_action input#check-all').on('ifChecked', function () {
    checkState = 'all';
    countChecked();
});
jq('.bulk_action input#check-all').on('ifUnchecked', function () {
    checkState = 'none';
    countChecked();
});

function countChecked() {
    if (checkState === 'all') {
        jq(".bulk_action input[name='table_records']").iCheck('check');
    }
    if (checkState === 'none') {
        jq(".bulk_action input[name='table_records']").iCheck('uncheck');
    }

    var checkCount = jq(".bulk_action input[name='table_records']:checked").length;

    if (checkCount) {
        jq('.column-title').hide();
        jq('.bulk-actions').show();
        jq('.action-cnt').html(checkCount + ' Records Selected');
    } else {
        jq('.column-title').show();
        jq('.bulk-actions').hide();
    }
}

// Accordion
jq(document).ready(function() {
    jq(".expand").on("click", function () {
        jq(this).next().slideToggle(200);
        expand = jq(this).find(">:first-child");

        if (expand.text() == "+") {
            expand.text("-");
        } else {
            expand.text("+");
        }
    });
    
    jq(document).off("click",".edit_student_password").on("click",".edit_student_password",function(){
        var id = this.id.replace("edit_","");
        jq(".student_edit_box").hide();
        jq(".student_text_box").show();
        jq("#edit_text_" + id).hide();
        jq("#edit_box_" + id).show();
    });
    
    jq(document).off("click",".save_edit_password").on("click",".save_edit_password",function(){
        var id = this.id.replace("std_password_","");
        var new_pass = jq("#student_password_" + id).val();
        if ( jq.trim(new_pass).length == 0 )
        {
            alert("Invalid New Password");
            return false;
        }
        else
        {
            jq.ajax({
                type: 'POST' ,
                url: '/admin_users/setup_newpas',
                data: {
                  student_id: id,
                  pass: new_pass
                },
                success : function(data) {
                    if ( data == "Not_saved" )
                    {
                        alert("An error occur while saving password, Please try again later");
                    }
                    else
                    {
                        jq("#edit_" + id).html(new_pass);
                        jq("#student_password_" + id).val(new_pass);
                    }
                    jq(".student_edit_box").hide();
                    jq(".student_text_box").show();
                    jq("#edit_text_" + id).show();
                    jq("#edit_box_" + id).hide();
                }
            });
        }
    });
    
    jq(document).off("click",".reset_pass").on("click",".reset_pass",function(){
        var id = this.id.replace("pass_","");
        var new_pass = "123456";
        if ( jq.trim(new_pass).length == 0 )
        {
            alert("Invalid New Password");
            return false;
        }
        else
        {
            jq.ajax({
                type: 'POST' ,
                url: '/admin_users/setup_newpas',
                data: {
                  student_id: id,
                  pass: new_pass
                },
                success : function(data) {
                    if ( data == "Not_saved" )
                    {
                        alert("An error occur while saving password, Please try again later");
                    }
                    else
                    {
                        jq("#edit_" + id).html(new_pass);
                        jq("#student_password_" + id).val(new_pass);
                    }
                    jq(".student_edit_box").hide();
                    jq(".student_text_box").show();
                    jq("#edit_text_" + id).show();
                    jq("#edit_box_" + id).hide();
                }
            });
        }
    });
    
    jq(document).off("click",".edit_guardian_password").on("click",".edit_guardian_password",function(){
        var id = this.id.replace("gedit_","");
        jq(".guardian_edit_box").hide();
        jq(".guardian_text_box").show();
        jq("#gedit_text_" + id).hide();
        jq("#gedit_box_" + id).show();
    });
    
    jq(document).off("click",".save_gedit_password").on("click",".save_gedit_password",function(){
        var id = this.id.replace("grd_password_","");
        var new_pass = jq("#guardian_password_" + id).val();
        if ( jq.trim(new_pass).length == 0 )
        {
            alert("Invalid New Password");
            return false;
        }
        else
        {
            jq.ajax({
                type: 'POST' ,
                url: '/admin_users/setup_guardians_newpas',
                data: {
                  guardian_id: id,
                  pass: new_pass
                },
                success : function(data) {
                    if ( data == "Not_saved" )
                    {
                        alert("An error occur while saving password, Please try again later");
                    }
                    else
                    {
                        jq("#gedit_" + id).html(new_pass);
                        jq("#guardian_password_" + id).val(new_pass);
                    }
                    jq(".guardian_edit_box").hide();
                    jq(".guardian_text_box").show();
                    jq("#gedit_text_" + id).show();
                    jq("#gedit_box_" + id).hide();
                }
            });
        }
    });
    
    jq(document).off("click",".reset_gpass").on("click",".reset_gpass",function(){
        var id = this.id.replace("gpass_","");
        var new_pass = "123456";
        if ( jq.trim(new_pass).length == 0 )
        {
            alert("Invalid New Password");
            return false;
        }
        else
        {
            jq.ajax({
                type: 'POST' ,
                url: '/admin_users/setup_guardians_newpas',
                data: {
                  guardian_id: id,
                  pass: new_pass
                },
                success : function(data) {
                    if ( data == "Not_saved" )
                    {
                        alert("An error occur while saving password, Please try again later");
                    }
                    else
                    {
                        jq("#gedit_" + id).html(new_pass);
                        jq("#guardian_password_" + id).val(new_pass);
                    }
                    jq(".guardian_edit_box").hide();
                    jq(".guardian_text_box").show();
                    jq("#gedit_text_" + id).show();
                    jq("#gedit_box_" + id).hide();
                }
            });
        }
    });
});

// NProgress
if (typeof NProgress != 'undefined') {
    jq(document).ready(function () {
        NProgress.start();
    });

    jq(window).load(function () {
        NProgress.done();
    });
}