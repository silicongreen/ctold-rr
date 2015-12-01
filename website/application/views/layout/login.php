<li id="login_button_classune"><a style="color:#000;text-decoration:none;" id="classtune_a"  href="javascript:void(0)"><span>Login</span></a>
    <div id="login_form_classune">
        
        <div class="col-md-12">
            <div class="row-fluid">
                <form id="form_login_classtune" class="form-horizontal" method="post" action="<?php echo base_url(); ?>login">
                    <span class="legend" style="margin-bottom:10px; margin-top:10px; padding:5px; font-size:13px;"></span>

                    <div class="form-group">
                        <label class="col-md-4 control-label" style="margin-top: 15px; font-size:12px; letter-spacing: 2px; font-weight: 300" for="school_code">Username</label> 
                        <div class="col-md-8">
                            <input type="text"  class="form-control input-md" id="username" name="username" required>

                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label"  style="margin-top: 15px;font-size:12px; letter-spacing: 2px; font-weight: 300"  for="school_code">Password</label> 
                        <div class="col-md-8">
                            <input type="password" class="form-control input-md" id="password"  name="password"  required>
                        </div>

                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label" style="margin-top:0px;" for="singlebutton">
                            <button name="submit" type="submit" id="submit" style="margin-left: 5px;padding:3px 7px" class="btn btn-primary btn-success btn-lg">
                                <i class="fa fa-thumbs-up"></i> Login
                            </button>
                        </label>
                        <div class="col-md-8">

                        </div>
                    </div>
                </form>
            </div>     

        </div>

    </div>
</li>