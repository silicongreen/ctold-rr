<div class="item content" id="content_section26">
    <div class="wrapper grey" >

        <div class="container">
            <div class="col-md-12"  style="padding:0px; margin-top: 120px; margin-bottom:30px;">
            <div style="margin:30px 100px; float:left; width:80%;background: white; ">
          
            <div class="col-md-12"  style="padding:0px; ">
                <h2 class="lead text-center editContent"  style="color:#66D56A; font-weight: bold;margin-top: 20px;">
                    Registration For school Creation
                </h2>
            </div>
            <div class="col-md-12">
                <?php echo form_error('first_name', '<div class="error">', '</div>'); ?>
                <?php echo form_error('last_name', '<div class="error">', '</div>'); ?>
                <?php echo form_error('email', '<div class="error">', '</div>'); ?>
                <?php echo form_error('confirm_email', '<div class="error">', '</div>'); ?> 
                <?php echo form_error('password', '<div class="error">', '</div>'); ?> 
                <?php echo form_error('confirm_password', '<div class="error">', '</div>'); ?>
                <?php if(isset($error)) :?>
                <div class="error"><?php echo $error; ?></div>
                <?php endif; ?>
            </div>

            <div class="col-md-12">
                <form id="form_newschool"  class="form-horizontal"  method="post" action="<?php echo base_url(); ?>createschool/userregister/<?php echo $school_type; ?>">
                    <legend></legend>
                    <input type="hidden" name="school_type" value="<?php echo $school_type; ?>" />
                    
                    <div class="form-group">
                          <label class="col-md-2 control-label" for="school_code"></label> 
                          <div class="col-md-8">
                                <input type="text" value="<?php echo $this->input->post("first_name")?>" class="form-control input-md" name="first_name" placeholder="First Name *" required>
                                <span class="help-block"><font color="#ccc" size="1">More then 3 character</font></span>
                                
                          </div>
                    </div>
                    <div class="form-group">
                          <label class="col-md-2 control-label" for="school_code"></label> 
                          <div class="col-md-8">
                                <input type="text" value="<?php echo $this->input->post("last_name")?>" class="form-control input-md" name="last_name" placeholder="Last Name *" required>
                                <span class="help-block"><font color="#ccc" size="1">More then 3 character</font></span>
                          </div>
                    </div>
                    
                    
                    <div class="form-group">
                        <label class="col-md-2 control-label" for="school_code"></label> 
                        <div class="col-md-8">
                            <input type="email" value="<?php echo $this->input->post("email")?>" class="form-control input-md" id="email" name="email" placeholder="Email *" required>
                            <span class="help-block"><font color="#ccc" size="1">Valid Email</font></span>
                        </div>
                        
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label" for="school_code"></label> 
                        <div class="col-md-8">
                            <input type="email" value="<?php echo $this->input->post("confirm_email")?>" class="form-control input-md" name="confirm_email" placeholder="Confirm Email *" required>
                            <span class="help-block"><font color="#ccc" size="1">Confirm Email Match Email Address</font></span>
                        </div>
                        
                    </div>
                    
                    <div class="form-group">
                        <label class="col-md-2 control-label" for="school_code"></label> 
                        <div class="col-md-8">
                            <input type="password" class="form-control input-md"  name="password" placeholder="Password *" required>
                             <span class="help-block"><font color="#ccc" size="1">More then 6 character</font></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label" for="school_code"></label> 
                        <div class="col-md-8">
                            <input type="password" class="form-control input-md"  name="confirm_password" placeholder="Confirm Password *" required>
                            <span class="help-block"><font color="#ccc" size="1">Match Password</font></span>
                           
                        </div>
                        
                    </div>
                    <div class="form-group">
                                <label class="col-md-2 control-label" for="singlebutton"></label>
                                <div class="col-md-8">
                                    <button name="submit" type="submit" id="submit" class="btn btn-primary btn-success btn-lg">
                                        <i class="fa fa-thumbs-up"></i> SEND
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


