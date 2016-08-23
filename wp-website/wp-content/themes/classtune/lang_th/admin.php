<?php
/**
 * The template for displaying pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other "pages" on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

/*
Template Name: admin-th
*/

get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<link rel="stylesheet" href="<?php echo get_site_url(); ?>/bootstrap/css/bootstrap-lightbox.min.css" type="text/css" media="all" />
<div class="container"><div class="container">
	<div class="wrapper">
		<div id="startWrap">
			
		</div>

		<div id="beforeWrap" style="background: transparent url(<?php bloginfo('template_url'); ?>/images/cover/admin-page.png) no-repeat bottom left;background-size:cover ;">			
			<h2 class="f2" style="margin-top:100px;"><i>จัดการธุรกรรมโรงเรียนได้ง่ายอย่างไม่เคยเป็นมาก่อน!</i></h2>
			<div class="postlist-tab2">
				<div style=" background-color: #fff;height: 220px;margin-left: 100px;position: relative;top: -120px;width: 500px;z-index: 1;box-shadow: 2px 4px 2px -3px gray;border:1px solid #ccc;">
					<div class="col-sm-12" style="margin-top:40px;">
						<div class="col-sm-3" style="border:1px solid;margin-top:12px;"><span style=""></span></div>
						<div class="col-sm-6" style="font-size:18px;"><b>เลือกแพคเกจของคุณ</b>	</div>					
						<div class="col-sm-3" style="border:1px solid;margin-top:12px;"><span style=""></span></div>
					</div>
					<div class="col-sm-12" style="">
					<div class="col-sm-6"><a href="<?php echo get_site_url().'/'.$lang; ?>/package-type<?php echo "-".$lang;?>?local=basic" class="btn-basic-pack">
					Basic</a><!--span class="btn-basic-pack-text">Free</span--></div> 
					<div class="col-sm-6"><a href="<?php echo get_site_url().'/'.$lang; ?>/package-type<?php echo "-".$lang;?>?local=premium" class="btn-primium-pack">
					Premium</a><!--span class="btn-primium-pack-text">$1.99 Per Month/ Student</span--></div> 					
					
					</div>
				</div>
			</div>
		</div>

		<div id="cronWrap" style="background-color:#F4FAFA;background-image:none;top:500px;height:800px;">
			<div style="border: 0 solid #ccc;left: 50%;margin: 0 0 0 -499px;position: absolute;width: 1000px;padding:55px 10px 0;">
				<h2 class="f2" style="text-align:center;"><i>เจ้าหน้าที่ธุรการ มีความสุขกับความง่ายในการเก็บสถิติต่างๆ!</i></h2><br>
				<p style="font-size:16px;"><b>ClassTune</b>ทำให้การติดตามเฝ้าดูและจัดการของเจ้าหน้าที่ธุรการทำได้ง่ายและมีประสิทธิภาพ โดยสามารถเข้าถึง จัดการ มีส่วนร่วมกับ คุณครู นักเรียน และผู้ปกครอง ทั้งหมดในโรงเรียนผ่านช่องทางเดียว ซึ่งจะสามารถสนับสนุนมาตรการในการปรับปรุงผลการเรียนรู้.</p><br>
				<div id="slider"  class="flexslider" style="margin-top:30px;width:1000px;">
					<ul class="slides">
						<li>
							<div class="col-sm-12" style="height:300px;width:1000px;">								
								<div class="col-sm-1" style="text-align:-moz-center;"></div>
                                                                <div class="col-sm-3" style="text-align:-moz-center;">
                                                                    <div class="thumbnail-item">
                                                                        <a href="#"><img class="img-responsive" src="<?php bloginfo('template_url'); ?>/images/dashpallete/view/attendance.png" style="width:170px;" /></a>
                                                                        <div class="tooltip">
                                                                            <img src="<?php bloginfo('template_url'); ?>/images/dashpallete/view/attendance.png" alt="" style="width:100%;" />
                                                                        </div> 
                                                                    </div>
								</div>
								<div class="col-sm-1" style="text-align:-moz-center;"></div>
								<div class="col-sm-7">
									<div class="col-sm-12">
										<div class="col-sm-4" style="width: 60px;	height: 60px;	border-radius: 30px;	-webkit-border-radius: 30px;	-moz-border-radius: 30px;	background: #64B846 url(<?php bloginfo('template_url'); ?>/images/dashpallete/attandance.png) no-repeat center;"></div>
										<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>การเช็คชื่อ
</i></p></div>
									</div>
									
									<div class="col-sm-12"  style="margin-top:30px;">
									<p>
                                                                            ดูการเช็คชื่อทั้งหมดของโรงเรียนผ่านช่องทางเดียว ในทุกชั้นเรียนและทุกส่วน ช่วยให้ติดตามการเข้าเรียนได้ง่าย เรียกดูบันทึกย้อนหลังได้ทุกเมื่อที่ต้องการ ในภาพรวมทำให้คุณนับจำนวนนักเรียนที่ขาดเรียนทั้งโรงเรียนได้อย่างรวดเร็ว. 
									</p>
                                                                        <h3 class="f2" style="font-size:20px;padding: 16px 5px;">ดูการเช็คชื่อของทั้งโรงเรียนได้ง่ายผ่านช่องทางเดียว.</h3>
									</div>									
								</div>								
							</div>	
						</li>
						<li>
							<div class="col-sm-12" style="height:300px;width:1000px;">
								<div class="col-sm-1" style="text-align:-moz-center;"></div>
								<div class="col-sm-3" style="text-align:-moz-center;">								
                                                                    <div class="thumbnail-item">
                                                                        <a href="#"><img class="img-responsive" src="<?php bloginfo('template_url'); ?>/images/dashpallete/view/academic_calender.png" style="width:170px;" /></a>
                                                                        <div class="tooltip">
                                                                            <img src="<?php bloginfo('template_url'); ?>/images/dashpallete/view/academic_calender.png" alt="" style="width:100%;" />
                                                                        </div> 
                                                                    </div>
								</div>
								<div class="col-sm-1" style="text-align:-moz-center;"></div>
								<div class="col-sm-7">
									<div class="col-sm-12">
										<div class="col-sm-4" style="width: 60px;	height: 60px;	border-radius: 30px;	-webkit-border-radius: 30px;	-moz-border-radius: 30px;	background: #64B846 url(<?php bloginfo('template_url'); ?>/images/dashpallete/academic_calender.png) no-repeat center;"></div>
										<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>ปฏิทินการศึกษา</i></p></div>
									</div>
									
									<div class="col-sm-12"  style="margin-top:30px;">
									<p>
										อัพโหลดปฏิทินการศีกษาและตั้งค่าให้ผู้ที่มีส่วนเกี่ยวข้องมองเห็นได้ ช่วยให้ทุกคนวางแผนในอนาคตได้ง่าย โดยคุณสามารถอัพโหลดปฏิทินการศึกษาได้ตั้งแต่ต้นปี และสามารถแก้ไขได้อย่างยืดหยุ่นทุกเมื่อที่ต้องการ.
									</p>
                                                                        <h3 class="f2" style="font-size:20px;padding: 16px 5px;">ทุก วันสำคัญ อยู่ที่ปลาย นิ้วของคุณ.</h3>
									</div>									
								</div>								
							</div>	
						</li>
						<li>
							<div class="col-sm-12" style="height:300px;width:1000px;">
								<div class="col-sm-1" style="text-align:-moz-center;"></div>
								<div class="col-sm-3" style="text-align:-moz-center;">									
                                                                        <div class="thumbnail-item">
                                                                                <a href="#"><img class="img-responsive" src="<?php bloginfo('template_url'); ?>/images/dashpallete/view/lesson_plan.png" style="width:170px;" /></a>
                                                                                <div class="tooltip">
                                                                                        <img src="<?php bloginfo('template_url'); ?>/images/dashpallete/view/lesson_plan.png" alt="" style="width:100%;" />
                                                                                        
                                                                                </div> 
                                                                        </div>
								</div>
								<div class="col-sm-1" style="text-align:-moz-center;"></div>
								<div class="col-sm-7">
									<div class="col-sm-12">
										<div class="col-sm-4" style="width: 60px;	height: 60px;	border-radius: 30px;	-webkit-border-radius: 30px;	-moz-border-radius: 30px;	background: #64B846 url(<?php bloginfo('template_url'); ?>/images/dashpallete/lesson_paln-white.png) no-repeat center;"></div>
										<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>แผนการเรียน</i></p></div>
									</div>
									
									<div class="col-sm-12"  style="margin-top:30px;">
									<p>
										แผนการเรียนสามารถดูผ่านออนไลน์ได้ และแก้ไขได้ทุกเมื่อที่ต้องการ สะดวกในการติดตามแผนกาเรียนกับปฏิทินการศึกษา.    
									</p>
                                                                        <h3 class="f2" style="font-size:20px;padding: 16px 5px;">สะดวกสบายในการปรับปรุงแผนการเรียนของโรงเรียน.</h3>
									</div>									
								</div>								
							</div>		
						</li>
						<li>
							<div class="col-sm-12" style="height:300px;width:1000px;">
								<div class="col-sm-1" style="text-align:-moz-center;"></div>
								<div class="col-sm-3" style="text-align:-moz-center;">
                                                                    <div class="thumbnail-item">
                                                                        <a href="#"><img class="img-responsive" src="<?php bloginfo('template_url'); ?>/images/dashpallete/view/Routine.png" style="width:170px;" /></a>
                                                                        <div class="tooltip">
                                                                            <img src="<?php bloginfo('template_url'); ?>/images/dashpallete/view/Routine.png" alt="" style="width:100%;" />
                                                                        </div> 
                                                                    </div>
								</div>
								<div class="col-sm-1" style="text-align:-moz-center;"></div>
								<div class="col-sm-7">
									<div class="col-sm-12">
										<div class="col-sm-4" style="width: 60px;	height: 60px;	border-radius: 30px;	-webkit-border-radius: 30px;	-moz-border-radius: 30px;	background: #64B846 url(<?php bloginfo('template_url'); ?>/images/dashpallete/trachers_routine.png) no-repeat center;"></div>
										<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>ตารางสอนสำหรับครู</i></p></div>
									</div>
									
									<div class="col-sm-12"  style="margin-top:30px;">
									<p>
										สะดวกสบายในการอัพโหลดตารางสอนสำหรับครู ดูตารางสอนประจำวันและประจำสัปดาห์ของครูแต่ละคนได้. 
									</p>
                                                                        <h3 class="f2" style="font-size:20px;padding: 16px 5px;">อัปโหลด ประจำ ครู เพื่อความสะดวก ของพวกเขา. </h3>
									</div>									
								</div>								
							</div>		
						</li>
						<li>
							<div class="col-sm-12" style="height:300px;width:1000px;">
								<div class="col-sm-1" style="text-align:-moz-center;"></div>
								<div class="col-sm-3" style="text-align:-moz-center;">                                                                    
                                                                    <div class="thumbnail-item">
                                                                        <a href="#"><img class="img-responsive" src="<?php bloginfo('template_url'); ?>/images/dashpallete/view/Homework.png" style="width:170px;" /></a>
                                                                        <div class="tooltip">
                                                                            <img src="<?php bloginfo('template_url'); ?>/images/dashpallete/view/Homework.png" alt="" style="width:100%;" />
                                                                        </div> 
                                                                    </div>
								</div>
								<div class="col-sm-1" style="text-align:-moz-center;"></div>
								<div class="col-sm-7">
									<div class="col-sm-12">
										<div class="col-sm-4" style="width: 60px;	height: 60px;	border-radius: 30px;	-webkit-border-radius: 30px;	-moz-border-radius: 30px;	background: #64B846 url(<?php bloginfo('template_url'); ?>/images/dashpallete/homework.png) no-repeat center;"></div>
										<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>การบ้าน</i></p></div>
									</div>
									
									<div class="col-sm-12"  style="margin-top:30px;">
									<p>
										ตรวจสอบ การบ้าน ของ นักเรียน ส่วน ชั้น หนึ่งหรือ ทั้งโรงเรียน ที่ แพลตฟอร์มเดียว. 
									</p>
                                                                        <h3 class="f2" style="font-size:20px;padding: 16px 5px;">ความยืดหยุ่นในการ ตรวจสอบ การบ้าน ของ ทุกชั้น.</h3>
									</div>									
								</div>								
							</div>	
						</li>
						<li>
							<div class="col-sm-12" style="height:300px;width:1000px;">
								<div class="col-sm-1" style="text-align:-moz-center;"></div>
								<div class="col-sm-3" style="text-align:-moz-center;">									
                                                                    <div class="thumbnail-item">
                                                                        <a href="#"><img class="img-responsive" src="<?php bloginfo('template_url'); ?>/images/dashpallete/view/fees.png" style="width:170px;" /></a>                                                                        
                                                                    </div>
								</div>
								<div class="col-sm-1" style="text-align:-moz-center;"></div>
								<div class="col-sm-7">
									<div class="col-sm-12">
										<div class="col-sm-4" style="width: 60px;	height: 60px;	border-radius: 30px;	-webkit-border-radius: 30px;	-moz-border-radius: 30px;	background: #64B846 url(<?php bloginfo('template_url'); ?>/images/dashpallete/fees.png) no-repeat center;"></div>
										<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>ค่าเล่าเรียน</i></p></div>
									</div>
									
									<div class="col-sm-12"  style="margin-top:30px;">
									<p>
                                                                            ค่าธรรมเนียม กำหนดการและ พร้อมรับคำ แจ้งเตือน ค่าบริการ อัตโนมัติ สำหรับการชำระเงิน ที่ค้างอยู่ ไม่ต้องกังวล เกี่ยวกับการจัดการ การจัดเก็บ และ การสร้าง / การรักษา ใบเสร็จรับเงิน เครื่องมือที่ดี สำหรับการตรวจสอบ การชำระเงิน และการเก็บรักษา บัญชี โรงเรียน. 
									</p>
                                                                        <h3 class="f2" style="font-size:20px;padding: 16px 5px;">ความยุ่งยากใน การเก็บ ค่าใช้จ่าย ฟรี.</h3>
									</div>									
								</div>								
							</div>
						</li>
						<li>
							<div class="col-sm-12" style="height:300px;width:1000px;">
								<div class="col-sm-1" style="text-align:-moz-center;"></div>
								<div class="col-sm-3" style="text-align:-moz-center;">									
                                                                    <div class="thumbnail-item">
                                                                        <a href="#"><img class="img-responsive" src="<?php bloginfo('template_url'); ?>/images/dashpallete/view/Forms.png" style="width:170px;" /></a>                                                                        
                                                                    </div>
								</div>
								<div class="col-sm-1" style="text-align:-moz-center;"></div>
								<div class="col-sm-7">
									<div class="col-sm-12">
										<div class="col-sm-4" style="width: 60px;	height: 60px;	border-radius: 30px;	-webkit-border-radius: 30px;	-moz-border-radius: 30px;	background: #64B846 url(<?php bloginfo('template_url'); ?>/images/dashpallete/defult.png) no-repeat center;"></div>
										<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>เอกสาร</i></p></div>
									</div>
									
									<div class="col-sm-12"  style="margin-top:30px;">
									<p>
										อัพโหลดเอกสารที่จำเป็นในการใช้งานสำหรับคนอื่นๆที่ต้องการให้สามารถดาวน์โหลดไปใช้ได้ ไม่ต้องต่อคิวรอรับเอกสารที่ห้องพักครู และมีประสิทธิภาพสูงในการบริหารจัดการ.
									</p>
                                                                        <h3 class="f2" style="font-size:20px;padding: 16px 5px;">เอกสารออนไลน์ ที่ง่ายสำหรับการใช้งาน.</h3>
									</div>									
								</div>								
							</div>	
						</li>
						<li>
							<div class="col-sm-12" style="height:300px;width:1000px;">
								<div class="col-sm-1" style="text-align:-moz-center;"></div>
								<div class="col-sm-3" style="text-align:-moz-center;">                                                                    
                                                                    <div class="thumbnail-item">
                                                                        <a href="#"><img class="img-responsive" src="<?php bloginfo('template_url'); ?>/images/dashpallete/view/meeting_request.png" style="width:170px;" /></a>                                                                        
                                                                    </div>
								</div>
								<div class="col-sm-1" style="text-align:-moz-center;"></div>
								<div class="col-sm-7">
									<div class="col-sm-12">
										<div class="col-sm-4" style="width: 60px;	height: 60px;	border-radius: 30px;	-webkit-border-radius: 30px;	-moz-border-radius: 30px;	background: #64B846 url(<?php bloginfo('template_url'); ?>/images/dashpallete/meeting_request.png) no-repeat center;"></div>
										<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>การขอนัดพบ</i></p></div>
									</div>
									
									<div class="col-sm-12"  style="margin-top:30px;">
									<p>
										ความสำคัญคือมันเป็นวิธีการที่จะพบกับครูทุกคนและผู้ปกครอง! ทำให้การนัดประชุมกับผู้ปกครองเป็นเรื่องง่าย แม้แต่การเปลี่ยนนัดอย่างกระทันหัน ซึ่งมีระบบแจ้งเตือนพวกเค้าอย่างรวดเร็ว. 
									</p>
                                                                        <h3 class="f2" style="font-size:20px;padding: 16px 5px;">เรียกว่าการนัดทำได้ง่ายมากในตอนนี้! </h3>
									</div>									
								</div>								
							</div>	
						</li>
						<li>
							<div class="col-sm-12" style="height:300px;width:1000px;">
								<div class="col-sm-1" style="text-align:-moz-center;"></div>
								<div class="col-sm-3" style="text-align:-moz-center;">									
                                                                    <div class="thumbnail-item">
                                                                        <a href="#"><img class="img-responsive" src="<?php bloginfo('template_url'); ?>/images/dashpallete/view/report_card.png" style="width:170px;" /></a>                                                                        
                                                                    </div>
								</div>
								<div class="col-sm-1" style="text-align:-moz-center;"></div>
								<div class="col-sm-7">
									<div class="col-sm-12">
										<div class="col-sm-4" style="width: 60px;	height: 60px;	border-radius: 30px;	-webkit-border-radius: 30px;	-moz-border-radius: 30px;	background: #64B846 url(<?php bloginfo('template_url'); ?>/images/dashpallete/report_card.png) no-repeat center;"></div>
										<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>ผลสอบ</i></p></div>
									</div>
									
									<div class="col-sm-12"  style="margin-top:30px;">
									<p>
										สอบประจำชั้น, ผลสอบ และ รายงานโปรเจค ที่สามารถใช้งานได้ในรูปแบบดิจิตอล คุณสามารถสร้าง ปรับปรุง พัฒนา และกระจายผลสอบได้ง่ายอย่างไม่เคยมาก่อน นอกจากนั้นยังบันทึกไว้ในระบบได้ตลอดไป.
									</p>
                                                                        <h3 class="f2" style="font-size:20px;padding: 16px 5px;">ผลสอบออนไลน์ </h3>
									</div>									
								</div>								
							</div>	
						</li>
						<li>
                                                    <div class="col-sm-12" style="height:300px;width:1000px;">
                                                            <div class="col-sm-1" style="text-align:-moz-center;"></div>
                                                            <div class="col-sm-3" style="text-align:-moz-center;">                                                                
                                                                <div class="thumbnail-item">
                                                                    <a href="#"><img class="img-responsive" src="<?php bloginfo('template_url'); ?>/images/dashpallete/view/events.png" style="width:170px;" /></a>                                                                        
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-1" style="text-align:-moz-center;"></div>
                                                            <div class="col-sm-7">
                                                                    <div class="col-sm-12">
                                                                            <div class="col-sm-4" style="width: 60px;	height: 60px;	border-radius: 30px;	-webkit-border-radius: 30px;	-moz-border-radius: 30px;	background: #64B846 url(<?php bloginfo('template_url'); ?>/images/dashpallete/event.png) no-repeat center;"></div>
                                                                            <div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>กิจกรรม</i></p></div>
                                                                    </div>

                                                                    <div class="col-sm-12"  style="margin-top:30px;">
                                                                    <p>
                                                                            มันใจว่าข้อมูลทุกกิจกรรมที่กำลังจะเกิดขึ้น จะส่งถึงนักเรียน ผู้ปกครอง และครูทุกคนอย่างแน่นอน โดยคุณสามารถสร้างและอัพเดทกิจกรรมได้ตลอดเวลา และยังทำให้คุณทราบว่ามีนักเรียน ผู้ปกครอง และครู กี่คนที่จะเข้าร่วมกิจกรรม.  
                                                                    </p>
                                                                    <h3 class="f2" style="font-size:20px;padding: 16px 5px;">ไม่พลาดทุกกิจกรรมที่กำลังจะเกิดผ่านแพลตฟอร์มเดียว!</h3>
                                                                    </div>									
                                                            </div>								
                                                    </div>	
						</li>	
                                                <li>
                                                    <div class="col-sm-12" style="height:300px;width:1000px;">
                                                            <div class="col-sm-1" style="text-align:-moz-center;"></div>
                                                            <div class="col-sm-3" style="text-align:-moz-center;">
                                                                <div class="thumbnail-item">
                                                                    <a href="#"><img class="img-responsive" src="<?php bloginfo('template_url'); ?>/images/dashpallete/view/transport.png" style="width:170px;" /></a>                                                                        
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-1" style="text-align:-moz-center;"></div>
                                                            <div class="col-sm-7">
                                                                    <div class="col-sm-12">
                                                                            <div class="col-sm-4" style="width: 60px;	height: 60px;	border-radius: 30px;	-webkit-border-radius: 30px;	-moz-border-radius: 30px;	background: #64B846 url(<?php bloginfo('template_url'); ?>/images/dashpallete/transport.png) no-repeat center;"></div>
                                                                            <div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>การรับส่ง</i></p></div>
                                                                    </div>

                                                                    <div class="col-sm-12"  style="margin-top:30px;">
                                                                    <p>
                                                                            วางแผนการรับส่งนักเรียนและอัพโหลดไว้ที่นี่ และมั่นใจว่าผู้ปกครองจะได้รับการแจ้งเตือนภายในไม่กี่นาที เมื่อมีการเปลี่ยนแปลงแผนการรับส่ง. 
                                                                    </p>
                                                                    <h3 class="f2" style="font-size:20px;padding: 16px 5px;">ติดตามตารางการรับส่งลูกๆของคุณอย่างไกล้ชิด</h3>
                                                                    </div>									
                                                            </div>								
                                                    </div>	
						</li>	
                                                <li>
                                                    <div class="col-sm-12" style="height:300px;width:1000px;">
                                                            <div class="col-sm-1" style="text-align:-moz-center;"></div>
                                                            <div class="col-sm-3" style="text-align:-moz-center;">
                                                                <div class="thumbnail-item">
                                                                    <a href="#"><img class="img-responsive" src="<?php bloginfo('template_url'); ?>/images/dashpallete/view/Routine.png" style="width:170px;" /></a>                                                                        
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-1" style="text-align:-moz-center;"></div>
                                                            <div class="col-sm-7">
                                                                    <div class="col-sm-12">
                                                                            <div class="col-sm-4" style="width: 60px;	height: 60px;	border-radius: 30px;	-webkit-border-radius: 30px;	-moz-border-radius: 30px;	background: #64B846 url(<?php bloginfo('template_url'); ?>/images/dashpallete/class_routine.png) no-repeat center;"></div>
                                                                            <div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>ตารางเรียน</i></p></div>
                                                                    </div>

                                                                    <div class="col-sm-12"  style="margin-top:30px;">
                                                                    <p>
                                                                            สร้างตารางเรียนของทุกห้องเรียน ทุกชั้นปีได้ง่าย และเผยแพร่ให้ทุกคนที่ต้องการดู นอกจากนั้นยังแก้ไขปรับเปลียนได้ทุกเมื่อที่คุณต้องการ และยังแจ้งเตือนไปยังผู้ที่เกี่ยวข้องกับตารางเรียนนั้นด้วยง. 
                                                                    </p>
                                                                    <h3 class="f2" style="font-size:20px;padding: 16px 5px;">ตารางเรียนที่ยืดหยุ่นและง่ายสำหรับการปรับปรุง</h3>
                                                                    </div>									
                                                            </div>								
                                                    </div>	
						</li>	
                                                <li>
                                                    <div class="col-sm-12" style="height:300px;width:1000px;">
                                                            <div class="col-sm-1" style="text-align:-moz-center;"></div>
                                                            <div class="col-sm-3" style="text-align:-moz-center;">
                                                                <div class="thumbnail-item">
                                                                    <a href="#"><img class="img-responsive" src="<?php bloginfo('template_url'); ?>/images/dashpallete/view/syllabus.png" style="width:170px;" /></a>                                                                        
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-1" style="text-align:-moz-center;"></div>
                                                            <div class="col-sm-7">
                                                                    <div class="col-sm-12">
                                                                            <div class="col-sm-4" style="width: 60px;	height: 60px;	border-radius: 30px;	-webkit-border-radius: 30px;	-moz-border-radius: 30px;	background: #64B846 url(<?php bloginfo('template_url'); ?>/images/dashpallete/sylabus.png) no-repeat center;"></div>
                                                                            <div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>หลักสูตร</i></p></div>
                                                                    </div>

                                                                    <div class="col-sm-12"  style="margin-top:30px;">
                                                                    <p>
                                                                            เปิดให้โรงเรียนอัพโหลดหลักสูตรประจำปีเข้าสู่ระบบ ช่วยให้คุณสามารถสร้างการเรียน การสอบ หรือหลักสูตรที่ชาญฉลาด และยังสามารถติดตามสถาณะหลักสูตรของแต่ละชั้นเรียนได้อีกด้วย.  
                                                                    </p>
                                                                    <h3 class="f2" style="font-size:20px;padding: 16px 5px;">อัพโหลดหลักสูตรประจำปีของโรงเรียนคุณ</h3>
                                                                    </div>									
                                                            </div>								
                                                    </div>	
						</li>	
                                                <li>
                                                    <div class="col-sm-12" style="height:300px;width:1000px;">
                                                            <div class="col-sm-1" style="text-align:-moz-center;"></div>
                                                            <div class="col-sm-3" style="text-align:-moz-center;">                                                                
                                                                <div class="thumbnail-item">
                                                                    <a href="#"><img class="img-responsive" src="<?php bloginfo('template_url'); ?>/images/dashpallete/view/notice.png" style="width:170px;" /></a>                                                                        
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-1" style="text-align:-moz-center;"></div>
                                                            <div class="col-sm-7">
                                                                    <div class="col-sm-12">
                                                                            <div class="col-sm-4" style="width: 60px;	height: 60px;	border-radius: 30px;	-webkit-border-radius: 30px;	-moz-border-radius: 30px;	background: #64B846 url(<?php bloginfo('template_url'); ?>/images/dashpallete/notice.png) no-repeat center;"></div>
                                                                            <div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>การประกาศ</i></p></div>
                                                                    </div>

                                                                    <div class="col-sm-12"  style="margin-top:30px;">
                                                                    <p>
                                                                            แน่ใจหรือว่าทุกคนได้รับประกาศฉบับด่วนของคุณ? ไม่ต้องห่วง! การประกาศของเราแจ้งเตือนทันทีตามเวลาจริง สามารถโต้ตอบได้ และไม่จำกัดจำนวนแค่ 160 ตัวอักษร คุณสามารถแจ้งประกาศตรงถึงนักเรียนแค่ห้องเดียว หรือทั้งโรงเรียนก็ได้.  
                                                                    </p>
                                                                    <h3 class="f2" style="font-size:20px;padding: 16px 5px;">มั่นใจได้เลยว่าทุกคนได้รับประกาศที่สำคัญทุกฉบับ</h3>
                                                                    </div>									
                                                            </div>								
                                                    </div>	
						</li>	
                                                <li>
                                                    <div class="col-sm-12" style="height:300px;width:1000px;">
                                                            <div class="col-sm-1" style="text-align:-moz-center;"></div>
                                                            <div class="col-sm-3" style="text-align:-moz-center;">                                                                
                                                                <div class="thumbnail-item">
                                                                    <a href="#"><img class="img-responsive" src="<?php bloginfo('template_url'); ?>/images/dashpallete/view/leave.png" style="width:170px;" /></a>                                                                        
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-1" style="text-align:-moz-center;"></div>
                                                            <div class="col-sm-7">
                                                                    <div class="col-sm-12">
                                                                            <div class="col-sm-4" style="width: 60px;	height: 60px;	border-radius: 30px;	-webkit-border-radius: 30px;	-moz-border-radius: 30px;	background: #64B846 url(<?php bloginfo('template_url'); ?>/images/dashpallete/leave_application.png) no-repeat center;"></div>
                                                                            <div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>การลา</i></p></div>
                                                                    </div>

                                                                    <div class="col-sm-12"  style="margin-top:30px;">
                                                                    <p>
                                                                            การลาที่ง่ายและยืดหยุ่นผ่านทางออนไลน์ การอณุมัติพร้อมระบบแจ้งเตือน ทำให้คุณไม่ต้องจัดการจดหมายการลาและการตอบรับที่ยุ่งยากเหมือนเมื่อก่อน โดยการขอลานั้นผูกไปกับการเช็คชื่อในห้องเรียนซึ่งมันสะดวกขึ้นมาก. 
                                                                    </p>
                                                                    <h3 class="f2" style="font-size:20px;padding: 16px 5px;">การขอลาและการอนุมัติที่ง่ายขึ้น</h3>
                                                                    </div>									
                                                            </div>								
                                                    </div>	
						</li>
                                                	
					</ul>
				</div>
				<div id="carousel" class="flexslider">
				    <ul class="slides">
						<li>
						<img src="<?php bloginfo('template_url'); ?>/images/dashpallete/thumb/Attendance.png" style="width:140px;padding:5px;" />
						</li>
						<li>
						<img src="<?php bloginfo('template_url'); ?>/images/dashpallete/thumb/academic_calendar.png" style="width:140px;padding:5px;"  />
						</li>
						<li>
						<img src="<?php bloginfo('template_url'); ?>/images/dashpallete/thumb/Lesson_Plan.png" style="width:140px;padding:5px;"  />
						</li>
						<li>
						<img src="<?php bloginfo('template_url'); ?>/images/dashpallete/thumb/Teachers_Routine.png" style="width:140px;padding:5px;"  />
						</li>
						<li>
							<img src="<?php bloginfo('template_url'); ?>/images/dashpallete/thumb/Homework.png" style="width:140px;padding:5px;"  />
						</li>
						<li>
							<img src="<?php bloginfo('template_url'); ?>/images/dashpallete/thumb/Fees.png" style="width:140px;padding:5px;"  />
						</li>						
						<li>
							<img src="<?php bloginfo('template_url'); ?>/images/dashpallete/thumb/Forms.png" style="width:140px;padding:5px;"  />
						</li>
						<li>
							<img src="<?php bloginfo('template_url'); ?>/images/dashpallete/thumb/Meeting_Request.png" style="width:140px;padding:5px;"  />
						</li>
						<li>
							<img src="<?php bloginfo('template_url'); ?>/images/dashpallete/thumb/Report_Card.png" style="width:140px;padding:5px;"  />
						</li>
						<li>
							<img src="<?php bloginfo('template_url'); ?>/images/dashpallete/thumb/Event.png" style="width:140px;padding:5px;"  />
						</li>
                                                <li>
							<img src="<?php bloginfo('template_url'); ?>/images/dashpallete/thumb/Transport.png" style="width:140px;padding:5px;"  />
						</li>
                                                <li>
							<img src="<?php bloginfo('template_url'); ?>/images/dashpallete/thumb/Routine.png" style="width:140px;padding:5px;"  />
						</li>
                                                <li>
							<img src="<?php bloginfo('template_url'); ?>/images/dashpallete/thumb/Syllabus.png" style="width:140px;padding:5px;"  />
						</li>
                                                <li>
							<img src="<?php bloginfo('template_url'); ?>/images/dashpallete/thumb/Notice.png" style="width:140px;padding:5px;"  />
						</li>
                                                <li>
							<img src="<?php bloginfo('template_url'); ?>/images/dashpallete/thumb/Leave.png" style="width:140px;padding:5px;"  />
						</li>
                                               
				    </ul>
				</div>
			</div>
		</div>

	</main><!-- .site-main -->

	

</div><!-- .content-area -->

<?php get_footer('inner'); ?>
