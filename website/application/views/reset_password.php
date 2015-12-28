<div class="item content" id="content_section26">
    <div class="wrapper grey" >

        <div class="container">
            <div class="col-md-12"  style="padding:0px; margin-top: 120px; margin-bottom:30px;">
                <div style="margin:30px 100px; float:left; width:80%;background: white; ">

                    <div class="col-md-12"  style="padding:0px; ">

                        <?php if (!isset($token) || empty($token)) { ?>
                            <div class="alert alert-danger">Invalid request</div>
                        <?php } else { ?>

                            <h2 class="lead text-center editContent"  style="color:#66D56A; font-weight: bold;margin-top: 20px;">
                                Reset current password
                            </h2>
                        <?php } ?>
                    </div>

                    <div class="clearfix"></div>

                    <!--<div class="col-md-12">-->

                    <?php echo form_error('password', '<div class="alert alert-danger">', '</div>'); ?>
                    <?php echo form_error('cnf_password', '<div class="alert alert-danger">', '</div>'); ?>

                    <?php if (isset($error) && !empty($error)) { ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php } ?>

                    <!--</div>-->

                    <div class="col-md-12">

                        <?php if (isset($token) && !empty($token)) { ?>

                            <form id="form_newschool" class="form-horizontal"  method="post" action="<?php echo base_url(); ?>login/reset-password">
                                <legend></legend>

                                <input type="hidden" value="<?php echo $token; ?>" class="form-control input-md" id="token" name="token" />

                                <div class="form-group">
                                    <label class="col-md-2 control-label" for="password"></label> 
                                    <div class="col-md-8">
                                        <input type="password" value="" class="form-control input-md" id="password" name="password" placeholder="New Password" />
                                        <!--<span class="help-block"><font color="#ccc" size="1">More then 5 character</font></span>-->
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-2 control-label" for="cnf_password"></label> 
                                    <div class="col-md-8">
                                        <input type="password" value="" class="form-control input-md" id="cnf_password" name="cnf_password" placeholder="Re-enter Password"  />
                                        <!--<span class="help-block"><font color="#ccc" size="1">More then 5 character</font></span>-->
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-2 control-label" for="singlebutton"></label>
                                    <div class="col-md-8">
                                        <button name="submit" type="submit" id="submit" class="btn btn-primary btn-success btn-lg">
                                            <i class="fa fa-thumbs-up"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </form>
                        <?php } ?>

                    </div><!-- /.col-md-5 -->

                </div> 
            </div>      

        </div><!-- /.container -->

    </div><!-- /.wrapper -->
</div>

<style type="text/css">
    .alert-danger {
        margin-top: 5px;
    }
</style>