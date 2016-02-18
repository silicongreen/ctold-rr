

<div class="col-md-12"  style="padding:0px; margin-top: 10px; margin-bottom:10px;">
    <div style="margin:0px 100px; float:left; width:80%;background: white; ">

        <div class="col-md-12"  style="padding:0px; ">
            <h2 class="lead text-center editContent"  style="color:#66D56A; font-weight: bold;margin-top: 20px;">
                Login
            </h2>
        </div>
        <div class="col-md-12">
            <?php echo form_error('username', '<div class="error">', '</div>'); ?>
            <?php echo form_error('password', '<div class="error">', '</div>'); ?>
            <?php if (isset($error) && $error) : ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
        </div>

        <div class="col-md-12">
            <div class="row-fluid">
                <form id="form_newschool" class="form-horizontal" method="post" action="<?php echo base_url(); ?>login">
                    <legend></legend>

                    <div class="form-group">
                        <label class="col-md-2 control-label" for="school_code"></label> 
                        <div class="col-md-8">
                            <input type="text" value="<?php echo $this->input->post("username") ?>" class="form-control input-md" name="username" placeholder="User Name *" required>

                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label" for="school_code"></label> 
                        <div class="col-md-8">
                            <input type="password" class="form-control input-md"  name="password" placeholder="Password *" required>
                        </div>

                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label" for="singlebutton"></label>
                        <div class="col-md-8">
                            <button name="submit" type="submit" id="submit" class="btn btn-primary btn-success btn-lg">
                                <i class="fa fa-thumbs-up"></i> Login
                            </button>
                        </div>
                    </div>
                </form>
            </div>     

        </div><!-- /.col-md-5 -->

    </div>
</div>    



