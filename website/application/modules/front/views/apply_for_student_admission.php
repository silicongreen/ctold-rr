<html>
    <head>
        <title>Select School</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" type="text/css" media="all" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker.css" type="text/css" media="all" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.css" type="text/css" media="all" />
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
        <!--[if lt IE 9]>
         <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
         <script src="https://cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
        <style>
            #pluswrap {
                position: fixed;
                width: 100%;
                height:100%;
                display: flex;
                align-items: center;
                background: rgba(255, 255, 255, 0.91);
                top: 0;
            }

            body {
                margin: 0;
            }

            .plus {
                display: flex;
                margin: 0 auto;
            }
            #content_signup
            {
                display:none;
            }
            .error
            {
                font-size:10px;
                color:#C5232A;
            }
            label.valid {
                width: 24px;
                height: 24px;
                background: url(<?php echo base_url('styles/layouts/tdsfront/images/user_auth/valid.png'); ?>) center center no-repeat !important;
                display: inline-block;
                text-indent: -9999px;
                position: absolute;
                top:3px;
                right:-16px;
            }
            label.error {
                width: 24px;
                height: 24px;
                background: url(<?php echo base_url('styles/layouts/tdsfront/images/user_auth/error.png'); ?>) center center no-repeat;
                display: inline-block;
                text-indent: -9999px;
                position: absolute;
                top:3px;
                right:-16px;
            }
	#ajaxLoading{
		height: 24px;
		position: absolute;
		right: -16px;		
		top: 3px;
		width: 24px;
	}
        </style>
    </head>
    <body>
        <input type="hidden" id="ci_base_url" value="<?php echo base_url(); ?>" >
        <div id="pluswrap">
            <div class="plus">
                <img src="<?php echo base_url('styles/layouts/tdsfront/spelling_bee/loader_home_slider.gif'); ?>" />
            </div>
        </div>
        <div class="item content" id="content_signup">
            <div class="wrapper grey" >

                <div class="container">			  
                    <div class="col-md-12" style="padding:0px; margin-top: 40px;">
                        <h2 class="lead text-center editContent"  style="color:#66D56A; font-weight: bold;">
                            Sign Up For Student
                        </h2>
                    </div>
                    <div class="col-md-12">
                        <?php if (isset($error)) : ?>					
                            <div class="alert alert-danger">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-12">
                        <div class="row-fluid">
                            <form class="form-horizontal" id="apply_for_student_admission" method="post" action="<?php echo base_url('front/paid/apply_for_student_admission?back_url=' . $back_url . '&user_type=' . $user_type); ?>">
                                <input type="hidden" id="form1_data" name="form1_data" value="<?php echo $form1_data; ?>" >
                                <input type="hidden" id="paid_school_id" name="paid_school_id" value="<?php echo $paid_school_id; ?>" >
                                <div class="error_validation" class="col-md-12" ><?php echo validation_errors(); ?></div>
                                <fieldset>

                                    <!-- Form Name -->
                                    <legend></legend>

                                    <!-- Select Basic -->
                                    <div class="form-group">
                                        <label class="col-md-5 control-label" for="school_code">Unique Id</label>  
                                        <div class="col-md-4">
                                            <input type="text" name="admission_no" id="admission_no" required="" class="form-control input-md" placeholder="Enter School Admission ID / Set a Username *" />
											<span class="valid_admission_no" style="color:green;font-size: 13px;"></span>
											<span class="help-block"><font color="#ccc" size="1">It will be used for generating your username for ClassTune. Admission no. should include a-z , A-Z , 0-9 , - , _" </font></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-5 control-label" for="sn_username">User Name</label>  
                                        <div class="col-md-4">
                                            <input type="text" name="sn_username" id="sn_username" class="form-control input-md" <?php if ($post_data['add_guardian'] != "no") { ?>required=""<?php } ?> readonly placeholder="Generated Username *" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-5 control-label" for="school_code">Admission Date</label>  
                                        <div class="col-md-4">
                                            <input data-date-format="yyyy-mm-dd" type="text" class="form-control datepicker" required=""  name="admission_date" value="<?php echo $post_data['admission_date']; ?>"  placeholder="Select Admission date" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-5 control-label" for="school_code">Roll No (If available)</label>  
                                        <div class="col-md-4">
                                            <input type="text" name="class_roll_no" id="class_roll_no" class="form-control input-md" placeholder="Roll No. (Optional)" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-5 control-label" for="selectbasic">Shift Class and Section</label>
                                        <div class="col-md-4">
                                            <?php echo get_paid_school_class($paid_school_id); ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-5 control-label" for="school_code">Date of birth</label>  
                                        <div class="col-md-4">
                                            <input data-date-format="yyyy-mm-dd" type="text" class="form-control datepicker" required=""  name="date_of_birth" placeholder="Select Date of Birth" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-5 control-label" for="Gender">Select Gender</label>
                                        <div class="col-md-4">
                                            <div class="radio">
                                                <label for="radios-0">
                                                    <input type="radio" name="gender" id="radios-m" value="m" checked="checked">
                                                    Male
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label for="radios-f">
                                                    <input type="radio" name="gender" id="radios-f" value="f">
                                                    Female
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-5 control-label" for="school_code">Your City</label>  
                                        <div class="col-md-4">
                                            <input type="text" name="city" id="city" class="form-control input-md" required="" placeholder="Enter your City *" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-5 control-label" for="Person">Add Parent</label>
                                        <div class="col-md-4">
                                            <div class="radio">
                                                <label for="radios-0">
                                                    <input type="radio" id="radios-0" name="add_guardian" class="add_guardian" value="one" checked="checked">
                                                    One Person
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label for="radios-1">
                                                    <input type="radio" id="radios-1"  name="add_guardian" class="add_guardian" value="two" >
                                                    Two Persons
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label for="radios-2">
                                                    <input type="radio" id="radios-2"   name="add_guardian" class="add_guardian" value="no">
                                                    Skip
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group gfield checkbox-group">
                                        <label class="col-md-5 control-label" for="school_code">Choose From Existing</label>  
                                        <div class="col-md-4">
                                            <input type="checkbox" id="checkbox-1"  class="checkbox choose_guardian"  name="choose_guardian" value="choose" />
                                        </div>
                                    </div>
                                    
                                    <div class="form-group gfield choosebox-group" style="display:none;">
                                        <label class="col-md-5 control-label" for="school_code">Parent's User Name</label>  
                                        <div class="col-md-4">
                                            <input type="text" name="g_username" id="g_username" class="form-control input-md g_username" placeholder="Parent's Username *">
                                            <input type="hidden" id="g_id" name="g_id" value="">
											<span id="valid_g_username" style="color:green;font-size: 13px;"></span>
                                            <span class="g_fullname" style="color:green;font-size: 13px;"></span>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group gfield" >
                                        <label class="col-md-5 control-label" for="school_code">Parent's First Name</label>  
                                        <div class="col-md-4">
                                            <input type="text" name="gfirst_name" id="gfirst_name" class="form-control input-md" placeholder="Parent's First Name *">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group gfield" >
                                        <label class="col-md-5 control-label" for="school_code">Parent's Last Name</label>  
                                        <div class="col-md-4">
                                            <input type="text" name="glast_name" id="glast_name" class="form-control input-md" placeholder="Parent's Last Name *" />
                                        </div>
                                    </div>
                                    
                                    <div class="form-group gfield relationbox-group" >
                                        <label class="col-md-5 control-label" for="school_code">Relation</label>  
                                        <div class="col-md-4">
                                            <input type="text" name="relation" id="relation" class="form-control input-md" placeholder="Write Relationship *">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group gfield" >
                                        <label class="col-md-5 control-label" for="school_code">Parent's Password</label>  
                                        <div class="col-md-4">
                                            <input type="password" name="gpassword" id="gpassword" value="" class="form-control input-md" placeholder="Parent's Password *">
                                            <span class="help-block"><font color="#ccc" size="1">At least 6 character</font></span>
                                        </div>
                                    </div>

                                    <div class="form-group gfield2 checkbox-group2" style="display:none;">
                                        <label class="col-md-5 control-label" for="school_code">Choose From Existing (2nd)</label>  
                                        <div class="col-md-4">
                                            <input type="checkbox" id="checkbox-2"  class="checkbox choose_guardian2"  name="choose_guardian2" value="choose" />
                                        </div>
                                    </div>
                                    
                                    <div class="form-group gfield2 choosebox-group2" style="display:none;">
                                        <label class="col-md-5 control-label" for="school_code">Parent's User Name (2nd)</label>  
                                        <div class="col-md-4">
                                            <input type="text" name="g_username2" id="g_username2" class="form-control input-md g_username2" placeholder="Parent's User Name (2nd) *" />
                                            <input type="hidden" id="g_id2" name="g_id2" value="">
                                            <span class="g_fullname2" style="color:green;font-size: 13px;"></span>
											<span id="valid_g2_username" style="color:green;font-size: 13px;"></span>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group gfield2" style="display:none;">
                                        <label class="col-md-5 control-label" for="school_code">Parent's First Name (2nd)</label>  
                                        <div class="col-md-4">
                                            <input type="text" name="gfirst_name2" id="gfirst_name2" class="form-control input-md" placeholder="Parent's First Name (2nd) *" />
                                        </div>
                                    </div>
                                    
                                    <div class="form-group gfield2" style="display:none;">
                                        <label class="col-md-5 control-label" for="school_code">Parent's Last Name (2nd)</label>
                                        <div class="col-md-4">
                                            <input type="text" name="glast_name2" id="glast_name2" class="form-control input-md" placeholder="Parent's Last Name (2nd) *" />
                                        </div>
                                    </div>
                                    
                                    <div class="form-group gfield2 relationbox-group2" style="display:none;">
                                        <label class="col-md-5 control-label" for="school_code">Relation (2nd)</label>  
                                        <div class="col-md-4">
                                            <input type="text" name="relation2" id="relation2" class="form-control input-md" placeholder="Write Relationship (2nd) *" />
                                        </div>
                                    </div>
                                    
                                    <div class="form-group gfield2" style="display:none;">
                                        <label class="col-md-5 control-label" for="school_code">Parent's Password (2nd)</label>
                                        <div class="col-md-4">
                                            <input type="password" name="gpassword2" id="gpassword2" value="" class="form-control input-md" placeholder="Parent's Password (2nd) *" />
                                            <span class="help-block"><font color="#ccc" size="1">At least 6 character</font></span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-5 control-label" for="singlebutton"></label>
                                        <div class="col-md-4">
                                            <button name="submit" type="submit" id="submit" class="btn btn-primary btn-success btn-lg">
                                                <i class="fa fa-thumbs-up"></i> Apply
                                            </button>
                                        </div>
                                    </div>

                                </fieldset>
                            </form>
                        </div><!-- /.row-fluid -->
                    </div><!-- /.col-md-12 -->
                </div><!-- /.container -->

            </div><!-- /.wrapper -->
        </div>
		<div id="ajaxLoading" style="display: none">
			<img src="<?php echo base_url('styles/layouts/tdsfront/classtune_ajax_loader'); ?>/loader.gif" width="24" />
		</div>
    </body>
</html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="<?php echo base_url('scripts/iframe-resizer/js/iframeResizer.contentWindow.js?v=1'); ?>"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.14.0/jquery.validate.min.js"></script>
<script src="<?php echo base_url('scripts/apply_for_student_admission/apply_for_student_admission.js?v=2'); ?>"></script>

<script type="text/javascript">
//document.domain = "champs21.com";
    $(document).ready(function () {
		
		$('#admission_no').bind("cut copy paste",function(e) {
          e.preventDefault();
        });
		
		
        $(document).on("click", ".add_guardian", function () {

            if ($(this).val() == "no")
            {
                $(".gfield").hide();
                $(".gfield2").hide();

                $(".gfield input").attr("required", false);
                $(".gfield2 input").attr("required", false);
            }

            if ($(this).val() == "one")
            {
                $(".gfield").show();
                $(".gfield2").hide();

                $(".gfield2 input").attr("required", false);
                $(".gfield input").attr("required", true);
            }
            if ($(this).val() == "two")
            {
                $(".gfield").show();
                $(".gfield2").show();

                $(".gfield2 input").attr("required", true);
                $(".gfield input").attr("required", true);
            }

            $('input[type=checkbox]').attr('checked', false);
            $('input[type=checkbox]').attr('required', false);
            $(".choosebox-group").hide();
            $(".choosebox-group input").attr("required", false);
            $(".choosebox-group2").hide();
            $(".choosebox-group2 input").attr("required", false);

            var height = $("#apply_for_student_admission").height() + 150;

            var timeout_var = setTimeout(function () {
                window.parentIFrame.size(height);
                clearTimeout(timeout_var)
            }, 100);
//        window.parent.document.getElementById('iframe_change_height').style.height = height +'px';
        });
        $('.datepicker').datepicker({startView: 'decade',autoclose: true});
      

        $('.g_username').blur(function () {
            if (!$(this).val())
            {
                $('.g_fullname').html("");
                $('#g_username-error').remove();
            }
        });
        $('.g_username2').blur(function () {
            if (!$(this).val())
            {
                $('.g_fullname2').html("");
                $('#g_username2-error').remove();
            }
        });
        $(document).on("click", ".choose_guardian", function () {
            if ($('.choose_guardian').is(':checked'))
            {
                $(".gfield").hide();
                $(".gfield input").attr("required", false);

                $(".checkbox-group").show();
                $(".choosebox-group").show();
                $(".choosebox-group input").attr("required", true);
                $(".relationbox-group").show();
                $(".relationbox-group input").attr("required", true);
            }
            else
            {
                $(".gfield").show();
                $(".gfield input").attr("required", true);

                $(".choosebox-group").hide();
                $(".choosebox-group input").attr("required", false);

                $('input[type=checkbox]').attr('required', false);
            }
            var height = $("#apply_for_student_admission").height() + 150;
            var timeout_var1 = setTimeout(function () {
                window.parentIFrame.size(height);
                clearTimeout(timeout_var1)
            }, 100);

        });
        $(document).on("click", ".choose_guardian2", function () {
            if ($('.choose_guardian2').is(':checked'))
            {
                $(".gfield2").hide();
                $(".gfield2 input").attr("required", false);

                $(".checkbox-group2").show();
                $(".choosebox-group2").show();
                $(".choosebox-group2 input").attr("required", true);
                $(".relationbox-group2").show();
                $(".relationbox-group2 input").attr("required", true);
            }
            else
            {
                $(".gfield2").show();
                $(".gfield2 input").attr("required", true);

                $(".choosebox-group2").hide();
                $(".choosebox-group2 input").attr("required", false);

                $('input[type=checkbox]').attr('required', false);
            }
            var height = $("#apply_for_student_admission").height() + 150;
            var timeout_var2 = setTimeout(function () {
                window.parentIFrame.size(height);
                clearTimeout(timeout_var2)
            }, 100);

        });
    });
</script>

<script>
    $('#pluswrap').show();
    $(window).bind("load", function () {
        $('#pluswrap').hide();
        $("#content_signup").show();
    });
    $("#admission_no").bind("keyup paste", function () {
        var paid_school_id = $("#paid_school_id").val();
        if (paid_school_id < 10)
            $("#sn_username").val(0 + paid_school_id + "-" + $(this).val());
        else
            $("#sn_username").val(paid_school_id + "-" + $(this).val());
    });
</script>
<style>
    .error_validation p {
        color: red;
        padding: 5px 0px;
        font-weight: bold;
    }
</style>