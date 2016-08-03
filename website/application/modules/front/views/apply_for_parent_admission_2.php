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
                    <div class="col-md-12" style="padding:0px; margin-top: 40px;" >
                        <h2 class="lead text-center editContent"  style="color:#66D56A; font-weight: bold;">
                                Sign Up For Guardian
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
                            <form class="form-horizontal" id="apply_for_parent_admission_2" method="post" action="<?php echo base_url('front/paid/apply_for_parent_addmission_2?back_url='.$back_url.'&user_type='.$user_type); ?>">
                                <input type="hidden" id="form_data" name="form_data" value="<?php echo $form_data; ?>" >                              
                                
                                <input type="hidden" id="paid_school_id" name="paid_school_id" value="<?php echo $paid_school_id; ?>" >
                                <input type="hidden" id="student_no" name="student_no" value="<?php echo $student_no; ?>" >
                                <div class="error_validation" class="col-md-12" ><?php echo validation_errors(); ?></div>
                                <fieldset>

                                    <!-- Form Name -->
                                    <legend></legend>             

                                   <div class="form-group gfield checkbox-group" <?php if($post_data['add_guardian']=="no") { ?>style="display:none;"<?php } ?>>
                                        <label class="col-md-5 control-label" for="choose_guardian">Choose Student From Existing</label>  
                                        <div class="col-md-4">
                                            <input type="checkbox" id="checkbox-1"  class="checkbox choose_guardian"  name="choose_guardian" value="choose">
                                            
                                        </div>
                                    </div>
                                    
                                    <div class="form-group gfield choosebox-group" <?php if($post_data['add_guardian']!="no") { ?>style="display:none;"<?php } ?>>
                                        <label class="col-md-5 control-label" for="s_username">Student User Name</label>  
                                        <div class="col-md-4">
                                            <input type="text" name="s_username" value="<?php echo $post_data['gfirst_name']; ?>" id="s_username" class="form-control input-md g_username" <?php if($post_data['add_guardian']!="no") { ?>required=""<?php } ?> placeholder="Student User Name *">
                                            <input type="hidden" id="s_id" name="s_id" value="">
                                            <span class="s_fullname" style="color:green;font-size: 13px;"></span>
                                        </div>
                                    </div>
									<div class="form-group gfield lead " <?php if($post_data['add_guardian']=="no") { ?>style="display:none;"<?php } ?>>
                                        <label class="col-md-5 control-label" for="s_admission_no">Enter Student Information</label>                                          
                                    </div>
                                    <div class="form-group gfield" <?php if($post_data['add_guardian']=="no") { ?>style="display:none;"<?php } ?>>
                                        <label class="col-md-5 control-label" for="s_admission_no">Admission No.</label>  
                                        <div class="col-md-4">
                                            <input type="text" name="s_admission_no" id="s_admission_no" value="<?php echo $post_data['admission_no']; ?>" required="" class="form-control input-md" placeholder="Admission No *" >                                            
                                            <span class="valid_admission_no" style="color:green;font-size: 13px;"></span>
											<span class="help-block"><font color="#ccc" size="1">It will be used for generating your student's username for ClassTune. Admission no. should include a-z , A-Z , 0-9 , - , _" </font></span>
                                        </div>
                                    </div>
                                    <div class="form-group gfield" <?php if($post_data['add_guardian']=="no") { ?>style="display:none;"<?php } ?>>
                                        <label class="col-md-5 control-label" for="sn_username">Student User Name</label>  
                                        <div class="col-md-4">
                                            <input type="text" name="sn_username" value="<?php echo $post_data['username']; ?>" id="sn_username" class="form-control input-md" <?php if($post_data['add_guardian']!="no") { ?>required=""<?php } ?> readonly placeholder="User Name *">                                            
                                        </div>
                                    </div>
                                    <div class="form-group gfield" <?php if($post_data['add_guardian']=="no") { ?>style="display:none;"<?php } ?>>
                                        <label class="col-md-5 control-label" for="admission_date">Admission date</label>  
                                        <div class="col-md-4">
                                                <input data-date-format="yyyy-mm-dd" type="text" class="form-control datepicker" required=""  id="s_admission_date" name="s_admission_date" value="<?php echo $post_data['admission_date']; ?>"  placeholder="yyyy-mm-dd">

                                        </div>
                                    </div>
                                    <div class="form-group gfield" <?php if($post_data['add_guardian']=="no") { ?>style="display:none;"<?php } ?>>
                                        <label class="col-md-5 control-label" for="class_roll_no">Roll No (If available)</label>  
                                        <div class="col-md-4">
                                            <input type="text" name="s_class_roll_no" value="<?php echo $post_data['class_roll_no']; ?>" id="s_class_roll_no" class="form-control input-md" placeholder="Roll No" >

                                        </div>
                                    </div>
                                    <div class="form-group gfield" <?php if($post_data['add_guardian']=="no") { ?>style="display:none;"<?php } ?>>
                                        <label class="col-md-5 control-label" for="first_name">Student First Name</label>  
                                        <div class="col-md-4">
                                            <input type="text" name="s_first_name" value="<?php echo $post_data['gfirst_name']; ?>" id="s_first_name" class="form-control input-md" <?php if($post_data['add_guardian']!="no") { ?>required=""<?php } ?> placeholder="First Name *">

                                        </div>
                                    </div>
                                    <div class="form-group gfield" <?php if($post_data['add_guardian']=="no") { ?>style="display:none;"<?php } ?>>
                                        <label class="col-md-5 control-label" for="middle_name">Student Middle Name</label>  
                                        <div class="col-md-4">
                                            <input type="text" name="s_middle_name" value="<?php echo $post_data['glast_name']; ?>" id="s_middle_name" class="form-control input-md" <?php if($post_data['add_guardian']!="no") { ?>required=""<?php } ?>  placeholder="Middle Name">

                                        </div>
                                    </div>
                                    <div class="form-group gfield" <?php if($post_data['add_guardian']=="no") { ?>style="display:none;"<?php } ?>>
                                        <label class="col-md-5 control-label" for="last_name">Student Last Name</label>  
                                        <div class="col-md-4">
                                            <input type="text" name="s_last_name" value="<?php echo $post_data['glast_name']; ?>" id="s_last_name" class="form-control input-md" <?php if($post_data['add_guardian']!="no") { ?>required=""<?php } ?>  placeholder="Last Name *">

                                        </div>
                                    </div>
                                    <div class="form-group gfield" <?php if($post_data['add_guardian']=="no") { ?>style="display:none;"<?php } ?>>
                                        <label class="col-md-5 control-label" for="batch_id">Student Shift Class and section</label>  
                                        <div class="col-md-4">
                                            <?php echo get_paid_school_class($paid_school_id); ?>

                                        </div>
                                    </div>
                                    <div class="form-group gfield" <?php if($post_data['add_guardian']=="no") { ?>style="display:none;"<?php } ?>>
                                        <label class="col-md-5 control-label" for="gender">Select Gender</label>
                                        <div class="col-md-4">
                                            <div class="radio">
                                                <label for="radios-0">
                                                    <input type="radio" <?php if($post_data['gender']!="m") { ?> checked="checked"<?php } ?> name="s_gender" id="s_radios_m" value="m" checked="checked">
                                                    Male
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label for="radios-f">
                                                    <input type="radio" <?php if($post_data['gender']=="f") { ?> checked="checked"<?php } ?> name="s_gender" id="s_radios_f" value="f">
                                                    Female
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group gfield">
                                        <label class="col-md-5 control-label" for="school_code">Select Birth Date</label>  
                                        <div class="col-md-4">
                                                <input data-date-format="yyyy-mm-dd" type="text" class="form-control datepicker" required=""  name="date_of_birth" value="<?php echo $post_data['date_of_birth']; ?>" placeholder="yyyy-mm-dd" >

                                        </div>
                                    </div>
                                    <div class="form-group gfield relationbox-group" <?php if($post_data['add_guardian']=="no") { ?>style="display:none;"<?php } ?>>
                                        <label class="col-md-5 control-label" for="relation">Relation</label>  
                                        <div class="col-md-4">
                                            <input type="text" name="s_relation" value="<?php echo $post_data['relation']; ?>" id="s_relation" class="form-control input-md" <?php if($post_data['add_guardian']!="no") { ?> required="" <?php } ?> placeholder="Relation *">

                                        </div>
                                    </div>
                                    <div class="form-group gfield" <?php if($post_data['add_guardian']=="no") { ?>style="display:none;"<?php } ?>>
                                        <label class="col-md-5 control-label" for="password">Student Password</label>  
                                        <div class="col-md-4">
                                            <input type="password" name="s_password" id="s_password" value="" class="form-control input-md" <?php if($post_data['add_guardian']!="no") { ?>required=""<?php } ?> placeholder="Password *">
                                            <span class="help-block"><font color="#ccc" size="1">At least 6 character</font></span>
                                        </div>
                                    </div>

                                    



                                    <div class="form-group">
                                        <label class="col-md-5 control-label" for="singlebutton"></label>
                                        <div class="col-md-5">
                                            <button name="submit" type="submit" id="submit" class="btn btn-primary btn-success btn-lg">
                                                <i class="fa fa-plus"></i> Add New Student
                                            </button>
                                            <?php
                                                //if($student_no >1)
                                                //{
                                            ?>
                                                <button name="skip_to_confirmation" type="btn" id="skip_to_confirmation" class="cancel btn btn-primary btn-success btn-lg">
                                                    <i class="fa fa-thumbs-up"></i> Confirm
                                                </button>
                                            <?php 
                                                //}else{
                                            ?>
												<!--button name="submit" type="submit" id="submit" class="btn btn-primary btn-success btn-lg">
													<i class="fa fa-thumbs-up"></i> Continue
												</button-->
												<?php //}?>
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
<script src="<?php echo base_url('scripts/apply_for_parent_admission/apply_for_parent_admission_2.js?v=3'); ?>"></script>

<script type="text/javascript">
//document.domain = "champs21.com";

$(document).ready(function () {
	
	$('#s_admission_no').bind("cut copy paste",function(e) {
          e.preventDefault();
	});
		
	
    $(document).on("click", ".add_guardian", function () {
       
        if($(this).val()=="no")
        {
            $(".gfield").hide();
            $(".gfield2").hide();
                        
            $(".gfield input").attr("required",false);
            $(".gfield2 input").attr("required",false);            
        }
        
        if($(this).val()=="one" )
        {           
            $(".gfield").show();
            $(".gfield2").hide();
                        
            $(".gfield2 input").attr("required",false);            
            $(".gfield input").attr("required",true);
        }
        if($(this).val()=="two" )
        {
            $(".gfield").show();
            $(".gfield2").show();
            
            $(".gfield2 input").attr("required",true);
            $(".gfield input").attr("required",true);            
        }
        
        $('input[type=checkbox]').attr('required',false);   
        $(".choosebox-group").hide();
        $(".choosebox-group input").attr("required",false);
        $(".choosebox-group2").hide();
        $(".choosebox-group2 input").attr("required",false);
        var height_body = $("#apply_for_parent_admission_2").height()+ 100;
        var timeout_var = setTimeout(function(){ window.parentIFrame.size(height_body); clearTimeout(timeout_var) }, 100);
    });
    $('.datepicker').datepicker({startView: 'decade',autoclose: true});
    
    
    $('.g_username').blur(function()  {  
        if(!$(this).val())
        {
            $('.g_fullname').html("");
            $('#g_username-error').remove();
        }
    });
    $('.g_username2').blur(function()  {  
        if(!$(this).val())
        {
            $('.g_fullname2').html("");
            $('#g_username2-error').remove();
        }
    });
    $(document).on("click", ".choose_guardian", function () {
        if($('.choose_guardian').is(':checked'))
        {
            $(".gfield").hide();
            $(".gfield input").attr("required",false);

            $(".checkbox-group").show();
            $(".choosebox-group").show();
            $(".choosebox-group input").attr("required",true);
            $(".relationbox-group").show();
            $(".relationbox-group input").attr("required",true);
        }
        else
        {
            $(".gfield").show();
            $(".gfield input").attr("required",true);            
                        
            $(".choosebox-group").hide();
            $(".choosebox-group input").attr("required",false);
            
            $('input[type=checkbox]').attr('required',false);  
        }
        var height_body = $("#apply_for_parent_admission_2").height()+ 200;

        var timeout_var2 = setTimeout(function(){ window.parentIFrame.size(height_body); clearTimeout(timeout_var2) }, 100);
        
    });
    $(document).on("click", ".choose_guardian2", function () {
        if($('.choose_guardian2').is(':checked'))
        {
            $(".gfield2").hide();
            $(".gfield2 input").attr("required",false);

            $(".checkbox-group2").show();
            $(".choosebox-group2").show();
            $(".choosebox-group2 input").attr("required",true);
            $(".relationbox-group2").show();
            $(".relationbox-group2 input").attr("required",true);
        }
        else
        {
            $(".gfield2").show();
            $(".gfield2 input").attr("required",true);            
                        
            $(".choosebox-group2").hide();
            $(".choosebox-group2 input").attr("required",false);
            
            $('input[type=checkbox]').attr('required',false);  
        }
        var height_body = $("#apply_for_parent_admission_2").height()+ 200;
        var timeout_var3 = setTimeout(function(){ window.parentIFrame.size(height_body); clearTimeout(timeout_var3) }, 100);
        
    });
});    
</script>

<script>
        $('#pluswrap').show();
        $(window).bind("load", function() {  
                $('#pluswrap').hide();			
                $("#content_signup").show(); 
        });
        $("#s_admission_no").bind("keyup paste", function() {
            var paid_school_id = $("#paid_school_id").val();
            if(paid_school_id < 10)
                $("#sn_username").val(0+paid_school_id+"-"+$(this).val());
            else
                $("#sn_username").val(paid_school_id+"-"+$(this).val());
        });
</script>
<style>
.error_validation p {
    color: red;
    padding: 5px 0px;
    font-weight: bold;
}
</style>