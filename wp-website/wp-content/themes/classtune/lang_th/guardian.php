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
Template Name: guardian-th
*/
get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<div class="container"><div class="container">
	<div class="wrapper">
		<div id="startWrap">
			
		</div>

		<div id="beforeWrap" style="background: transparent url(<?php bloginfo('template_url'); ?>/images/cover/parent-page.png) no-repeat top left;background-size:cover;">
			<!--img src="<?php bloginfo('template_url'); ?>/images/test/CLASSTUNE-COVER.png" alt="" title="" width="100%" /<li style="color:#64B846;">|</li>-->
			<h2 class="f2" style="float: right;margin-right: 170px;margin-top: 200px;width: 600px;"><i>ผู้ช่วยในการเฝ้าดูความคืบหน้าลูกๆของคุณ ไม่ว่าเมื่อไหร่ หรือที่ไหหนก็ได้ที่คุณต้องการ!</i></h2>
			<div class="postlist-tab2">
				<div style=" position: relative;top: 48px;z-index: 1;">
					<a href="<?php echo get_site_url().'/'.$lang; ?>/signup<?php echo "-".$lang;?>?user_type=4" style="background-color: #64b846;color: #fff;font-size: 20px;padding: 20px 40px;text-decoration: none;border-radius:5px;	-moz-border-radius:5px;	-webkit-border-radius:5px;border:1px solid #fff;box-shadow: 0 4px 2px -2px gray;">
					สมัครสมาชิก ฟรี</a>
				</div>
			</div>
		</div>

		<div id="cronWrap" style="background-color:#F4FAFA;background-image:none;top:500px;height:800px;">
			<div style="border: 0 solid #ccc;left: 50%;margin: 0 0 0 -499px;position: absolute;width: 1000px;padding:55px 10px 0;">
				<h2 class="f2" style="text-align:center;"><i>พ่อแม่อยู่เคียงข้างและสนับสนุนลูกๆเสมอ!</i></h2><br>
				<p style="font-size:16px;"><b>ClassTune</b>  ไม่เพียงแค่ทำให้คุณมองเห็นได้มากขึ้น หรือเข้าใจวิถีการเรียนรู้ของเด็ก แต่ยังมีบางสิ่งที่ช่วยคุณในระหว่างนั้นด้วย เช่นขยายการเข้าถึง.</p><br>
				<div id="slider"  class="flexslider" style="margin-top:30px;width:1000px;">
					<ul class="slides">
						<li>
							<div class="col-sm-12" style="height:300px;width:1000px;">								
								<div class="col-sm-1" style="text-align:-moz-center;"></div>
                                                                <div class="col-sm-3" style="text-align:-moz-center;">
                                                                    <div class="thumbnail-item">
                                                                        <a href="#"><img class="img-responsive" src="<?php bloginfo('template_url'); ?>/images/dashpallete/view/attendance.png" style="width:170px;" /></a>                                                                        
                                                                    </div>
								</div>
								<div class="col-sm-1" style="text-align:-moz-center;"></div>
								<div class="col-sm-7">
									<div class="col-sm-12">
										<div class="col-sm-4" style="width: 60px;	height: 60px;	border-radius: 30px;	-webkit-border-radius: 30px;	-moz-border-radius: 30px;	background: #64B846 url(<?php bloginfo('template_url'); ?>/images/dashpallete/attandance.png) no-repeat center;"></div>
										<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>การเช็คชื่อ</i></p></div>
									</div>
									
									<div class="col-sm-12"  style="margin-top:30px;">
									<p>
										ได้รับการแจ้งเตือนในทันที ที่ลูกๆของคุณได้รับการเช็คชื่อ และคุณยังทราบรูปแบบต่างๆของรายงานที่เกี่ยวข้องกับการเช็คชื่อ.
									</p>
                                                                        <h3 class="f2" style="font-size:20px;padding: 16px 5px;">รายการเช็คชื่อลูกๆของคุณอยู่ที่นี่!</h3>
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
										ช่วยให้คุณพ่อคุณแม่วางแผนการท่องเที่ยวหรือกิจกรรมสำคัญในวันหยุดได้สะดวก และไม่พลาดการอัพเดทที่สำคัญเกี่ยวกับการศึกษาของลูกๆคุณ!
									</p>
                                                                        <h3 class="f2" style="font-size:20px;padding: 16px 5px;">ทำให้การวางแผนการท่องเที่ยวในวันหยุดกลายเป็นเรื่องง่าย!</h3>
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
									 คุณพ่อคุณแม่สามารถตรวจดูแผนการเรียนประจำสัปดาห์ ประจำเดือน หรือประจำปีได้ทุกเมื่อ สำหรับช่วยในการเตรียมพร้อมลูกๆของคุณล่วงหน้าได้อย่างดี.
									</p>
                                                                        <h3 class="f2" style="font-size:20px;padding: 16px 5px;">เตรียมพร้อมลูกๆของคุณล่วงหน้าเพื่อผลลัพท์ที่สุดยอด</h3>
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
										ช่วยให้คุณพ่อคุณแม่รับรู้ว่าลูกๆของคุณวันนี้ มีการบ้านอะไรบ้าง และมีกำหนดส่งเมื่อไหร่ ช่วยลดความตึงเครียดและได้รับการแจ้งเตือนจากลูกของคุณทันทีที่การบ้านเสร็จ.
									</p>
                                                                        <h3 class="f2" style="font-size:20px;padding: 16px 5px;">อย่าลืมติดตามว่าวันนี้ลูกของคุณมีการบ้านอะไรบ้าง! </h3>
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
										<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>ค่าธรรมเนียม</i></p></div>
									</div>
									
									<div class="col-sm-12"  style="margin-top:30px;">
									<p>
										ช่วยให้คุณพ่อคุณแม่ไม่ต้องกังวลเกี่ยวกับค่าธรรมเนียมของโรงเรียน ไม่ต้องเข้าธนาคารอีกแล้ว คุณสามารถจ่ายค่าธรรมเนียมผ่านบัตรเครดิตหรือช่องทางชำระเงินของสมาร์ทโฟนต่างๆ.
									</p>
                                                                        <h3 class="f2" style="font-size:20px;padding: 16px 5px;">ตรวจสอบกำหนดชำระค่าธรรมเนียมของโรงเรียน และตังค่าการแจ้งเตือน! </h3>
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
										คุณพ่อคุณแม่สามารถใช้งานทุกเอกสารของโรงเรียนได้อย่างง่ายดาย และสะดวกสบายมากขึ้นกับการไม่ต้องไปเข้าคิวเพื่อรอรับเอกสารที่โรงเรียน. 
									</p>
                                                                        <h3 class="f2" style="font-size:20px;padding: 16px 5px;">คุณพ่อคุณแม่สามารถดาวน์โหลดเอกสารทุกเมื่อคุณต้องการ</h3>
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
										<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>การนัดพบ</i></p></div>
									</div>
									
									<div class="col-sm-12"  style="margin-top:30px;">
									<p>
										เพียงแค่ถามคุณครูเกี่ยวกับการนัดพบ โดยคุณพ่อคุณแม่สามารถแจ้งนัดผ่านสมาร์ทโฟนหรือคอมพิวเตอร์ และตั้งแจ้งเตือนเมื่อการนัดได้รับการอนุมัติ. 
									</p>
                                                                        <h3 class="f2" style="font-size:20px;padding: 16px 5px;">นัดพบกับคุณครูได้ง่ายๆเพียงแค่คลิกเท่านั้น!</h3>
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
										คุณพ่อคุณแม่สามารถดูผลสอบของลูกๆทางออนไลน์ และยังสามารถดูกราฟแสดงความคืบหน้าของลูกๆคุณ และสามารถกดรับทราบส่งกลับไปที่โรงเรียนได้ทันที. 
									</p>
                                                                        <h3 class="f2" style="font-size:20px;padding: 16px 5px;">ตรวจสอบความคืบหน้าลูกๆของคุณ  </h3>
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
                                                                            ทำให้คุณพ่อคุณแม่ไม่พลาดกิจกรรมทุกอย่าง ไม่ว่าจะเป็นวันกีฬาสี วันเลือกชมรม หรือวันทัศนศึกษา ไม่ว่าจะเป็นการอัพเดทเกี่ยวกับวัน เวลา หรือสถานที่จัดงาน โดยรับการแจ้งเตือนที่จำเป็นเกี่ยวกับกิจกรรมต่างๆ.
                                                                    </p>
                                                                    <h3 class="f2" style="font-size:20px;padding: 16px 5px;">ตรวจดูรายละเอียดกิจกรรมต่างๆ</h3>
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
                                                                           คุณพ่อคุณแม่สามารถตรวจสอบจุดรับส่ง เวลาลงรถของลูกๆจากรถโรงเรียน ไม่ต้องกังวลเกี่ยวกับการรับส่งว่าจะตรงเวลาหรือไม่ โดยได้รับการแจ้งเตือนล่วงหน้า.
                                                                    </p>
                                                                    <h3 class="f2" style="font-size:20px;padding: 16px 5px;">ไม่ต้องเครียดเกี่ยวกับรถโรงเรียน!</h3>
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
                                                                            ตรวจดูตารางเรียนของลูกๆคุณ เพลิดเพลินกับความสะดวกและได้รับการอัพเดททันที ที่มีการเปลี่ยนแปลง. 
                                                                    </p>
                                                                    <h3 class="f2" style="font-size:20px;padding: 16px 5px;">การเปลี่ยนแปลงใน ตารางเรียน- ได้รับ การแจ้งเตือน ! </h3>
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
                                                                            คุณพ่อคุณแม่สามารถเรียกดูรายวิชาและการสอบตามหลักสูตรของลูกๆคุณได้ง่ายๆเพียงคลิกเดียว สามารถเตรียมพร้อมลูกๆของคุณสำหรับการสอบด้วยความมั่นใจ.
                                                                    </p>
                                                                    <h3 class="f2" style="font-size:20px;padding: 16px 5px;">ดูหลักสูตรการเรียนได้ง่ายๆผ่านออนไลน์</h3>
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
                                                                            การประกาศแบบเรียลไทม์และสามารถโต้ตอบได้ ทำให้คุณพ่อคุณแม่ไม่พลาดประกาศฉบับสำคัญของโรงเรียนผ่าน ClassTune. 
                                                                    </p>
                                                                    <h3 class="f2" style="font-size:20px;padding: 16px 5px;">ไม่พลาดทุกประกาศฉบับสำคัญของโรงเรียน!</h3>
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
                                                                           การยื่นขอลาสำหรับลูกๆคุณนั้นง่ายมาก ทำได้ผ่านบัญชีของคุณ ตรวจสอบสถานะอนุมัติการลาและรอการแจ้งเตือนกลับ.  
                                                                    </p>
                                                                    <h3 class="f2" style="font-size:20px;padding: 16px 5px;">การขอลานั้นง่ายขึ้นมาก! </h3>
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
