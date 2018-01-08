
<div id="candletoPopup"> 

    <div class="close" style="display:block;"></div>
    <div class="goto"></div>
    <div style="clear:both;"></div>
    <div id="popup_content"> <!--your content start-->
        <div class="candle_left_box">
            <img src="<?php echo base_url('Profiler/images/right/candle.png'); ?>" alt="Candle" style="width:100%;margin: 0 auto;display: block;"> 

        </div>
        <div class="candle_right_box">
            <p class="f2 candle_right_box_p1">What is candle?</p>
            <p class="f5 candle_right_box_p2">
                <img src="<?php echo base_url('Profiler/images/right/Candle-Page.png'); ?>" alt="Candle" style="width:100%;margin: 0 auto;display: block;"> 
            </p>

        </div>
        <div style="clear:both;"></div>

    </div> <!--your content end-->
    <div class="slide innerTop">
        <?php echo form_open('', array('id' => 'validate_form', 'class' => 'validate_form', 'enctype' => "multipart/form-data")); ?>
        <div id="section_form">
            <p class="candle_form_title"><img src="<?php echo base_url('Profiler/images/right/candle_text.png'); ?>" alt="Candle" style="width:90%;margin: 0 auto;display: block;"> </p>
            <p class="candle_school_form_title" style="display:none;margin-bottom: 20px;"><span style="color:rgba(45, 39, 39, 0.87);font-family:'Bree Serif','SolaimanLipi' !important;font-size: 50px;font-weight: bold;">Candling for School</span> </p>
            <div class="candle_form_left">
                <label class="candle-checkbox">
                    <input id="demo_box_4" name="type_post[]" value="1" class="css-checkbox" type="checkbox" checked="checked" />
                    <label for="demo_box_4" class="css-label f5">Visitor</label>

                    <input id="demo_box_1" name="type_post[]" value="2" class="css-checkbox" type="checkbox" checked="checked" />
                    <label for="demo_box_1"  class="css-label f5">Student</label>

                    <input id="demo_box_2" name="type_post[]" value="3"  class="css-checkbox" type="checkbox" checked="checked" />
                    <label for="demo_box_2"  class="css-label f5">Parents</label>


                    <input id="demo_box_3" name="type_post[]"  value="4"    class="css-checkbox" type="checkbox" checked="checked" />
                    <label for="demo_box_3"  class="css-label f5">Teacher</label>

                </label>
                <label class="candle-selectbox" id="category_select_box">

                </label>
                
                <label id="candle_type_select_box" style="display:none;">
                    
                </label>
                <label class="candle-input">
                    <input type="text" name="headline" id="headline" class="cd-input f5" placeholder="Title">
                </label>
                <label class="candle-input" id="candle_input_mobile">
                    <input type="text" name="mobile_num" id="mobile_num" value="<?php echo $user_mobile_number; ?>" class="cd-input f5" placeholder="Your Mobile Number">
                </label>
                <label class="candle-textarea">
                    <textarea class="cd-textarea f5" id="content" name="content" placeholder="Content"></textarea>
                </label>
            </div>
            <div class="candle_form_right" style=display:none;">
                <p class="candle_form_settings" style="padding-left:0px;text-align: left;color:#616161;"><img src="<?php echo base_url('Profiler/images/right/settings.png'); ?>" alt="Settings" style="width:30%;margin: 0 auto;display: block;cursor: pointer;"> </p>
                <div class="candle_form_settings_data" style="display:none;margin-top:30px;margin-left: 30px;">
                    <p style="padding-left:0px;text-align: left;color:#616161;"><b>Settings</b></p>
                    <input id="demo_box_5" name="can_comment" value="1" class="css-checkbox" type="checkbox" />
                    <label for="demo_box_5" class="css-label f5">Can Comment?</label>

                    <input id="demo_box_6" name="show_comment_to_all" value="1" class="css-checkbox" type="checkbox" />
                    <label for="demo_box_6"  class="css-label f5">Show comment to all?</label>
                </div>
            </div>
            


                <div class="button icon-attach"   style="width:150px;float:left;overflow: hidden;margin-right:10px;">
                    <label class="candle-btn">
                        <a class="f5 " id="file_attach" href="javascript:void(0);">Attach a file</a>
                    </label>                    
                </div>
                <div class="button icon-upload" style="width:160px;float:left;overflow: hidden;margin-right:10px;">
                    <label class="candle-btn">
                        <a class="f5 " id="image_attach" href="javascript:void(0);">Upload Picture</a>
                    </label>                    
                </div>
                <div class="button icon-send" style="width:105px;float:left;overflow: hidden;margin-right:10px;">
                    <label class="candle-btn">
                        <a class="f5 " id="candle_send" href="javascript:void(0);">Send</a>
                    </label>
                </div>

                <input type="submit" id="submit_form" style="display:none;" value="Submit" />
            </label>
            <label class="candle-input">
                <input type="file" style="display:none;"  id="attach_file" name="attach_file"  />
            </label>
            <label class="candle-input">
                <input type="file" style="display:none;"  id="leadimage" name="leadimage"  />
            </label>
            <div class="clearfix"></div>

        </div>
        <div class="clearfix"></div>
        <?= form_close(); ?>    
        <div id="section_thanks" style="display:none;">
            <p><img src="<?php echo base_url('Profiler/images/right/candle_thanks.png'); ?>" alt="Candle" style="width:100%;margin: 0 auto;display: block;"> </p>
        </div>
    </div>
</div> <!--candletoPopup end-->

<div class="loader"></div>
<div id="candlebackgroundPopup"></div>
<script type="text/javascript">
    /* 
     author: istockphp.com
     */
    var t = 0;
    //var mobileTest = ^\+[1-9]{1}[0-9]{6,16}$;
    function loadCategory()
    {
        var candle_category_id = '';
        if($('#candle_category_id').length > 0) {
            candle_category_id = $('#candle_category_id').val();
        }
        
        $.ajax({
            type: "GET",
            url: $("#base_url").val() + "front/ajax/get_category_main/",
            data: {candle_category_id: candle_category_id},
            async: false,
            success: function(data) {
                if (data == 0)
                {
                    $(".tz_social a.login-user").trigger('click');
                    return false;
                }
                else
                {
                    t = 1;
                    $("#category_select_box").html(data);
                    return true;
                }
            }
        });
    }
    function loadCandleType()
    {
        var cnadle_popup_select_box_html = '<div class="select-style"><select class="f5" name="category"><option value="<?php echo $this->config->config["school_candle_category_id"];?>" checked="checked">school category</option>';
        var cnadle_type_popup_select_box_html = '<div class="select-style"><select class="f5" name="candle_type"><option value="1" checked="checked">Common</option><option value="2">Question</option></select></div>';
        
        t = 1;
        $("#category_select_box").html(cnadle_popup_select_box_html);
        $("#candle_type_select_box").html(cnadle_type_popup_select_box_html);
        return true;
    }
    jQuery(function($) {

        $('#file_attach').click(function(e) {

            $('#attach_file').trigger('click');
        });

        $(document).on('change', '#attach_file', function(e) {

            var filename = $(this).val();

            var ext = filename.split('.').pop().toLowerCase();

            if ($.inArray(ext, ['doc', 'pdf', 'docs', 'docx']) == -1) {
                alert('not valid!');
                return false;
            }
            else {
                $('#file_attach').html(filename);
            }

        });


        $('#image_attach').click(function(e) {

            $('#attach_file').trigger('disable');
            $('#leadimage').trigger('click');
        });
        $(document).on('change', '#leadimage', function(e) {

            var filename = $(this).val();
            var ext = filename.split('.').pop().toLowerCase();

            if ($.inArray(ext, ['png', 'jpg', 'gif', 'jpeg']) == -1) {
                alert('not valid!');
            }
            else {
                $('#image_attach').html(filename);
            }
            return false;

        });



        $(".candlepopup").click(function() {
            
            if (t == 0)
            {                
                var sc_id = "";
                sc_id = $('#school_didi').text();
                
                loading(); // loading
                if(sc_id != "")
                {
                    loadCandleType();                    
                }
                else
                {
                    loadCategory();                    
                }
                if (t == 1)
                {
                    setTimeout(function() { // then show popup, deley in .5 second
                        $("a.champs21_scrollToTop").trigger("click");
                        if(sc_id != "")
                        {                            
                            loadPopupSchool(); // function show popup 
                            $('#section_form').append('<input type="hidden" id="school_id" name="school_id" value="'+ sc_id +'">');
                        }
                        else
                        {
                            loadPopup(); // function show popup 
                        }
                    }, 500);
                }
                else
                {
                    closeloading();
                }
            }
            return false;
        });

        /* event for close the popup */
        $("div.goto").hover(
                function() {
                    $('span.ecs_tooltip').show();
                },
                function() {
                    $('span.ecs_tooltip').hide();
                }
        );

        $("div.close").click(function() {
            disablePopup();  // function close pop up
            t = 0;
        });

        $("form#validate_form").submit(function(event) {

            //disable the default form submission
            event.preventDefault();
            var mobile_number = $.trim($("#mobile_num").val());
            if ($.trim($("#headline").val()) == "")
            {
                alert("Title must not be empty");
            }
            else if ($.trim($("#content").val()) == "")
            {
                alert("Content must not be empty");
            }
            else if (mobile_number == "")
            {
                alert("Mobile Number must not be empty");
            }    
            else
            {
                loading(); // loading
                var formData = new FormData($(this)[0]);
                $.ajax({
                    url: $("#base_url").val() + "front/ajax/add_post/",
                    type: 'POST',
                    data: formData,
                    async: false,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(returndata)
                    {
                        closeloading();
                        if (returndata == 0)
                        {
                            alert("Critical Error occur Please refresh page and submit data again");
                            return false;
                        }
                        else
                        {
                            $('#section_form').css("display", 'none');
                            $("#section_thanks").fadeIn(500);
                        }
                    }
                });

                return false;
            }
        });

        $("#candle_send").click(function() {

            $('#submit_form').trigger('click');

        });

        $('div.goto').toggle(function() {
         
            $('.innerTop').animate({'left': '3px'}, {
                duration: 1000,
                step: function() {
                    $('.innerTop').css("display", 'block');
                    $('div.close').css("display", 'block');
                    $('#section_form').css("display", 'block');
                    $('#popup_content').css("display", 'none');
                    $('div.goto').css("display", 'none');
                }
            });
        }, function() {
            $('.innerTop').animate({'left': '103px'});
        });

        $(this).keyup(function(event) {
            if (event.which == 27) { // 27 is 'Ecs' in the keyboard
                disablePopup();  // function close pop up
            }
        });

        $("div#candlebackgroundPopup").click(function() {
            //disablePopup();  // function close pop up
        });

        $('a.livebox').click(function() {
            alert('Hello World!');
            return false;
        });

        $("p.candle_form_settings").click(function() {
            $('.candle_form_left').animate({'width': '65%'}, {
                duration: 1000,
                step: function() {
                    setTimeout(function(){
                    $('.candle_form_settings').css("display", 'none');
                    $('.candle_form_right').css("width", '35%');
                    $('.candle_form_settings_data').delay(1000).css("display", 'block');
                    }, 1000); 
                }
            });
        });

        /************** start: functions. **************/
        function loading() {
            $("div.loader").show();
        }
        function closeloading() {
            $("div.loader").fadeOut('normal');
        }

        var popupStatus = 0; // set value

        function loadPopup() {
            if (popupStatus == 0) { // if value is 0, show popup
                closeloading(); // fadeout loading
                $("#candletoPopup").fadeIn(0500); // fadein popup div
                $("#candlebackgroundPopup").css("opacity", "0.7"); // css opacity, supports IE7, IE8
                $("#candlebackgroundPopup").fadeIn(0001);
                $('.innerTop').css("display", 'none');
                $('div.close').css("display", 'block');
                $('#popup_content').css("display", 'block');
                $('div.goto').css("display", 'block');
                $("#section_thanks").css("display", 'none');
                popupStatus = 1; // and set value to 1
            }
        }
        
        function loadPopupSchool() {
            if (popupStatus == 0) { // if value is 0, show popup
                closeloading(); // fadeout loading
                $("#candletoPopup").fadeIn(0500); // fadein popup div
                $("#candlebackgroundPopup").css("opacity", "0.7"); // css opacity, supports IE7, IE8
                $("#candlebackgroundPopup").fadeIn(0001);
                $('#popup_content').css("display", 'none');
                
                $('div.close').css("display", 'block');
                $('div.goto').css("display", 'none');
                $("#section_thanks").css("display", 'none');
                popupStatus = 1; // and set value to 1
                
                
                $('.innerTop').animate({'left': '3px'}, {
                duration: 1000,
                step: function() {
                    $(".candle_form_left").css("float", "left");
                    $(".candle_form_left").css("width", "85%");
                    
                    $(".candle_form_right").css("float", "right");
                    $(".candle_form_right").css("width", "15%");
        
                    $('.innerTop').css("display", 'block');
                    $('.candle_form_title').css("display", 'none');
                    $('.candle_school_form_title').css("display", 'block');
                    
                    
                    if($('#can_school_canlde').length < 1) {
                        $('#section_form').html('');
                        $('#section_form').html('<p class="candle_school_form_title" style="display: block; margin-bottom: 20px;"><span class="f2" style="line-height: 1.5; color: #484343; font-size: 25px; font-weight: bold;">Your school join request is processing. You can candle to your school page after your request is approved.</span> </p>');
                    }
                    
                    
                    $('#section_form').css("display", 'block');
                    $('#candle_type_select_box').css("display", 'block');
                    $('#category_select_box').css("display", 'none');
                    $( "#candle_input_mobile" ).css("display", 'none');
                    $('#popup_content').css("display", 'none');
                    $('.candle_form_right').css("display", 'block');
                    
                    
                }
            });
            }
        }

        function disablePopup() {
            if (popupStatus == 1) { // if value is 1, close popup
                $("#candletoPopup").fadeOut("normal");
                $("#candlebackgroundPopup").fadeOut("normal");
                
                $('.innerTop').css("display", 'none');
                $('div.close').css("display", 'none');
                $('#popup_content').css("display", 'none');
                $('div.goto').css("display", 'none');
                $("#section_thanks").css("display", 'none');
                $('.candle_school_form_title').css("display", 'none');
                $('.candle_form_title').css("display", 'block');
                //$( "#category_select_box" ).css("display", 'block');
                $( "#candle_input_mobile" ).css("display", 'block');
                
                $( ".candle_form_right" ).css("display", 'none');
                $(".candle_form_left").css("width", '100%');
                
                
                $('.candle_form_settings').css("display", 'block');
                $('.candle_form_right').css("width", '15%');
                $('.candle_form_left').css("width", '85%');
                $('.candle_form_settings_data').css("display", 'none');
                $('#candle_type_select_box').css("display", 'none');
                $('#category_select_box').css("display", 'block');
                
                popupStatus = 0;  // and set value to 0
            }
        }
        /************** end: functions. **************/
    }); // jQuery End
</script>
<style>
    div.close 
    {
         background: url("/merapi/img/close.png") no-repeat scroll 0 0 transparent !important;
    }
    #button-top { width: 100px; position: absolute; left: 75%; top: 40px; padding-left: 100px;overflow: hidden;}
    #button-top:hover, #button-bottom:hover {cursor: pointer;}
    .slide { position: relative;left:300px;margin: 0 auto; width: 70%;  text-align: center; }
    .slide img {position: relative; z-index: 100;}
    .slide p { padding:8px 16px; color: #fff; margin: 0; }
    .innerTop, .innerBottom { display:none;   z-index: -1;  }

    #button-bottom { width: 100px; position: absolute; left: 75%; top: 240px; padding-left: 100px;overflow: hidden;}
    .button:hover {
        background-color: #fff;
        color: #000;
    }
    .candle-checkbox{margin-left:27px;}
    .candle-btn{margin-left:1px;}
    .candle-btn .button{text-align: left;font-size:15px;}
    .candle-btn a{color:#A6AEAF;}
    .button a:hover{background:none;color:#000000;}
    .candle-btn a.button.icon {
        padding-left: 11px;
    }

    .icon-attach{
        padding-left: 30px;
        background: url("/Profiler/images/right/icon-attach.png") no-repeat 5px 6px;
        background-color: #fff;
    }
    .icon-upload{
        padding-left: 30px;
        background: url("/Profiler/images/right/icon-photo.png") no-repeat 5px 6px;
        background-color: #fff;
    }
    .icon-send{
        padding-left: 30px;
        background: url("/Profiler/images/right/icon-send.png") no-repeat 5px 6px;
        background-color: #fff;
    }
    .cd-input{width:100%;background: #E7EBEE;font-size:13px !important;}
    .cd-textarea{width:100%;background: #E7EBEE;font-size:13px !important;}
.slide #section_form label
        {
            width:100%
        }



    input[type=checkbox].css-checkbox {
        position: absolute; 
        overflow: hidden; 
        clip: rect(0 0 0 0); 
        height:1px; 
        width:1px; 
        margin:-1px; 
        padding:0;
        border:0;
    }

    input[type=checkbox].css-checkbox + label.css-label {
        padding-left:31px;
        height:20px; 
        display:inline-block;
        line-height:20px;
        background-repeat:no-repeat;
        background-position: 0 0;
        font-size:15px;
        color:#A6AEAF;
        vertical-align:middle;
        cursor:pointer;
        margin-right:26px;
    }

    input[type=checkbox].css-checkbox:checked + label.css-label {
        background-position: 0 -20px;
    }

    .css-label{ background-image:url("/Profiler/images/right/checkbox.png"); width:auto !important;}



    .select-style {
        border: 1px solid #ccc;
        width: 100%;
        height:35px;
        border-radius: 2px;
        overflow: hidden;
        background: #E7EBEE url("/Profiler/images/right/downarrow.png") no-repeat 100% 50%;
    }

    .select-style select {
        padding: 5px 8px;
        color:#3D3D3B;
        opacity: 0.5;
        font-size: 13px;
        width: 105%;
        border: none;
        box-shadow: none;
        background: transparent;
        background-image: none;
        -webkit-appearance: none;
    }

    .select-style select:focus {
        outline: none;
    }					

.candle_left_box
{
    float:left;
    width:35%;
    height:auto;
}
.candle_right_box
{
   float:right;
   width:64%;
   border-left:1px solid #ccc;
   height:auto;
   padding-left:20px; 
}
.candle_right_box_p1
{
    color:#000;
    font-size:60px;
    line-height:65px;
}
.candle_right_box_p2
{
    color:#CACACA;
    font-size:21px;
    line-height:26px;
}


</style>