<html>
    <head>
        <title>Select School</title>
        <link rel="stylesheet" id="bootstrap-css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" type="text/css" media="all" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.css" rel="stylesheet">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">


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
        <div class="item content" id="content_select_school">
            <div class="wrapper grey" >

                <div class="container">

                    <div class="col-md-12" style="padding:0px; margin-top: 30px;">
                        <h2 class="lead text-center editContent" style="color:#66D56A; font-weight: bold;">
                            Create Your Account
                            <?php // if($_GET['user_type']==2): ?>
                                <!--Student-->
                            <?php // elseif($_GET['user_type']== 3): ?>
                                <!--Teacher-->
                            <?php // elseif($_GET['user_type']==4): ?>
                                <!--Guardian-->
                            <?php // endif; ?>
                        </h2>
                    </div>
                    <div class="col-md-12">                            
                        <?php if (isset($error)) : ?>					
                            <div class="alert alert-danger">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        <?php echo validation_errors(); ?>
                    </div>

                    <div class="col-md-12">
                        <div class="row-fluid">
                            <form id="select_school_for_sign_up_form" class="form-horizontal" method="post" action="<?php echo base_url('front/paid/select_school?back_url=' . $back_url . '&user_type=' . $user_type); ?>">
                                
                                <legend></legend>

                                <fieldset>                                    
                                    <!-- Text input-->
                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for="school_code">School Code</label>  
                                        <div class="col-md-4">
                                            <input id="school_code" name="school_code" type="text" placeholder="Enter school code" class="form-control input-md" >
                                            <input type="hidden" id="paid_school_id" name="paid_school_id" value="">
                                            <span class="school_name" style="color:green;font-size: 13px;"></span>
                                            <span class="help-block"><font color="#ccc" size="1">Collect Code from your School</font></span>   
                                            <?php echo form_error('school_code', '<div class="error">', '</div>'); ?>

                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for="name">First Name</label>
                                        <div class="col-md-4">
                                            <input type="text" value="<?php echo $this->input->post("first_name") ?>" class="form-control input-xlarge" name="first_name" id="first_name" placeholder="First Name *" required>
                                            <span><font color="#ccc" size="1"></font></span>
                                            <?php echo form_error('first_name', '<div class="error">', '</div>'); ?>
                                        </div>
                                    </div>
                                    <?php
                                    if ($user_type == 2 || $user_type == 3) {
                                        ?>
                                        <div class="form-group">
                                            <label class="col-md-4 control-label" for="middle_name">Middle Name</label>  
                                            <div class="col-md-4">
                                                <input type="text" name="middle_name" id="middle_name" class="form-control input-md" placeholder="Middle Name">

                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for="name">Last Name</label>
                                        <div class="col-md-4">
                                            <input type="text" value="<?php echo $this->input->post("last_name") ?>" class="form-control input-xlarge" name="last_name" id="last_name" placeholder="Last Name *" required>
                                            <span><font color="#ccc" size="1"></font></span>
                                            <?php echo form_error('last_name', '<div class="error">', '</div>'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for="name">Email Address</label>
                                        <div class="col-md-4">
                                            <input type="email" value="<?php echo $this->input->post("email") ?>" class="form-control input-xlarge" id="email" name="email" id="email" placeholder="Enter valid email address *" required>
                                            <?php echo form_error('email', '<div class="error">', '</div>'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for="name">Confirm Email Address</label>
                                        <div class="col-md-4">
                                            <input type="email" value="<?php echo $this->input->post("confirm_email") ?>" class="form-control input-xlarge" name="confirm_email" id="confirm_email" placeholder="Confirm email address *" required>
                                            <?php echo form_error('confirm_email', '<div class="error">', '</div>'); ?> 
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for="name">Password</label>
                                        <div class="col-md-4">
                                            <input type="password" class="form-control input-xlarge"  name="password" id="password" placeholder="Password must be at least 6 characters. " required>
                                            <?php echo form_error('password', '<div class="error">', '</div>'); ?> 
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for="name">Confirm Password</label>
                                        <div class="col-md-4">
                                            <input type="password" class="form-control input-xlarge"  name="confirm_password" id="confirm_password" placeholder="Confirm Password *" required>
                                            <?php echo form_error('confirm_password', '<div class="error">', '</div>'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for="singlebutton"></label>
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
        </div><!-- /.item content -->
    </body>
</html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="<?php echo base_url('scripts/iframe-resizer/js/iframeResizer.contentWindow.js?v=1'); ?>"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css" rel="stylesheet" />
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.14.0/jquery.validate.min.js"></script>
<script src="/scripts/select_school_for_sign_up/select_school_for_sign_up.js?v=1"></script>



<style>
    .error_validation p {
        color: red;
        padding: 5px 0px;
        font-weight: bold;
    }
</style>
<script>
                    //document.domain = "champs21.com";
                    $(document).ready(function () {
                        $('.selectpicker').select2();
                    });

</script>
<script>
    $('#pluswrap').show();
    $(window).bind("load", function () {
        $('#pluswrap').hide();
        $("#content_select_school").show();
    });
</script>