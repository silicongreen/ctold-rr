<html>
    <head>
        <title>Reset Password</title>
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
                            Reset Password
                        </h2>
                    </div>
                    <div class="col-md-12">                            
                        <?php if (isset($success) && $success) : ?>					
                            <div class="alert alert-success">
                                <?php echo $success; ?>
                            </div>
                        <?php endif; ?>
                        <?php echo validation_errors(); ?>
                    </div>

                    <div class="col-md-12">
                        <div class="row-fluid">
                            <form id="forget_password_form" class="form-horizontal" method="post" action="">
                                
                                <legend></legend>

                                <fieldset>                                    
                                   
                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for="name">Password</label>
                                        <div class="col-md-4">
                                            <input type="password" class="form-control input-xlarge"  name="password" id="password" placeholder="Password must be at least 6 characters. " required>
                                            
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for="name">Confirm Password</label>
                                        <div class="col-md-4">
                                            <input type="password" class="form-control input-xlarge"  name="confirm_password" id="confirm_password" placeholder="Confirm Password *" required>
                                           
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for="singlebutton"></label>
                                        <div class="col-md-4">
                                            <button name="submit" type="submit" id="submit" class="btn btn-primary btn-success btn-lg">
                                                <i class="fa fa-thumbs-up"></i> Submit
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