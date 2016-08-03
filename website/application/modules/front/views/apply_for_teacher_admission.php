
<html>
    <head>
        <title>Select School</title>
        <link rel="stylesheet" id="bootstrap-css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" type="text/css" media="all" />
        <link rel="stylesheet" id="bootstrap-css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker.css" type="text/css" media="all" />
        <link rel="stylesheet" href="<?php echo base_url('styles/layouts/tdsfront/css/intl-tel-input/intlTelInput.css'); ?>" />
		<link rel="stylesheet" href="<?php echo base_url('styles/layouts/tdsfront/css/intl-tel-input/isValidNumber.css'); ?>" />
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
                                Sign Up For Teacher
                        </h2>
                    </div>
                    <div class="col-md-12">
                        <?php if(isset($error)) :?>					
                                <div class="alert alert-danger">
                                  <?php echo $error; ?>
                                </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-12">
                        <div class="row-fluid">
                            <form id="apply_for_teacher_admission" class="form-horizontal col-md-10" method="post" action="<?php echo base_url('front/paid/apply_for_teacher_admission?back_url='.$back_url.'&user_type='.$user_type); ?>">
                                <input type="hidden" id="form1_data" name="form1_data" value="<?php echo $form1_data; ?>" >
                                <input type="hidden" id="paid_school_id" name="paid_school_id" value="<?php echo $paid_school_id; ?>" >
                                    <div class="error_validation" class="col-md-12" ><?php echo validation_errors(); ?></div>
                                    <fieldset>

                                        <!-- Form Name -->
                                        <legend></legend>

                                        <!-- Select Basic -->
                                         <div class="form-group">
                                            <label class="col-md-4 control-label" for="school_code">Employee No</label>  
                                            <div class="col-md-4">
                                                <input type="text" name="admission_no" id="admission_no" value="<?php echo $post_data['admission_no']; ?>" required="" class="form-control input-md" >
                                                <span class="help-block"></span>  
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-4 control-label" for="sn_username">User Name</label>  
                                            <div class="col-md-4">
                                                <input type="text" name="tn_username" id="tn_username" class="form-control input-md" readonly placeholder="User Name *">                                            
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="col-md-4 control-label" for="school_code">Joining Date</label>  
                                            <div class="col-md-4">

                                                    <input data-date-format="yyyy-mm-dd" type="text" class="form-control datepicker" required=""  name="joining_date" value="<?php echo $post_data['joining_date']; ?>" >



                                            </div>
                                        </div>
                                        
										<div class="form-group">
											<label class="col-xs-3 text-left" for="Gender">Select Gender</label>
											<div class="col-xs-2">
												<div class="radio">
													<label for="radios-0">
														<input type="radio" <?php if($post_data['gender']!="m") { ?> checked="checked"<?php } ?> name="gender" id="radios-0" value="m" checked="checked">
														Male
													</label>
												</div>
											</div>
											<div class="col-xs-2">
												<div class="radio">
													<label for="radios-1">
														<input type="radio" <?php if($post_data['gender']=="f") { ?> checked="checked"<?php } ?> name="gender" id="radios-1" value="f">
														Female
													</label>
												</div>
											</div>
										</div>

                                        <div class="form-group">
                                            <label class="col-md-4 control-label" for="selectbasic">Shift Class and Section (Select if only class teacher)</label>

                                            <div class="col-md-4">
                                                <?php echo get_paid_school_class($paid_school_id,"",true); ?>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="col-md-4 control-label" for="job_title">Job Title</label>  
                                            <div class="col-md-4">
                                                <input type="text" name="job_title" id="job_title" value="<?php echo $post_data['job_title']; ?>"  class="form-control input-md" >
                                                <span class="help-block"></span>  
                                            </div>
                                        </div>

                                        <div class="form-group">
											<label class="col-xs-3 text-left">Mobile Phone</label>
											<div class="col-xs-8">
												<input type="tel" class="form-control" id="mobile_phone" name="mobile_phone" value="<?php echo $post_data['mobile_phone']; ?>" />
												<span id="valid-msg" class="hide">✓ Valid</span>
												<span id="error-msg" class="hide">Invalid number</span>
											</div>
										</div>
										<div class="form-group">
											<label class="col-xs-3 text-left">Country</label>
											<div class="col-xs-5">
												<input type="hidden" id="country_id" name="country_id" value="" >
												<input type="text" name="parent_country" value="" id="parent_country" class="form-control input-md" required="" placeholder="Country*" readonly>
												
												<?php echo form_error('country', '<div class="error">', '</div>'); ?>
											</div>
										</div>	



                                        <div class="form-group">
                                            <label class="col-md-4 control-label" for="selectbasic">Department</label>

                                            <div class="col-md-4">
                                                <?php echo get_paid_employee_department_droupdown($paid_school_id,$post_data['employee_department_id']); ?>
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <label class="col-md-4 control-label" for="selectbasic">Category</label>

                                            <div class="col-md-4">
                                                <?php echo get_paid_employee_category_droupdown($paid_school_id,$post_data['employee_category']); ?>
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <label class="col-md-4 control-label" for="selectbasic">Position</label>

                                            <div class="col-md-4" id="position_dropdown">
                                                <?php echo get_paid_employee_position_droupdown($paid_school_id,$post_data['employee_category'],$post_data['employee_position_id']); ?>
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <label class="col-md-4 control-label" for="selectbasic">Grade</label>

                                            <div class="col-md-4">
                                                <?php echo get_paid_employee_grade_droupdown($paid_school_id,$post_data['employee_grade_id']); ?>
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <label class="col-md-4 control-label" for="school_code">Date of birth</label>  
                                            <div class="col-md-4">
                                                    <input data-date-format="yyyy-mm-dd" type="text" class="form-control datepicker" required=""  name="date_of_birth" value="<?php echo $post_data['date_of_birth']; ?>" >

                                            </div>
                                        </div>






                                        <div class="form-group">
                                            <label class="col-md-4 control-label" for="singlebutton"></label>
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
  </body>
</html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="<?php echo base_url('scripts/iframe-resizer/js/iframeResizer.contentWindow.js?v=1'); ?>"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.14.0/jquery.validate.min.js"></script>
<script src="<?php echo base_url('scripts/apply_for_teacher_admission/apply_for_teacher_admission.js?v=1'); ?>"></script>
<script src="<?php echo base_url('scripts/intl-tel-input/intlTelInput.min.js?v=1'); ?>"></script>
<script>
$(document).ready(function() {
    $('#apply_for_teacher_admission')
        .find('[name="mobile_phone"]')
            .intlTelInput({
                utilsScript: '<?php echo base_url('scripts/intl-tel-input/utils.js'); ?>',
                autoPlaceholder: true,
                preferredCountries: ['bd', 'gb', 'th' ]
            });

	var telInput = $("#mobile_phone"),
	errorMsg = $("#error-msg"),
	validMsg = $("#valid-msg");
	
	var reset = function() {
	  telInput.removeClass("error");
	  errorMsg.addClass("hide");
	  validMsg.addClass("hide");
	};

	// on blur: validate
	telInput.blur(function() {
	  reset();
	  if ($.trim(telInput.val())) {
		if (telInput.intlTelInput("isValidNumber")) {
		  validMsg.removeClass("hide");
		  getCountryAll();
		} else {
		  telInput.addClass("error");
		  errorMsg.removeClass("hide");
		}
	  }
	});

	// on keyup / change flag: reset
	telInput.on("keyup change", reset);
	
	function getCountryAll()
	{
		var countrystr = $(".selected-flag").attr('title');
		var countryAr = countrystr.split(":");
		var countryTitle = countryAr[0];
		
		var countryDialCode = $(".country-list").find('li.active').attr('data-dial-code');
		var countryCode = $(".country-list").find('li.active').attr('data-country-code');
		
		$.post($("#ci_base_url").val()+"front/paid/getCountryid", 
		{	
			countryCode: countryCode, 
			countryDialCode: countryDialCode
		})
		.done(function (data) {
			if(data > 0)
			{
				$("#parent_country").val(countryTitle);
				$("#country_id").val(data);
			}
			else
			{
				
			}
		}
		);
	}
});
</script>
<script type="text/javascript">
//document.domain = "champs21.com";
$(document).ready(function () {
	
	$('#admission_no').bind("cut copy paste",function(e) {
          e.preventDefault();
	});
	
    $(document).on("change", "#change_position", function () {
        $.post('/front/ajax/getpositiondropudown/',
                        {category_id: $(this).val(),school_id:$( "#paid_school_id" ).val()}, function (data) {
                      // alert(data);
                    $("#position_dropdown").html(data);
                }
        );
    });
    $('.datepicker').datepicker({startView: 'decade',autoclose: true});
    
});    
</script>
<script>
        $('#pluswrap').show();
        $(window).bind("load", function() {  
                $('#pluswrap').hide();			
                $("#content_signup").show(); 
        });
        $("#admission_no").bind("keyup paste", function() {
            var paid_school_id = $("#paid_school_id").val();
            if(paid_school_id < 10)
                $("#tn_username").val(0+paid_school_id+"-"+$(this).val());
            else
                $("#tn_username").val(paid_school_id+"-"+$(this).val());
        });
</script>
<style>
.error_validation p {
    color: red;
    padding: 5px 0px;
    font-weight: bold;
}
</style>