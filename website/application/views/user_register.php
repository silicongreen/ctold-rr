<div class="item content" id="content_section26">
    <div class="wrapper grey" >

        <div class="container">
            <div class="col-md-12" style="padding:0px; <?php echo ($headless) ? '' : 'margin-top: 120px;' ?> margin-bottom:30px;">
                <div style="<?php echo ($headless) ? '' : 'margin-top:30px; margin-bottom: 30px;' ?> margin-left: 100px; margin-right: 100px; float:left; width:80%;background: white; ">

                    <div class="col-md-12"  style="padding:0px; ">
                        <h2 class="lead text-center editContent" style="color:#66D56A; font-weight: bold;margin-top: 20px;">
                            Registration For school Creation
                        </h2>
                    </div>

                    <div class="col-md-12">
                        <?php echo form_error('first_name', '<div class="alert alert-danger">', '</div>'); ?>
                        <?php echo form_error('last_name', '<div class="alert alert-danger">', '</div>'); ?>
                        <?php echo form_error('email', '<div class="alert alert-danger">', '</div>'); ?>
                        <?php echo form_error('confirm_email', '<div class="alert alert-danger">', '</div>'); ?> 
                        <?php echo form_error('password', '<div class="alert alert-danger">', '</div>'); ?> 
                        <?php echo form_error('confirm_password', '<div class="alert alert-danger">', '</div>'); ?>
                        <?php if (isset($error)) : ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                            <legend></legend>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-12">
                        <form id="form_newschool"  class="form-horizontal"  method="post" action="<?php echo base_url(); ?>createschool/userregister/<?php echo $school_type; ?>">
                            <legend></legend>
                            <input type="hidden" name="school_type" value="<?php echo $school_type; ?>" />
                            <input type="hidden" name="headless" value="<?php echo $headless; ?>" />

                            <div class="form-group">
                                <label class="col-md-2 control-label" for="first_name"></label> 
                                <div class="col-md-8">
                                    <input id="first_name" type="text" value="<?php echo $this->input->post("first_name") ?>" class="form-control input-md" name="first_name" placeholder="First Name *" required="required" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label" for="last_name"></label> 
                                <div class="col-md-8">
                                    <input id="last_name" type="text" value="<?php echo $this->input->post("last_name") ?>" class="form-control input-md" name="last_name" placeholder="Last Name *" required="required" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label" for="email"></label> 
                                <div class="col-md-8">
                                    <input id="email" type="email" value="<?php echo $this->input->post("email") ?>" class="form-control input-md" name="email" placeholder="Enter valid email address *" required="required" />
                                    <span class="help-block"><font color="#ccc" size="1">Your email address is your username.</font></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label" for="conf_email"></label> 
                                <div class="col-md-8">
                                    <input id="conf_email" type="email" value="<?php echo $this->input->post("confirm_email") ?>" class="form-control input-md" name="confirm_email" placeholder="Confirm Email *" required="required" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label" for="password"></label> 
                                <div class="col-md-8">
                                    <input id="password" type="password" class="form-control input-md"  name="password" placeholder="Password *" required="required" />
                                    <span class="help-block"><font color="#ccc" size="1">Password must be at least 6 characters.</font></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label" for="school_code"></label> 
                                <div class="col-md-8">
                                    <input type="password" class="form-control input-md"  name="confirm_password" placeholder="Confirm Password *" required="required" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label" for="submit"></label>
                                <div class="col-md-8">
                                    <button name="submit" type="submit" id="submit" class="btn btn-primary btn-success btn-lg">
                                        <i class="fa fa-thumbs-up"></i> Create Account
                                    </button>
                                </div>
                            </div>

                        </form>

                    </div><!-- /.col-md-5 -->

                </div> 
            </div>      

        </div><!-- /.container -->

    </div><!-- /.wrapper -->
</div>
<style>
    .form-horizontal input
    {
        margin-top:0px;
    }
    .error
    {
        color:red;
    }

</style>

<?php if ($headless) { ?>
    <script src="<?php echo base_url(); ?>js/iframe-resizer/iframeResizer.contentWindow.js"></script>
<?php } ?>