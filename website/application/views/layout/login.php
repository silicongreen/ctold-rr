<li id="login_button_classune"><a style="color:#000;text-decoration:none;" id="classtune_a"  href="javascript:void(0)"><span>Login</span></a>
    <div id="login_form_classune" class="col-md-3" style="overflow: hidden; width:23%;">
        
        <div class="col-md-12">
            <div class="row-fluid">
                <form id="form_login_classtune" class="form-horizontal" method="post" action="http://www.classtune.com/login">
  					
                    <span class="legend" style="margin-bottom:10px; margin-top:10px; padding:5px; font-size:13px;"></span>

                    <div class="form-group">
                         
                        <div class="col-md-12">
                            <input type="text" class="form-control input-md" id="username" name="username" placeholder="Username *" required="">

                        </div>
                    </div>

                    <div class="form-group">
                         
                        <div class="col-md-12">
                            <input type="password" class="form-control input-md" id="password" name="password" placeholder="Password *" required="">
                        </div>

                    </div>
                    <legend></legend>
                    <div class="col-md-12" style="text-align: left;padding-left:0px; margin-bottom:10px;">
                        <a href="javascript:void(0);" style="color:black;" id="register_show">New user sign up</a> 
                    </div>    
                    <div class="form-group" id="register_from_login_div">
                        <div class="row">
                            
                            <button name="admin" onclick="location.href='<?php echo base_url();?>signup/admin'" type="button" id="admin_register" class="btn-xs btn btn-default">
                                <i class="fa"></i> Admin
                            </button>


                            <button name="student" onclick="location.href='<?php echo base_url();?>signup/student'" type="button" id="student_register" class="btn-xs btn btn-default">
                                <i class="fa"></i> Student
                            </button>


                            <button name="parent" onclick="location.href='<?php echo base_url();?>signup/guardian'" type="button" id="parent_register" class="btn-xs btn btn-default">
                                <i class="fa"></i> Parent
                            </button>


                            <button name="student" onclick="location.href='<?php echo base_url();?>signup/teacher'" type="button" id="student_register" class="btn-xs btn btn-default">
                                <i class="fa"></i> Student
                            </button>
                           
                        </div>    
                    </div>

                    <div class="form-group">
                        
                        <div class="col-md-12">
                        <button name="submit" type="submit" id="submit" style="" class="btn btn-primary col-md-12 btn-success">
                                <i class="fa fa-thumbs-up"></i> Login
                            </button>
                        </div>
                    </div>
                </form>
            </div>     

        </div>

    </div>
</li>