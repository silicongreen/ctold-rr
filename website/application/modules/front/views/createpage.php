<?php $s_ci_key = (isset($ci_key)) ? $ci_key : NULL; ?>
<?php
            
    $has_profile_img = FALSE;
    //$profile_image_url = base_url('upload/free_user_profile_images/2.jpg');
    $profile_image_url = base_url('Profiler/images/right/nopic.png');

    if( free_user_logged_in() ){
        $user_data = get_free_user_session();

        if( !empty($user_data['profile_image'] )){
            //$profile_image_url = base_url() . 'upload/free_user_profile_images/' . $user_data->profile_image;
            $profile_image_url = $user_data['profile_image'];
            $has_profile_img = TRUE;

        }else{
            $_user_data = get_user_data();

            $user_data = get_free_user_session();

            if( !empty($_user_data->profile_image) ){
                $profile_image_url = $_user_data->profile_image;
                $has_profile_img = TRUE;
            }

        }
    }

?>
<div class="container" style="width: 77%;min-height:250px;">	 
	<div style="margin:30px 20px;height:60px;">
			<div style="float:left">
				<h2 class="f2">Create Your Page</h2>
			</div>
			<div class="header-bg" style="display:block;float:right;  margin-top:5px;">
				<div style="float: left;margin:5px;">                
					<form method="get" class="searchform" action="<?php echo base_url('search'); ?>" role="search">                    
						<input class="field" name="s" id="s" class='search' placeholder="Search this site" type="search" style="border-radius: 6px; -moz-border-radius: 6px; -webkit-border-radius: 6px; width: 220px; margin-top: 3px;">
						<input class="submit search-button" value="" type="submit" />
					</form>                
				</div>
			</div>
	</div>
	<div id="toPopup"> 

    
    
    <div class="createpage">
        <?= form_open('', array('id' => 'validate_form_school', 'class' => 'validate_form', 'enctype' => "multipart/form-data")); ?>
        <div id="section_form_school">
            <div class="createpage_left">
                <label class="candle-input">
                    <input type="text" name="school_name" id="school_name" class="cd-input f5" placeholder="School Name">
                    <span>As written in you School ID.</span>
                </label>
                <label class="candle-input">
                    <input type="text" name="contact" id="contact" class="cd-input f5" placeholder="Contact Number">
                    <span>Legal Mobile Number (example:8801716824757).</span>
                </label>
                <label class="candle-input">
                    <input type="text" name="address" id="address" class="cd-input f5" placeholder="Address">
                    <span>Mailing Address as we will send you a document mail.</span>
                </label>
                <label class="candle-input">
                    <input type="text" name="zip_code" id="zip_code" class="cd-input f5" placeholder="Zip Code">
                    <span>Mailing Address Zip Code.</span>
                </label>
                <label class="candle-textarea">
                    <textarea class="cd-textarea f5" id="about" name="about" placeholder="About You"></textarea>
                    <span>Write something for expressing yourself.</span>
                </label>
            </div>
            <div class="createpage_right">
                <img src="<?php echo base_url('Profiler/images/right/have_a_smiley_face.png');?>" style="width:100%;" />
                <img src="<?php echo $profile_image_url;?>" style="width:100%;" />
                <p>All your 
                <span class="a">Information</span> need to be
                <span class="b">Parfect</span>.
                </p>
            </div>
            <div class="createpage_full">
                <label>


                    <a class="button f5 icon-attach" id="file_attach_school" href="javascript:void(0);">Upload National ID</a>
                    <a class="button f5 icon-upload" id="image_attach_school" href="javascript:void(0);">Upload School ID</a>
                    <a class="button f5 icon-send" id="candle_send_school" href="javascript:void(0);">Send</a>

                    <input type="submit" id="submit_form_school" style="display:none;" value="Submit" />
                </label>
                <label class="candle-input">
                    <input type="file" style="display:none;"  id="attach_file_school" name="national_card"  />
                </label>
                <label class="candle-input">
                    <input type="file" style="display:none;"  id="leadimage_school" name="school_card"  />
                </label>
            </div>

        </div>
        <?= form_close(); ?>    
        <div id="section_thanks_school" style="display:none;">
            <p><img src="<?php echo base_url('Profiler/images/right/school_thanks.png'); ?>" width="500" height="100" alt="Candle" style="margin: 0 auto;display: block;"> </p>
        </div>
    </div>



</div> <!--toPopup end-->

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
                
                var formData = new FormData($(this)[0]);
                $.ajax({
                    url: $("#base_url").val() + "front/ajax/createteacherpage/",
                    type: 'POST',
                    data: formData,
                    async: false,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(returndata)
                    {
                        
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
    .createpage { margin: 0 auto; width: 90%;  text-align: center; }  
    .createpage label{width:100%;}
    .createpage label span{color: #ccc;    float: left;    font-size: 11px;}
    .createpage p { padding:8px 16px; color: #fff; margin: 0; }
    #button-bottom { width: 100px; position: absolute; left: 75%; top: 240px; padding-left: 100px;overflow: hidden;}
    .createpage_left{width:60%;float:left;}
    .createpage_right{width:30%;float:right;padding-left:20px;}
    .createpage_right span{text-align:left;font-size: 16px;}
    .createpage_full{width:100%;}
    .createpage_right p{color:gray;font-size:27px;}
    .createpage_right span.a{color:#71B0DF;font-size:35px;font-weight: bold;line-height: 40px;}
   .createpage_right span.b{color:#DB3434;font-size:37px;font-weight: bold;line-height: 40px;}


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

	
</div>

<style>
#backgroundPopup { 
	z-index:5000;
	position: fixed;
	display:none;
	height:100%;
	width:100%;
	background:#000000;	
	top:0px;
	left:0px;
}
#toPopup {
	font-family: "lucida grande",tahoma,verdana,arial,sans-serif;
    background: none repeat scroll 0 0 #FFFFFF;
    padding: 40px 20px !important;	
    border-radius: 3px 3px 3px 3px;
    color: #333333;
    display: block !important;
    font-size: 14px;
    position: relative !important;
    left: 0px !important;
    top: 0px !important;
    width: 96% !important;
    z-index: 6000 !important;
	margin:30px 20px !important;
}
div.loader {
    background: url("../merapi/img/bx_loader.gif") no-repeat scroll 0 0 transparent;
    height: 32px;
    width: 32px;
	display: none;
	z-index: 9999;
	top: 40%;
	left: 50%;
	position: absolute;
	margin-left: -10px;
}
div.close {
    background: url("../merapi/img/close.png") no-repeat scroll 0 0 transparent;
    bottom: 30px;
    cursor: pointer;
    float: right;
    height: 30px;
    left: 10px;
    position: relative;
    width: 31px;
	display:none !important;
}
</style>