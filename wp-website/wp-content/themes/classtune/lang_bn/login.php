<?php
  wp_nonce_field("login_security","login_security_field");
?>
<?php
	$login = "লগইন";$username="ব্যবহারকারীর নাম *";$password="পাসওয়ার্ড *";$forgot_password="পাসওয়ার্ড ভুলে গেছেন ?";$already_signup="একটি ব্যবহারকারীর নাম আছে না?";$signup="রেজিস্ট্রেশন করুন";$teacher = "শিক্ষক";$parent = "অভিভাবক";$student = "শিক্ষার্থী";$sAdmin = "স্কুল অ্যাডমিন";
?>
<li id="login_button_classune"><a style="color:#fff;text-decoration:none;" id="classtune_a"  href="javascript:void(0)"><span><?php echo $login;?></span></a>
    <div id="login_form_classune" class="col-md-3" style="overflow: hidden; width:23%;">
        
        <div class="col-md-12">
            <div class="row-fluid">
                <form id="form_login_classtune" class="form-horizontal" method="post" action="http://www.classtune.com/login">
  					
                    <span class="legend" style="margin-bottom:10px; margin-top:10px; padding:5px; font-size:13px;"></span>

                    <div class="form-group">
                         
                        <div class="col-md-12">
                            <input type="text" class="form-control input-md" id="username" name="username" placeholder="<?php echo $username;?>" required="">

                        </div>
                    </div>

                    <div class="form-group">
                         
                        <div class="col-md-12">
                            <input type="password" class="form-control input-md" id="password" name="password" placeholder="<?php echo $password;?>" required="">
                        </div>

                    </div>
                    <legend></legend>
                     <div class="col-md-12" style="text-align: left;padding-left:0px; margin-bottom:10px;">
                        <p><a href="<?php echo get_site_url(); ?>/forget-password" style="color:#000 !important;"><?php echo $forgot_password;?></a> </p>
                        <p><?php echo $already_signup;?></p>
                        <p><a href="javascript:void(0);" style="color:black;" id="register_show"><?php echo $signup;?></a></p>  
                    </div>     
                    <div class="form-group" id="register_from_login_div">
                        <div class="row">
                            
                            <button name="admin" onclick="location.href='<?php echo get_site_url().'/'.$lang; ?>/admin-page<?php echo "-".$lang;?>'" type="button" id="admin_register" class="btn-primary btn-xs btn btn-default">
                                <i class="fa"></i> <?php echo $sAdmin;?>
                            </button>


                            <button name="student" onclick="location.href='<?php echo get_site_url().'/'.$lang; ?>/student-page<?php echo "-".$lang;?>'" type="button" id="student_register" class="btn-info btn-xs btn btn-default">
                                <i class="fa"></i> <?php echo $student ;?>
                            </button>


                            <button name="parent" onclick="location.href='<?php echo get_site_url().'/'.$lang; ?>/guardian-page<?php echo "-".$lang;?>'" type="button" id="parent_register" class="btn-warning btn-xs btn btn-default">
                                <i class="fa"></i> <?php echo $parent;?>
                            </button>


                            <button name="student" onclick="location.href='<?php echo get_site_url().'/'.$lang; ?>/teacher-page<?php echo "-".$lang;?>'" type="button" id="student_register" class="btn-danger btn-xs btn btn-default">
                                <i class="fa"></i> <?php echo $teacher;?>
                            </button>
                           
                        </div>    
                    </div>

                    <div class="form-group">
                        
                        <div class="col-md-12">
                        <button name="submit" type="submit" id="submit" style="" class="btn btn-primary col-md-12 btn-success">
                                <i class="fa"></i> <?php echo $login;?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>     

        </div>

    </div>
</li>