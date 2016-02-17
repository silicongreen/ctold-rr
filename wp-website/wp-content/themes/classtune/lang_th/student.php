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
Template Name: student-th
*/
get_header(); ?>

<div class="container"><div class="container">
	<div class="wrapper">
		<div id="startWrap">
			
		</div>

		<div id="beforeWrap" style="background: transparent url(<?php bloginfo('template_url'); ?>/images/cover/student-page.png) no-repeat top left;background-size:cover;">
			<!--img src="<?php bloginfo('template_url'); ?>/images/test/CLASSTUNE-COVER.png" alt="" title="" width="100%" /<li style="color:#64B846;">|</li>-->
			<h2 class="f2" style="float: right;margin-right: 120px;margin-top: 150px;width: 300px;clear:both;"><i>ระบบที่จะทำให้น้องๆมีพัฒนาการการเรียนรู้ที่ดีมากขึ้น!</i></h2>
			<div class="postlist-tab2">
				<div style=" position: relative;top: 48px;z-index: 1;">
					<a href="<?php echo get_site_url().'/'.$lang; ?>/signup<?php echo "-".$lang;?>?user_type=2" style="background-color: #64b846;color: #fff;font-size: 20px;padding: 20px 40px;text-decoration: none;border-radius:5px;	-moz-border-radius:5px;	-webkit-border-radius:5px;border:1px solid #fff;box-shadow: 0 4px 2px -2px gray;">
					สร้างบัญชีของน้องๆได้ฟรี</a>
				</div>
			</div>
		</div>

		<div id="cronWrap" style="background-color:#F4FAFA;background-image:none;top:500px;height:800px;">
			<div style="border: 0 solid #ccc;left: 50%;margin: 0 0 0 -499px;position: absolute;width: 1000px;padding:55px 10px 0;">
				<h2 class="f2" style="text-align:center;"><i>เครืองมือสำคัญที่จะทำให้นักเรียนสามารถรักษาระดับการเรียนได้อย่างง่ายดาย!</i></h2><br>
				<p style="font-size:16px;"><b>ClassTune</b> มอบเครื่องมือทุกอย่างที่น้องๆต้องการ สำหรับจัดการเรื่องการเรียน และมีความสุขกับกิจกรรมที่โรงเรียน.</p>
				<p style="font-size:16px;"><b>ClassTune</b> ถูกออกแบบมาสำหรับเชื่อมต่อ และโต้ตอบระหว่างน้องๆกับครู แม้กระทั้งเมื่อน้องๆกลับจากโรงเรียนผู้ปกครองยังเห็นถึงประสิทธิภาพในการเรียนที่มากขึ้นของน้องๆ . </p><br>
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
										น้องๆสามารถดูรายการเช็คชื่อย้อนหลังได้ ทั้งแบบประจำสัปดาห์ ประจำเดือน และประจำปี นอกจากนั้นยังดูวันที่น้องมาสายได้อีกด้วย.
									</p>
                                                                        <h3 class="f2" style="font-size:20px;padding: 16px 5px;">ดูรายการเข้าเรียนได้ง่ายๆ  </h3>
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
										น้องๆสามารถดูปฏิทินการศึกษาได้ง่ายๆผ่านช่องทางออนไลน์ ซึ่งจะช่วยให้ครอบครัวของน้องๆสามารถวางแผนการพักผ่อนในช่วงสุดสัปดาห์ล่วงหน้าได้สะดวกขึ้น นอกจากนั้นยังไม่พลาดกิจกรรมสนุกๆที่โรงเรียนจะจัดทุกกิจกรรม. 
									</p>
                                                                        <h3 class="f2" style="font-size:20px;padding: 16px 5px;">ทุกการอัพเดทที่สำคัญอยู่แค่ปลายนิ้วน้องๆเท่านั้นเอง</h3>
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
                                                                            น้องๆสามารถเข้าไปดูแผนการเรียนเพื่อเตรียมพร้อมในการเรียนได้ล่วงหน้า ซึ่งผลการเรียนที่นั้นมีผลจากการเตรียมตัวที่ดีเช่นกัน.
									</p>
                                                                        <h3 class="f2" style="font-size:20px;padding: 16px 5px;">ช่วยน้องๆเตรียมตัวล่วงหน้าเกี่ยวกับบทเรียน! </h3>
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
										น้องๆสามารถเรียกดูการบ้านเฉพาะรายวิชา และไม่พลาดที่จะส่งการบ้านทันตามกำหนด นอกจากนั้นน้องๆยังตั้งการแจ้งเตือนเมื่อมีการบ้านและสามารถกดยืนยันเมื่อทำการบ้านเสร็จแล้ว. 
									</p>
                                                                        <h3 class="f2" style="font-size:20px;padding: 16px 5px;">ค้นหาการบ้านที่ได้รับมอบหมายได้อย่างง่ายดาย</h3>
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
										น้องๆสามารถเข้าไปดูทุกเอกสารและแบบฟอร์มจากโรงเรียนผ่านสมาร์ทโฟนและเว็บไซต์ นอกจากนั้นยังกรอกแบบฟอร์มและกดส่งผ่านหน้านั้นได้เลย ที่สำคัญไม่ต้องเสียเวลาต่อแถวที่ห้องพักครูเพื่อรอรับเอกสารอีกด้วย.
									</p>
                                                                        <h3 class="f2" style="font-size:20px;padding: 16px 5px;">เรียกใช้เอกสารสำคัญได้ง่ายผ่านช่องทางออนไลน์.</h3>
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
										น้องๆและพ่อแม่สามารถดูผลสอบออนไลน์ได้เลย และยังมีระบบเก็บบันทึกไว้ดูภายหลังได้อีกด้วย.
									</p>
                                                                        <h3 class="f2" style="font-size:20px;padding: 16px 5px;">รู้ผลสอบอย่างรวดเร็ว ผ่านออนไลน์ </h3>
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
                                                                            จากนี้เป็นต้นไปน้องๆไม่พลาดกิจกรรมเด็ดๆอีกแล้ว ไม่ว่าจะเป็นวันกีฬาสี วันเลือกชมรม หรือวันทัศนศึกษา ทั้งหมดนี้ให้ ClassTune คอยอัพเดทให้น้องๆเอง. 
                                                                    </p>
                                                                    <h3 class="f2" style="font-size:20px;padding: 16px 5px;">อัพเดททุกกิจรรมของโรงเรียน! </h3>
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
                                                                            ช่วยให้น้องๆสามารถดูเวลาการรับส่งของรถโรงเรียน ตรวจดูซิว่ารถโรงเรียนมาตรงเวลาหรือปล่าว.
                                                                    </p>
                                                                    <h3 class="f2" style="font-size:20px;padding: 16px 5px;">รู้ทุกตารางรับส่งของรถโรงเรียน</h3>
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
                                                                           ตารางเรียนประจำวันที่จะทำให้น้องๆไม่ลืมการบ้าน หนังสือ หรือแม้กระทั่ง วัศดุอุปกรณ์สำหรับงานประดิษฐ์ สำหรับวันพรุ่งนี้. 
                                                                    </p>
                                                                    <h3 class="f2" style="font-size:20px;padding: 16px 5px;">ช่วยน้องๆเตรียมตัวสำหรับการเรียนพรุ่งนี้ </h3>
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
                                                                            น้องๆสามารถดูรายวิชาและการสอบตามหลักสูตรผ่าน ClassTune ได้ง่ายๆเพียงแค่คลิกเดียว ช่วยให้น้องๆเตรียมความพร้อมได้ด้วยตัวเองตามความเหมาะสม. 
                                                                    </p>
                                                                    <h3 class="f2" style="font-size:20px;padding: 16px 5px;">หลักสูตรของน้องๆอยู่นี่แล้ว!</h3>
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
                                                                            พลาดข่าวประกาศที่สำคัญจากโรงเรียน! ไม่ต้องห่วง! บอร์ดปักประกาศของโรงเรียนอยู่ใน ClassTune แล้ว.
                                                                    </p>
                                                                    <h3 class="f2" style="font-size:20px;padding: 16px 5px;">ดูบอร์ดประกาศของโรงเรียนได้ทุกเมื่อ!</h3>
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
							<img src="<?php bloginfo('template_url'); ?>/images/dashpallete/thumb/Forms.png" style="width:140px;padding:5px;"  />
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
                        
                                               
				    </ul>
				</div>
			</div>
		</div>

<?php get_footer('inner'); ?>
