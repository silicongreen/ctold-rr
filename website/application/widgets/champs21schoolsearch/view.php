<!--<a href="#" class="topopup">
        <img src="<?php echo base_url('upload/gallery/image/category/icon-info.png'); ?>" alt="<?php echo $row->name ?>" style="margin: 0 auto;display: block;">
</a>-->

<div id="toPopup"> 

    <div class="close"></div>
    <div style="clear:both;"></div>
    <div id="popup_content"> <!--your content start-->
        <div style="float:left;width:55%;height:300px;">
            <?php echo form_open('schoolsearch', array('class' => 'white-pink', 'enctype' => "multipart/form-data")); ?>

<!--<form action="<?php #echo site_url('home/schoolsearch');  ?>" method="post" class="white-pink"><h1>Contact Form<span>Please fill all the texts in the fields.</span></h1>-->
            <p>
                <label><span>Name :</span><input id="name" type="text" name="name" placeholder="Your Full Name"></label>
                <label><span>Division</span><input id="division" type="text" name="division" placeholder="Valid Division"></label>		
                <label><span>Level</span><input id="level" type="text" name="level" placeholder="Valid Level"></label>		
                <label><span>&nbsp;</span><input type="submit" class="button" value="Search"></label>
            </p>
            <?php echo form_close(); ?> 

        </div>
        <div style="float:right;width:45%;border-left:1px solid #ccc;height:300px;padding-left:20px;">
            <p class="f2" style="color:red;font-size:25px;line-height:30px;">We are the Bridge Between Schools & Parents</p>
            <p class="f5" style="color:#CACACA;font-size:17px;line-height:21x;">Search & find information on 100+ schools in the Dhaka and around the nation</p>
            <hr style="border:dashed grey; border-width:1px 0 0 0; height:0;line-height:0px;font-size:0;margin:0;padding:0;"></hr>
            <p class="f5" style="color:#000;font-size:15px;line-height:20px;margin-top:30px;">Share your School's information on Champs21.com now!!</p>

            <p><input type="button" class="btn_myschool" value="My School"></p>
        </div>
        <div style="clear:both;"></div>
        <div style="width:100%;">
            <ul>
                <li><a href="<?php echo base_url() . 'schoolsearch?str=A'; ?>">A</a></li>
                <li class="bar">|</li>
                <li><a href="<?php echo base_url() . 'schoolsearch?str=B'; ?>">B</a></li>
                <li class="bar">|</li>
                <li><a href="<?php echo base_url() . 'schoolsearch?str=C'; ?>">C</a></li>
                <li class="bar">|</li>
                <li><a href="<?php echo base_url() . 'schoolsearch?str=D'; ?>">D</a></li>
                <li class="bar">|</li>
                <li><a href="<?php echo base_url() . 'schoolsearch?str=E'; ?>">E</a></li>
                <li class="bar">|</li>
                <li><a href="<?php echo base_url() . 'schoolsearch?str=F'; ?>">F</a></li>
                <li class="bar">|</li>
                <li><a href="<?php echo base_url() . 'schoolsearch?str=G'; ?>">G</a></li>
                <li class="bar">|</li>
                <li><a href="<?php echo base_url() . 'schoolsearch?str=H'; ?>">H</a></li>
                <li class="bar">|</li>
                <li><a href="<?php echo base_url() . 'schoolsearch?str=I'; ?>">I</a></li>
                <li class="bar">|</li>
                <li><a href="<?php echo base_url() . 'schoolsearch?str=J'; ?>">J</a></li>
                <li class="bar">|</li>
                <li><a href="<?php echo base_url() . 'schoolsearch?str=K'; ?>">K</a></li>
                <li class="bar">|</li>
                <li><a href="<?php echo base_url() . 'schoolsearch?str=L'; ?>">L</a></li>
                <li class="bar">|</li>
                <li><a href="<?php echo base_url() . 'schoolsearch?str=M'; ?>">M</a></li>
                <li class="bar">|</li>
                <li><a href="<?php echo base_url() . 'schoolsearch?str=N'; ?>">N</a></li>
                <li class="bar">|</li>
                <li><a href="<?php echo base_url() . 'schoolsearch?str=O'; ?>">O</a></li>
                <li class="bar">|</li>
                <li><a href="<?php echo base_url() . 'schoolsearch?str=P'; ?>">P</a></li>
                <li class="bar">|</li>
                <li><a href="<?php echo base_url() . 'schoolsearch?str=Q'; ?>">Q</a></li>
                <li class="bar">|</li>
                <li><a href="<?php echo base_url() . 'schoolsearch?str=R'; ?>">R</a></li>
                <li class="bar">|</li>
                <li><a href="<?php echo base_url() . 'schoolsearch?str=S'; ?>">S</a></li>
                <li class="bar">|</li>
                <li><a href="<?php echo base_url() . 'schoolsearch?str=T'; ?>">T</a></li>
                <li class="bar">|</li>
                <li><a href="<?php echo base_url() . 'schoolsearch?str=U'; ?>">U</a></li>
                <li class="bar">|</li>
                <li><a href="<?php echo base_url() . 'schoolsearch?str=V'; ?>">V</a></li>
                <li class="bar">|</li>
                <li><a href="<?php echo base_url() . 'schoolsearch?str=W'; ?>">W</a></li>
                <li class="bar">|</li>
                <li><a href="<?php echo base_url() . 'schoolsearch?str=X'; ?>">X</a></li>
                <li class="bar">|</li>
                <li><a href="<?php echo base_url() . 'schoolsearch?str=Y'; ?>">Y</a></li>
                <li class="bar">|</li>
                <li><a href="<?php echo base_url() . 'schoolsearch?str=Z'; ?>">Z</a></li>					
            </ul>
        </div>
    </div> <!--your content end-->

    <div class="slide innerTop">
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

            <label class="candle-btn">


                <a class="button f5 icon-attach" id="file_attach_school" href="javascript:void(0);">Upload Picture</a>
                <a class="button f5 icon-upload" id="image_attach_school" href="javascript:void(0);">Upload Logo</a>
                <a class="button f5 icon-send" id="candle_send_school" href="javascript:void(0);">Send</a>

                <input type="submit" id="submit_form_school" style="display:none;" value="Submit" />
            </label>
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


        $('input.btn_myschool').click(function() {
            checkLogin();
            loading();
            if (alreadyopen == 1)
            {
                closeloading();
                $('.innerTop').animate({'left': '3px'}, {
                    duration: 1000,
                    step: function() {
                        $('div#popup_content').hide();
                        $('.innerTop').css("display", 'block');

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
                            $('#section_form_school').hide();
                            $("#section_thanks_school").fadeIn(500);
                        }
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
    .innerTop, .innerBottom { display:none;   z-index: -1;  }

    #button-bottom { width: 100px; position: absolute; left: 75%; top: 240px; padding-left: 100px;overflow: hidden;}






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










</style>
