<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */
/*
Template Name: home-th
*/
get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<div class="container">
				<div class="wrapper">
					<div id="startWrap">            
						<h2 class="f2" style="margin-top: 40px;"><i>สื่อสารระหว่าง นักเรียน ผู้ปกครอง และครู</i></h2>
						<div class="tx-pxcontentboxes-pi1">
							<div id="boxesBG">
								<div class="row1">
									<div id="box_107" class="box">
										<img title="" alt="" src="<?php bloginfo('template_url'); ?>/images/aboutus/1.png" width="100%">
										<div style="height:120px;backgound:#EFF2F3;">


											<a href="<?php echo get_site_url().'/'.$lang; ?>/admin-user<?php echo "-".$lang;?>" style="color:gray;text-decoration:none;"><h3>สำหรับผู้ดูแลระบบ</h3></a>

											<p style="color: gray;font-size: 14px;padding: 20px; text-align: left;">
												เป็นระบบกลางที่ตอบโจทย์ความต้องการพัฒนาด้านการศึกษา ในด้านต่างๆ และทำให้คุณครูมีเวลาเพิ่มขึ้นไม่ต้องจัดการงานเอกสารมากจนเกินไป.<br>
											</p>
											<p style="background: #DFE4E7 none repeat scroll 0 0;border-radius: 0px;bottom: 10px;padding: 8px 0;position: relative;width: 80px;left:10px;font-weight: bold;">
												<a href="<?php echo get_site_url().'/'.$lang; ?>/admin-user<?php echo "-".$lang;?>" style="color:#000;text-decoration:none;">เรียนรู้เพิ่มเติม</a>
											</p>

										</div>
									</div>
									<div id="box_105" class="box">
										<img title="" alt="" src="<?php bloginfo('template_url'); ?>/images/aboutus/2.png" width="100%">
										<div style="height:120px;backgound:#EFF2F3;">


											<a href="<?php echo get_site_url().'/'.$lang; ?>/guardian-user<?php echo "-".$lang;?>" style="color:gray;text-decoration:none;"><h3>สำหรับผู้ปกครอง</h3></a>

											<p style="color: gray;font-size: 14px;padding: 20px; text-align: left;">
												<b>ClassTune</b> ถูกออกแบบมาให้ใช้งานง่ายและสะดวกในการมีส่วนร่วมในกิจกรรมการเรียนของลูกๆคุณ.<br>
											</p>
											<p style="background: #DFE4E7 none repeat scroll 0 0;border-radius: 0px;bottom: 10px;padding: 8px 0;position: relative;width: 80px;left:10px;font-weight: bold;">
												<a href="<?php echo get_site_url().'/'.$lang; ?>/guardian-user<?php echo "-".$lang;?>" style="color:#000;text-decoration:none;">เรียนรู้เพิ่มเติม</a>
											</p>

										</div>
									</div>

									<div id="box_103" class="box lastinrow">
										<img title="" alt="" src="<?php bloginfo('template_url'); ?>/images/aboutus/3.png" width="100%">
										<div style="height:120px;backgound:#EFF2F3;">


											<a href="<?php echo get_site_url().'/'.$lang; ?>/teacher-user<?php echo "-".$lang;?>" style="color:gray;text-decoration:none;"><h3>สำหรับครู</h3></a>

											<p style="color: gray;font-size: 14px;padding: 20px; text-align: left;">
												<b>ClassTune</b> ถูกออกแบบมาสำหรับเพิ่มประสิทธิภาพการเรียนการสอนในห้องเรียนของคุณในรูปแบบดิจิตอล. 
											</p>
											<p style="background: #DFE4E7 none repeat scroll 0 0;border-radius: 0px;bottom: 10px;padding: 8px 0;position: relative;width: 80px;left:10px;font-weight: bold;">
												<a href="<?php echo get_site_url().'/'.$lang; ?>/teacher-user<?php echo "-".$lang;?>" style="color:#000;text-decoration:none;">เรียนรู้เพิ่มเติม</a>
											</p>

										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div id="beforeWrap">
						<div class="owl-carousel">
							<div class="item" style="background-image: url(<?php bloginfo('template_url'); ?>/images/cover/cover-1.png);">
								<div class="slider-inner">
									<div class="container" style="width:100%;">
										<div class="row">
											<div class="col-sm-12">
												<div class="carousel-content">
													<div class="col-md-12"  style="">					
														<div class="col-md-4"></div>
														<div class="col-md-8">				
															<div class="row">
																<div class="col-md-12">
																	<div class="col-md-2"></div>
																	<div class="col-md-8" style="text-align: left;">
																		<h2 class="f2" style="margin-top:130px;"><i>การบริหารโรงเรียนง่ายกว่าที่เคยเป็นมา...</i></h2>
																		<p style="margin-top:30px;font-size:16px;">ClassTune เสมือนสะพานเชือมระหว่างนักเรียน ผู้ปกครอง ครู พร้อมด้วยระบบจัดการที่ง่ายและรวดเร็วสำหรับผู้บริหารระบบ ที่จะเข้าถึงทุกส่วนงานได้ภายใต้ระบบงานระบบเดียว.</p>

																	</div>
																	<div class="col-md-2"></div>
																</div>
															</div>
															<div class="row">
																<div class="col-md-12">
																	<div class="postlist-tab3">
																		<ul>
																			<li>                                                    
																				<a href="<?php echo get_site_url().'/'.$lang; ?>/admin-user<?php echo "-".$lang;?>">ผู้ดูแลระบบ</a>
																				<a href="<?php echo get_site_url().'/'.$lang; ?>/student-user<?php echo "-".$lang;?>">นักเรียน</a>
																				<a href="<?php echo get_site_url().'/'.$lang; ?>/guardian-user<?php echo "-".$lang;?>" >ผู้ปกครอง</a>
																				<a href="<?php echo get_site_url().'/'.$lang; ?>/teacher-user<?php echo "-".$lang;?>" >ครู</a>
																			</li>
																		</ul>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div><!--/.item-->
							
							<!--div class="item bettlink" style="background-image: url(<?php bloginfo('template_url'); ?>/images/cover/bett-banner.jpg);cursor:pointer;">
								<div class="slider-inner">
									<div class="container" style="width:100%;">
										<div class="row">
											<div class="col-sm-12">
												<div class="carousel-content">
													
												</div>
											</div>
										</div>
									</div>
								</div>
							</div--><!--/.item-->				
						</div><!--/.owl-carousel-->      
						
					</div>

					<div id="cronWrap" style="background-color:#F4FAFA;background-image:none;height:800px;">
						<div style="border: 0 solid #ccc;left: 50%;margin: 0 0 0 -499px;position: absolute;width: 1000px;padding:55px 10px 0;">
							<h2 class="f2" style="text-align:center;"><i>เจ้าหน้าที่ธุรการ มีความสุขกับความง่ายในการเก็บสถิติต่างๆ!</i></h2><br>
							<p style="font-size:16px;"><b>ClassTune</b> ทำให้การติดตามเฝ้าดูและจัดการของเจ้าหน้าที่ธุรการทำได้ง่ายและมีประสิทธิภาพ โดยสามารถเข้าถึง จัดการ มีส่วนร่วมกับ คุณครู นักเรียน และผู้ปกครอง ทั้งหมดในโรงเรียนผ่านช่องทางเดียว ซึ่งจะสามารถสนับสนุนมาตรการในการปรับปรุงผลการเรียนรู้.</p><br>
							<div id="slider"  class="flexslider" style="margin-top:30px;width:1000px;">
								<ul class="slides">
									<li>
										<div class="col-sm-12" style="height:300px;width:1000px;">								
											
																			<div class="col-sm-4" style="text-align:-moz-center;">
																				<div class="thumbnail-item" style="background-color: transparent;">
																					<a href="#"><img class="img-responsive" src="<?php bloginfo('template_url'); ?>/slider/new/attendance.png" style="width:300px;" /></a>
																					<div class="tooltip">
																						<img src="<?php bloginfo('template_url'); ?>/slider/new/attendance.png" alt="" style="width:100%;" />
																					</div> 
																				</div>
											</div>
											<div class="col-sm-1" style="text-align:-moz-center;"></div>
											<div class="col-sm-7">
												<div class="col-sm-12">
													<div class="col-sm-4" style="width: 60px;	height: 60px;	border-radius: 30px;	-webkit-border-radius: 30px;	-moz-border-radius: 30px;	background: #64B846 url(<?php bloginfo('template_url'); ?>/images/dashpallete/attandance.png) no-repeat center;"></div>
													<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;color:#67B270;"><i>การเช็คชื่อ</i></p></div>
												</div>
												
												<div class="col-sm-12"  style="margin-top:30px;">
												<p>
																						ดูการเช็คชื่อทั้งหมดของโรงเรียนผ่านช่องทางเดียว ในทุกชั้นเรียนและทุกส่วน ช่วยให้ติดตามการเข้าเรียนได้ง่าย เรียกดูบันทึกย้อนหลังได้ทุกเมื่อที่ต้องการ ในภาพรวมทำให้คุณนับจำนวนนักเรียนที่ขาดเรียนทั้งโรงเรียนได้อย่างรวดเร็ว. 
												</p>
																					<h3 class="f2" style="font-size:20px;padding: 16px 5px;color:#67B270;">ดูการเช็คชื่อของทั้งโรงเรียนได้ง่ายผ่านช่องทางเดียว.</h3>
												</div>									
											</div>								
										</div>	
									</li>
									<li>
										<div class="col-sm-12" style="height:300px;width:1000px;">
											
											<div class="col-sm-4" style="text-align:-moz-center;">								
																				<div class="thumbnail-item" style="background-color: transparent;">
																					<a href="#"><img class="img-responsive" src="<?php bloginfo('template_url'); ?>/slider/new/academic_calender.png" style="width:300px;" /></a>
																					<div class="tooltip">
																						<img src="<?php bloginfo('template_url'); ?>/slider/new/academic_calender.png" alt="" style="width:100%;" />
																					</div> 
																				</div>
											</div>
											<div class="col-sm-1" style="text-align:-moz-center;"></div>
											<div class="col-sm-7">
												<div class="col-sm-12">
													<div class="col-sm-4" style="width: 60px;	height: 60px;	border-radius: 30px;	-webkit-border-radius: 30px;	-moz-border-radius: 30px;	background: #64B846 url(<?php bloginfo('template_url'); ?>/images/dashpallete/academic_calender.png) no-repeat center;"></div>
													<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;color:#4ECAF0;"><i>ปฏิทินการศึกษา</i></p></div>
												</div>
												
												<div class="col-sm-12"  style="margin-top:30px;">
												<p>
													อัพโหลดปฏิทินการศีกษาและตั้งค่าให้ผู้ที่มีส่วนเกี่ยวข้องมองเห็นได้ ช่วยให้ทุกคนวางแผนในอนาคตได้ง่าย โดยคุณสามารถอัพโหลดปฏิทินการศึกษาได้ตั้งแต่ต้นปี และสามารถแก้ไขได้อย่างยืดหยุ่นทุกเมื่อที่ต้องการ.
												</p>
																					<h3 class="f2" style="font-size:20px;padding: 16px 5px;color:#4ECAF0;">ทุก วันสำคัญ อยู่ที่ปลาย นิ้วของคุณ.</h3>
												</div>									
											</div>								
										</div>	
									</li>
									<li>
										<div class="col-sm-12" style="height:300px;width:1000px;">
											
											<div class="col-sm-4" style="text-align:-moz-center;">									
																					<div class="thumbnail-item" style="background-color: transparent;">
																							<a href="#"><img class="img-responsive" src="<?php bloginfo('template_url'); ?>/slider/new/lesson_plan.png" style="width:300px;" /></a>
																							<div class="tooltip">
																									<img src="<?php bloginfo('template_url'); ?>/slider/new/lesson_plan.png" alt="" style="width:100%;" />
																									
																							</div> 
																					</div>
											</div>
											<div class="col-sm-1" style="text-align:-moz-center;"></div>
											<div class="col-sm-7">
												<div class="col-sm-12">
													<div class="col-sm-4" style="width: 60px;	height: 60px;	border-radius: 30px;	-webkit-border-radius: 30px;	-moz-border-radius: 30px;	background: #64B846 url(<?php bloginfo('template_url'); ?>/images/dashpallete/lesson_paln-white.png) no-repeat center;"></div>
													<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;color:#AA7B7B;"><i>แผนการเรียน</i></p></div>
												</div>
												
												<div class="col-sm-12"  style="margin-top:30px;">
												<p>
													แผนการเรียนสามารถดูผ่านออนไลน์ได้ และแก้ไขได้ทุกเมื่อที่ต้องการ สะดวกในการติดตามแผนกาเรียนกับปฏิทินการศึกษา.      
												</p>
																					<h3 class="f2" style="font-size:20px;padding: 16px 5px;color:#AA7B7B;">สะดวกสบายในการปรับปรุงแผนการเรียนของโรงเรียน.</h3>
												</div>									
											</div>								
										</div>		
									</li>
									<li>
										<div class="col-sm-12" style="height:300px;width:1000px;">
											
											<div class="col-sm-4" style="text-align:-moz-center;">
																				<div class="thumbnail-item" style="background-color: transparent;">
																					<a href="#"><img class="img-responsive" src="<?php bloginfo('template_url'); ?>/slider/new/Routine.png" style="width:300px;" /></a>
																					<div class="tooltip">
																						<img src="<?php bloginfo('template_url'); ?>/slider/new/Routine.png" alt="" style="width:100%;" />
																					</div> 
																				</div>
											</div>
											<div class="col-sm-1" style="text-align:-moz-center;"></div>
											<div class="col-sm-7">
												<div class="col-sm-12">
													<div class="col-sm-4" style="width: 60px;	height: 60px;	border-radius: 30px;	-webkit-border-radius: 30px;	-moz-border-radius: 30px;	background: #64B846 url(<?php bloginfo('template_url'); ?>/images/dashpallete/trachers_routine.png) no-repeat center;"></div>
													<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;color:#D95FC4;"><i>ตารางสอนสำหรับครู</i></p></div>
												</div>
												
												<div class="col-sm-12"  style="margin-top:30px;">
												<p>
													สะดวกสบายในการอัพโหลดตารางสอนสำหรับครู ดูตารางสอนประจำวันและประจำสัปดาห์ของครูแต่ละคนได้. 
												</p>
																					<h3 class="f2" style="font-size:20px;padding: 16px 5px;color:#D95FC4;">อัปโหลด ประจำ ครู เพื่อความสะดวก ของพวกเขา.  </h3>
												</div>									
											</div>								
										</div>		
									</li>
									<li>
										<div class="col-sm-12" style="height:300px;width:1000px;">
											
											<div class="col-sm-4" style="text-align:-moz-center;">                                                                    
																				<div class="thumbnail-item" style="background-color: transparent;">
																					<a href="#"><img class="img-responsive" src="<?php bloginfo('template_url'); ?>/slider/new/Homework.png" style="width:300px;" /></a>
																					<div class="tooltip">
																						<img src="<?php bloginfo('template_url'); ?>/slider/new/Homework.png" alt="" style="width:100%;" />
																					</div> 
																				</div>
											</div>
											<div class="col-sm-1" style="text-align:-moz-center;"></div>
											<div class="col-sm-7">
												<div class="col-sm-12">
													<div class="col-sm-4" style="width: 60px;	height: 60px;	border-radius: 30px;	-webkit-border-radius: 30px;	-moz-border-radius: 30px;	background: #64B846 url(<?php bloginfo('template_url'); ?>/images/dashpallete/homework.png) no-repeat center;"></div>
													<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;color:#D994DB;"><i>การบ้าน</i></p></div>
												</div>
												
												<div class="col-sm-12"  style="margin-top:30px;">
												<p>
													ตรวจสอบ การบ้าน ของ นักเรียน ส่วน ชั้น หนึ่งหรือ ทั้งโรงเรียน ที่ แพลตฟอร์มเดียว. 
												</p>
																					<h3 class="f2" style="font-size:20px;padding: 16px 5px;color:#D994DB;">ความยืดหยุ่นในการ ตรวจสอบ การบ้าน ของ ทุกชั้น.</h3>
												</div>									
											</div>								
										</div>	
									</li>
									<li>
										<div class="col-sm-12" style="height:300px;width:1000px;">
											
											<div class="col-sm-4" style="text-align:-moz-center;">									
																				<div class="thumbnail-item" style="background-color: transparent;">
																					<a href="#"><img class="img-responsive" src="<?php bloginfo('template_url'); ?>/slider/new/fees.png" style="width:300px;" /></a>                                                                        
																				</div>
											</div>
											<div class="col-sm-1" style="text-align:-moz-center;"></div>
											<div class="col-sm-7">
												<div class="col-sm-12">
													<div class="col-sm-4" style="width: 60px;	height: 60px;	border-radius: 30px;	-webkit-border-radius: 30px;	-moz-border-radius: 30px;	background: #64B846 url(<?php bloginfo('template_url'); ?>/images/dashpallete/fees.png) no-repeat center;"></div>
													<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;color:#F0282F;"><i>ค่าเล่าเรียน</i></p></div>
												</div>
												
												<div class="col-sm-12"  style="margin-top:30px;">
												<p>
																						 ค่าธรรมเนียม กำหนดการและ พร้อมรับคำ แจ้งเตือน ค่าบริการ อัตโนมัติ สำหรับการชำระเงิน ที่ค้างอยู่ ไม่ต้องกังวล เกี่ยวกับการจัดการ การจัดเก็บ และ การสร้าง / การรักษา ใบเสร็จรับเงิน เครื่องมือที่ดี สำหรับการตรวจสอบ การชำระเงิน และการเก็บรักษา บัญชี โรงเรียน. 
												</p>
																					<h3 class="f2" style="font-size:20px;padding: 16px 5px;color:#F0282F;">ความยุ่งยากใน การเก็บ ค่าใช้จ่าย ฟรี.</h3>
												</div>									
											</div>								
										</div>
									</li>
									<li>
										<div class="col-sm-12" style="height:300px;width:1000px;">
											
											<div class="col-sm-4" style="text-align:-moz-center;">									
																				<div class="thumbnail-item" style="background-color: transparent;">
																					<a href="#"><img class="img-responsive" src="<?php bloginfo('template_url'); ?>/images/dashpallete/view/Forms.png" style="width:300px;" /></a>                                                                        
																				</div>
											</div>
											<div class="col-sm-1" style="text-align:-moz-center;"></div>
											<div class="col-sm-7">
												<div class="col-sm-12">
													<div class="col-sm-4" style="width: 60px;	height: 60px;	border-radius: 30px;	-webkit-border-radius: 30px;	-moz-border-radius: 30px;	background: #64B846 url(<?php bloginfo('template_url'); ?>/images/dashpallete/defult.png) no-repeat center;"></div>
													<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;color:#1E99D5;"><i>เอกสาร</i></p></div>
												</div>
												
												<div class="col-sm-12"  style="margin-top:30px;">
												<p>
													อัพโหลดเอกสารที่จำเป็นในการใช้งานสำหรับคนอื่นๆที่ต้องการให้สามารถดาวน์โหลดไปใช้ได้ ไม่ต้องต่อคิวรอรับเอกสารที่ห้องพักครู และมีประสิทธิภาพสูงในการบริหารจัดการ.
												</p>
																					<h3 class="f2" style="font-size:20px;padding: 16px 5px;color:#1E99D5;">เอกสารออนไลน์ ที่ง่ายสำหรับการใช้งาน.</h3>
												</div>									
											</div>								
										</div>	
									</li>
									<li>
										<div class="col-sm-12" style="height:300px;width:1000px;">
											
											<div class="col-sm-4" style="text-align:-moz-center;">                                                                    
																				<div class="thumbnail-item" style="background-color: transparent;">
																					<a href="#"><img class="img-responsive" src="<?php bloginfo('template_url'); ?>/slider/new/meeting_request.png" style="width:300px;" /></a>                                                                        
																				</div>
											</div>
											<div class="col-sm-1" style="text-align:-moz-center;"></div>
											<div class="col-sm-7">
												<div class="col-sm-12">
													<div class="col-sm-4" style="width: 60px;	height: 60px;	border-radius: 30px;	-webkit-border-radius: 30px;	-moz-border-radius: 30px;	background: #64B846 url(<?php bloginfo('template_url'); ?>/images/dashpallete/meeting_request.png) no-repeat center;"></div>
													<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;color:#73D8BA;"><i>การขอนัดพบ</i></p></div>
												</div>
												
												<div class="col-sm-12"  style="margin-top:30px;">
												<p>
													ความสำคัญคือมันเป็นวิธีการที่จะพบกับครูทุกคนและผู้ปกครอง! ทำให้การนัดประชุมกับผู้ปกครองเป็นเรื่องง่าย แม้แต่การเปลี่ยนนัดอย่างกระทันหัน ซึ่งมีระบบแจ้งเตือนพวกเค้าอย่างรวดเร็ว. 
												</p>
																					<h3 class="f2" style="font-size:20px;padding: 16px 5px;color:#73D8BA;">เรียกว่าการนัดทำได้ง่ายมากในตอนนี้! </h3>
												</div>									
											</div>								
										</div>	
									</li>
									<li>
										<div class="col-sm-12" style="height:300px;width:1000px;">
											
											<div class="col-sm-4" style="text-align:-moz-center;">									
																				<div class="thumbnail-item" style="background-color: transparent;">
																					<a href="#"><img class="img-responsive" src="<?php bloginfo('template_url'); ?>/slider/new/report_card.png" style="width:300px;" /></a>                                                                        
																				</div>
											</div>
											<div class="col-sm-1" style="text-align:-moz-center;"></div>
											<div class="col-sm-7">
												<div class="col-sm-12">
													<div class="col-sm-4" style="width: 60px;	height: 60px;	border-radius: 30px;	-webkit-border-radius: 30px;	-moz-border-radius: 30px;	background: #64B846 url(<?php bloginfo('template_url'); ?>/images/dashpallete/report_card.png) no-repeat center;"></div>
													<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;color:#8C93B5;"><i>ผลสอบ</i></p></div>
												</div>
												
												<div class="col-sm-12"  style="margin-top:30px;">
												<p>
													สอบประจำชั้น, ผลสอบ และ รายงานโปรเจค ที่สามารถใช้งานได้ในรูปแบบดิจิตอล คุณสามารถสร้าง ปรับปรุง พัฒนา และกระจายผลสอบได้ง่ายอย่างไม่เคยมาก่อน นอกจากนั้นยังบันทึกไว้ในระบบได้ตลอดไป.
												</p>
																					<h3 class="f2" style="font-size:20px;padding: 16px 5px;color:#8C93B5;">ผลสอบออนไลน์ </h3>
												</div>									
											</div>								
										</div>	
									</li>
									<li>
																<div class="col-sm-12" style="height:300px;width:1000px;">
																		
																		<div class="col-sm-4" style="text-align:-moz-center;">                                                                
																			<div class="thumbnail-item" style="background-color: transparent;">
																				<a href="#"><img class="img-responsive" src="<?php bloginfo('template_url'); ?>/slider/new/events.png" style="width:300px;" /></a>                                                                        
																			</div>
																		</div>
																		<div class="col-sm-1" style="text-align:-moz-center;"></div>
																		<div class="col-sm-7">
																				<div class="col-sm-12">
																						<div class="col-sm-4" style="width: 60px;	height: 60px;	border-radius: 30px;	-webkit-border-radius: 30px;	-moz-border-radius: 30px;	background: #64B846 url(<?php bloginfo('template_url'); ?>/images/dashpallete/event.png) no-repeat center;"></div>
																						<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;color:#BED877;"><i>กิจกรรม</i></p></div>
																				</div>

																				<div class="col-sm-12"  style="margin-top:30px;">
																				<p>
																						มันใจว่าข้อมูลทุกกิจกรรมที่กำลังจะเกิดขึ้น จะส่งถึงนักเรียน ผู้ปกครอง และครูทุกคนอย่างแน่นอน โดยคุณสามารถสร้างและอัพเดทกิจกรรมได้ตลอดเวลา และยังทำให้คุณทราบว่ามีนักเรียน ผู้ปกครอง และครู กี่คนที่จะเข้าร่วมกิจกรรม.  
																				</p>
																				<h3 class="f2" style="font-size:20px;padding: 16px 5px;color:#BED877;">ไม่พลาดทุกกิจกรรมที่กำลังจะเกิดผ่านแพลตฟอร์มเดียว!</h3>
																				</div>									
																		</div>								
																</div>	
									</li>	
															<li>
																<div class="col-sm-12" style="height:300px;width:1000px;">
																		
																		<div class="col-sm-4" style="text-align:-moz-center;">
																			<div class="thumbnail-item" style="background-color: transparent;">
																				<a href="#"><img class="img-responsive" src="<?php bloginfo('template_url'); ?>/slider/new/transport.png" style="width:300px;" /></a>                                                                        
																			</div>
																		</div>
																		<div class="col-sm-1" style="text-align:-moz-center;"></div>
																		<div class="col-sm-7">
																				<div class="col-sm-12">
																						<div class="col-sm-4" style="width: 60px;	height: 60px;	border-radius: 30px;	-webkit-border-radius: 30px;	-moz-border-radius: 30px;	background: #64B846 url(<?php bloginfo('template_url'); ?>/images/dashpallete/transport.png) no-repeat center;"></div>
																						<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;color:#F9B047;"><i>การรับส่ง</i></p></div>
																				</div>

																				<div class="col-sm-12"  style="margin-top:30px;">
																				<p>
																						 วางแผนการรับส่งนักเรียนและอัพโหลดไว้ที่นี่ และมั่นใจว่าผู้ปกครองจะได้รับการแจ้งเตือนภายในไม่กี่นาที เมื่อมีการเปลี่ยนแปลงแผนการรับส่ง. 
																				</p>
																				<h3 class="f2" style="font-size:20px;padding: 16px 5px;color:#F9B047;">ติดตามตารางการรับส่งลูกๆของคุณอย่างไกล้ชิด</h3>
																				</div>									
																		</div>								
																</div>	
									</li>	
															<li>
																<div class="col-sm-12" style="height:300px;width:1000px;">
																		
																		<div class="col-sm-4" style="text-align:-moz-center;">
																			<div class="thumbnail-item" style="background-color: transparent;">
																				<a href="#"><img class="img-responsive" src="<?php bloginfo('template_url'); ?>/slider/new/sRoutine.png" style="width:300px;" /></a>                                                                        
																			</div>
																		</div>
																		<div class="col-sm-1" style="text-align:-moz-center;"></div>
																		<div class="col-sm-7">
																				<div class="col-sm-12">
																						<div class="col-sm-4" style="width: 60px;	height: 60px;	border-radius: 30px;	-webkit-border-radius: 30px;	-moz-border-radius: 30px;	background: #64B846 url(<?php bloginfo('template_url'); ?>/images/dashpallete/class_routine.png) no-repeat center;"></div>
																						<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;color:#AF88B4;"><i>ตารางเรียน</i></p></div>
																				</div>

																				<div class="col-sm-12"  style="margin-top:30px;">
																				<p>
																						สร้างตารางเรียนของทุกห้องเรียน ทุกชั้นปีได้ง่าย และเผยแพร่ให้ทุกคนที่ต้องการดู นอกจากนั้นยังแก้ไขปรับเปลียนได้ทุกเมื่อที่คุณต้องการ และยังแจ้งเตือนไปยังผู้ที่เกี่ยวข้องกับตารางเรียนนั้นด้วยง. 
																				</p>
																				<h3 class="f2" style="font-size:20px;padding: 16px 5px;color:#AF88B4;">ตารางเรียนที่ยืดหยุ่นและง่ายสำหรับการปรับปรุง</h3>
																				</div>									
																		</div>								
																</div>	
									</li>	
															<li>
																<div class="col-sm-12" style="height:300px;width:1000px;">
																	   
																		<div class="col-sm-4" style="text-align:-moz-center;">
																			<div class="thumbnail-item" style="background-color: transparent;">
																				<a href="#"><img class="img-responsive" src="<?php bloginfo('template_url'); ?>/slider/new/syllabus.png" style="width:300px;" /></a>                                                                        
																			</div>
																		</div>
																		<div class="col-sm-1" style="text-align:-moz-center;"></div>
																		<div class="col-sm-7">
																				<div class="col-sm-12">
																						<div class="col-sm-4" style="width: 60px;	height: 60px;	border-radius: 30px;	-webkit-border-radius: 30px;	-moz-border-radius: 30px;	background: #64B846 url(<?php bloginfo('template_url'); ?>/images/dashpallete/sylabus.png) no-repeat center;"></div>
																						<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;color:#D3658A;"><i>หลักสูตร</i></p></div>
																				</div>

																				<div class="col-sm-12"  style="margin-top:30px;">
																				<p>
																						 เปิดให้โรงเรียนอัพโหลดหลักสูตรประจำปีเข้าสู่ระบบ ช่วยให้คุณสามารถสร้างการเรียน การสอบ หรือหลักสูตรที่ชาญฉลาด และยังสามารถติดตามสถาณะหลักสูตรของแต่ละชั้นเรียนได้อีกด้วย.  
																				</p>
																				<h3 class="f2" style="font-size:20px;padding: 16px 5px;color:#D3658A;">อัพโหลดหลักสูตรประจำปีของโรงเรียนคุณ</h3>
																				</div>									
																		</div>								
																</div>	
									</li>	
															<li>
																<div class="col-sm-12" style="height:300px;width:1000px;">
																	   
																		<div class="col-sm-4" style="text-align:-moz-center;">                                                                
																			<div class="thumbnail-item" style="background-color: transparent;">
																				<a href="#"><img class="img-responsive" src="<?php bloginfo('template_url'); ?>/slider/new/notice.png" style="width:300px;" /></a>                                                                        
																			</div>
																		</div>
																		<div class="col-sm-1" style="text-align:-moz-center;"></div>
																		<div class="col-sm-7">
																				<div class="col-sm-12">
																						<div class="col-sm-4" style="width: 60px;	height: 60px;	border-radius: 30px;	-webkit-border-radius: 30px;	-moz-border-radius: 30px;	background: #64B846 url(<?php bloginfo('template_url'); ?>/images/dashpallete/notice.png) no-repeat center;"></div>
																						<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;color:#81C2D6;"><i>การประกาศ</i></p></div>
																				</div>

																				<div class="col-sm-12"  style="margin-top:30px;">
																				<p>
																						แน่ใจหรือว่าทุกคนได้รับประกาศฉบับด่วนของคุณ? ไม่ต้องห่วง! การประกาศของเราแจ้งเตือนทันทีตามเวลาจริง สามารถโต้ตอบได้ และไม่จำกัดจำนวนแค่ 160 ตัวอักษร คุณสามารถแจ้งประกาศตรงถึงนักเรียนแค่ห้องเดียว หรือทั้งโรงเรียนก็ได้.  
																				</p>
																				<h3 class="f2" style="font-size:20px;padding: 16px 5px;color:#81C2D6;">มั่นใจได้เลยว่าทุกคนได้รับประกาศที่สำคัญทุกฉบับ</h3>
																				</div>									
																		</div>								
																</div>	
									</li>	
															<li>
																<div class="col-sm-12" style="height:300px;width:1000px;">
																		
																		<div class="col-sm-4" style="text-align:-moz-center;">                                                                
																			<div class="thumbnail-item" style="background-color: transparent;">
																				<a href="#"><img class="img-responsive" src="<?php bloginfo('template_url'); ?>/slider/new/leave.png" style="width:300px;" /></a>                                                                        
																			</div>
																		</div>
																		<div class="col-sm-1" style="text-align:-moz-center;"></div>
																		<div class="col-sm-7">
																				<div class="col-sm-12">
																						<div class="col-sm-4" style="width: 60px;	height: 60px;	border-radius: 30px;	-webkit-border-radius: 30px;	-moz-border-radius: 30px;	background: #64B846 url(<?php bloginfo('template_url'); ?>/images/dashpallete/leave_application.png) no-repeat center;"></div>
																						<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;color:#9BA280;"><i>การลา</i></p></div>
																				</div>

																				<div class="col-sm-12"  style="margin-top:30px;">
																				<p>
																						การลาที่ง่ายและยืดหยุ่นผ่านทางออนไลน์ การอณุมัติพร้อมระบบแจ้งเตือน ทำให้คุณไม่ต้องจัดการจดหมายการลาและการตอบรับที่ยุ่งยากเหมือนเมื่อก่อน โดยการขอลานั้นผูกไปกับการเช็คชื่อในห้องเรียนซึ่งมันสะดวกขึ้นมาก. 
																				</p>
																				<h3 class="f2" style="font-size:20px;padding: 16px 5px;color:#9BA280;">การขอลาและการอนุมัติที่ง่ายขึ้น</h3>
																				</div>									
																		</div>								
																</div>	
									</li>
																
								</ul>
							</div>
							<div id="carousel" class="flexslider">
								<ul class="slides">
									<li>
									<img src="<?php bloginfo('template_url'); ?>/slider/thumb/Attendance.png" style="width:140px;padding:5px;" />
									</li>
									<li>
									<img src="<?php bloginfo('template_url'); ?>/slider/thumb/academic_calendar.png" style="width:140px;padding:5px;"  />
									</li>
									<li>
									<img src="<?php bloginfo('template_url'); ?>/slider/thumb/Lesson_Plan.png" style="width:140px;padding:5px;"  />
									</li>
									<li>
									<img src="<?php bloginfo('template_url'); ?>/slider/thumb/Teachers_Routine.png" style="width:140px;padding:5px;"  />
									</li>
									<li>
										<img src="<?php bloginfo('template_url'); ?>/slider/thumb/Homework.png" style="width:140px;padding:5px;"  />
									</li>
									<li>
										<img src="<?php bloginfo('template_url'); ?>/slider/thumb/Fees.png" style="width:140px;padding:5px;"  />
									</li>						
									<li>
										<img src="<?php bloginfo('template_url'); ?>/slider/thumb/Forms.png" style="width:140px;padding:5px;"  />
									</li>
									<li>
										<img src="<?php bloginfo('template_url'); ?>/slider/thumb/Meeting_Request.png" style="width:140px;padding:5px;"  />
									</li>
									<li>
										<img src="<?php bloginfo('template_url'); ?>/slider/thumb/Report_Card.png" style="width:140px;padding:5px;"  />
									</li>
									<li>
										<img src="<?php bloginfo('template_url'); ?>/slider/thumb/Event.png" style="width:140px;padding:5px;"  />
									</li>
															<li>
										<img src="<?php bloginfo('template_url'); ?>/slider/thumb/Transport.png" style="width:140px;padding:5px;"  />
									</li>
															<li>
										<img src="<?php bloginfo('template_url'); ?>/slider/thumb/Routine.png" style="width:140px;padding:5px;"  />
									</li>
															<li>
										<img src="<?php bloginfo('template_url'); ?>/slider/thumb/Syllabus.png" style="width:140px;padding:5px;"  />
									</li>
															<li>
										<img src="<?php bloginfo('template_url'); ?>/slider/thumb/Notice.png" style="width:140px;padding:5px;"  />
									</li>
															<li>
										<img src="<?php bloginfo('template_url'); ?>/slider/thumb/Leave.png" style="width:140px;padding:5px;"  />
									</li>
														   
								</ul>
							</div>
						</div>
					</div>
		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php //get_sidebar(); ?>
<?php get_footer(); ?>
