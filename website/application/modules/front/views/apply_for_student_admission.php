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
    <form class="form-horizontal col-md-10" id="from_id" method="post" action="">
            <div class="error_validation" class="col-md-12" ><?php echo validation_errors(); ?></div>
            <fieldset>

                <!-- Form Name -->
                <legend></legend>

                <!-- Select Basic -->
                 <div class="form-group">
                    <label class="col-md-4 control-label" for="school_code">Admission NO Of Your School/if not available unique username</label>  
                    <div class="col-md-4">
                        <input type="text" name="admission_no" id="admission_no" value="<?php echo $post_data['admission_no']; ?>" required="" class="form-control input-md" >
                        <span class="help-block">It will be use for generate your new username for school and champs21</span>  
                    </div>
                </div>
                 <div class="form-group">
                    <label class="col-md-4 control-label" for="school_code">Your Login Password</label>  
                    <div class="col-md-4">
                        <input type="password" name="password" id="password" value="" class="form-control input-md" required="">
                        
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-4 control-label" for="school_code">Admission date</label>  
                    <div class="col-md-4">
                        
                            <input data-date-format="yyyy-mm-dd" type="text" class="form-control datepicker" required=""  name="admission_date" value="<?php echo $post_data['admission_date']; ?>" >
                       
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-4 control-label" for="school_code">Roll No (If available)</label>  
                    <div class="col-md-4">
                        <input type="text" name="class_roll_no" value="<?php echo $post_data['class_roll_no']; ?>" id="class_roll_no" class="form-control input-md" >
                     
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
                    <label class="col-md-4 control-label" for="selectbasic">select Shift Class and section</label>
                    
                    <div class="col-md-4">
                        <?php echo get_paid_school_class($user_data->paid_school_id,$post_data['batch_id']); ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-4 control-label" for="school_code">Select Birth Date</label>  
                    <div class="col-md-4">
                            <input data-date-format="yyyy-mm-dd" type="text" class="form-control datepicker" required=""  name="date_of_birth" value="<?php echo $post_data['date_of_birth']; ?>" >
                        
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
                    <label class="col-md-4 control-label" for="school_code">Your City</label>  
                    <div class="col-md-4">
                        <input type="text" name="city" value="<?php echo $post_data['city']; ?>" id="city" class="form-control input-md" required="">
                     
                    </div>
                </div>
       
                <div class="form-group">
                    <label class="col-md-4 control-label" for="radios">Add Guardian</label>
                    <div class="col-md-4">
                        <div class="radio">
                            <label for="radios-0">
                                <input type="radio" <?php if($post_data['add_guardian']!="no" && $post_data['add_guardian']!="two") { ?> checked="checked"<?php } ?> id="radios-0" name="add_guardian" class="add_guardian" value="one" checked="checked">
                                One
                            </label>
                        </div>
                        <div class="radio">
                            <label for="radios-1">
                                <input type="radio" <?php if($post_data['add_guardian']=="two") { ?> checked="checked"<?php } ?> id="radios-1"  name="add_guardian" class="add_guardian" value="two" >
                                two
                            </label>
                        </div>
                        <div class="radio">
                            <label for="radios-1">
                                <input type="radio" <?php if($post_data['add_guardian']=="no") { ?> checked="checked"<?php } ?> id="radios-2"   name="add_guardian" class="add_guardian" value="no">
                                Skip
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="form-group gfield" <?php if($post_data['add_guardian']=="no") { ?>style="display:none;"<?php } ?>>
                    <label class="col-md-4 control-label" for="school_code">Guardian First Name</label>  
                    <div class="col-md-4">
                        <input type="text" name="gfirst_name" value="<?php echo $post_data['gfirst_name']; ?>" id="gfirst_name" class="form-control input-md" <?php if($post_data['add_guardian']!="no") { ?>required=""<?php } ?>>
                     
                    </div>
                </div>
                <div class="form-group gfield" <?php if($post_data['add_guardian']=="no") { ?>style="display:none;"<?php } ?>>
                    <label class="col-md-4 control-label" for="school_code">Guardian Last Name</label>  
                    <div class="col-md-4">
                        <input type="text" name="glast_name" value="<?php echo $post_data['glast_name']; ?>" id="glast_name" class="form-control input-md" <?php if($post_data['add_guardian']!="no") { ?>required=""<?php } ?> >
                     
                    </div>
                </div>
                <div class="form-group gfield" <?php if($post_data['add_guardian']=="no") { ?>style="display:none;"<?php } ?>>
                    <label class="col-md-4 control-label" for="school_code">Relation</label>  
                    <div class="col-md-4">
                        <input type="text" name="relation" value="<?php echo $post_data['relation']; ?>" id="relation" class="form-control input-md" <?php if($post_data['add_guardian']!="no") { ?>required=""<?php } ?>>
                     
                    </div>
                </div>
                <div class="form-group gfield" <?php if($post_data['add_guardian']=="no") { ?>style="display:none;"<?php } ?>>
                    <label class="col-md-4 control-label" for="school_code">Guardian Password</label>  
                    <div class="col-md-4">
                        <input type="password" name="gpassword" id="gpassword" value="" class="form-control input-md" <?php if($post_data['add_guardian']!="no") { ?>required=""<?php } ?>>
                        
                    </div>
                </div>
                
                <div class="form-group gfield2" <?php if($post_data['add_guardian']!="two") { ?>style="display:none;"<?php } ?>>
                    <label class="col-md-4 control-label" for="school_code">Guardian First Name (2nd)</label>  
                    <div class="col-md-4">
                        <input type="text" name="gfirst_name2" value="<?php echo $post_data['gfirst_name2']; ?>" id="gfirst_name2" class="form-control input-md"   <?php if($post_data['add_guardian']=="two") { ?>required=""<?php } ?>>
                     
                    </div>
                </div>
                <div class="form-group gfield2" <?php if($post_data['add_guardian']!="two") { ?>style="display:none;"<?php } ?>>
                    <label class="col-md-4 control-label" for="school_code">Guardian Last Name (2nd)</label>  
                    <div class="col-md-4">
                        <input type="text" name="glast_name2" value="<?php echo $post_data['glast_name2']; ?>" id="glast_name2" class="form-control input-md"   <?php if($post_data['add_guardian']=="two") { ?>required=""<?php } ?>>
                     
                    </div>
                </div>
                <div class="form-group gfield2" <?php if($post_data['add_guardian']!="two") { ?>style="display:none;"<?php } ?>>
                    <label class="col-md-4 control-label" for="school_code">Relation (2nd)</label>  
                    <div class="col-md-4">
                        <input type="text" name="relation2" value="<?php echo $post_data['relation2']; ?>" id="relation2" class="form-control input-md"   <?php if($post_data['add_guardian']=="two") { ?>required=""<?php } ?>>
                     
                    </div>
                </div>
                <div class="form-group gfield2" <?php if($post_data['add_guardian']!="two") { ?>style="display:none;"<?php } ?>>
                    <label class="col-md-4 control-label" for="school_code">Guardian Password (2nd)</label>  
                    <div class="col-md-4">
                        <input type="password" name="gpassword2" id="gpassword2" value="" class="form-control input-md"   <?php if($post_data['add_guardian']=="two") { ?>required=""<?php } ?>>
                        
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
    $(document).on("click", ".add_guardian", function () {
        
       
        if($(this).val()=="no")
        {
            $(".gfield").hide();
            $(".gfield2").hide();
            $(".gfield2 input").attr("required",false);
            $(".gfield input").attr("required",false);
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
   
        
        window.parent.document.getElementById('iframe_change_height').style.height = $("#from_id").height()+'px';
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