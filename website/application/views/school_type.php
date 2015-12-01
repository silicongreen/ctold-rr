<?php 
$act = $_GET['local'];
?>
<div id="page" class="page" >
    <div class="item pricing" id="pricing_table2" style="margin-top:120px;">

        <div class="container" style="width:800px;margin:0px auto;background:#fff;" >

            <div class="row">
				<div class="col-md-12" style="padding:0px;background:#FFF;border-bottom:1px solid #ccc;">
					<div class="col-md-6 pack_log" id="basic_log" style="<?php if($act == "premium"): ?>background:#F1F1F1;<?php endif;?>text-align: center;padding:20px;cursor:pointer;">
						<i style="background:#0EB0C7;" class="fa-a fa fa-home"></i>
						<h2 style="font-size:25px;">Basic</h2>
						<p>Smart Free Start Up</p>
					</div>
					<div class="col-md-6 pack_log" id="premium_log" style="<?php if($act == "basic"): ?>background:#F1F1F1;<?php endif;?>text-align: center;padding:20px;cursor:pointer;">
						<i style="background:#F56332;" class="fa-a fa fa-star"></i>
						<h2 style="font-size:25px;">Premium</h2>
						<p>$1.99 Per Month / Student</p>
					</div>
				</div>				
            </div><!-- /.row -->
			<div class="row package-box <?php if($act == "premium"): ?>hide<?php endif;?>" id="basic-box" style="padding:25px;">
				<div class="col-md-12"  style="">					
					<div class="col-md-8" style="text-align: left;">
						<h2 style="font-size:25px;font-weight:bold;margin-left:0px;">Create Your Free School...</h2>
					</div>
					<div class="col-md-3" style="text-align: left;">				
						<a href="<?php echo base_url()?>createschool/userregister/free" class="btn btn-info btn-basic" style="font-size:13px;padding:7px 20px;">
					Get Started Now</a>	
					</div>
				</div>
				<div class="col-md-12"  style="">					
                                    <div class="col-md-6" style="text-align: left;">
                                        <h3 style="color:#0EB0C9;font-size:17px;margin-left:1px;">Features for Students</h3>
                                            <ul>
                                                <li><i class="fa fa-check-square"></i><span>Student Profile</span></li>                                                
                                                <li><i class="fa fa-check-square"></i><span>Homework</span></li>
                                                <li><i class="fa fa-check-square"></i><span>Notice</span></li>
                                            </ul>
                                    </div>
                                    <div class="col-md-6" style="text-align: left;">
                                        <h3 style="color:#0EB0C9;font-size:17px;margin-left:1px;">Features for Parents</h3>
                                            <ul>
                                                    <li><i class="fa fa-check-square"></i><span>Student Profile</span></li>
                                                    <li><i class="fa fa-check-square"></i><span>Homework</span></li>
                                                    <li><i class="fa fa-check-square"></i><span>Notice</span></li>
                                            </ul>
                                    </div>
				</div>
				
				<div class="col-md-12"  style="">
					
					<div class="col-md-6" style="text-align: left;">
                                            <h3 style="color:#0EB0C9;font-size:17px;margin-left:1px;">Features for Teachers</h3>
						<ul>
							<li><i class="fa fa-check-square"></i><span>Student Profile</span></li>
							<li><i class="fa fa-check-square"></i><span>Teacher Profile</span></li>
							<li><i class="fa fa-check-square"></i><span>Homework</span></li>
							<li><i class="fa fa-check-square"></i><span>Notice</span></li>
						</ul>
					</div>	
                                        <div class="col-md-6" style="text-align: left;">
                                            <h3 style="color:#0EB0C9;font-size:17px;margin-left:1px;">Features for School Admin</h3>
						<ul>
							<li><i class="fa fa-check-square"></i><span>Create School</span></li>
							<li><i class="fa fa-check-square"></i><span>Create Profile ( Students, Parents, Teachers)</span></li>
							<li><i class="fa fa-check-square"></i><span>Create Notice</span></li>
							<li><i class="fa fa-check-square"></i><span>Manage Homework</span></li>
						</ul>
					</div>	
				</div> 
                                <div class="col-md-12"  style="">
					
					<div class="col-md-6" style="text-align: left;">
                                            <h3 style="color:#0EB0C9;font-size:17px;margin-left:1px;">Customer Support</h3>
						<ul>
							<li><i class="fa fa-check-square"></i><span>E-mail </span></li>
							<li><i class="fa fa-check-square"></i><span>Hotline </span></li>
							<li><i class="fa fa-check-square"></i><span>Query Form</span></li>
							<li><i class="fa fa-check-square"></i><span>Live Chat</span></li>
						</ul>
					</div>	
                                        <div class="col-md-6" style="text-align: left;">
                                            <h3 style="color:#0EB0C9;font-size:17px;margin-left:1px;">Online Help</h3>
						<ul>
							<li><i class="fa fa-check-square"></i><span>User Manual</span></li>
							<li><i class="fa fa-check-square"></i><span>Site Tour</span></li>
							<li><i class="fa fa-check-square"></i><span>FAQ</span></li>
							<li><i class="fa fa-check-square"></i><span>Tutorial Video</span></li>
						</ul>
					</div>	
				</div>
				<div class="col-md-12" style="text-align:center;margin-top:40px;margin-bottom:50px;">
					<a href="<?php echo base_url()?>createschool/userregister/free" class="btn btn-info btn-basic">
					Get Started Now</a>					
				</div>
			</div>
			
			<div class="row <?php if($act == "basic"): ?>hide<?php endif;?> package-box" id="primium-box" style="padding:25px;'">
				
				<div class="col-md-12"  style="">					
					<div class="col-md-8" style="text-align: left;">
						<h2 style="font-size:25px;font-weight:bold;margin-left:0px;">Create Your Premium School...</h2>
					</div>
					<div class="col-md-3" style="text-align: left;">				
						<a href="<?php echo base_url()?>createschool/userregister/paid" class="btn btn-danger btn-basic" style="font-size:13px;padding:7px 20px;">
					Get Started Now</a>	
					</div>
				</div>
				<div class="col-md-12"  style="">		
                                     <h3 style="color:#F56332;font-size:17px;margin-left:15px;">Features for Student</h3>
                                    <div class="col-md-6" style="text-align: left;">                                       
                                            <ul>
                                                <li><i class="fa fa-check-square"></i><span>Student Profile</span></li>                                                
                                                <li><i class="fa fa-check-square"></i><span>Homework</span></li>
                                                <li><i class="fa fa-check-square"></i><span>Notice</span></li>
                                                <li><i class="fa fa-check-square"></i><span>Attendance</span></li>
                                                <li><i class="fa fa-check-square"></i><span>Events</span></li>
                                                <li><i class="fa fa-check-square"></i><span>Academic Calendar</span></li>
                                                <li><i class="fa fa-check-square"></i><span>Syllabus</span></li>
                                            </ul>
                                    </div>
                                    <div class="col-md-6" style="text-align: left;">                                        
                                            <ul>
                                                    <li><i class="fa fa-check-square"></i><span>Class Routine</span></li>
                                                    <li><i class="fa fa-check-square"></i><span>Forms</span></li>
                                                    <li><i class="fa fa-check-square"></i><span>Leave</span></li>
                                                    <li><i class="fa fa-check-square"></i><span>Report Card</span></li>
                                                    <li><i class="fa fa-check-square"></i><span>Online Quiz</span></li>
                                                    <li><i class="fa fa-check-square"></i><span>Examination</span></li>
                                                    <li><i class="fa fa-check-square"></i><span>Transport</span></li>
                                            </ul>
                                    </div>
				</div>
                                
                                <div class="col-md-12"  style="">		
                                     <h3 style="color:#F56332;font-size:17px;margin-left:15px;">Features for Parents</h3>
                                    <div class="col-md-6" style="text-align: left;">                                       
                                            <ul>
                                                <li><i class="fa fa-check-square"></i><span>Student Profile</span></li>
                                                <li><i class="fa fa-check-square"></i><span>Homework</span></li>                                                
                                                <li><i class="fa fa-check-square"></i><span>Notice</span></li>
                                                <li><i class="fa fa-check-square"></i><span>Attendance</span></li>
                                                <li><i class="fa fa-check-square"></i><span>Events</span></li>
                                                <li><i class="fa fa-check-square"></i><span>Academic Calendar</span></li>
                                                <li><i class="fa fa-check-square"></i><span>Syllabus</span></li>
                                                <li><i class="fa fa-check-square"></i><span>Class Routine</span></li>
                                                
                                            </ul>
                                    </div>
                                    <div class="col-md-6" style="text-align: left;">                                        
                                            <ul>                                                    
                                                    <li><i class="fa fa-check-square"></i><span>Forms</span></li>
                                                    <li><i class="fa fa-check-square"></i><span>Leave</span></li>
                                                    <li><i class="fa fa-check-square"></i><span>Report Card</span></li>
                                                    <li><i class="fa fa-check-square"></i><span>Online Quiz</span></li>
                                                    <li><i class="fa fa-check-square"></i><span>Examination</span></li>
                                                    <li><i class="fa fa-check-square"></i><span>Fees</span></li>
                                                    <li><i class="fa fa-check-square"></i><span>Leave Application</span></li>
                                                    <li><i class="fa fa-check-square"></i><span>Meeting Request</span></li>
                                                    
                                            </ul>
                                    </div>
				</div>
                                
                                <div class="col-md-12"  style="">		
                                     <h3 style="color:#F56332;font-size:17px;margin-left:15px;">Features for Teachers</h3>
                                    <div class="col-md-6" style="text-align: left;">                                       
                                            <ul>
                                                <li><i class="fa fa-check-square"></i><span>Teacher Profile</span></li>
                                                <li><i class="fa fa-check-square"></i><span>Student Profile</span></li>
                                                <li><i class="fa fa-check-square"></i><span>Attendance</span></li>
                                                <li><i class="fa fa-check-square"></i><span>Homework</span></li>                                                
                                                <li><i class="fa fa-check-square"></i><span>Notice</span></li>
                                                <li><i class="fa fa-check-square"></i><span>Examination</span></li>
                                                <li><i class="fa fa-check-square"></i><span>Online Quiz</span></li>
                                            </ul>
                                    </div>
                                    <div class="col-md-6" style="text-align: left;">                                        
                                            <ul>                                                    
                                                    <li><i class="fa fa-check-square"></i><span>Leave Application</span></li>
                                                    <li><i class="fa fa-check-square"></i><span>Meeting Request</span></li>                                                    
                                                    <li><i class="fa fa-check-square"></i><span>Admin Access</span></li>
                                                    <li><i class="fa fa-check-square"></i><span>Report Card</span></li>
                                                    <li><i class="fa fa-check-square"></i><span>Academic Calendar</span></li>
                                                    <li><i class="fa fa-check-square"></i><span>Lesson Plan</span></li>
                                            </ul>
                                    </div>
				</div>
                                <div class="col-md-12"  style="">		
                                     <h3 style="color:#F56332;font-size:17px;margin-left:15px;">Feature for Admin</h3>
                                    <div class="col-md-6" style="text-align: left;">                                       
                                            <ul>
                                                <li><i class="fa fa-check-square"></i><span>Manage Employee (Admission, Profile, Attendance, Leave)</span></li>
                                                <li><i class="fa fa-check-square"></i><span>Manage Student (Admission, Attendance, Leave)</span></li>
                                                <li><i class="fa fa-check-square"></i><span>Manage Homework</span></li>
                                                <li><i class="fa fa-check-square"></i><span>Manage Examination</span></li>                                                
                                                <li><i class="fa fa-check-square"></i><span>Manage Routin</span></li>
                                                <li><i class="fa fa-check-square"></i><span>Manage Notice</span></li>
                                            </ul>
                                    </div>
                                    <div class="col-md-6" style="text-align: left;">                                        
                                            <ul>                                                    
                                                    <li><i class="fa fa-check-square"></i><span>Manage Transport</span></li>
                                                    <li><i class="fa fa-check-square"></i><span>Manage Task</span></li>                                                    
                                                    <li><i class="fa fa-check-square"></i><span>Manage Settings</span></li>
                                                    <li><i class="fa fa-check-square"></i><span>Client App Management</span></li>
                                                    <li><i class="fa fa-check-square"></i><span>Data Management</span></li>
                                            </ul>
                                    </div>
				</div>
                                <div class="col-md-12"  style="">		                                    
                                    <div class="col-md-6" style="text-align: left;">                                       
                                            <ul>
                                                <li><i class="fa fa-check-square"></i><span>General Settings</span></li>
                                                <li><i class="fa fa-check-square"></i><span>Send Email</span></li>
                                                <li><i class="fa fa-check-square"></i><span>Email Alert Settings</span></li>
                                                <li><i class="fa fa-check-square"></i><span>Manage Communication</span></li> 
                                            </ul>
                                    </div>
                                    <div class="col-md-6" style="text-align: left;">                                        
                                            <ul>                                                    
                                                    <li><i class="fa fa-check-square"></i><span>E-mail</span></li>
                                                    <li><i class="fa fa-check-square"></i><span>Hotline</span></li>                                                    
                                                    <li><i class="fa fa-check-square"></i><span>Query Form</span></li>
                                            </ul>
                                    </div>
				</div>
                                <div class="col-md-12"  style="">		                                    
                                    <div class="col-md-6" style="text-align: left;">                                       
                                            <ul>
                                                <li><i class="fa fa-check-square"></i><span>Live Chat</span></li>
                                                <li><i class="fa fa-check-square"></i><span>User Manual</span></li>
                                                <li><i class="fa fa-check-square"></i><span>Site Tour</span></li>
                                                <li><i class="fa fa-check-square"></i><span>FAQ</span></li> 
                                                <li><i class="fa fa-check-square"></i><span>Tutorial Video</span></li> 
                                            </ul>
                                    </div>
				</div>
				<div class="col-md-12" style="text-align:center;margin-top:40px;margin-bottom:50px;">
					<a href="<?php echo base_url()?>createschool/userregister/paid" class="btn btn-danger btn-basic">
					Get Started Now</a>					
				</div>
			</div>
        </div><!-- /.container -->

    </div><!-- /.item -->   
</div>

<script type="text/javascript">
    $(window).load(function () {
        $( "#basic_log" ).on( "click", function() {            
            $('.pack_log').css('background-color', '#FFFFFF');
            $('#premium_log').css('background-color',"#F1F1F1");
            $('#basic-box').removeClass('hide');
            $('#basic-box').addClass('show');
            $('#primium-box').removeClass('show');
            $('#primium-box').addClass('hide');
            
        });
        $( "#premium_log" ).on( "click", function() {            
            $('.pack_log').css('background-color',"#fff");
            $('#basic_log').css('background-color',"#F1F1F1");
            
            $('#primium-box').removeClass('hide');
            $('#basic-box').removeClass('show');
            $('#basic-box').addClass('hide');
            $('#primium-box').addClass('show');
        });
    });
</script>