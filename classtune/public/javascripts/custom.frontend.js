/**
 * Resize function without multiple trigger
 * 
 * Usage:
 * $(window).smartresize(function(){  
 *     // code here
 * });
 */

var toggle_course = true;
var alreadyFullScreen = false, lockSidebar = false;
var jq = jQuery.noConflict();
var closedData = false;

function reloadInquiryImage()
{
    jq(document).ready(function() {
        jq(document ).off('click','.btn_user_image').on('click','.btn_user_image' , function(){ 
            var student_id = this.id.replace("image_","");
            jq("#student_id").val(student_id);
            jq("#photo").bind("change");
            jq("#photo").trigger("click");
        });

        jq(document ).off('change','#photo').on('change','#photo' , function(){ 
            var data = new FormData();
            jQuery.each(jQuery('#photo')[0].files, function(i, file) {
                data.append('photo_data', file);
            });

            jq.ajax({
                url: '/student/inquiry_upload_photo/',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                type: 'POST',
                success: function(data){
                  jq("#std_id").val(jq("#student_id").val());
                  jq("#cropper_data").html(data);
                  jq("#cropper").modal('show');
                  jq('#cropper').on('hide.bs.modal', function () {
                  });
                  jq('#cropper').on('shown.bs.modal', function () {
                      start_copper_profile('inquiry_');
                  });
                }
            });
        });


        jq(document).off("click",".remove_profile_image").on("click",".remove_profile_image",function(){ 
            var student_id = this.id.replace("remove_","");

            if ( confirm("Are you sure, you want to remove this image?") )
            {
              jq.ajax({
                  url: '/student/inquiry_remove_profile_pic/' + student_id,
                  type: 'POST',
                  success: function(data){
                      jq("#student_profile_image_" + student_id).attr("src", "/images/dummy.png");
                      jq("#remove_" + student_id).unbind("click");
                      jq("#remove_" + student_id).remove();
                  }
              });
            }
        });
    });
}

function validateClasstimingForm()
{
    var error = 0;
    var errorText = "";
    var number_of_period = parseInt(jq("#number_of_period").val());
    var period_duration = parseInt(jq("#period_duration").val());
    var total_break_duration = 0;
    if ( parseInt(jq("#break_period").val()) > 0 )
    {
        var break_period = parseInt(jq("#break_period").val());
        if ( break_period >= number_of_period )
        {
            error++;
            errorText += "Break period must be smaller than Number of Period\n\n";
        }
        
        var break_duration = parseInt(jq("#break_duration").val());
        if ( break_duration == 0 )
        {
            error++;
            errorText += "Break Duration can't be Zero(0)\n\n";
        }
        if ( error == 0 )
        {
            if ( jq("#break_type").val() == "0" )
            {
                total_break_duration = break_duration;
            }
            else
            {
                var total_break = Math.floor( number_of_period / break_period);
                total_break_duration = total_break * break_duration;
            }
            
        }
    }
    
   var total_duration = ( number_of_period * period_duration ) + total_break_duration;
   var d = new Date();
   var eod = new Date(d.toDateString() + " 23:59:59");
   var dt = new Date(d.toDateString() + " " + jq("#start_time").val());
   
   var ndt = new Date(dt); 
   ndt.setTime(dt.getTime() + total_duration*60000);
   
   
   var actualTimeDiff = Math.abs(eod.getTime() - dt.getTime());
   var actualDiffDays = actualTimeDiff / (1000 * 3600 * 24); 
   
   var timeDiff = Math.abs(dt.getTime() - ndt.getTime());
   var diffDays = timeDiff / (1000 * 3600 * 24); 
   
   if ( diffDays > actualDiffDays )
   {
        error++;
        errorText += "Period End time exceed maximum time allow for a day (which is: 12:00 AM)\n";
   }
   
   if ( error )
   {
       alert(errorText);
       return false;
   }
   else
   {
       var i = 0;
       var j = 1;
       var time_table_start = [];
       var time_table_end = [];
       var breaks_period = [];
       var ndate = new Date(dt); 
       var break_done = false;
       for(var l=0; l<number_of_period; l++)
       {
           var hours = ( ndate.getHours() < 10 ) ? "0" + ndate.getHours() : ndate.getHours();
           var minutes = ( ndate.getMinutes() < 10 ) ? "0" + ndate.getMinutes() : ndate.getMinutes();
           var seconds = ( ndate.getSeconds() < 10 ) ? "0" + ndate.getSeconds() : ndate.getSeconds();
           time_table_start[i] = hours + ":" + minutes + ":" + seconds;
           breaks_period[i] = 0;
           ndate.setTime(ndate.getTime() + period_duration*60000);
           var hours = ( ndate.getHours() < 10 ) ? "0" + ndate.getHours() : ndate.getHours();
           var minutes = ( ndate.getMinutes() < 10 ) ? "0" + ndate.getMinutes() : ndate.getMinutes();
           var seconds = ( ndate.getSeconds() < 10 ) ? "0" + ndate.getSeconds() : ndate.getSeconds();
           time_table_end[i] = hours + ":" + minutes + ":" + seconds;
           i++;
           if ( break_period > 0 && break_duration > 0 && ! break_done )
           {
                if ( jq("#break_type").val() == "0" )
                {
                    if ( break_period == j )
                    {
                        break_done = true;
                        var hours = ( ndate.getHours() < 10 ) ? "0" + ndate.getHours() : ndate.getHours();
                        var minutes = ( ndate.getMinutes() < 10 ) ? "0" + ndate.getMinutes() : ndate.getMinutes();
                        var seconds = ( ndate.getSeconds() < 10 ) ? "0" + ndate.getSeconds() : ndate.getSeconds();
                        time_table_start[i] = hours + ":" + minutes + ":" + seconds;
                        breaks_period[i] = 1;
                        ndate.setTime(ndate.getTime() + break_duration*60000);
                        var hours = ( ndate.getHours() < 10 ) ? "0" + ndate.getHours() : ndate.getHours();
                        var minutes = ( ndate.getMinutes() < 10 ) ? "0" + ndate.getMinutes() : ndate.getMinutes();
                        var seconds = ( ndate.getSeconds() < 10 ) ? "0" + ndate.getSeconds() : ndate.getSeconds();
                        time_table_end[i] = hours + ":" + minutes + ":" + seconds;
                        i++;
                    }
                }
                else
                {
                    if ( break_period == j )
                    {
                        var hours = ( ndate.getHours() < 10 ) ? "0" + ndate.getHours() : ndate.getHours();
                        var minutes = ( ndate.getMinutes() < 10 ) ? "0" + ndate.getMinutes() : ndate.getMinutes();
                        var seconds = ( ndate.getSeconds() < 10 ) ? "0" + ndate.getSeconds() : ndate.getSeconds();
                        time_table_start[i] = hours + ":" + minutes + ":" + seconds;
                        breaks_period[i] = 1;
                        ndate.setTime(ndate.getTime() + break_duration*60000);
                        var hours = ( ndate.getHours() < 10 ) ? "0" + ndate.getHours() : ndate.getHours();
                        var minutes = ( ndate.getMinutes() < 10 ) ? "0" + ndate.getMinutes() : ndate.getMinutes();
                        var seconds = ( ndate.getSeconds() < 10 ) ? "0" + ndate.getSeconds() : ndate.getSeconds();
                        time_table_end[i] = hours + ":" + minutes + ":" + seconds;
                        i++;
                        j = 0;
                    }
                }
                    
           }
           j++;
       }
       if ( parseInt(breaks_period[breaks_period.length - 1]) == 1 )
       {
           time_table_start.splice(-1, 1);
           time_table_end.splice(-1, 1);
           breaks_period.splice(-1,1);
       }
       jq("#start_time_all").val(time_table_start);
       jq("#end_time").val(time_table_end);
       jq("#break_periods").val(breaks_period);
       return true;
   }
}

function numberinput()
{
    jq(document).on("click",".btn_up", function(){
        if ( typeof(jq(this).data('bind-input')) != 'undefined' )
        {
            var input = jq(this).data('bind-input');
            if ( jq("#" + input).length > 0 )
            {
                if ( typeof(jq(this).data('max')) != 'undefined' )
                {
                    max = parseInt(jq(this).data('max'));
                    current_val = parseInt(jq("#" + input).val());
                    if ( current_val < max )
                    {
                        if ( typeof(jq(this).data('step')) != 'undefined' )
                        {
                            var step = jq(this).data('step');
                            current_val += step;
                        }
                        else
                        {
                            current_val++;
                        }
                        if ( current_val > max )
                        {
                            current_val = max;
                        }
                        jq("#" + input).val(current_val);
                    }
                }
            }
        }
    });
    
    jq(document).on("click",".btn_down", function(){
        if ( typeof(jq(this).data('bind-input')) != 'undefined' )
        {
            var input = jq(this).data('bind-input');
            if ( jq("#" + input).length > 0 )
            {
                if ( typeof(jq(this).data('min')) != 'undefined' )
                {
                    min = parseInt(jq(this).data('min'));
                    current_val = parseInt(jq("#" + input).val());
                    if ( current_val > min )
                    {
                        if ( typeof(jq(this).data('step')) != 'undefined' )
                        {
                            var step = jq(this).data('step');
                            current_val -= step;
                        }
                        else
                        {
                            current_val--;
                        }
                        if ( current_val < min )
                        {
                            current_val = min;
                        }
                        jq("#" + input).val(current_val);
                    }
                }
            }
        }
    });
    
    jq(document).off("keydown",".number_input").on("keydown",".number_input", function(e){
        
        // Allow: backspace, delete, tab, escape, enter and .
        if (jq.inArray(e.keyCode, [46, 8, 9, 27, 110, 190]) !== -1 ||
             // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) ||
             // Allow: Ctrl+C
            (e.keyCode == 67 && e.ctrlKey === true) ||
             // Allow: Ctrl+X
            (e.keyCode == 88 && e.ctrlKey === true) ||
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 37) ||
            (e.keyCode == 39)) {
                 // let it happen, don't do anything
                 return;
        }
        
        console.log(e.keyCode)
        if ( e.keyCode == 38 )
        {
            var val = parseInt(this.value);
            if ( typeof(jq(this).data('step')) != 'undefined' )
            {
                var step = parseInt(jq(this).data('step'));
                val += step;
            }
            else
            {
                val++;
            }
            console.log(val)
            if ( val > jq(this).data('max') )
            {
                val = jq(this).data('max');
                jq(this).val(val);
            }
            else
            {
                jq(this).val(val);
            }
            e.preventDefault();
            return;
        }
        
        if ( e.keyCode == 40 )
        {
            e.preventDefault();
            var val = parseInt(this.value);
            if ( typeof(jq(this).data('step')) != 'undefined' )
            {
                var step = parseInt(jq(this).data('step'));
                val -= step;
            }
            else
            {
                val--;
            }
            
            if ( val < jq(this).data('min') )
            {
                val = jq(this).data('min');
                jq(this).val(val);
            }
            else
            {
                jq(this).val(val);
            }
            return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }

        if (e.keyCode == 13) {
            e.preventDefault();
        }
    });

    jq(document).on("blur",".number_input", function(){
        if ( isNaN(parseInt(this.value)) )
        {
          jq(this).val("0")
        }
        if ( typeof(jq(this).data('max')) != 'undefined' )
        {
            if ( parseInt(jq(this).val()) > parseInt(jq(this).data('max')) )
            {
                jq(this).val(jq(this).data('max'));
            }
        }
        if ( typeof(jq(this).data('min')) != 'undefined' )
        {
            if ( parseInt(jq(this).val()) < parseInt(jq(this).data('min')) )
            {
                jq(this).val(jq(this).data('min'));
            }
        }
    });
}

function enableTime()
{
    jq('#start_time_picker').datetimepicker({
        format: 'LT',
        stepping: 5
    });
}

function enableEndTime()
{
    jq('#end_time_picker').datetimepicker({
        format: 'LT',
        stepping: 5
    });
}

function collaspPanel()
{
    jq('.collapse-link').closest('.x_title').css('cursor','pointer');
    jq('.collapse-link').closest('.x_title').off('click').on('click', function() {
        var BOX_PANEL = jq(this).parent('.x_panel'),
            ICON = jq(this).find('.collapse-link').children('i'),
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
}

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

function showModal(modal_id)
{
    jq("#" + modal_id).modal('show');
}

function loadcheckbox()
{
    if (jq("input.flat")[0]) {
        jq(document).ready(function () {
            jq('input.flat').iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'
            });
        });
    }
}

function class_timing_js()
{
    numberinput();
    enableTime();
    collaspPanel();
    reload_select2();
}

function class_timing_js_edit()
{
    numberinput();
    enableTime();
    enableEndTime();
    loadcheckbox();
}

function reload_attendance_date()
{
    if ( jq(".date_input_attendance").length > 0 )
    {
        var startDate = moment();
        var maxDate = moment().add(5, 'years');
        if (jq(".date_input_attendance").data('start-date'))
        {
            startDate = jq(".date_input_attendance").data('start-date')
        }
        jq(".date_input_attendance").daterangepicker({
                singleDatePicker: true,
                startDate: startDate,
                maxDate: maxDate,
                showDropdowns: true,
                format: 'YYYY-MM-DD',
                calender_style: 'picker_1'
        });
        
        
        jq(".date_input_attendance").on('apply.daterangepicker', function(ev, picker) {
            closedData = true; 
            jq(this).show();
            if ( jq(this).parent().children("div").hasClass("fieldWithErrors") )
            {
                jq(this).parent().children("div").children("input").val(picker.startDate.format('YYYY-MM-DD'));
            }
            else
            {
                jq(this).parent().children("input").val(picker.startDate.format('YYYY-MM-DD'));
            }
            jq.ajax({
                url: "/attendances/list_subject",
                data: { dt : picker.startDate.format('YYYY-MM-DD') },
                type: 'POST',
                success: function(data){
                    jq('#register').html("");
                    jq("#subject_div").html(data);
                    reload_select2();

                }
            });
            jq(this).parent().children("input").focus();
            jq(this).parent().children("div").children("input").focus();
        });
        
        jq(".date_input_attendance").on('cancel.daterangepicker', function(ev, picker) {
            closedData = true; 
            jq(this).show();
            jq(this).parent().children("input").focus();
            jq(this).parent().children("div").children("input").focus();
        });

        jq(".date_input_attendance").on('hide.daterangepicker', function(ev, picker) {
            closedData = true; 
            ev.preventDefault();
            jq(this).parent().children("input").focus();
            jq(this).parent().children("div").children("input").focus();
        });
    }
}

function reload_month_date()
{
    jq('#month_view').datepicker().on('changeDate', function(ev){
        var fd_date = new Date(ev.date);
        fd_date.setMonth(ev.date.getMonth() + 1);
        var d_date = new Date(ev.date);
        d_date.setMonth(fd_date.getMonth() + 1);
        
        var f_month = ( fd_date.getMonth() < 10 ) ? "0" + fd_date.getMonth() : fd_date.getMonth();
        var f_date = ( fd_date.getDate() < 10 ) ? "0" + fd_date.getDate() : fd_date.getDate();
        var from_date = fd_date.getFullYear() + "-" + f_month + "-" + f_date;
        
        var t_month = ( d_date.getMonth() < 10 ) ? "0" + d_date.getMonth() : d_date.getMonth();
        var t_date = ( d_date.getDate() < 10 ) ? "0" + d_date.getDate() : d_date.getDate();
        var to_date = d_date.getFullYear() + "-" + t_month + "-" + t_date;
        jq.ajax({
            url: "/attendances/list_subject",
            data: { from_date : from_date, to_date: to_date },
            type: 'POST',
            success: function(data){
                jq('#register').html("");
                jq("#subject_div").html(data);
                reload_select2();

            }
        });
        

    });
}

function reload_report_date()
{
    var from_date = jq('#month_view_from').datepicker();
    var to_date = jq('#month_view_to').datepicker().on('changeDate', function(ev){
        var f_date_val = from_date.children("input").val();
        var a_dates = f_date_val.split("-")
        if ( a_dates.length == 2 )
        {
            var f_month = a_dates[0];
            var f_year = a_dates[1];
            
            var t_month = ev.date.getMonth() + 1;
            var t_year = ev.date.getFullYear();
            if ( t_year >= f_year  )
            {
                if ( t_month >= f_month  )
                {
                    var new_date = new Date();
                    new_date.setDate("1");
                    new_date.setMonth(parseInt(f_month) - 1);
                    new_date.setFullYear(parseInt(f_year));
                    
                    var fd_date = new Date(new_date);
                    fd_date.setMonth(new_date.getMonth() + 1);
                    
                    if ( t_month == f_month )
                    {
                        var d_date = new Date(new_date);
                        d_date.setMonth(fd_date.getMonth() + 1);
                    }
                    else
                    {
                        var d_date = new Date(parseInt(t_year), t_month, 0);
                        
                    }
                    var f_month = ( fd_date.getMonth() < 10 ) ? "0" + fd_date.getMonth() : fd_date.getMonth();
                    var f_date = ( fd_date.getDate() < 10 ) ? "0" + fd_date.getDate() : fd_date.getDate();
                    var from_date_str = fd_date.getFullYear() + "-" + f_month + "-" + f_date;

                    var t_month = ( d_date.getMonth() < 10 ) ? "0" + d_date.getMonth() : d_date.getMonth();
                    var t_date = ( d_date.getDate() < 10 ) ? "0" + d_date.getDate() : d_date.getDate();
                    var to_date = d_date.getFullYear() + "-" + t_month + "-" + t_date;
                    jq.ajax({
                        url: "/attendances/list_subject",
                        data: { from_date : from_date_str, to_date: to_date },
                        type: 'POST',
                        success: function(data){
                            jq('#register').html("");
                            jq("#subject_div").html(data);
                            reload_select2();

                        }
                    });
                }
                else
                {
                    var new_date = new Date();
                    new_date.setDate("1");
                    new_date.setMonth(parseInt(f_month) - 1);
                    new_date.setFullYear(parseInt(f_year));
                    ev.date.setMonth(new_date.getMonth());
                    ev.date.setYear(new_date.getFullYear());
                }
            }
            else
            {
                var new_date = new Date();
                new_date.setDate("1");
                new_date.setMonth(parseInt(f_month) - 1);
                new_date.setFullYear(parseInt(f_year));
                ev.date.setMonth(new_date.getMonth());
                ev.date.setYear(new_date.getFullYear());
            }
        }
        else
        {
            alert("Invalid From Date");
        }
    });
}

function reload_daterange_report()
{
    var optionSet1 = {
        startDate: moment().subtract(29, 'days'),
        endDate: moment(),
        dateLimit: {
          days: 365
        },
        autoApply: true,
        opens: 'left',
        ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    };
    
    if ( jq(".text-input-bg span.date_input_attendance_report").length > 0 )
    {
        jq(".text-input-bg span.date_input_attendance_report").daterangepicker(optionSet1);
        
        jq(".text-input-bg span.date_input_attendance_report").on('apply.daterangepicker', function(ev, picker) {
            jq(this).show();
            if ( jq(this).parent().children("div").hasClass("fieldWithErrors") )
            {
                jq(this).parent().children("div").children("input").val(picker.startDate.format('MMMM D, YYYY') + " - " + picker.endDate.format('MMMM D, YYYY'));
            }
            else
            {
                jq(this).parent().children("input").val(picker.startDate.format('MMMM D, YYYY') + " - " + picker.endDate.format('MMMM D, YYYY'));
            }
            jq.ajax({
                url: "/attendances/list_subject",
                data: { from_date : picker.startDate.format('YYYY-MM-DD'), to_date : picker.endDate.format('YYYY-MM-DD') },
                type: 'POST',
                success: function(data){
                    jq('#register').html("");
                    jq("#subject_div").html(data);
                    reload_select2();

                }
            });
            jq(this).parent().children("input").focus();
            jq(this).parent().children("div").children("input").focus();
        });
        
        jq(".text-input-bg span.date_input_attendance_report").on('cancel.daterangepicker', function(ev, picker) {
            jq(this).show();
            jq(this).parent().children("input").focus();
            jq(this).parent().children("div").children("input").focus();
        });

        jq(".text-input-bg span.date_input_attendance_report").on('hide.daterangepicker', function(ev, picker) {
            ev.preventDefault();
            jq(this).parent().children("input").focus();
            jq(this).parent().children("div").children("input").focus();
        });
    }
}

function getMainTableHash()
{
    var tableStruct = [];
    var i = 0;
    jq(".list_view_table thead th").each(function(){
        if ( typeof( jq(this).data("visible") ) == 'undefined' && typeof( jq(this).data("orderable") ) == 'undefined' )
        {
            tableStruct[i] = null;
        }
        else if ( typeof( jq(this).data("visible") ) == 'undefined' && typeof( jq(this).data("orderable") ) != 'undefined' )
        {
            tableStruct[i] = {orderable: jq(this).data("orderable") };
        }
        else if ( typeof( jq(this).data("visible") ) != 'undefined' && typeof( jq(this).data("orderable") ) == 'undefined' )
        {
            tableStruct[i] = {visible: jq(this).data("visible") };
        }
        else if ( typeof( jq(this).data("visible") ) != 'undefined' && typeof( jq(this).data("orderable") ) != 'undefined' )
        {
            tableStruct[i] = {visible: jq(this).data("visible") };
            tableStruct[i] = {orderable: jq(this).data("orderable") };
        }
        i++;
    });
    
    return tableStruct;
}

function createListDataTable()
{
    jq(".list_view_table").each(function(){
        var id = jq(this).attr('id');
        var obj = jq('#' + id);
        oTable = jq('#' + id).DataTable({
            deferRender: true,
            pagingType: 'full_numbers',
            columns: getMainTableHash(),
            bLengthChange: (typeof( jq(this).data("length") ) == 'undefined') ? false : true,
            bPaginate: (typeof( jq(this).data("paginate") ) == 'undefined') ? false : true,
            bSort: (typeof( jq(this).data("sort") ) == 'undefined') ? false : true,
            initComplete: function(settings, json) {
            }
        });
    });
}

function filterText()
{
    jq(document).off('keyup', '#filter').on('keyup', '#filter', function(e){
        var val = this.value;
        if ( jq.trim(val).length == 0 )
        {
            jq("ul#list_to_assign li").each(function(){
                jq(this).show();
            });
        }
        else
        {
            jq("ul#list_to_assign li").each(function(){
                jq(this).show();
            });
            jq("ul#list_to_assign li").each(function(){
                var data = jq.trim(jq(this).children('p').html());
                if ( data.toLowerCase().indexOf(val.toLowerCase()) == -1 )
                {
                    jq(this).hide();
                }
            });
        }
    });
}

function filterTable()
{
    jq(document).off('keyup', '#filter_table').on('keyup', '#filter_table', function(e){
        var val = this.value;
        
        if ( jq.trim(val).length == 0 )
        {
            jq("tr#list_tr").each(function(){
                jq(this).show();
            });
        }
        else
        {
            jq("tr#list_tr").each(function(){
                jq(this).show();
            });
            jq("tr#list_tr").each(function(){
//                var data = jq.trim(jq(this).children('td.searchable-full-name').html());
//                if ( data.indexOf(val) == -1 )
//                {
//                    jq(this).hide();
//                }
                
                var data = jq.trim(jq(this).children('td.searchable').html()).toLowerCase();
                if ( data.indexOf(val.toLowerCase()) == -1 )
                {
                    jq(this).hide();
                }
            });
        }
    });
}

function initScrollBar()
{
    jq('.scrollbar-macosx').scrollbar();
}

function date_init( obj )
{
    var startDate = moment();
    var maxDate = moment().add(5, 'years');
    if (jq("." + obj).data('start-date'))
    {
        startDate = jq("." + obj).data('start-date')
    }
    jq("." + obj).daterangepicker({
            singleDatePicker: true,
            startDate: startDate,
            maxDate: maxDate,
            showDropdowns: true,
            format: 'YYYY-MM-DD',
            calender_style: 'picker_1'
    });


    jq("." + obj).on('apply.daterangepicker', function(ev, picker) {
        closedData = true; 
        jq(this).show();
        if ( jq(this).parent().children("div").hasClass("fieldWithErrors") )
        {
            jq(this).parent().children("div").children("input").val(picker.startDate.format('YYYY-MM-DD'));
        }
        else
        {
            jq(this).parent().children("input").val(picker.startDate.format('YYYY-MM-DD'));
        }
        jq(this).parent().children("input").focus();
        jq(this).parent().children("div").children("input").focus();
    });

    jq("." + obj).on('cancel.daterangepicker', function(ev, picker) {
        closedData = true; 
        jq(this).show();
        jq(this).parent().children("input").focus();
        jq(this).parent().children("div").children("input").focus();
    });

    jq("." + obj).on('hide.daterangepicker', function(ev, picker) {
        closedData = true; 
        ev.preventDefault();
        jq(this).parent().children("input").focus();
        jq(this).parent().children("div").children("input").focus();
    });
}

function reloadClassTiming()
{
    jq( "ul.assign_batches" ).sortable({
        connectWith: "ul",
        cancel: '.not-draggable',
        receive: function( event, ui ) {
            var class_timing_set_id = jq("#class_timing_set_id").val();
            if ( typeof(ui.item.data("title")) == 'undefined' )
            {
                var semester_section_id = ui.item.attr("id");
            }
            else
            {
                var semester_section_id = ui.item.data("title");
            }
            
            jq.ajax( {
                type: "POST",
                url: "/class_timing_sets/add_batch",
                data: {ids : semester_section_id, class_timing_set_id: class_timing_set_id},
                success: function( response ) {}
            });
        }
    });
 
    jq( "ul.available_batches" ).sortable({
        connectWith: "ul",
        receive: function( event, ui ) {
            
                var class_timing_set_id = jq("#class_timing_set_id").val();
                var semester_section_id = ui.item.attr("id");
                var type = jq("#move_type").val();

                if ( typeof(ui.item.data("title")) == 'undefined' )
                {
                    var semester_section_id = ui.item.attr("id");
                }
                else
                {
                    var semester_section_id = ui.item.data("title");
                }
                jq.ajax( {
                    type: "POST",
                    url: "/class_timing_sets/remove_batch",
                    data: {ids : semester_section_id, class_timing_set_id: class_timing_set_id},
                    success: function( response ) {}
                });
          }
    });
    
    collaspPanel();
    
    reload_select2();
    
    filterText();
    
    filterTable();
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

function filter_data_list()
{
    jq.ajax( {
        type: "POST",
        url: "/student/update_list_ajax",
        data: jq("#profile_report").serialize(),
        success: function( response ) {
          jq("#list_data").html( response );
          createListDataTable();
        }
    });
}

function inquiry_filter_data_list()
{
    jq.ajax( {
        type: "POST",
        url: "/student/inquiry_update_list_ajax",
        data: jq("#profile_report").serialize(),
        success: function( response ) {
          jq("#list_data").html( response );
          createListDataTable();
        }
    });
}
function filter_data_employee()
{
    jq.ajax( {
        type: "POST",
        url: "/employee/update_picture_list_ajax",
        data: jq("#profile_report").serialize(),
        success: function( response ) {
          jq("#list_data").html( response );
        }
    });
}

function filter_data()
{
    jq.ajax( {
        type: "POST",
        url: "/student/update_picture_list_ajax",
        data: jq("#profile_report").serialize(),
        success: function( response ) {
          jq("#list_data").html( response );
        }
    });
}

function reload_select2()
{
    if ( jq(".no-seacrh-custom-combo").length > 0 )
    {
        jq('.no-seacrh-custom-combo').select2({
            minimumResultsForSearch: -1
        });
    }
    
    if ( jq('.custom-combo').length > 0 )
    {
        jq('.custom-combo').select2({
        });
        
        jq(".custom-combo").off("select2:select").on("select2:select", function (e) { 
            if (e.params.data.text.indexOf(jq(this).data('placeholder')) == -1)
            {
                if ( jq(this).data('input-populate-func') )
                {
                    var func = jq(this).data("input-populate-func");
                    var fn = window[func];
                    if(typeof fn === 'function') {
                        fn(e.params.data);
                    }
                }
            }
            if (jq(this).data('url') && e.params.data.text.indexOf(jq(this).data('placeholder')) == -1)
            {
                var obj = this;
                var url = jq(this).data('url');
                var div = jq(this).data('update-id');
                jq.ajax({
                    type: 'POST' ,
                    url: url,
                    data : {
                      id: e.params.data.id
                    },
                    success : function(data) {
                        jq("#" + div).html(data);
                        if (jq("input.flat")[0]) {
                            jq(document).ready(function () {
                                jq('input.flat').iCheck({
                                    checkboxClass: 'icheckbox_flat-green',
                                    radioClass: 'iradio_flat-green'
                                });
                            });
                        }
                        if ( jq(obj).data("dual-ajax") )
                        {
                            var dual_ajax = jq(obj).data("dual-ajax");
                            if ( dual_ajax )
                            {
                                var url_data = jq(obj).data('url2');
                                var div_id = jq(obj).data('update2');
                                jq.ajax({
                                    type: 'POST' ,
                                    url: url_data,
                                    data : {
                                      id: e.params.data.id
                                    },
                                    success : function(data) {
                                        jq("#" + div_id ).html(data);
                                        var func = jq(obj).data("func2");
                                        var fn = window[func];
                                        if(typeof fn === 'function') {
                                            fn();
                                        }
                                        if (jq(".js-switch")[0]) {
                                            var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
                                            elems.forEach(function (html) {
                                                var switchery = new Switchery(html, {
                                                    color: '#26B99A'
                                                });
                                            });
                                        }
                                    }
                                });
                            }
                        }
                        else
                        {
                            var func = jq(obj).data("func2");
                                        var fn = window[func];
                                        if(typeof fn === 'function') {
                                            fn();
                                        }
                        }    
                    }
                });
            }
            else if (jq(this).data('url') && e.params.data.text.indexOf(jq(this).data('placeholder')) != -1)
            {
                var div = jq(this).data('update-id');
                if (jq("#" + div).hasClass('custom-combo'))
                {
                    jq("#" + div).html("");
                }
            }
        });
        
        jq(".no-seacrh-custom-combo").on("select2:select", function (e) { 
            if ( jq(this).data('input-populate-func') )
            {
                var func = jq(this).data("input-populate-func");
                var fn = window[func];
                if(typeof fn === 'function') {
                    fn(e.params.data);
                }
            }
        });
    }
}
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
    jq("#flash_box").html("");
    jq("#flash-msg").remove();
    jq(".alert-warning").html("");
    jq(".alert-warning").css("opacity","0");
    jq(".alert-warning").css("padding","0");
    jq(".alert-warning").css("margin-bottom","0");
},10000);

// Sidebar
var setNewContentHeight = function () {
        
    // reset height
    jq('.left_col').css('min-height', jq(window).height());
    jq('.right_col').css('min-height', jq(window).height());

    var bodyHeight = jq('body').outerHeight(),
        footerHeight = jq('body').hasClass('footer_fixed') ? -10 : jq('footer').height(),
        leftColHeight = jq('.left_col').eq(1).height() + jq('.sidebar-footer').height(),
        contentHeight = bodyHeight < leftColHeight ? leftColHeight : bodyHeight;

    // normalize content
    contentHeight -= jq('.nav_menu').height() + footerHeight;

    jq('.left_col').css('min-height', contentHeight - 40);
    jq('.right_col').css('min-height', contentHeight - 15);
};

function menu_display( link_id, replace_str, loader_id, typedisplay )
{
    var l_id = link_id.replace(replace_str,"");
    jq("#" + link_id).removeAttr("onclick");
    jq("#" + link_id).attr("onclick", "menu_display('" + link_id + "')");
    jq("#" + link_id).attr("href", "javascript:;");
    var li = jq("#" + link_id).parent();
    
    if ( typeof(typedisplay) == 'undefined' )
    {
        if (li.is('.active')) {
            li.removeClass('active active-sm');
            jq('ul:first', li).slideUp(function() {
                setNewContentHeight();
                jq("#" + loader_id + l_id).hide();
            });
        } else {
            // prevent closing menu if we are on child menu
            if (!li.parent().is('.child_menu')) {
                jq('#sidebar-menu').find('li').removeClass('active active-sm');
                jq('#sidebar-menu').find('li ul').slideUp();
            }

            li.addClass('active');

            jq('ul:first', li).slideDown(function() {
                setNewContentHeight();
                jq("#" + loader_id + l_id).hide();
            });
        }
    }
}

jq(document).ready(function() {
    filterText();
    filterTable();
    if ( jq('.tags_input').length > 0 )
    {
        jq('.tags_input').tagsInput({
                width: 'auto',
                defaultText: "+ Add"
        });
    }
    
    if ( jq(".list_view_table")[0] )
    {
        createListDataTable();
    }
    
    // TODO: This is some kind of easy fix, maybe we can improve this
    var setContentHeight = function () {
        
        // reset height
        jq('.left_col').css('min-height', jq(window).height());
        jq('.right_col').css('min-height', jq(window).height());

        var bodyHeight = jq('body').outerHeight(),
            footerHeight = jq('body').hasClass('footer_fixed') ? -10 : jq('footer').height(),
            leftColHeight = jq('.left_col').eq(1).height() + jq('.sidebar-footer').height(),
            contentHeight = bodyHeight < leftColHeight ? leftColHeight : bodyHeight;

        // normalize content
        contentHeight -= jq('.nav_menu').height() + footerHeight;

        jq('.left_col').css('min-height', contentHeight - 40);
        jq('.right_col').css('min-height', contentHeight - 15);
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
    
//    jq('#sidebar-menu').find('a').on('click', function(ev) {
//        var li = jq(this).parent();
//
//        if (li.is('.active')) {
//            li.removeClass('active active-sm');
//            jq('ul:first', li).slideUp(function() {
//                setContentHeight();
//            });
//        } else {
//            // prevent closing menu if we are on child menu
//            if (!li.parent().is('.child_menu')) {
//                jq('#sidebar-menu').find('li').removeClass('active active-sm');
//                jq('#sidebar-menu').find('li ul').slideUp();
//            }
//            
//            li.addClass('active');
//
//            jq('ul:first', li).slideDown(function() {
//                setContentHeight();
//            });
//        }
//    });
    
    var m2 = setInterval(function(){
        setContentHeight();
    },500);
    
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

//    // recompute content when resizing
//    jq(window).smartresize(function(){  
//        setContentHeight();
//    });

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
    
    if (jq("input.flat_status")[0]) {
        jq(document).ready(function () {
            jq('input.flat_status').iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'
            });
        }).on('ifChanged', function(e) {
            var isChecked = e.target.checked;
            
            if (isChecked == true) {
                jq("#student_status").show();
            }
            else
            {
                jq("#student_status").hide();
            }
        });
    }
});

function hideOrShowSetUp()
{
    jq.ajax({
        type: 'POST' ,
        url: '/dashboards/hide_show_setup',
        success : function(data) {
            jq("#show_setup").html(data); 
            if (jq(".progress .progress-bar").length > 0) {
                jq('.progress .progress-bar').progressbar();
            }
            jq("#show_setup").slideToggle(); 
            jq("#hidesetup").off("click").on("click",function(){
                hideOrShowSetUp();
            });
        }
    });
}
// Panel toolbox
jq(document).ready(function() {
    jq("#hidesetup").off("click").on("click",function(){
        hideOrShowSetUp();
    });
    
//    jq(".x_title").on("click", function(){
//        jq(".x_title").find('.collapse-link').trigger("click");
//    });
    
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
    if ( jq('[data-toggle="tooltip"]').length > 0 )
    {
        jq('[data-toggle="tooltip"]').tooltip({
            position: {
                my: "center bottom-20",
                at: "center top",
                using: function( position, feedback ) {
                  jq( this ).css( position );
                  jq( "<div>" )
                    .addClass( "arrow" )
                    .addClass( feedback.vertical )
                    .addClass( feedback.horizontal )
                    .appendTo( this );
                }
            },    
            container: 'body'
        });
    }
    
});
// /Tooltip

function populate_admission_no(data)
{
    var txt = data.text;
    var id = data.id;
    var a_ids = txt.split("-");
    jq("#admission_no_program_code").val(jq.trim(a_ids[0]));
}

function populate_batch(data)
{
    var id = data.id;
    jq("#admission_no_batch_id").val(jq.trim(id));
}

function populate_academic(data)
{
    var id = data.id;
    jq("#admission_no_academic_year").val(jq.trim(id).substr(2,2));
}

//select2 Combo
jq(document).ready(function() {
    if (jq(".js-switch")[0]) {
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        elems.forEach(function (html) {
            var switchery = new Switchery(html, {
                color: '#26B99A'
            });
        });
    }
    
    if ( jq(".no-seacrh-custom-combo").length > 0 )
    {
        jq('.no-seacrh-custom-combo').select2({
            minimumResultsForSearch: -1
        });
    }
    
    if ( jq('.custom-combo').length > 0 )
    {
        jq('.custom-combo').select2({
        });
        
        jq(".custom-combo").off("select2:select").on("select2:select", function (e) { 
            var id = jq(this).attr('id');
            if ( jq("#" + id).data('func') )
            {
                var combofunc = jq("#" + id).data('func');
                var fn = window[combofunc];
                if(typeof fn === 'function') {
                    fn(e);
                }
            }
            else
            {
                if (e.params.data.text.indexOf(jq(this).data('placeholder')) == -1)
                {
                    if ( jq(this).data('input-populate-func') )
                    {
                        var func = jq(this).data("input-populate-func");
                        var fn = window[func];
                        if(typeof fn === 'function') {
                            fn(e.params.data);
                        }
                    }
                }
                if (jq(this).data('url') && e.params.data.text.indexOf(jq(this).data('placeholder')) == -1)
                {
                    var obj = this;
                    var url = jq(this).data('url');
                    var div = jq(this).data('update-id');
                    jq.ajax({
                        type: 'POST' ,
                        url: url,
                        data : {
                          id: e.params.data.id
                        },
                        success : function(data) {
                            jq("#" + div).html(data);
                            if (jq("input.flat")[0]) {
                                jq(document).ready(function () {
                                    jq('input.flat').iCheck({
                                        checkboxClass: 'icheckbox_flat-green',
                                        radioClass: 'iradio_flat-green'
                                    });
                                });
                            }
                            if ( jq(obj).data("dual-ajax") )
                            {
                                var dual_ajax = jq(obj).data("dual-ajax");
                                if ( dual_ajax )
                                {
                                    var url_data = jq(obj).data('url2');
                                    var div_id = jq(obj).data('update2');
                                    jq.ajax({
                                        type: 'POST' ,
                                        url: url_data,
                                        data : {
                                          id: e.params.data.id
                                        },
                                        success : function(data) {
                                            jq("#" + div_id ).html(data);
                                            var func = jq(obj).data("func2");
                                            var fn = window[func];
                                            if(typeof fn === 'function') {
                                                fn();
                                            }
                                            if (jq(".js-switch")[0]) {
                                                var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
                                                elems.forEach(function (html) {
                                                    var switchery = new Switchery(html, {
                                                        color: '#26B99A'
                                                    });
                                                });
                                            }
                                        }
                                    });
                                }
                            }
                            else
                            {
                                var func = jq(obj).data("func2");
                                var fn = window[func];
                                if(typeof fn === 'function') {
                                    fn();
                                }
                            }
                        }
                    });
                }
                else if (jq(this).data('url') && e.params.data.text.indexOf(jq(this).data('placeholder')) != -1)
                {
                    var div = jq(this).data('update-id');
                    if (jq("#" + div).hasClass('custom-combo'))
                    {
                        jq("#" + div).html("");
                    }
                }
            }
        });
        
        jq(".no-seacrh-custom-combo").on("select2:select", function (e) { 
            if ( jq(this).data('input-populate-func') )
            {
                var func = jq(this).data("input-populate-func");
                var fn = window[func];
                if(typeof fn === 'function') {
                    fn(e.params.data);
                }
            }
        });
    }
    
});
// /Select2 Combo

function toggle_course_outline()
{
    jq('#course_outline').slideToggle({
        direction: 'up'
    }, 400);
}

function toggle_course_hide(obj)
{
    if ( jq("#course_outline").outerHeight() == 50 )
    {
        toggle_course = true;
        jq(obj).children("i").removeClass("fa-chevron-up");
        jq(obj).children("i").addClass("fa-chevron-down");
        jq("#course_outline").animate({height: '600px'});
    }
    else
    {
        toggle_course = false;
        jq(obj).children("i").removeClass("fa-chevron-down");
        jq(obj).children("i").addClass("fa-chevron-up");
        jq("#course_outline").animate({height: '50px'});
    }
}

// Progressbar
if (jq(".progress .progress-bar")[0]) {
    jq('.progress .progress-bar').progressbar();
}
// /Progressbar


jq(document).ready(function() {
    if (jq("input.input_mask")[0]) {
        jq("input.input_mask").inputmask();
    }
    
    jq(".mobile").each(function(){
        jq(this).attr("data-inputmask","'mask' : '+8801999999999'");
        jq(this).addClass("input_mask");
        jq(this).inputmask();
    });
    
    jq(".roll_no").each(function(){
        jq(this).attr("data-inputmask","'mask' : 'a{1,2}99-99-99-999'");
        jq(this).addClass("input_mask");
        jq(this).inputmask();
    });
    
    jq(".form_data").find("input").on("keydown",function(e){
        var pressKey = false;
        if(e.shiftKey && e.keyCode == 9) 
        {
            e.preventDefault();
            if ( jq(this).data("title") )
            {
                var title = parseInt(jq(this).data("title")) - 1;
                pressKey = true;
            }
        }
        else if ( e.keyCode == 9 )
        { 
            e.preventDefault();
            if ( jq(this).data("title") )
            {
                var title = parseInt(jq(this).data("title")) + 1;
                pressKey = true;
            }
        }
        else if ( e.keyCode == 13 )
        { 
            if (jq(this).hasClass("unlock_pass_it"))
            {
                e.preventDefault();
                jq(this).parent().children("span").trigger("click");
            }
            if (jq(this).hasClass("unlock_it"))
            {
                e.preventDefault();
                jq(this).parent().children("span").trigger("click");
            }
        }
        
        if ( pressKey )
        {
            if ( jq("input[data-title='" + title + "']").length == 0 && jq("select[data-title='" + title + "']").length == 0 )
            {
                for( var i=title; i<title+4; i++ )
                {
                    if ( jq("input[data-title='" + i + "']").length > 0 )
                    {
                        title = i;
                        break;
                    }
                    else if ( jq("select[data-title='" + i + "']").length > 0 )
                    {
                        title = i;
                        break;
                    }
                    else if ( jq("textarea[data-title='" + i + "']").length > 0 )
                    {
                        title = i;
                        break;
                    }
                }
            }
            if ( jq("input[data-title='" + title + "']").length > 0 )
            {
                if ( jq(this).attr("id").indexOf("date") !== -1 ) 
                {
                    jq(this).next("span").trigger("click");
                    jq(".daterangepicker").css("display","none");
                }
                if ( jq(this).attr("id").indexOf("dob") !== -1 ) 
                {
                    jq(this).next("span").trigger("click");
                    jq(".daterangepicker").css("display","none");
                }
                
                if (jq(this).hasClass("unlock_pass_it") && !jq(this).hasClass("locked"))
                {
                    jq(this).parent().children("span").trigger("click");
                }
                
                if (jq(this).hasClass("unlock_it") && !jq(this).hasClass("locked"))
                {
                    jq(this).parent().children("span").trigger("click");
                }
                
                if ( jq("input[data-title='" + title + "']").length > 0 && jq("input[data-title='" + title + "']").attr("id").indexOf("date") !== -1 ) 
                {
                    jq("input[data-title='" + title + "']").next("span").trigger("click");
                }
                if ( jq("input[data-title='" + title + "']").length > 0 && jq("input[data-title='" + title + "']").attr("id").indexOf("dob") !== -1 ) 
                {
                    jq("input[data-title='" + title + "']").next("span").trigger("click");
                }
                jq("input[data-title='" + title + "']").focus();
                jq('html,body').animate({ scrollTop: jq("input[data-title='" + title + "']").offset().top - ( jq(window).height() - jq("input[data-title='" + title + "']").outerHeight(true) ) / 2  }, 200);
                jq(".daterangepicker").each(function(){
                    if (jq(this).css("display") == "block")
                    {
                        jq(this).find("table").children("tbody").children("tr").find("td.active").css("border","1px solid #1ABB9C");
                        jq(this).find("table").children("tbody").children("tr").find("td.active").css("border-radius","5px");
                    }
                });
            }
            else if ( jq("textarea[data-title='" + title + "']").length > 0 )
            {
                jq("textarea[data-title='" + title + "']").focus();
                jq('html,body').animate({ scrollTop: jq("textarea[data-title='" + title + "']").offset().top - ( jq(window).height() - jq("textarea[data-title='" + title + "']").outerHeight(true) ) / 2  }, 200);
            }
            else
            {
                if ( jq("select[data-title='" + title + "']").length > 0 )
                {
                    if ( jq("select[data-title='" + title + "']").hasClass("custom-combo") || jq("select[data-title='" + title + "']").hasClass("no-seacrh-custom-combo") )
                    {
                        jq("select[data-title='" + title + "']").focus();
                        jq("select[data-title='" + title + "']").select2().focus();
                        var select2 = jq("select[data-title='" + title + "']").data('select2');
                        setTimeout(function() {
                            select2.open();
                        }, 0); 
                        jq('html,body').animate({ scrollTop: jq("select[data-title='" + title + "']").offset().top - ( jq(window).height() - jq("select[data-title='" + title + "']").outerHeight(true) ) / 2  }, 200);
                    }
                    else
                    {
                        jq("select[data-title='" + title + "']").focus();
                        jq('html,body').animate({ scrollTop: jq("select[data-title='" + title + "']").offset().top - ( jq(window).height() - jq("select[data-title='" + title + "']").outerHeight(true) ) / 2  }, 200);
                    }
                }
            }
        }
    });

    jq(document).on('keydown', '.select2', function (e) {
        var pressKey = false;
        var title = "";
        if(e.shiftKey && e.keyCode == 9) 
        {
            e.preventDefault();
            var select_id = jq(this).parent().children("select").attr("id");
            if (jq("#" + select_id).data('title'))
            {
                title = parseInt(jq("#" + select_id).data('title')) - 1;
                pressKey = true;
            }
        }
        else if ( e.keyCode == 9 )
        { 
            e.preventDefault();
            var select_id = jq(this).parent().children("select").attr("id");
            if (jq("#" + select_id).data('title'))
            {
                title = parseInt(jq("#" + select_id).data('title')) + 1;
                pressKey = true;
            }
        }
        if ( pressKey )
        {
            if ( jq("input[data-title='" + title + "']").length == 0 && jq("select[data-title='" + title + "']").length == 0 )
            {
                for( var i=title; i<title+4; i++ )
                {
                    if ( jq("input[data-title='" + i + "']").length > 0 )
                    {
                        title = i;
                        break;
                    }
                    else if ( jq("select[data-title='" + i + "']").length > 0 )
                    {
                        title = i;
                        break;
                    }
                    else if ( jq("textarea[data-title='" + i + "']").length > 0 )
                    {
                        title = i;
                        break;
                    }
                }
            }
            if ( jq("input[data-title='" + title + "']").length > 0 )
            {
                if ( jq("#" + select_id).attr("id").indexOf("date") !== -1 )
                {
                    jq(this).next("span").trigger("click");
                    jq(".daterangepicker").css("display","none");
                }
                if ( jq("#" + select_id).attr("id").indexOf("dob") !== -1 )
                {
                    jq(this).next("span").trigger("click");
                    jq(".daterangepicker").css("display","none");
                }
                
                if ( jq("input[data-title='" + title + "']").length > 0 && jq("input[data-title='" + title + "']").attr("id").indexOf("date") !== -1 ) 
                {
                    jq("input[data-title='" + title + "']").next("span").trigger("click");
                }
                if ( jq("input[data-title='" + title + "']").length > 0 && jq("input[data-title='" + title + "']").attr("id").indexOf("dob") !== -1 ) 
                {
                    jq("input[data-title='" + title + "']").next("span").trigger("click");
                }
                jq("input[data-title='" + title + "']").focus();
                jq('html,body').animate({ scrollTop: jq("input[data-title='" + title + "']").offset().top - ( jq(window).height() - jq("input[data-title='" + title + "']").outerHeight(true) ) / 2  }, 200);
            }
            else if ( jq("textarea[data-title='" + title + "']").length > 0 )
            {
                jq("textarea[data-title='" + title + "']").focus();
                jq('html,body').animate({ scrollTop: jq("textarea[data-title='" + title + "']").offset().top - ( jq(window).height() - jq("textarea[data-title='" + title + "']").outerHeight(true) ) / 2  }, 200);
            }
            else
            {
                if ( jq("select[data-title='" + title + "']").length > 0 )
                {
                    if ( jq("select[data-title='" + title + "']").hasClass("custom-combo") || jq("select[data-title='" + title + "']").hasClass("no-seacrh-custom-combo") )
                    {
                        jq("select[data-title='" + title + "']").focus();
                        jq("select[data-title='" + title + "']").select2().focus();
                        var select2 = jq("select[data-title='" + title + "']").data('select2');
                        setTimeout(function() {
                            select2.open();
                        }, 0); 
                        jq('html,body').animate({ scrollTop: jq("select[data-title='" + title + "']").offset().top - ( jq(window).height() - jq("select[data-title='" + title + "']").outerHeight(true) ) / 2  }, 200);
                    }
                    else
                    {
                        jq("select[data-title='" + title + "']").focus();
                        jq('html,body').animate({ scrollTop: jq("select[data-title='" + title + "']").offset().top - ( jq(window).height() - jq("select[data-title='" + title + "']").outerHeight(true) ) / 2  }, 200);
                    }
                }
            }
        }
    });
    
    jq(document).on('keydown', '.select2-search__field', function (ev) {
        var me = jq(this);
        if (me.data('listening') != 1)
        {
            me.data('listening', 1).keydown(function(e) 
            {
                var pressKey = false;
                if(e.shiftKey && e.keyCode == 9) 
                {
                    var title = "";
                    e.preventDefault();
                    var id = jq(this).parent().next().children("ul").attr("id");
                    var select_id = id.replace("select2-","");
                    select_id = select_id.replace("-results","");
                    if (jq("#" + select_id).data('title'))
                    {
                        title = parseInt(jq("#" + select_id).data('title')) - 1;
                        pressKey = true;
                    }
                }
                else if ( e.keyCode == 9 )
                { 
                    e.preventDefault();
                    var id = jq(this).parent().next().children("ul").attr("id");
                    var select_id = id.replace("select2-","");
                    select_id = select_id.replace("-results","");
                    if (jq("#" + select_id).data('title'))
                    {
                        title = parseInt(jq("#" + select_id).data('title')) + 1;
                        pressKey = true;
                    }
                }
                
                if ( pressKey )
                {
                    if ( jq("input[data-title='" + title + "']").length == 0 && jq("select[data-title='" + title + "']").length == 0 )
                    {
                        for( var i=title; i<title+4; i++ )
                        {
                            if ( jq("input[data-title='" + i + "']").length > 0 )
                            {
                                title = i;
                                break;
                            }
                            else if ( jq("select[data-title='" + i + "']").length > 0 )
                            {
                                title = i;
                                break;
                            }
                            else if ( jq("textarea[data-title='" + i + "']").length > 0 )
                            {
                                title = i;
                                break;
                            }
                        }
                    }
                    if ( jq("input[data-title='" + title + "']").length > 0 )
                    {
                        if ( jq("#" + select_id).attr("id").indexOf("date") !== -1 )
                        {
                            jq(this).next("span").trigger("click");
                            jq(".daterangepicker").css("display","none");
                        }
                        if ( jq("#" + select_id).attr("id").indexOf("dob") !== -1 )
                        {
                            jq(this).next("span").trigger("click");
                            jq(".daterangepicker").css("display","none");
                        }
                        

                        if ( jq("input[data-title='" + title + "']").length > 0 && jq("input[data-title='" + title + "']").attr("id").indexOf("date") !== -1 ) 
                        {
                            jq("input[data-title='" + title + "']").next("span").trigger("click");
                        }
                        if ( jq("input[data-title='" + title + "']").length > 0 && jq("input[data-title='" + title + "']").attr("id").indexOf("dob") !== -1 ) 
                        {
                            jq("input[data-title='" + title + "']").next("span").trigger("click");
                        }
                        jq("input[data-title='" + title + "']").focus();
                        jq('html,body').animate({ scrollTop: jq("input[data-title='" + title + "']").offset().top - ( jq(window).height() - jq("input[data-title='" + title + "']").outerHeight(true) ) / 2  }, 200);
                    }
                    else if ( jq("textarea[data-title='" + title + "']").length > 0 )
                    {
                        jq("textarea[data-title='" + title + "']").focus();
                        jq('html,body').animate({ scrollTop: jq("textarea[data-title='" + title + "']").offset().top - ( jq(window).height() - jq("textarea[data-title='" + title + "']").outerHeight(true) ) / 2  }, 200);
                    }
                    else
                    {
                        if ( jq("select[data-title='" + title + "']").length > 0 )
                        {
                            if ( jq("select[data-title='" + title + "']").hasClass("custom-combo") || jq("select[data-title='" + title + "']").hasClass("no-seacrh-custom-combo") )
                            {
                                jq("select[data-title='" + title + "']").focus();
                                jq("select[data-title='" + title + "']").select2().focus();
                                var select2 = jq("select[data-title='" + title + "']").data('select2');
                                setTimeout(function() {
                                    select2.open();
                                }, 0); 
                                jq('html,body').animate({ scrollTop: jq("select[data-title='" + title + "']").offset().top - ( jq(window).height() - jq("select[data-title='" + title + "']").outerHeight(true) ) / 2  }, 200);
                            }
                            else
                            {
                                jq("select[data-title='" + title + "']").focus();
                                jq('html,body').animate({ scrollTop: jq("select[data-title='" + title + "']").offset().top - ( jq(window).height() - jq("select[data-title='" + title + "']").outerHeight(true) ) / 2  }, 200);
                            }
                        }
                    }
                }
            });
        }
    });

    if (jq("input.flat_permanent")[0]) {
        jq(document).ready(function () {
            jq('input.flat_permanent').iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'
            });
        }).on('ifChanged', function(e) {
            var isChecked = e.target.checked;
            
            if (isChecked == true) {
                jq(".permanent_bind").each(function(e){
                    var id = this.id.replace("student_","");
                    jq("#student_permanent_" + id).val(this.value);
                });
                
                jq(".permanent_bind_select").each(function(){
                    var id = this.id.replace("student_","");
                    jq("#student_permanent_" + id).val(this.value).trigger('change');
                });
            }
            else
            {
                jq(".permanent_bind").each(function(e){
                    var id = this.id.replace("student_","");
                    jq("#student_permanent_" + id).val("");
                });
                
                jq(".permanent_bind_select").each(function(){
                    var id = this.id.replace("student_","");
                    jq("#student_permanent_" + id).val(14).trigger('change');
                });
            }
        });
    }
    
    jq(document).on("keyup",".permanent_bind",function(e){
        if (jq('#same_as_present_address').prop('checked'))
        {
            var id = this.id.replace("student_","");
            jq("#student_permanent_" + id).val(this.value);
        }
    });
    
    jq(".permanent_bind_select").off("select2:select").on("select2:select", function(){
        var id = this.id.replace("student_","");
        jq("#student_permanent_" + id).val(this.value).trigger('change');
    });
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
if (typeof(moment) != 'undefined')
{
    var optionSet1 = {
            startDate: moment().subtract(1, 'year'),
            endDate: moment(),
            dateLimit: {
              days: 365
            },
            format: 'YYYY-MM-DD',
            opens: 'left'
    };
}

jq(document).ready(function(){
    
    
    if ( jq(".btn-file").length > 0 )
    {
        if ( jq('.btn-file').data('file-textfield') )
        {
            var fieldField = jq('.btn-file').data('file-textfield');
            var upload_url = jq('.btn-file').data('upload-url');
            var func = "";
            if ( jq('.btn-file').data("func") )
            {
                func = jq('.btn-file').data("func");
            }
            var params = "";
            if ( jq('.btn-file').data("params") )
            {
                params = jq('.btn-file').data("params");
            }
            jq(document ).on('click','.btn-file' , function(){ 
                jq("#" + fieldField).bind("change");
                jq("#" + fieldField).trigger("click");
            });
            
            if ( upload_url )
            {
                jq("#" + fieldField).change(function(){ 
                    var data = new FormData();
                    jQuery.each(jQuery('#' + fieldField)[0].files, function(i, file) {
                        data.append('photo_data', file);
                    });

                    jq.ajax({
                        url: upload_url,
                        data: data,
                        cache: false,
                        contentType: false,
                        processData: false,
                        type: 'POST',
                        success: function(data){
                            jq("#cropper-id").html(data);
                            jq("#cropper").modal('show');
                            jq('#cropper').on('hide.bs.modal', function () {
                            });
                            jq('#cropper').on('shown.bs.modal', function () {
                                var fn = window[func];
                                if(typeof fn === 'function') {
                                    if ( jq.trim(params).length == 0 )
                                    {
                                        fn();
                                    }
                                    else
                                    {
                                        fn(params);
                                    }
                                }
                            });
                            
                        }
                    });
                });
            }
            else
            {
                var fn = window[func];
                if(typeof fn === 'function') {
                    if ( jq.trim(params).length == 0 )
                    {
                        fn(fieldField, jq('.btn-file'));
                    }
                    else
                    {
                        fn(jq('.btn-file'), params);
                    }
                }
            }
        }
    }
    
    
     if ( jq(".btn-file-image").length > 0 )
     {
        if ( jq('.btn-file-image').data('file-textfield') )
        {
            
            var fieldField2 = jq('.btn-file-image').data('file-textfield');
            var upload_url2 = jq('.btn-file-image').data('upload-url');
            var func2 = "";
            if ( jq('.btn-file-image').data("func") )
            {
                func2 = jq('.btn-file-image').data("func");
            }
            var params2 = "";
            if ( jq('.btn-file-image').data("params") )
            {
                params2 = jq('.btn-file-image').data("params");
            }
            jq(document ).on('click','.btn-file-image' , function(){ 
                jq("#" + fieldField2).bind("change");
                jq("#" + fieldField2).trigger("click");
            });
            
            
            if ( upload_url2 )
            {
                
                jq("#" + fieldField2).change(function(){ 
                
                    var data2 = new FormData();
                    jQuery.each(jQuery('#' + fieldField2)[0].files, function(i, file) {
                        data2.append('photo_data', file);
                    });

                    jq.ajax({
                        url: upload_url2,
                        data: data2,
                        cache: false,
                        contentType: false,
                        processData: false,
                        type: 'POST',
                        success: function(data2){
                            jq("#cropper-id").html(data2);
                            jq("#cropper").modal('show');
                            jq('#cropper').on('hide.bs.modal', function () {
                            });
                            jq('#cropper').on('shown.bs.modal', function () {
                                var fn2 = window[func2];
                                if(typeof fn2 === 'function') {
                                    if ( jq.trim(params2).length == 0 )
                                    {
                                        fn2();
                                    }
                                    else
                                    {
                                        fn2(params2);
                                    }
                                }
                            });
                            
                        }
                    });
                });
            }
            else
            {
                var fn2 = window[func2];
                if(typeof fn2 === 'function') {
                    if ( jq.trim(params).length == 0 )
                    {
                        fn2(fieldField2, jq('.btn-file-image'));
                    }
                    else
                    {
                        fn2(jq('.btn-file-image'), params);
                    }
                }
            }
        }
    }
    
    jq(".unlock-link").parent().children("input").on("keydown",function(e){
        if (jq(this).hasClass("locked"))
        {
            e.preventDefault();
            return false;
        }
    });
    
    jq(".unlock-pass-link").parent().children("input").on("keydown",function(e){
        if (jq(this).hasClass("locked"))
        {
            e.preventDefault();
            return false;
        }
    });
    
    jq(".unlock-link").parent().children("div").children("input").on("keydown",function(e){
        if (jq(this).hasClass("locked"))
        {
            e.preventDefault();
            return false;
        }
    });
    
    jq(document).on("click", "#add_another_guardian", function(){
        jq("#new_admission_form").slideToggle().animate({
            left: '250px',
            opacity: '1'
        });
    });
    
    jq(document).on("click", ".unlock-link", function(){
        if ( jq(this).hasClass("fa-edit") )
        {
            jq(this).parent().children("input.unlock_it").removeClass("locked");
            jq(this).removeClass("fa-edit");
            jq(this).addClass("fa-lock");
            
            jq(this).parent().children("div").children("input.unlock_it").removeClass("locked");
            jq(this).removeClass("fa-edit");
            jq(this).addClass("fa-lock");
        }
        else if ( jq(this).hasClass("fa-lock") )
        {
            jq(this).parent().children("div").children("input.unlock_it").addClass("locked");
            jq(this).parent().children("input.unlock_it").addClass("locked");
            jq(this).removeClass("fa-lock");
            jq(this).addClass("fa-edit");
        }
    });
    
    jq(document).on("click", ".unlock-pass-link", function(){
        if ( jq(this).hasClass("fa-edit") )
        {
            jq(this).parent().children("input.unlock_pass_it").removeClass("locked");
            jq(this).parent().children("input.unlock_pass_it").val("");
            jq(this).parent().children("input.unlock_pass_it").attr("type","password");
            jq(this).removeClass("fa-edit");
            jq(this).addClass("fa-lock");
            jq(this).parent().children("input.unlock_pass_it").focus();
            
            jq(this).parent().children("div").children("input.unlock_pass_it").removeClass("locked");
            jq(this).parent().children("div").children("input.unlock_pass_it").val("");
            jq(this).parent().children("div").children("input.unlock_pass_it").attr("type","password");
            jq(this).removeClass("fa-edit");
            jq(this).addClass("fa-lock");
            jq(this).parent().children("div").children("input.unlock_pass_it").focus();
        }
        else if ( jq(this).hasClass("fa-lock") )
        {
            jq(this).parent().children("div").children("input.unlock_pass_it").addClass("locked");
            jq(this).parent().children("input.unlock_pass_it").val("(Auto)");
            jq(this).parent().children("input.unlock_pass_it").attr("type","text");
            
            jq(this).parent().children("div").children("input.unlock_pass_it").val("(Auto)");
            jq(this).parent().children("input.unlock_pass_it").addClass("locked");
            jq(this).parent().children("div").children("input.unlock_pass_it").attr("type","text");
            jq(this).removeClass("fa-lock");
            jq(this).addClass("fa-edit");
        }
    });
    
    if ( jq(".date_input").length > 0 )
    {
        var startDate = moment();
        var maxDate = moment().add(5, 'years');
        if (jq(".date_input").data('start-date'))
        {
            startDate = jq(".date_input").data('start-date')
        }
        jq(".date_input").daterangepicker({
                singleDatePicker: true,
                startDate: startDate,
                maxDate: maxDate,
                showDropdowns: true,
                format: 'YYYY-MM-DD',
                calender_style: 'picker_1'
        });
        
        
        jq(".date_input").on('apply.daterangepicker', function(ev, picker) {
            closedData = true; 
            jq(this).show();
            if ( jq(this).parent().children("div").hasClass("fieldWithErrors") )
            {
                jq(this).parent().children("div").children("input").val(picker.startDate.format('YYYY-MM-DD'));
            }
            else
            {
                jq(this).parent().children("input").val(picker.startDate.format('YYYY-MM-DD'));
            }
            jq(this).parent().children("input").focus();
            jq(this).parent().children("div").children("input").focus();
        });
        
        jq(".date_input").on('cancel.daterangepicker', function(ev, picker) {
            closedData = true; 
            jq(this).show();
            jq(this).parent().children("input").focus();
            jq(this).parent().children("div").children("input").focus();
        });

        jq(".date_input").on('hide.daterangepicker', function(ev, picker) {
            closedData = true; 
            ev.preventDefault();
            jq(this).parent().children("input").focus();
            jq(this).parent().children("div").children("input").focus();
        });
    }
    
    if ( jq(".date_input_attendance").length > 0 )
    {
        var startDate = moment();
        var maxDate = moment().add(5, 'years');
        if (jq(".date_input_attendance").data('start-date'))
        {
            startDate = jq(".date_input_attendance").data('start-date')
        }
        jq(".date_input_attendance").daterangepicker({
                singleDatePicker: true,
                startDate: startDate,
                maxDate: maxDate,
                showDropdowns: true,
                format: 'YYYY-MM-DD',
                calender_style: 'picker_1'
        });
        
        
        jq(".date_input_attendance").on('apply.daterangepicker', function(ev, picker) {
            closedData = true; 
            jq(this).show();
            if ( jq(this).parent().children("div").hasClass("fieldWithErrors") )
            {
                jq(this).parent().children("div").children("input").val(picker.startDate.format('YYYY-MM-DD'));
            }
            else
            {
                jq(this).parent().children("input").val(picker.startDate.format('YYYY-MM-DD'));
            }
            jq.ajax({
                url: "/attendances/list_subject",
                data: { dt : picker.startDate.format('YYYY-MM-DD') },
                type: 'POST',
                success: function(data){
                    jq('#register').html("");
                    jq("#subject_div").html(data);
                    reload_select2();

                }
            });
            jq(this).parent().children("input").focus();
            jq(this).parent().children("div").children("input").focus();
        });
        
        jq(".date_input_attendance").on('cancel.daterangepicker', function(ev, picker) {
            closedData = true; 
            jq(this).show();
            jq(this).parent().children("input").focus();
            jq(this).parent().children("div").children("input").focus();
        });

        jq(".date_input_attendance").on('hide.daterangepicker', function(ev, picker) {
            closedData = true; 
            ev.preventDefault();
            jq(this).parent().children("input").focus();
            jq(this).parent().children("div").children("input").focus();
        });
    }
    
    if ( jq(".date_birth_input").length > 0 )
    {   
        var startDate = moment().subtract(10, 'year');
        if (jq(".date_birth_input").data('start-date'))
        {
            startDate = jq(".date_birth_input").data('start-date')
        }
        jq('.date_birth_input').daterangepicker({
                showDropdowns: true,
                singleDatePicker: true,
                format: 'YYYY-MM-DD',
                startDate: startDate,
                calender_style: 'picker_1'
        });
        
        jq(".date_birth_input").on('apply.daterangepicker', function(ev, picker) {
            closedData = true; 
            jq(this).show();
            if ( jq(this).parent().children("div").hasClass("fieldWithErrors") )
            {
                jq(this).parent().children("div").children("input").val(picker.startDate.format('YYYY-MM-DD'));
            }
            else
            {
                jq(this).parent().children("input").val(picker.startDate.format('YYYY-MM-DD'));
            }
            jq(this).parent().children("input").focus();
            jq(this).parent().children("div").children("input").focus();
        });
        
        jq(".date_birth_input").on('cancel.daterangepicker', function(ev, picker) {
            closedData = true; 
            jq(this).show();
            jq(this).parent().children("input").focus();
            jq(this).parent().children("div").children("input").focus();
        });

        jq(".date_birth_input").on('hide.daterangepicker', function(ev, picker) {
            closedData = true; 
            ev.preventDefault();
            jq(this).parent().children("input").focus();
            jq(this).parent().children("div").children("input").focus();
        });
    }
    
    if ( jq(".ns_date_birth_input").length > 0 )
    {
        var startDate = moment().subtract(20, 'year');
        if (jq(".ns_date_birth_input").data('start-date'))
        {
            startDate = jq(".ns_date_birth_input").data('start-date')
        }
        
        jq('.ns_date_birth_input').daterangepicker({
                showDropdowns: true,
                singleDatePicker: true,
                format: 'YYYY-MM-DD',
                startDate: startDate,
                calender_style: 'picker_1'
        });
        
        jq(".ns_date_birth_input").on('apply.daterangepicker', function(ev, picker) {
            jq(this).show();
            closedData = true; 
            if ( jq(this).parent().children("div").hasClass("fieldWithErrors") )
            {
                jq(this).parent().children("div").children("input").val(picker.startDate.format('YYYY-MM-DD'));
            }
            else
            {
                jq(this).parent().children("input").val(picker.startDate.format('YYYY-MM-DD'));
            }
            jq(this).parent().children("input").focus();
            jq(this).parent().children("div").children("input").focus();
        });
        
        jq(".ns_date_birth_input").on('cancel.daterangepicker', function(ev, picker) {
            jq(this).show();
            closedData = true; 
            jq(this).parent().children("input").focus();
            jq(this).parent().children("div").children("input").focus();
        });

        jq(".ns_date_birth_input").on('hide.daterangepicker', function(ev, picker) {
            ev.preventDefault();
            closedData = true; 
            jq(this).parent().children("input").focus();
            jq(this).parent().children("div").children("input").focus();
        });
    }
    
    
});

//hot keys
jq(document).bind('keydown', 'ctrl+up', function(e) {
    if ( toggle_course == false )
    {
        var obj = jq("#course_outline .x_panel .x_title ul.panel_toolbox li:first a.collapse-link");
        toggle_course_hide(obj);
        return false;
    }
});
jq(document).bind('keydown', 'ctrl+down', function(e) {
    if ( toggle_course  )
    {
        var obj = jq("#course_outline .x_panel .x_title ul.panel_toolbox li:first a.collapse-link");
        toggle_course_hide(obj);
        return false;
    }
});
jq(document).bind('keydown', 'ctrl+x', function(e) {
    toggle_course_outline()
    return false;
});

jq(document).bind('keydown', 'ctrl+b', function(e) {
    hideOrShowSetUp();
    return false;
});




function show_attachement()
{
    var fieldField = jq('.btn-file').data('file-textfield');
    jq("#" + fieldField).change(function(){ 
        jQuery.each(jQuery('#' + fieldField)[0].files, function(i, file) {
            jq("#files").html('<span id="attachment_' + i + '" class="alert alert-success alert-text" style="padding: 5px; color: #fff;">' + file.name + '&nbsp;&nbsp;&nbsp;<a href="javascript:;" onclick="remove_attachement(' + i + ');" style="color: #fff; font-size: 16px;"><b>x</b></a></span>');
        });
        jq(document ).off('click','.btn-file');
        jq('.btn-file').css('background','#ccc');
    });
}

function remove_attachement( no )
{
    jq("#attachment_" + no).remove();
    var fieldField = jq('.btn-file').data('file-textfield');
    var upload_url = jq('.btn-file').data('upload-url');
    
    var control = jq("#" + fieldField);
    control.replaceWith( control = control.clone( true ) );
    var func = "";
    if ( jq('.btn-file').data("func") )
    {
        func = jq('.btn-file').data("func");
    }
    jq('.btn-file').css('background','#fff');
    jq(document ).on('click','.btn-file' , function(){ 
        jq("#" + fieldField).bind("change");
        jq("#" + fieldField).trigger("click");
    });
   
    
    var fn = window[func];
    if(typeof fn === 'function') {
        fn(fieldField, jq('.btn-file'));
    }
}

function start_copper( params )
{
    var image = jq('#image');
    var dataX = jq('#dataX');
    var dataY = jq('#dataY');
    var dataHeight = jq('#dataHeight');
    var dataWidth = jq('#dataWidth');
    var dataRotate = jq('#dataRotate');
    var dataScaleX = jq('#dataScaleX');
    var dataScaleY = jq('#dataScaleY');
    var options = {
          aspectRatio: 16 / 9,
          preview: '.img-preview',
          crop: function (e) {
            dataX.val(Math.round(e.x));
            dataY.val(Math.round(e.y));
            dataHeight.val(Math.round(e.height));
            dataWidth.val(Math.round(e.width));
            dataRotate.val(e.rotate);
            dataScaleX.val(e.scaleX);
            dataScaleY.val(e.scaleY);
          }
        };

    jq('[data-toggle="tooltip"]').tooltip();   
    // Cropper
    image.on({
      'build.cropper': function (e) {
        console.log(e.type + "hehehe");
      },
      'built.cropper': function (e) {
        console.log(e.type);
      },
      'cropstart.cropper': function (e) {
        console.log(e.type, e.action);
      },
      'cropmove.cropper': function (e) {
        console.log(e.type, e.action);
      },
      'cropend.cropper': function (e) {
        console.log(e.type, e.action);
      },
      'crop.cropper': function (e) {
        console.log(e.type, e.x, e.y, e.width, e.height, e.rotate, e.scaleX, e.scaleY);
      },
      'zoom.cropper': function (e) {
        console.log(e.type, e.ratio);
      }
    }).cropper(options);


    // Buttons
    if (!jq.isFunction(document.createElement('canvas').getContext)) {
      jq('button[data-method="getCroppedCanvas"]').prop('disabled', true);
    }

    if (typeof document.createElement('cropper').style.transition === 'undefined') {
      jq('button[data-method="rotate"]').prop('disabled', true);
      jq('button[data-method="scale"]').prop('disabled', true);
    }


    // Options
    jq('.docs-toggles').off('change', 'input').on('change', 'input', function () {
      var obj = jq(this);
      var name = obj.attr('name');
      var type = obj.prop('type');
      var cropBoxData;
      var canvasData;

      if (!image.data('cropper')) {
        return;
      }

    if (type === 'checkbox') {
        options[name] = obj.prop('checked');
        cropBoxData = image.cropper('getCropBoxData');
        canvasData = image.cropper('getCanvasData');

        options.built = function () {
          image.cropper('setCropBoxData', cropBoxData);
          image.cropper('setCanvasData', canvasData);
        };
        
    } 
    else if (type === 'radio') {
        options[name] = obj.val();
    }
    image.cropper('destroy').cropper(options);
      
    });


    // Methods
    jq('.docs-buttons').off('click', '[data-method]').on('click', '[data-method]', function () {
      var obj = jq(this);
      var data = obj.data();
      var target;
      var result;

      if (obj.prop('disabled') || obj.hasClass('disabled')) {
        return;
      }

      if (image.data('cropper') && data.method) {
        data = jq.extend({}, data); // Clone a new one

        if (typeof data.target !== 'undefined') {
          target = jq(data.target);

          if (typeof data.option === 'undefined') {
            try {
              data.option = JSON.parse(target.val());
            } catch (e) {
              console.log(e.message);
            }
          }
        }

        result = image.cropper(data.method, data.option, data.secondOption);
        
        switch (data.method) {
          case 'scaleX':
          case 'scaleY':
            jq(this).data('option', -data.option);
            break;

          case 'getCroppedCanvas':
           
            if (result) {
                
              // upload the crop image again
              var dataURL =  result.toDataURL(jq("#image_type").val());
              if ( typeof(atob) == 'function' )
              {
                    var binary = atob(dataURL.split(',')[1]);
                  
                    var array = [];
                    for(var i = 0; i < binary.length; i++) {
                        array.push(binary.charCodeAt(i));
                    }
                    
                    var file = new Blob([new Uint8Array(array)], {type: jq("#image_type").val()});
                    var fd = new FormData(); 
                    
                    fd.append("photo_data", file);
                    fd.append("orginal_fielname", "cropped-" + jq('#file_name').val());
                    
                    if (typeof(params) == 'undefined')
                    {
                        params = "";
                    }
                    
                    jq.ajax({
                        url: "/student/" + params + "upload_photo_cropped",
                        data: fd,
                        cache: false,
                        contentType: false,
                        processData: false,
                        type: 'POST',
                        success: function(data){
                            jq("#image_info").html(data);
                            
                        }
                    });

              }
              jq("#cropper").modal('hide');
              jq('#image-view').html(result);
            }

            break;
        }

        if (jq.isPlainObject(result) && target) {
          try {
            target.val(JSON.stringify(result));
          } catch (e) {
            console.log(e.message);
          }
        }

      }
    });

    // Keyboard
    jq(document.body).off('keydown').on('keydown', function (e) {
      if (!image.data('cropper') || this.scrollTop > 300) {
        return;
      }

      switch (e.which) {
        case 37:
          e.preventDefault();
          image.cropper('move', -1, 0);
          break;

        case 38:
          e.preventDefault();
          image.cropper('move', 0, -1);
          break;

        case 39:
          e.preventDefault();
          image.cropper('move', 1, 0);
          break;

        case 40:
          e.preventDefault();
          image.cropper('move', 0, 1);
          break;
      }
    });

    // Import image
    var inputImage = jq('#inputImage');
    var URL = window.URL || window.webkitURL;
    var blobURL;

    if (URL) {
      inputImage.change(function () {
        var files = this.files;
        var file;

        if (!image.data('cropper')) {
          return;
        }

        if (files && files.length) {
          file = files[0];

          if (/^image\/\w+$/.test(file.type)) {
            blobURL = URL.createObjectURL(file);
            image.one('built.cropper', function () {

              // Revoke when load complete
              URL.revokeObjectURL(blobURL);
            }).cropper('reset').cropper('replace', blobURL);
            inputImage.val('');
          } else {
            window.alert('Please choose an image file.');
          }
        }
      });
    } else {
      inputImage.prop('disabled', true).parent().addClass('disabled');
    }
}

function start_copper_profile(type_upload,controller)
{
    
    var image = jq('#image');
    var dataX = jq('#dataX');
    var dataY = jq('#dataY');
    var dataHeight = jq('#dataHeight');
    var dataWidth = jq('#dataWidth');
    var dataRotate = jq('#dataRotate');
    var dataScaleX = jq('#dataScaleX');
    var dataScaleY = jq('#dataScaleY');
    var options = {
          aspectRatio: 16 / 9,
          preview: '.img-preview',
          crop: function (e) {
            dataX.val(Math.round(e.x));
            dataY.val(Math.round(e.y));
            dataHeight.val(Math.round(e.height));
            dataWidth.val(Math.round(e.width));
            dataRotate.val(e.rotate);
            dataScaleX.val(e.scaleX);
            dataScaleY.val(e.scaleY);
          }
        };

    jq('[data-toggle="tooltip"]').tooltip();   
    // Cropper
    image.on({
      'build.cropper': function (e) {
        console.log(e.type + "hehehe");
      },
      'built.cropper': function (e) {
        console.log(e.type);
      },
      'cropstart.cropper': function (e) {
        console.log(e.type, e.action);
      },
      'cropmove.cropper': function (e) {
        console.log(e.type, e.action);
      },
      'cropend.cropper': function (e) {
        console.log(e.type, e.action);
      },
      'crop.cropper': function (e) {
        console.log(e.type, e.x, e.y, e.width, e.height, e.rotate, e.scaleX, e.scaleY);
      },
      'zoom.cropper': function (e) {
        console.log(e.type, e.ratio);
      }
    }).cropper(options);


    // Buttons
    if (!jq.isFunction(document.createElement('canvas').getContext)) {
      jq('button[data-method="getCroppedCanvas"]').prop('disabled', true);
    }

    if (typeof document.createElement('cropper').style.transition === 'undefined') {
      jq('button[data-method="rotate"]').prop('disabled', true);
      jq('button[data-method="scale"]').prop('disabled', true);
    }


    // Options
    jq('.docs-toggles').off('change', 'input').on('change', 'input', function () {
      var obj = jq(this);
      var name = obj.attr('name');
      var type = obj.prop('type');
      var cropBoxData;
      var canvasData;

      if (!image.data('cropper')) {
        return;
      }

    if (type === 'checkbox') {
        options[name] = obj.prop('checked');
        cropBoxData = image.cropper('getCropBoxData');
        canvasData = image.cropper('getCanvasData');

        options.built = function () {
          image.cropper('setCropBoxData', cropBoxData);
          image.cropper('setCanvasData', canvasData);
        };
        
    } 
    else if (type === 'radio') {
        options[name] = obj.val();
    }
    image.cropper('destroy').cropper(options);
      
    });
jq('.docs-buttons-employee').off('click', '[data-method]').on('click', '[data-method]', function () {
      var obj = jq(this);
      var data = obj.data();
      var target;
      var result;

      if (obj.prop('disabled') || obj.hasClass('disabled')) {
        return;
      }

      if (image.data('cropper') && data.method) {
        data = jq.extend({}, data); // Clone a new one

        if (typeof data.target !== 'undefined') {
          target = jq(data.target);

          if (typeof data.option === 'undefined') {
            try {
              data.option = JSON.parse(target.val());
            } catch (e) {
              console.log(e.message);
            }
          }
        }

        result = image.cropper(data.method, data.option, data.secondOption);
        
        switch (data.method) {
          case 'scaleX':
          case 'scaleY':
            jq(this).data('option', -data.option);
            break;

          case 'getCroppedCanvas':
            if (result) {
              // upload the crop image again
              var dataURL =  result.toDataURL(jq("#image_type").val());
              if ( typeof(atob) == 'function' )
              {
                    var binary = atob(dataURL.split(',')[1]);
                  
                    var array = [];
                    for(var i = 0; i < binary.length; i++) {
                        array.push(binary.charCodeAt(i));
                    }
                    
                    var file = new Blob([new Uint8Array(array)], {type: jq("#image_type").val()});
                    var fd = new FormData(); 
                    
                    fd.append("photo_data", file);
                    fd.append("orginal_fielname", "cropped-" + jq('#file_name').val());
                    
                    jq.ajax({
                        url: "/employee/save_cropped_photo/" + jq("#emp_id").val(),
                        data: fd,
                        cache: false,
                        contentType: false,
                        processData: false,
                        type: 'POST',
                        success: function(data){
                            jq("#employee_profile_image_" + jq("#emp_id").val()).attr("src",data);
                            jq("#emp_id").val("");
                        }
                    });

              }
              jq("#cropper").modal('hide');
              jq('#image-view').html(result);
            }

            break;
        }

        if (jq.isPlainObject(result) && target) {
          try {
            target.val(JSON.stringify(result));
          } catch (e) {
            console.log(e.message);
          }
        }

      }
    });

    // Methods
    jq('.docs-buttons').off('click', '[data-method]').on('click', '[data-method]', function () {
      var obj = jq(this);
      var data = obj.data();
      var target;
      var result;

      if (obj.prop('disabled') || obj.hasClass('disabled')) {
        return;
      }

      if (image.data('cropper') && data.method) {
        data = jq.extend({}, data); // Clone a new one

        if (typeof data.target !== 'undefined') {
          target = jq(data.target);

          if (typeof data.option === 'undefined') {
            try {
              data.option = JSON.parse(target.val());
            } catch (e) {
              console.log(e.message);
            }
          }
        }

        result = image.cropper(data.method, data.option, data.secondOption);
        
        switch (data.method) {
          case 'scaleX':
          case 'scaleY':
            jq(this).data('option', -data.option);
            break;

          case 'getCroppedCanvas':
            if (result) {
              // upload the crop image again
              var dataURL =  result.toDataURL(jq("#image_type").val());
              if ( typeof(atob) == 'function' )
              {
                    var binary = atob(dataURL.split(',')[1]);
                  
                    var array = [];
                    for(var i = 0; i < binary.length; i++) {
                        array.push(binary.charCodeAt(i));
                    }
                    
                    var file = new Blob([new Uint8Array(array)], {type: jq("#image_type").val()});
                    var fd = new FormData(); 
                    
                    fd.append("photo_data", file);
                    fd.append("orginal_fielname", "cropped-" + jq('#file_name').val());
                    
                    if (typeof(type_upload) == 'undefined')
                    {
                        type_upload = "";
                    }
                    
                    jq.ajax({
                        url: "/" + controller + "/" + type_upload + "save_cropped_photo/" + jq("#std_id").val(),
                        data: fd,
                        cache: false,
                        contentType: false,
                        processData: false,
                        type: 'POST',
                        success: function(data){
                            jq("#student_profile_image_" + jq("#std_id").val()).attr("src",data);
                            jq("#std_id").val("");
                        }
                    });

              }
              jq("#cropper").modal('hide');
              jq('#image-view').html(result);
            }

            break;
        }

        if (jq.isPlainObject(result) && target) {
          try {
            target.val(JSON.stringify(result));
          } catch (e) {
            console.log(e.message);
          }
        }

      }
    });

    // Keyboard
    jq(document.body).off('keydown').on('keydown', function (e) {
      if (!image.data('cropper') || this.scrollTop > 300) {
        return;
      }

      switch (e.which) {
        case 37:
          e.preventDefault();
          image.cropper('move', -1, 0);
          break;

        case 38:
          e.preventDefault();
          image.cropper('move', 0, -1);
          break;

        case 39:
          e.preventDefault();
          image.cropper('move', 1, 0);
          break;

        case 40:
          e.preventDefault();
          image.cropper('move', 0, 1);
          break;
      }
    });

    // Import image
    var inputImage = jq('#inputImage');
    var URL = window.URL || window.webkitURL;
    var blobURL;

    if (URL) {
      inputImage.change(function () {
        var files = this.files;
        var file;

        if (!image.data('cropper')) {
          return;
        }

        if (files && files.length) {
          file = files[0];

          if (/^image\/\w+$/.test(file.type)) {
            blobURL = URL.createObjectURL(file);
            image.one('built.cropper', function () {

              // Revoke when load complete
              URL.revokeObjectURL(blobURL);
            }).cropper('reset').cropper('replace', blobURL);
            inputImage.val('');
          } else {
            window.alert('Please choose an image file.');
          }
        }
      });
    } else {
      inputImage.prop('disabled', true).parent().addClass('disabled');
    }
}

function init_leaving_date()
{
    var startDate = moment();
    if (jq(".date_input_single").data('start-date'))
    {
        startDate = jq(".date_input_single").data('start-date')
    }
    jq(".date_input_single").daterangepicker({
            singleDatePicker: true,
            startDate: startDate,
            showDropdowns: true,
            format: 'YYYY-MM-DD',
            calender_style: 'picker_1'
    });

    jq(".date_input_single").on('apply.daterangepicker', function(ev, picker) {
        jq(this).show();
        jq(this).parent().children("input").val(picker.startDate.format('YYYY-MM-DD'));
    });

    jq(".date_input_single").on('cancel.daterangepicker', function(ev, picker) {
        jq(this).show();
    });

    jq(".date_input_single").on('hide.daterangepicker', function(ev, picker) {
        ev.preventDefault();
    });
    
    
}

var fixHelper2 = function(e, ui) {
    var parent_id = ui.parent().parent().attr('id');
    if ( parent_id == "subject_list_outline" )
    {
        if ( jq(".employee_table tbody tr").find(".dataTables_empty").length > 0 )
        {
            jq(".employee_table tbody tr").find(".dataTables_empty").height("25px");
            jq(".employee_table tbody tr").find(".dataTables_empty").html("");
        }
    }

    ui.children().each(function() {
        jq(this).width(jq(this).width());
    });
    return ui;
};

function populate_datatable_employee()
{
    oTableDept.columns().search("").draw();
    jq("#department-name-course-outline").val(jq("#department-name-course-outline option:first").val()).trigger("change");
    oTable = jq('.employee_table').DataTable({
        deferRender: true,
        pagingType: 'simple',
        bFilter: false,
        bPaginate: false,
        bLengthChange: false,
        bInfo: false,
        columns: [
          { visible: true, orderable: false }, {orderable: false}, {orderable: false}, {orderable: false}
        ]
    });
    jq(".items tbody, .projects tbody").sortable({
        helper: fixHelper2,
        cancel: ".dataTables_empty, .not_draggable",
        connectWith: ".items tbody",
        receive: function( event, ui ) {
                 
                 var drag_id = ui.sender.closest("table").attr("id");
                 var group = ui.item.parent().parent().attr("id");
                 var item_id = jq.trim(ui.item.children('td').html());
                 
                 if(group.indexOf("employee_table") == 0)
                 {
                    ui.item.clone().prependTo('#subject_list_outline .ui-sortable'); 
                 } 
                 if( jq("#"+group+" #"+item_id).length == 0 && group.indexOf("employee_table") == 0 )
                 {
                     var employee_id = group.replace('employee_table_', '');
                     var item_id = item_id //.replace('subjects_', '');
                     var type = "add";
                     var status = employeeAsignDelete(employee_id,item_id,type);
                     if(status == "SAVED")
                     {
                         
                     }
                     else
                     {
                         ui.item.remove();
                         alert(status);
                     }    
                 }
                 
                 if(group.indexOf("employee_table") == -1 )
                 {
                     var employee_id = drag_id.replace('employee_table_', '');
                     var item_id = item_id.replace('subjects_', '');
                     var type = "remove";
                     var status = employeeAsignDelete(employee_id,item_id,type);
                     if(status == "SAVED")
                     {
                         if(jq('#'+drag_id+' .ui-sortable tr').length==0)
                         {
                             jq('#'+drag_id+' .ui-sortable').append('<tr class="odd"><td valign="top" colspan="4" class="dataTables_empty"></td></tr>');
                         } 
                         ui.item.remove();
                     }
                     else
                     {
                        ui.item.clone().prependTo('#'+drag_id+' .ui-sortable'); 
                        ui.item.remove();
                        alert(status);
                     }    
                 }
                 
              
                 if( jq("#"+group+" #"+item_id).length > 1 && group.indexOf("employee_table") == 0 )
                 {
                     alert("This subject is already assign to the employee");
                     ui.item.remove();
                 }
                 else if(jq("#"+group+" #"+item_id).length > 1)
                 {
                    ui.item.remove();
                      
                 }    
                
        }
    }).disableSelection();
    
}

function employeeAsignDelete(employee_id,subject_id,type)
{
     var returntext = "";
     jq.ajax({
        type: 'POST' ,
        url: "/employee/save_employee_subjects",
        async: false,
        data : {
          subject_id: subject_id,
          employee_id: employee_id,
          type: type
        },
        success : function(datareturn) {
            returntext = datareturn;
          
        }
    });
    return returntext;
}

window.onload = function(){
    if ( jq('.redactor_call')[0] )
    {
        jq('.redactor_call').each(function(a,b){
            jq('.redactor_call').redactor({
                buttons: ['html', '|',
                          'bold', 'italic', 'underline','deleted','|',
                          'redo', 'undo', '|',
                          'selectall', '|',
                          'formatting', '|',
                          'subscript','superscript', '|',
                          'unorderedlist', 'orderedlist', 'outdent', 'indent', '|',
                          'alignment', '|',
                          'horizontalrule', '|',
                          'image', 'video', 'file', 'table', 'link'],
                buttonsCustom: {
                          superscript: {
                              title: 'Superscript',
                              callback: function(event, key) {
                                  this.execCommand(event,'superscript');
                              }
                          },
                          subscript: {
                              title: 'Subscript',
                              callback: function(obj, event, key) {
                                  this.execCommand('subscript');
                              }
                          },
                          redo: {
                              title: 'Redo',
                              callback: function(event, key) {
                                  this.execCommand(event,'redo');
                              }
                          },
                          undo: {
                              title: 'Undo',
                              callback: function(obj, event, key) {
                                  this.execCommand('undo');
                              }
                          },
                          selectall: {
                              title: 'Select all',
                              callback: function(obj, event, key) {
                                  this.selectall = true;
                                  this.execCommand('selectall');
                              }
                          },
                          paste: {
                              title: 'Paste',
                              callback: function(obj, event, key) {
                                  //this.selectall = true;
                                  this.execCommand('inserthtml');
                              }
                          }
                        },
                focus: true,
                plugins: ['fontcolor',''],
                direction: 'ltr',
                lang: 'en',
                imageUpload: '/redactor/upload',
                                                uploadFields: {'authenticity_token':jq('input[name=authenticity_token]').val()},
                imageUploadErrorCallback: function(json){
                  jq('#redactor_upload_errors_'+b.id).attr('style','display:block');
                  jq('#redactor_upload_errors_'+b.id).html(json.error_message);
                },
                imageDeleteCallback: function(image){
                  image_location = image[0].src;
                  reg = /^.*uploads([0-9\/]*)\/images.*$/;
                  old_ids_to_delete = jq('#redactor_to_delete').val();
                  old_ids_to_update = jq('#redactor_to_update').val();
                  if(reg.match(image_location)){
                    image_location.match(reg);
                    new_id_to_delete = parseInt(RegExp.$1.split('/').join(''));
                    if(old_ids_to_delete == ''){
                      new_ids_to_delete = [ new_id_to_delete ];
                    }else{
                      new_ids_to_delete = old_ids_to_delete.split(',');
                      new_ids_to_delete.push(new_id_to_delete);
                      new_ids_to_delete = new_ids_to_delete.join(',');
                    }
                    if(old_ids_to_update != ''){
                      added_ids = old_ids_to_update.split(',');
                      if(added_ids.include(new_id_to_delete)){
                        added_ids.splice(added_ids.indexOf(new_id_to_delete.toString()),1);
                      }
                    }
                    jq('#redactor_to_delete').val(new_ids_to_delete);
                    jq('#redactor_to_update').val(added_ids.join(','));
                  }
                },
                imageUploadCallback: function(image, json){
                  new_id_to_update = json.id;
                  old_ids_to_update = jq('#redactor_to_update').val();
                  if(old_ids_to_update == ''){
                    new_ids_to_update = [ new_id_to_update ]
                  }else{
                    new_ids_to_update = old_ids_to_update.split(',');
                    new_ids_to_update.push(new_id_to_update);
                    new_ids_to_update = new_ids_to_update.join(',');
                  }
                  jq('#redactor_to_update').val(new_ids_to_update);
                }
          });

            jq('#'+b.id).parent().prepend('<div class="redactor_upload_errors" id="redactor_upload_errors_'+b.id+'"></div>');

            jq('.redactor_editor').find('iframe').each(function(a,b){
              if(b.src.indexOf('youtube.com')>=0 && b.src.indexOf('wmode')==-1){
                b.src = b.src+'?wmode=opaque';
              }
            });

            jq('.redactor_box').on('click',function(){
              if(jq('#redactor_upload_errors_'+b.id).html().length != 0){
                jq('#redactor_upload_errors_'+b.id).attr('style','');
                jq('#redactor_upload_errors_'+b.id).html('');
              }
            })
        });
        jq('#page-yield').append("");
        jq('.redactor_box').find('textarea').removeClass('redactor_call_style');
        jq('.redactor_box').find('textarea').css('height', '135px;');
    }
}