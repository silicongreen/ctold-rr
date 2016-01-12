<?php
$act = (isset($_GET['locale']) && !empty($_GET['locale'])) ? $_GET['locale'] : 'premium';
?>
<div id="page" class="page" >
    <div class="item pricing" id="pricing_table2" style="margin-top:120px;">

        <div class="container" style="width:1000px;margin:0px auto;" >

            <div class="row">
                <div class="col-md-12" style="padding:0px;border-bottom:1px solid #ccc;">
                    <div class="col-md-4 border_right" style="height:130px; width:26%;">
                    </div>
                    <div class="col-md-4 pack_log" id="basic_log" style="height:130px;width:32%;background:#E9E7E8;text-align: center;padding:20px;cursor:pointer;">
                        <i style="background:#0EB0C7;" class="fa-a fa fa-home"></i>
                        <h2 style="font-size:25px;">Basic</h2>
                        <!--p>Smart Free Start Up</p-->
                    </div>
                    <div class="col-md-4 pack_log border_left" id="premium_log" style="width:42%;height:130px;background:#E9E7E8;text-align: center;padding:20px;cursor:pointer;">
                        <i style="background:#F56332;" class="fa-a fa fa-star"></i>
                        <h2 style="font-size:25px;">Premium</h2>
                        <!--p>$1.99 Per Month / Student</p-->
                    </div>
                </div>				
            </div><!-- /.row -->
            <div class="row package-box <?php if ($act == "premium"): ?>hide<?php endif; ?>" id="basic-box" style="background-color:#FFF;">

                <div class="col-md-12"  style="">                                    
                    <div class="row" style="text-align: left;border-bottom:1px solid #ccc;">                                           
                        <div class="col-md-4 border_right"  style="width:26%;height: 232px;">                                                
                            <img src="<?php echo base_url(); ?>images/package-2.png" style="left: 50%;margin-left: -100px;margin-top: -50px;position: absolute;top: 50%;">
                        </div>
                        <div class="col-md-4" id="basic_log" style="width:32%;padding: 20px;height: 232px;">
                            <div class="col-md-6" >
                                <ul>
                                    <li><i class="fa fa-check-square"></i><span>Homeworks</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Notice</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Student Profile</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Teacher Profile</span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-4 border_left"  style="width:42%;padding: 20px;height: 232px;">
                            <div class="col-md-6" >
                                <ul>
                                    <li><i class="fa fa-check-square"></i><span>Homework</span></li>                                                
                                    <li><i class="fa fa-check-square"></i><span>Notice</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Student Profile</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Teacher Profile</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Meeting Request</span></li>
                                </ul>

                                <ul>
                                    <li><i class="fa fa-check-square"></i><span>Attendance</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Academic Calendar</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Online Quiz</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Events</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Transport</span></li>
                                </ul>
                            </div>
                            <div class="col-md-6" >
                                <ul>
                                    <li><i class="fa fa-check-square"></i><span>Fees</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Lesson Plan</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Syllabus</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Exam Routine</span></li>
                                </ul>
                                <ul>
                                    <li><i class="fa fa-check-square"></i><span>Routine</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Report Card</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Leave</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Forms</span></li>

                                </ul>
                            </div>
                        </div>                                                                           
                    </div> <!--row--> 
                    <div class="row" style="text-align: left;border-bottom:1px solid #ccc;">                                           
                        <div class="col-md-4 border_right"  style="width:26%;height: 150px;">
                            <img src="<?php echo base_url(); ?>images/package-3.png" style="left: 50%;margin-left: -100px;margin-top: -60px;position: absolute;top: 50%;">
                        </div>
                        <div class="col-md-4" id="basic_log" style="width:32%;padding: 20px;height: 150px;">
                            <!--img src="<?php echo base_url(); ?>images/close.png" style="left: 50%;margin-left: -60px;margin-top: -50px;position: absolute;top: 50%;"-->
							<ul>
								<li><i class="fa fa-check-square"></i><span>Unlimited User Registration</span></li>
								<li><i class="fa fa-check-square"></i><span>Profile Management</span></li>
								<li><i class="fa fa-check-square"></i><span>Manage Homework</span></li>                                                
								<li><i class="fa fa-check-square"></i><span>Manage Notice</span></li>                                               
							</ul>
                        </div>
                        <div class="col-md-4 border_left"  style="width:42%;padding: 20px;height: 150px;">
                            <div class="col-md-6" >
                                <ul>
                                    <li><i class="fa fa-check-square"></i><span>Unlimited Teacher and Admin</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Manage Employee</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Manage Student</span></li>                                                
                                    <li><i class="fa fa-check-square"></i><span>Manage Task</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Manage Task</span></li>
                                    <li><i class="fa fa-check-square"></i><span>App Management</span></li>                                                
                                </ul>
                            </div>
                            <div class="col-md-6" >
                                <ul>                                                    
                                    <li><i class="fa fa-check-square"></i><span>Data Management</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Manage Communication</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Email Alert Settings</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Real Time Notification</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Data Archive</span></li>                                                    
                                </ul>
                            </div>
                        </div>                                                                                          
                    </div> <!--row--> 
                    <div class="row" style="text-align: left;border-bottom:1px solid #ccc;">                                           
                        <div class="col-md-4 border_right"  style="width:26%;height: 165px;">
                            <img src="<?php echo base_url(); ?>images/package-4.png" style="left: 50%;margin-left: -100px;margin-top: -60px;position: absolute;top: 50%;">
                        </div>
                        <div class="col-md-4" id="basic_log" style="width:32%;padding: 20px;height: 165px;">
                            <div class="col-md-6" >
                                <ul>
                                    <li><i class="fa fa-check-square"></i><span>E-mail</span></li>                                                
                                    <li><i class="fa fa-check-square"></i><span>Hotline</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Query Form</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Live Chat</span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-4 border_left" style="width:42%;padding: 20px;height: 165px;">
                            <div class="col-md-12" >
                                <ul>
                                    <li><i class="fa fa-check-square"></i><span>Live Chat ( 6hrs/day, Choose your preferred time)</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Online IT Support</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Online Training</span></li>
                                    <li><i class="fa fa-check-square"></i><span>E-mail</span></li>                                                
                                    <li><i class="fa fa-check-square"></i><span>Hotline</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Query Form</span></li>
                                </ul>
                            </div>
                        </div>                                                                                          
                    </div> <!--row--> 
                    <div class="row" style="text-align: left;border-bottom:1px solid #ccc;">                                           
                        <div class="col-md-4 border_right"  style="width:26%;height: 145px;">
                            <img src="<?php echo base_url(); ?>images/package-1.png" style="left: 50%;margin-left: -100px;margin-top: -50px;position: absolute;top: 50%;">
                        </div>
                        <div class="col-md-4" id="basic_log" style="width:32%;padding: 20px;height: 118px;">
                            <div class="col-md-6" >
                                <ul>
                                    <li><i class="fa fa-check-square"></i><span>User Manual</span></li>                                                
                                    <li><i class="fa fa-check-square"></i><span>Site Tour</span></li>                                                
                                    <li><i class="fa fa-check-square"></i><span>FAQ</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Tutorial Video</span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-4 border_left"  style="width:42%;padding: 20px;height: 145px;">
                            <div class="col-md-6" >
                                <ul>
                                    <li><i class="fa fa-check-square"></i><span>User Manual</span></li>                                                
                                    <li><i class="fa fa-check-square"></i><span>Site Tour</span></li>                                                
                                    <li><i class="fa fa-check-square"></i><span>FAQ</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Tutorial Video</span></li>
                                </ul>
                            </div>
                        </div>                                                                                          
                    </div> <!--row--> 
                </div>	
                <div class="col-md-12" style="padding:0px;border-bottom:1px solid #ccc;">
                    <div class="col-md-4 border_right" style="width:26%;height: 90px;">
                    </div>
                    <div class="col-md-4 pack_log" id="basic_log" style="width:32%;height: 90px;background:#E9E7E8;text-align: center;padding:20px;cursor:pointer;">
                        <a href="<?php echo base_url() ?>createschool/userregister/free" class="btn btn-info btn-basic">
                            Get Started Now</a>
                    </div>
                    <div class="col-md-4 pack_log border_left" id="premium_log" style="width:42%;height: 90px;background:#E9E7E8;text-align: center;padding:20px;cursor:pointer;">
                        <!--a href="<?php echo base_url() ?>createschool/subscription?locale=premium" class="btn btn-danger btn-basic"-->
                        <a href="<?php echo base_url() ?>createschool/userregister/paid" class="btn btn-danger btn-basic">
                            Get Started Now</a>	
                    </div>
                </div>
            </div>


            <div class="row <?php if ($act == "basic"): ?>hide<?php endif; ?> package-box" id="primium-box" style="background-color:#FFF;">		

                <div class="col-md-12"  style="">                                    
                    <div class="row" style="text-align: left;border-bottom:1px solid #ccc;">                                           
                        <div class="col-md-4 border_right"  style="width:26%;height: 232px;">                                                
                            <img src="<?php echo base_url(); ?>images/package-2.png" style="left: 50%;margin-left: -100px;margin-top: -50px;position: absolute;top: 50%;">
                        </div>
                        <div class="col-md-4" id="basic_log" style="width:32%;padding: 20px;height: 232px;">
                            <div class="col-md-6" >
                                <ul>
                                    <li><i class="fa fa-check-square"></i><span>Homeworks</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Notice</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Student Profile</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Teacher Profile</span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-4 border_left"  style="width:42%;padding: 20px;height: 232px;">
                            <div class="col-md-6" >
                                <ul>
                                    <li><i class="fa fa-check-square"></i><span>Homework</span></li>                                                
                                    <li><i class="fa fa-check-square"></i><span>Notice</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Student Profile</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Teacher Profile</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Meeting Request</span></li>
                                </ul>

                                <ul>
                                    <li><i class="fa fa-check-square"></i><span>Attendance</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Academic Calendar</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Online Quiz</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Events</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Transport</span></li>
                                </ul>
                            </div>
                            <div class="col-md-6" >
                                <ul>
                                    <li><i class="fa fa-check-square"></i><span>Fees</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Lesson Plan</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Syllabus</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Exam Routine</span></li>
                                </ul>
                                <ul>
                                    <li><i class="fa fa-check-square"></i><span>Routine</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Report Card</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Leave</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Forms</span></li>

                                </ul>
                            </div>
                        </div>                                                                           
                    </div> <!--row--> 
                    <div class="row" style="text-align: left;border-bottom:1px solid #ccc;">                                           
                        <div class="col-md-4 border_right"  style="width:26%;height: 150px;">
                            <img src="<?php echo base_url(); ?>images/package-3.png" style="left: 50%;margin-left: -100px;margin-top: -60px;position: absolute;top: 50%;">
                        </div>
                        <div class="col-md-4" id="basic_log" style="width:32%;padding: 20px;height: 150px;">
                            <ul>
								<li><i class="fa fa-check-square"></i><span>Unlimited User Registration</span></li>
								<li><i class="fa fa-check-square"></i><span>Profile Management</span></li>
								<li><i class="fa fa-check-square"></i><span>Manage Homework</span></li>                                                
								<li><i class="fa fa-check-square"></i><span>Manage Notice</span></li>                                               
							</ul>
                        </div>
                        <div class="col-md-4 border_left"  style="width:42%;padding: 20px;height: 150px;">
                            <div class="col-md-6" >
                                <ul>
                                    <li><i class="fa fa-check-square"></i><span>Unlimited Teacher and Admin</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Manage Employee</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Manage Student</span></li>                                                
                                    <li><i class="fa fa-check-square"></i><span>Manage Task</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Manage Task</span></li>
                                    <li><i class="fa fa-check-square"></i><span>App Management</span></li>                                                
                                </ul>
                            </div>
                            <div class="col-md-6" >
                                <ul>                                                    
                                    <li><i class="fa fa-check-square"></i><span>Data Management</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Manage Communication</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Email Alert Settings</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Real Time Notification</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Data Archive</span></li>                                                    
                                </ul>
                            </div>
                        </div>                                                                                          
                    </div> <!--row--> 
                    <div class="row" style="text-align: left;border-bottom:1px solid #ccc;">                                           
                        <div class="col-md-4 border_right"  style="width:26%;height: 165px;">
                            <img src="<?php echo base_url(); ?>images/package-4.png" style="left: 50%;margin-left: -100px;margin-top: -60px;position: absolute;top: 50%;">
                        </div>
                        <div class="col-md-4" id="basic_log" style="width:32%;padding: 20px;height: 165px;">
                            <div class="col-md-6" >
                                <ul>
                                    <li><i class="fa fa-check-square"></i><span>E-mail</span></li>                                                
                                    <li><i class="fa fa-check-square"></i><span>Hotline</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Query Form</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Live Chat</span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-4 border_left" style="width:42%;padding: 20px;height: 165px;">
                            <div class="col-md-12" >
                                <ul>
                                    <li><i class="fa fa-check-square"></i><span>Live Chat ( 6hrs/day, Choose your preferred time)</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Online IT Support</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Online Training</span></li>
                                    <li><i class="fa fa-check-square"></i><span>E-mail</span></li>                                                
                                    <li><i class="fa fa-check-square"></i><span>Hotline</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Query Form</span></li>
                                </ul>
                            </div>
                        </div>                                                                                          
                    </div> <!--row--> 
                    <div class="row" style="text-align: left;border-bottom:1px solid #ccc;">                                           
                        <div class="col-md-4 border_right"  style="width:26%;height: 145px;">
                            <img src="<?php echo base_url(); ?>images/package-1.png" style="left: 50%;margin-left: -100px;margin-top: -50px;position: absolute;top: 50%;">
                        </div>
                        <div class="col-md-4" id="basic_log" style="width:32%;padding: 20px;height: 118px;">
                            <div class="col-md-6" >
                                <ul>
                                    <li><i class="fa fa-check-square"></i><span>User Manual</span></li>                                                
                                    <li><i class="fa fa-check-square"></i><span>Site Tour</span></li>                                                
                                    <li><i class="fa fa-check-square"></i><span>FAQ</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Tutorial Video</span></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-4 border_left"  style="width:42%;padding: 20px;height: 145px;">
                            <div class="col-md-6" >
                                <ul>
                                    <li><i class="fa fa-check-square"></i><span>User Manual</span></li>                                                
                                    <li><i class="fa fa-check-square"></i><span>Site Tour</span></li>                                                
                                    <li><i class="fa fa-check-square"></i><span>FAQ</span></li>
                                    <li><i class="fa fa-check-square"></i><span>Tutorial Video</span></li>
                                </ul>
                            </div>
                        </div>                                                                                          
                    </div> <!--row--> 
                </div>
                <div class="col-md-12" style="padding:0px;border-bottom:1px solid #ccc;">
                    <div class="col-md-4 border_right" style="width:26%;height: 90px;">
                    </div>
                    <div class="col-md-4 pack_log" id="basic_log" style="width:32%;height: 90px;background:#E9E7E8;text-align: center;padding:20px;cursor:pointer;">
                        <a href="<?php echo base_url() ?>createschool/userregister/free" class="btn btn-info btn-basic">
                            Get Started Now</a>
                    </div>
                    <div class="col-md-4 pack_log border_left" id="premium_log" style="width:42%;height: 90px;background:#E9E7E8;text-align: center;padding:20px;cursor:pointer;">
                        <a href="<?php echo base_url() ?>createschool/userregister/paid" class="btn btn-danger btn-basic">
                            Get Started Now</a>	
                    </div>
                </div>
            </div>
        </div><!-- /.container -->

    </div><!-- /.item -->   
</div>

<script type="text/javascript">
    $(window).load(function () {
        $("#basic_log").on("click", function () {
            $('.pack_log').css('background-color', '#FFFFFF');
            $('#premium_log').css('background-color', "#F1F1F1");
            $('#basic-box').removeClass('hide');
            $('#basic-box').addClass('show');
            $('#primium-box').removeClass('show');
            $('#primium-box').addClass('hide');

        });
        $("#premium_log").on("click", function () {
            $('.pack_log').css('background-color', "#fff");
            $('#basic_log').css('background-color', "#F1F1F1");

            $('#primium-box').removeClass('hide');
            $('#basic-box').removeClass('show');
            $('#basic-box').addClass('hide');
            $('#primium-box').addClass('show');
        });
    });
</script>