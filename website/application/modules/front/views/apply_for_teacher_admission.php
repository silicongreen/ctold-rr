
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
    </head>
    <body>
    <form class="form-horizontal col-md-10" method="post" action="">
            <div class="error_validation" class="col-md-12" ><?php echo validation_errors(); ?></div>
            <fieldset>

                <!-- Form Name -->
                <legend></legend>

                <!-- Select Basic -->
                 <div class="form-group">
                    <label class="col-md-4 control-label" for="school_code">Employee NO Of Your School/if not available unique username</label>  
                    <div class="col-md-4">
                        <input type="text" name="admission_no" id="admission_no" value="<?php echo $post_data['admission_no']; ?>" required="" class="form-control input-md" >
                        <span class="help-block"></span>  
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
                    <label class="col-md-4 control-label" for="school_code">First Name</label>  
                    <div class="col-md-4">
                        <input type="text" name="first_name" value="<?php echo $post_data['first_name']; ?>" id="first_name" class="form-control input-md" required="">
                     
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-4 control-label" for="school_code">Middle Name</label>  
                    <div class="col-md-4">
                        <input type="text" name="middle_name" value="<?php echo $post_data['middle_name']; ?>" id="middle_name" class="form-control input-md">
                     
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-4 control-label" for="school_code">Last Name</label>  
                    <div class="col-md-4">
                        <input type="text" name="last_name" value="<?php echo $post_data['last_name']; ?>" id="last_name" class="form-control input-md" required="">
                     
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
                        <?php echo get_paid_school_class($user_data->paid_school_id,$post_data['batch_id']); ?>
                    </div>
                </div>
                
                
                
                <div class="form-group">
                    <label class="col-md-4 control-label" for="selectbasic">Department</label>
                    
                    <div class="col-md-4">
                        <?php echo get_paid_employee_department_droupdown($user_data->paid_school_id,$post_data['employee_department_id']); ?>
                    </div>
                </div>
                
                
                <div class="form-group">
                    <label class="col-md-4 control-label" for="selectbasic">Category</label>
                    
                    <div class="col-md-4">
                        <?php echo get_paid_employee_category_droupdown($user_data->paid_school_id,$post_data['employee_category']); ?>
                    </div>
                </div>
                
                
                <div class="form-group">
                    <label class="col-md-4 control-label" for="selectbasic">Position</label>
                    
                    <div class="col-md-4" id="position_dropdown">
                        <?php echo get_paid_employee_position_droupdown($user_data->paid_school_id,$post_data['employee_category'],$post_data['employee_position_id']); ?>
                    </div>
                </div>
                
                
                <div class="form-group">
                    <label class="col-md-4 control-label" for="selectbasic">Grade</label>
                    
                    <div class="col-md-4">
                        <?php echo get_paid_employee_grade_droupdown($user_data->paid_school_id,$post_data['employee_grade_id']); ?>
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

  </body>
</html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">
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

<style>
.error_validation p {
    color: red;
    padding: 5px 0px;
    font-weight: bold;
}
</style>