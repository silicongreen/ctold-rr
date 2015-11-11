
<html>
    <head>
        <title>Select School</title>
        <link rel="stylesheet" id="bootstrap-css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" type="text/css" media="all" />
        <link rel="stylesheet" id="bootstrap-css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker.css" type="text/css" media="all" />
        
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
                        <h2 class="lead text-center editContent">
                                Sign Up For Student
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
                                            <label class="col-md-4 control-label" for="school_code">Your Login Password</label>  
                                            <div class="col-md-4">
                                                <input type="password" name="password" id="password" value="" class="form-control input-md" required="">

                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-4 control-label" for="school_code">Joining Date</label>  
                                            <div class="col-md-4">

                                                    <input data-date-format="yyyy-mm-dd" type="text" class="form-control datepicker" required=""  name="joining_date" value="<?php echo $post_data['joining_date']; ?>" >



                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-4 control-label" for="radios">Select Gender</label>
                                            <div class="col-md-4">
                                                <div class="radio">
                                                    <label for="radios-0">
                                                        <input type="radio" <?php if($post_data['gender']!="f") { ?> checked="checked"<?php } ?> name="radios" id="radios-0" value="m" checked="checked">
                                                        Male
                                                    </label>
                                                </div>
                                                <div class="radio">
                                                    <label for="radios-1">
                                                        <input type="radio" <?php if($post_data['gender']=="f") { ?> checked="checked"<?php } ?> name="radios" id="radios-1" value="f">
                                                        Female
                                                    </label>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <label class="col-md-4 control-label" for="selectbasic">Select Shift Class and section (select if only class teacher)</label>

                                            <div class="col-md-4">
                                                <?php echo get_paid_school_class($paid_school_id); ?>
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
                                            <label class="col-md-4 control-label" for="school_code">Select Birth Date</label>  
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
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.14.0/jquery.validate.min.js"></script>
<script src="<?php echo base_url('scripts/apply_for_teacher_admission/apply_for_teacher_admission.js'); ?>"></script>
<script type="text/javascript">
document.domain = "champs21.com";
$(document).ready(function () {
    $(document).on("change", "#change_position", function () {
        $.post('front/ajax/getpositiondropudown/',
                        {category_id: $(this).val()}, function (data) {
                      // alert(data);
                    $("#position_dropdown").html(data);
                }
        );
    });
    $('.datepicker').datepicker();
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