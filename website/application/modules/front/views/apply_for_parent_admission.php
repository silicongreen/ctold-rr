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
                            <form class="form-horizontal" id="apply_for_parent_admission" method="post" action="<?php echo base_url('front/paid/apply_for_parent_addmission?back_url='.$back_url.'&user_type='.$user_type); ?>">
                                <input type="hidden" id="form1_data" name="form1_data" value="<?php echo $form1_data; ?>" >
                                <input type="hidden" id="paid_school_id" name="paid_school_id" value="<?php echo $paid_school_id; ?>" >
                                <div class="error_validation" class="col-md-12" ><?php echo validation_errors(); ?></div>
                                <fieldset>

                                    <!-- Form Name -->
                                    <legend></legend>             

                                    <div class="form-group">
                                        <label class="col-md-5 control-label" for="school_code">Date of birth</label>  
                                        <div class="col-md-4">
                                                <input data-date-format="yyyy-mm-dd" type="text" id="date_of_birth" class="form-control datepicker" required=""  name="date_of_birth" value="<?php echo $post_data['date_of_birth']; ?>" placeholder="yyyy-mm-dd*" >
                                                <?php echo form_error('dob', '<div class="error">', '</div>'); ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-5 control-label" for="Gender">Select Gender</label>
                                        <div class="col-md-4">
                                            <div class="radio">
                                                <label for="radios-m">
                                                    <input type="radio" <?php if($post_data['gender']!="m") { ?> checked="checked"<?php } ?> name="gender" id="radios-m" value="m" checked="checked">
                                                    Male
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label for="radios-f">
                                                    <input type="radio" <?php if($post_data['gender']=="f") { ?> checked="checked"<?php } ?> name="gender" id="radios-f" value="f">
                                                    Female
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-5 control-label" for="school_code">Mobile Number</label>  
                                        <div class="col-md-5">
                                            <div class="col-md-12">
                                                <div class="col-md-4">
                                                    <select id="mobile_code" style="height:30" class="cd-input f5 form-control" name="mobile_code">                                                        
                                                        <option value="011">011</option>
                                                        <option value="015">015</option>
                                                        <option value="016">016</option>
                                                        <option value="017">017</option>
                                                        <option value="018">018</option>
                                                        <option value="019">019</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" name="phone_ext" id="phone_ext" class="form-control input-md" required="" placeholder="xx*" maxlength="2">
                                                </div>
                                                <div class="col-md-5">
                                                    <input type="text" name="phone_number" id="phone_number" class="form-control input-md" required="" placeholder="xxxxxx*" maxlength="6">
                                                </div>
                                            </div>      
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-5 control-label" for="city">Your City</label>  
                                        <div class="col-md-4">
                                            <input type="text" name="city" value="<?php echo $post_data['city']; ?>" id="city" class="form-control input-md" required="" placeholder="City*">
                                            <?php echo form_error('city', '<div class="error">', '</div>'); ?>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-md-5 control-label" for="address">Your Address</label>  
                                        <div class="col-md-4">
                                            <input type="text" name="address" value="<?php echo $post_data['address']; ?>" id="address" class="form-control input-md" required="" placeholder="Address*">
                                            <?php echo form_error('address', '<div class="error">', '</div>'); ?>
                                        </div>
                                    </div>

                                    



                                    <div class="form-group">
                                        <label class="col-md-5 control-label" for="singlebutton"></label>
                                        <div class="col-md-4">
                                            <button name="submit" type="submit" id="submit" class="btn btn-primary btn-success btn-lg">
                                                <i class="fa fa-thumbs-up"></i> Continue
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
<script src="<?php echo base_url('scripts/apply_for_parent_admission/apply_for_parent_admission.js?v=1'); ?>"></script>

<script type="text/javascript">
    //document.domain = "champs21.com";
$(document).ready(function () {
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

    });
    $('.datepicker').datepicker();    
    
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
        
        
    });
});    
</script>

<script>
        $('#pluswrap').show();
        $(window).bind("load", function() {  
                $('#pluswrap').hide();			
                $("#content_signup").show(); 
        });
</script>
<style>
.error_validation p {
    color: red;
    padding: 5px 0px;
    font-weight: bold;
}
</style>