<!--<a href="#" class="topopup">
        <img src="<?php echo base_url('upload/gallery/image/category/icon-info.png'); ?>" alt="<?php echo $row->name ?>" style="margin: 0 auto;display: block;">
</a>-->

<div id="toPopup" class="school_search_box"> 

    <div class="close"></div>
    <div style="clear:both;"></div>
    <div id="popup_content"> <!--your content start-->
        <div style="float:left;width:45%;height:200px;margin-top:100px;">
            <?php echo form_open('schoolsearch', array('class' => 'white-pink', 'enctype' => "multipart/form-data")); ?>

<!--<form action="<?php #echo site_url('home/schoolsearch');  ?>" method="post" class="white-pink"><h1>Contact Form<span>Please fill all the texts in the fields.</span></h1>-->
            <p class="search_form_box">
                <label><input id="name" type="text" name="name" placeholder="Name"></label>
                <label><input id="division" type="text" name="division" placeholder="Division"></label>		
                <label><input id="level" type="text" name="level" placeholder="Level"></label>		
                <label><input type="submit" value=""></label>
            </p>
            <?php echo form_close(); ?> 

        </div>
        <div style="float:right; width:55%;">
<!--            <p class="f2" style="color:red;font-size:25px;line-height:30px;">We are the Bridge Between Schools & Parents</p>
            <p class="f5" style="color:#CACACA;font-size:17px;line-height:21x;">Search & find information on 100+ schools in the Dhaka and around the nation</p>
            <hr style="border:dashed grey; border-width:1px 0 0 0; height:0;line-height:0px;font-size:0;margin:0;padding:0;"></hr>
            <p class="f5" style="color:#000;font-size:15px;line-height:20px;margin-top:30px;">Share your School's information on Champs21.com now!!</p>

            <p><input type="button" class="btn_myschool" value="My School"></p>-->
            <p style="position:relative; top:100%; text-align: right;">
                <button class="btn_school_entry f2" value="">Create Your School Website</button>
            </p>
        </div>
        <div style="clear:both;"></div>
    </div> <!--your content end-->
    
    <div style="width: 100%; height:61px; position:absolute; bottom:0px; background: #fff; opacity: .9; margin: 0px -20px;">
        <div class="school_arrow f2">OR, SEARCH BY ALPHABET</div>
        <div style="float:right; padding-top:16px; margin-right:15px;">
            <ul class="school-search-ul">
                <?php foreach (range('A', 'Z') as $alphabet){ ?>
                    <li class="f2"><a href="<?php echo base_url() . 'schoolsearch?str=' . $alphabet; ?>"><?php echo $alphabet; ?></a></li>
                    <?php if ($alphabet < 'Z') { ?>
                    <li class="bar">|</li>
                    <?php }?>
                <?php } ?>
            </ul>
        </div>
    </div>

    <div class="slide innerTop-school">
        <?php echo form_open('', array('id' => 'validate_form_school', 'class' => 'validate_form', 'enctype' => "multipart/form-data")); ?>
        <div id="section_form_school">
            <h2 class="f2">My School</h2>

            <br/>
            <label class="candle-input">
                <input type="text" name="school_name" id="school_name" class="cd-input f5" placeholder="School Name">
            </label>
            <label class="candle-input">
                <input type="text" name="contact" id="contact" class="cd-input f5" placeholder="contact">
            </label>
            <label class="candle-input">
                <input type="text" name="address" id="address" class="cd-input f5" placeholder="address">
            </label>
            <label class="candle-input">
                <input type="text" name="zip_code" id="zip_code" class="cd-input f5" placeholder="zip_code">
            </label>
            <label class="candle-textarea">
                <textarea class="cd-textarea f5" id="about" name="about" placeholder="about"></textarea>
            </label>

<!--            <label class="candle-btn">


                <a class="button f5 icon-attach" id="file_attach_school" href="javascript:void(0);">Upload Picture</a>
                <a class="button f5 icon-upload" id="image_attach_school" href="javascript:void(0);">Upload Logo</a>
                <a class="button f5 icon-send" id="candle_send_school" href="javascript:void(0);">Send</a>

                <input type="submit" id="submit_form_school" style="display:none;" value="Submit" />
            </label>-->
            <div style="clear:both;"></div>
            <div style="width:160px;float:left;overflow: hidden;margin-right:10px;">
                <label class="candle-btn">
                    <a class="button f5 icon-attach" id="file_attach_school" href="javascript:void(0);">Upload Picture</a>
                </label>
            </div>
            <div style="width:150px;float:left;overflow: hidden;margin-right:10px;">
                <label class="candle-btn">
                    <a class="button f5 icon-upload" id="image_attach_school" href="javascript:void(0);">Upload Logo</a>
                </label>
            </div>
            <div style="width:95px;float:left;overflow: hidden;margin-right:10px;">
                <label class="candle-btn">
                   <a class="button f5 icon-send" id="candle_send_school" href="javascript:void(0);">Send</a>
                </label>
            </div>
            <label class="candle-input">
                <input type="file" style="display:none;"  id="attach_file_school" name="picture"  />
            </label>
            <label class="candle-input">
                <input type="file" style="display:none;"  id="leadimage_school" name="logo"  />
            </label>

        </div>
        <?php echo form_close(); ?>    
        <div id="section_thanks_school" style="display:none;">
            <p><img src="<?php echo base_url('Profiler/images/right/school_thanks.png'); ?>" width="500" height="100" alt="Candle" style="margin: 0 auto;display: block;"> </p>
        </div>
    </div>



</div> <!--toPopup end-->

<div class="loader"></div>
<div id="backgroundPopup"></div>
<script type="text/javascript">
    /* 
     author: istockphp.com
     */

    var alreadyopen = 0;
    function checkLogin()
    {
        $.ajax({
            type: "GET",
            url: $("#base_url").val() + "front/ajax/get_category_main/",
            data: {},
            async: false,
            success: function(data) {
                if (data == 0)
                {
                    $(".tz_social a.login-user").trigger('click');
                    return false;
                }
                else
                {
                    alreadyopen = 1;
                    return true;
                }
            }
        });
    }

    jQuery(function($) {

        $('#file_attach_school').click(function(e) {
            $('#attach_file_school').trigger('click');
        });

        $(document).on('change', '#attach_file_school', function(e) {

            var filename = $(this).val();

            var ext = filename.split('.').pop().toLowerCase();

            if ($.inArray(ext, ['png', 'jpg', 'gif', 'jpeg']) == -1) {
                alert('not valid!');
                return false;
            }
            else {
                $('#file_attach_school').html(filename);
            }

        });


        $('#image_attach_school').click(function(e) {
            $('#leadimage_school').trigger('click');
        });
        $(document).on('change', '#leadimage_school', function(e) {

            var filename = $(this).val();
            var ext = filename.split('.').pop().toLowerCase();

            if ($.inArray(ext, ['png', 'jpg', 'gif', 'jpeg']) == -1) {
                alert('not valid!');
            }
            else {
                $('#image_attach_school').html(filename);
            }
            return false;

        });

        $("li.topopup").click(function() {
            loading(); // loading
            setTimeout(function() { // then show popup, deley in .5 second
                $('html,body').animate({scrollTop : 0});
                loadPopup(); // function show popup 
            }, 500); // .5 second
            return false;
        });


        $('button.btn_school_entry').click(function() {
            checkLogin();
            loading();
            if (alreadyopen == 1)
            {
                var icc_quiz_cookie = readCookie('c21_icc_quiz');
                var icc_quiz_level = readCookie('c21_icc_quiz_level');
                
                if(icc_quiz_cookie === false || icc_quiz_level === false) {
                    icc_quiz_cookie = '';
                    icc_quiz_level = '';
                }
                
                $("form#validate_form_school").append('<input type="hidden" id="icc_quiz_cookie" name="icc_quiz_cookie" value="' + icc_quiz_cookie + '">');
                $("form#validate_form_school").append('<input type="hidden" id="icc_quiz_level" name="icc_quiz_level" value="' + icc_quiz_level + '">');
                $("form#validate_form_school").append('<input type="hidden" id="add_to_school" name="add_to_school" value="true">');
                    
                closeloading();
                $('.innerTop-school').animate({'left': '3px'}, {
                    duration: 1000,
                    step: function() {
                        $('div#popup_content').hide();
                        $('.innerTop-school').css("display", 'block');

                    }
                });
            }
            else
            {
                closeloading();
            }

        });


        /* event for close the popup */
        $("div.close").hover(
        function() {
            $('span.ecs_tooltip').show();
        },
        function() {
            $('span.ecs_tooltip').hide();
        }
    );

        $("div.close").click(function() {
            disablePopup();  // function close pop up
        });

        $(this).keyup(function(event) {
            if (event.which == 27) { // 27 is 'Ecs' in the keyboard
                disablePopup();  // function close pop up
            }
        });

        $("div#backgroundPopup").click(function() {
            disablePopup();  // function close pop up
        });

        $('a.livebox').click(function() {
            alert('Hello World!');
            return false;
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
                $("#toPopup").fadeIn(0500); // fadein popup div
                $("#backgroundPopup").css("opacity", "0.7"); // css opacity, supports IE7, IE8
                $("#backgroundPopup").fadeIn(0001);
                popupStatus = 1; // and set value to 1
            }
        }

        function disablePopup() {
            if (popupStatus == 1) { // if value is 1, close popup
                $("#toPopup").fadeOut("normal");
                $("#backgroundPopup").fadeOut("normal");
                popupStatus = 0;  // and set value to 0
            }
        }
        
        
        $("form#validate_form_school").submit(function(event) {

            //disable the default form submission
            event.preventDefault();
            if ($.trim($("#school_name").val()) == "")
            {
                alert("School name must not be empty");
            }
            else if ($.trim($("#contact").val()) == "")
            {
                alert("contact must not be empty");
            }
            else if ($.trim($("#address").val()) == "")
            {
                alert("address must not be empty");
            }
            else if ($.trim($("#about").val()) == "")
            {
                alert("About School must not be empty");
            }
            else
            {
                loading(); // loading
                var formData = new FormData($(this)[0]);
                
                $.ajax({
                    url: $("#base_url").val() + "front/ajax/add_school/",
                    type: 'POST',
                    dataType : 'json',
                    data : formData,
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
                            $('#section_form_school').hide();
                            $("#section_thanks_school").fadeIn(500);
                        }
                        
                        eraseCookie('c21_icc_quiz');
                        eraseCookie('c21_icc_quiz_level');
                    }
                });

                return false;
            }
        });

        $("#candle_send_school").click(function() {

            $('#submit_form_school').trigger('click');

        });
        
        /************** end: functions. **************/
    }); // jQuery End
</script>

<style>
    #button-top { width: 100px; position: absolute; left: 75%; top: 40px; padding-left: 100px;overflow: hidden;}
    #button-top:hover, #button-bottom:hover {cursor: pointer;}
    .slide { position: relative;left:300px;margin: 0 auto; width: 550px;  text-align: center; }
    .slide img {position: relative; z-index: 100;}
    .slide p { padding:8px 16px; color: #fff; margin: 0; }
    .innerTop-school, .innerBottom { display:none;   z-index: -1;  }

    #button-bottom { width: 100px; position: absolute; left: 75%; top: 240px; padding-left: 100px;overflow: hidden;}
    .school_search_box{
        background: url("<?php echo base_url('Profiler/images/right/schools_bg.png')?>") no-repeat !important;
        background-size: 100% !important;
    }
    .search_form_box{margin-left:110px;}
    
    .search_form_box input[type="text"] {
        height: 35px;
        margin-bottom: 5px;
        width: 90%;
        font-size: 15px;
    }
    .search_form_box input[type="submit"]{
        background:url("<?php echo base_url('Profiler/images/right/btn-schools-search.png')?>") no-repeat !important;
        background-size:100% !important;
        border: medium none;
        border-radius: 0;
        float: right;
        height: 40px;
        margin-right: 122px;
        width: 110px;
        
    }
    
    .btn_school_entry {
        background-color: #db3434;
        border: medium none;
        color: #fff;
        font-size: 20px;
        font-weight: bold;
        height: 70px;
        line-height: 1.25;
        width: 35%;
    }
    .btn_school_entry:hover{
        background-color: #bbbbbb;
        -webkit-transition: all 0.5s ease;
        -moz-transition: all 0.5s ease;
        -o-transition: all 0.5s ease;
        -ms-transition: all 0.5s ease;
        transition: all 0.5s ease;
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

    .css-label{ background-image:url("Profiler/images/right/checkbox.png"); }



    .select-style {
        border: 1px solid #ccc;
        width: 440px;
        height:35px;
        border-radius: 2px;
        overflow: hidden;
        background: #E7EBEE url("Profiler/images/right/downarrow.png") no-repeat 100% 50%;
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


.school_arrow {
    float:left;
    height: 61px;
    width:23%;    
    background-color: #a7abb0;    
    padding: 20px 0;
    padding-left: 20px;
    position: relative;
    font-size:15px;
    font-family: "Bree Serif";
    color: #fff;
}
.school_arrow:after {
  content: '';
  position: absolute;
  top: 0px;
  left: 100%;
  width: 0;
  height: 0;
  border: 30px solid transparent;
  border-left: 40px solid #a7abb0;
}
.school_arrow:before {
  content: '';
  position: absolute;
  top: 0px;
  left: 100%;
  width: 0;
  height: 0;
  border: 30px solid transparent;
  border-left: 40px solid #a7abb0;
}


.candle-input {
    float: left;
    margin: 5px;
}

.candle-textarea {
    float: left;
    margin: 5px;
}

#section_form_school {
  background-color: #ffffff;
  height: 259px;
  margin-bottom: 10px;
  margin-top: 85px;
  padding: 1px 20px;
}

.school-search-ul li {
    display: inline-block;
    float: left;
    padding: 3px;
    font-size: 13px;
    cursor: pointer;
}
.school-search-ul li a:hover {
    font-size: 18px;
    color: #000;
    text-decoration: underline;
    font-weight: bold;
}

@media all and (min-width: 200px) and (max-width: 314px) {
    #toPopup
    {
        width:auto !important;
        z-index:1 !important;
    }
    .school_search_box
    {
        background-size: 130% !important;
        background-position: center center !important;
    }
}





</style>
