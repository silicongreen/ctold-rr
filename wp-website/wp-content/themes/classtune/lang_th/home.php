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

					<div id="cronWrap">
						<!--<div id="cronheader" class="slide" data-stellar-ratio="0.7" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
								<h1>150 Jahre GEZE Geschichte</h1>
								<h6>Vom Handwerksbetrieb<br />zum Technologieunternehmen</h6>
						</div>-->
						<div id="wp_dummy">&nbsp;</div>
						<div id="wp_dummyimg">&nbsp;</div>
						<div id="cron">

							<div class="tx-pxhistory-pi1">

								<div class="hitem slide" id="hitem_35" style="top: 410px; left: 4650px; width: 263px; height: 180px;" data-stellar-ratio="0.90" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-3/Layer-2/transport.png" alt="" title="" />
									<div class="texticon" style="top: -5px; left: -40px;">
										<div class="text_l text_layer" data-value="transport_layer">
											<div class="cb_top">&nbsp;</div>
											<div class="cb_middle">
												<div class="cb_middlePad">
													<h5>รถโรงเรียน</h5>
													<p class="bodytext">พ่อแม่สามารถตรวจสอบการรับส่งลูกๆของคุณโดยรถโรงเรียนได้ทางออนไลน์ และเมื่อมีการเปลี่ยนแปลงตารางเวลารับส่ง ผู้ปกครองจะทราบภายในเวลาไม่กี่นาที.
			</p>
												</div>
											</div>
											<div class="cb_bottom">&nbsp;</div>
										</div>
									</div>


								</div>

								<div class="hitem slide" id="hitem_7" style="top: 165px; left: 0px; width: 144px; height: 159px;" data-stellar-ratio="0.70" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-1/Layer-1/cloud_1.png" alt="classtuneslide1" title="" />

								</div>
								<div data-stellar-horiz="1" data-stellar-vertical-offset="0" data-stellar-horizontal-offset="83" data-stellar-ratio="0.95" style="top: 325px; left: 2900.43px; width: 400px; height: 175px;" id="hitem_28" class="hitem slide">
									<img title="" alt="" src="<?php bloginfo('template_url'); ?>/slider/Slide-2/Layer-4/leave.png" width="400">
									<div class="texticon" style="top: 10px; left: 270px;">
										<div class="text_l text_layer" data-value="leave_layer">
											<div class="cb_top">&nbsp;</div>
											<div class="cb_middle">
												<div class="cb_middlePad">
													<h5>การลา</h5>
													<p class="bodytext">"การยื่นขอลาทำได้ไม่ยุ่งยาก โดยผู้ปกครองสามารถทำเรืองลาให้กับลูกๆของคุณและได้รับการแจ้งการอนุมัติทันทีเมื่อได้รับอนุญาต และ
คุณครูสามารถอนุญาตให้นักเรียนลาพร้อมกันนั้นยังใช้ในการลาของครูเองได้ด้วย".</p>
												</div>
											</div>
											<div class="cb_bottom">&nbsp;</div>
										</div>
									</div>

								</div>
								<div class="hitem slide" id="hitem_8" style="top: 160px; left: 1060px; width: 120px; height: 80px;" data-stellar-ratio="0.70" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-1/Layer-1/cloud_2.png" alt="" title=""  width="115" />

								</div>
								<div data-stellar-horiz="1" data-stellar-vertical-offset="0" data-stellar-horizontal-offset="83" data-stellar-ratio="1.10" style="top: 233px; left: 600px; width: 144px; height: 159px;" id="hitem_7" class="hitem slide">
									<img title="" alt="classtuneslide2" src="<?php bloginfo('template_url'); ?>/slider/Slide-1/Layer-1/cloud_1.png" width="60">					
								</div>
								<div class="hitem slide" id="hitem_5" style="top: 90px; left: 1180px; width: 75px; height: 65px;" data-stellar-ratio="0.85" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-1/Layer-1/sun.png" alt="" title="" width="60" />
								</div>
								<div class="hitem slide" id="hitem_2" style="top: 170px; left: 208px; width: 261px; height: 125px;" data-stellar-ratio="0.70" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">

									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-1/Layer-1/cloud_2.png" alt="" title="" width="130" />
								</div>
								<div class="hitem slide" id="hitem_2" style="top: 440px; left: 180px; width: 261px; height: 125px;" data-stellar-ratio="0.70" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-1/Layer-1/tree.png" alt="" title="" width="240" />					
								</div>
								<!--div class="hitem slide" id="hitem_3" style="top:415px; left: 400px; width: 100px; height: 100px;" data-stellar-ratio="0.70" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">

									<img src="<?php //echo base_url(); ?>slider/Slide-1/Layer-2/top-tree1.png" alt="" title="" width="65" />
								</div-->


								<div class="hitem" id="hitem_6" style="top: 340px; left: 50px; width: 195px; height: 40px;">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-1/Layer-4/homework_text.png" alt="" title="" width="210" />					
								</div>
								<div class="hitem" id="hitem_6" style="top: 385px; left: -60px; width: 240px; height: 185px;">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-1/Layer-3/homework.png" alt="" title="" width="230" />

									<div class="texticon" style="top: -5px; left: 120px;">
										<div class="text_l text_layer" style="display:block;" data-value="homework_layer">
											<div class="cb_top">&nbsp;</div>
											<div class="cb_middle">
												<div class="cb_middlePad">
													<h5>การบ้าน</h5>
													<p class="bodytext">ฟีเจอร์นี่สร้างมาเพือทำให้ทุกคนที่อยู่ในระบบสามารถทำงานในส่วนของตัวเองได้อย่างมีประสิทธิภาพ คุณครูสามารถมอบหมายการบ้านให้นักเรียนทั้งชั้นในครั้งเดียว หรือเป็นรายบุคคล นักเรียนก็หาการบ้านที่ได้รับมอบหมายได้ง่าย อีกทั้งสามารถตั้งค่าการแจ้งเตือนและทำเครื่องหมายว่าเสร็จแล้วได้ในที่เดียว รวมถึงผู้ปกครองติดตามการบ้านที่ลูกๆได้รับมอบหมาย.</p>
													
												</div>
											</div>
											<div class="cb_bottom">&nbsp;</div>
										</div>
									</div>



								</div>

								<div class="hitem slide" id="hitem_15" style="top: 410px; left: 1300px; width: 225px; height: 291px;" data-stellar-ratio="0.75" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-2/Layer-1/apple.png" alt="" title="" width="150" />

								</div>
								<div class="hitem slide" id="hitem_3" style="top: 260px; left: 980px; width: 180px; height: 85px;" data-stellar-ratio="0.80" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-1/Layer-4/teachers_routine_text.png" alt="" title=""  width="175" />
								</div>

								<div class="hitem slide" id="hitem_3" style="top: 305px; left: 1070px; width:160px; height: 193px;" data-stellar-ratio="0.80" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-1/Layer-3/teachers_routine.png" alt="" title=""  width="145" />
									<div class="texticon" style="top: -15px; left: 80px;">
										<div class="text_l text_layer" data-value="troutine_layer">
											<div class="cb_top">&nbsp;</div>
											<div class="cb_middle">
												<div class="cb_middlePad">
													<h5>ตารางสอนสำหรับครู</h5>
													<p class="bodytext">คุณครูสามารถเรียกดูตารางงานได้ตลอดเวลา ซึ่งจะช่วยในการวางแผนงานและแน่นอนจะก่อให้เกิดประสิทธิภาพมากขึ้น.</p>
												</div>
											</div>
											<div class="cb_bottom">&nbsp;</div>
										</div>
									</div>



								</div>

								<div class="hitem slide" id="hitem_18" style="top: 360px; left: 1500px; width: 360px; height: 188px;" data-stellar-ratio="0.8" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-2/Layer-4/calendar.png" alt="" title="" width="350" />
									<div class="texticon" style="top: -20px; left: 70px;">
										<div class="text_l text_layer" data-value="acalendar_layer">
											<div class="cb_top">&nbsp;</div>
											<div class="cb_middle">
												<div class="cb_middlePad">
													<h5>ปฏิทินการศึกษา</h5>
													<p class="bodytext">ทางโรงเรียนสามารถเผยแพร่ปฏิทินการศึกษาได่ตั้งแต่ต้นปี และสามารถปรับปรุงได้ทุกเมื่อที่ต้องการ นั่นช่วยให้ผู้ปกครองวางแผนการท่องเที่ยวในวันหยุดกับครอบครัวได้ง่ายขึ้น.</p>
												</div>
											</div>
											<div class="cb_bottom">&nbsp;</div>
										</div>
									</div>



								</div>

								<div class="hitem slide" id="hitem_19" style="top: 130px; left: 3750px; width: 621px; height: 115px;" data-stellar-ratio="2.30" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-2/Layer-2/pencil_cutter.png" alt="" title="" />

								</div>

								<div class="hitem slide" id="hitem_25" style="top: 410px; left: 2000px; width: 180px; height: 105px;" data-stellar-ratio="0.80" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-2/Layer-3/three_kids.png" alt="" title="" width="160" />
								</div>

								<div class="hitem slide" id="hitem_26" style="top: 300px; left: 2200px; width: 200px; height: 120px;" data-stellar-ratio="0.85" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-2/Layer-4/report_card_text.png" alt="" title="" width="140" />



								</div>

								<div class="hitem slide" id="hitem_29" style="top: 300px; left: 3050px; width: 200px; height: 100px;" data-stellar-ratio="0.80" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-2/Layer-4/lesson_plan_text.png" alt="" title="" width="170" />

								</div>
								<div class="hitem slide" id="hitem_29" style="top: 300px; left: 5800px; width: 200px; height: 100px;" data-stellar-ratio="0.80" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-4/Layer-4/fees_text.png" alt="" title="" width="140" />

								</div>

								<div class="hitem slide" id="hitem_50" style="top: 360px; left: 6600px; width: 235px; height: 400px;" data-stellar-ratio="0.92" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-4/Layer-4/fees.png" alt="" title="" width="200" />

									<div class="texticon" style="top: -45px; left: 155px;">
										<div class="text_l text_layer" data-value="fees_layer">
											<div class="cb_top">&nbsp;</div>
											<div class="cb_middle">
												<div class="cb_middlePad">
													<h5>ค่าธรรมเนียม</h5>
													<p class="bodytext">"ผู้ปกครองสามารถชำระค่าธรรมเนียมผ่านบัตรเครดิต หรือการชำระผ่านบริการทางการเงินมือถือแบบอื่นๆได้
เป็นเครื่องมือที่ดีสำหรับตรวจสอบการชำระค่าธรรมเนียมและบันทึกบัญชีของโรงเรียน".</p>
												</div>
											</div>
											<div class="cb_bottom">&nbsp;</div>
										</div>
									</div>



								</div>
								<div class="hitem slide" id="hitem_29" style="top: 250px; left: 6000px; width: 200px; height: 100px;" data-stellar-ratio="0.90" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-4/Layer-4/forms_text.png" alt="" title="" width="150" />

								</div>
								<div class="hitem slide" id="hitem_48" style="top: 350px; left: 5700px; width: 180; height: 210px;" data-stellar-ratio="0.85" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-4/Layer-3/form_green.png" alt="" title="" width="200" />

									<div class="texticon" style="top: 60px; left: 175px;">
										<div class="text_l text_layer" data-value="forms_layer">
											<div class="cb_top">&nbsp;</div>
											<div class="cb_middle">
												<div class="cb_middlePad">
													<h5>แบบฟอร์ม</h5>
													<p class="bodytext">คุณสามารถใช้งานแบบฟอร์มทุกประเภทของโรงเรียนได้ผ่านสมาร์ทโฟนและเว็บไซต์ ที่ง่ายต่อการเข้าถึง และยังกรอกแบบฟอร์มและส่งกลับทางออนไลน์ ได้โดยไม่ต้องต่อคิวรอรับแบบฟอร์ม เพิ่อให้เกิดประสิทธิภาพสูงสุดในการบริหารจัดการ.</p>
												</div>
											</div>
											<div class="cb_bottom">&nbsp;</div>
										</div>
									</div>



								</div>

								<div class="hitem slide" id="hitem_53" style="top: 127px; left: 6800px; width: 248px; height: 307px;" data-stellar-ratio="0.85" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-4/Layer-2/jems_clip.png" alt="" title="" />					

								</div>

								<div class="hitem slide" id="hitem_19" style="top: 125px; left: 795px; width: 90px; height: 50px;" data-stellar-ratio="1.80" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-1/Layer-2/planet.png" alt="" title="" width="80" />
								</div>

								<div class="hitem slide" id="hitem_10" style="top: 120px; left: 320px; width: 95px; height: 80px;" data-stellar-ratio="1.49" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-1/Layer-2/rocket.png" alt="classtuneslide3" title="" width="80" />



								</div>

								<div class="hitem slide" id="hitem_13" style="top: 150px; left: 860px; width: 50px; height: 30px;" data-stellar-ratio="1.00" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-1/Layer-1/cloud_1.png" alt="" title="" width="40" />

								</div>

								<div class="hitem slide" id="hitem_14" style="top: 120px; left: 2300px; width: 205px; height: 164px;" data-stellar-ratio="1.50" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-1/Layer-1/cloud_2.png" alt="" title="" />


								</div>

								<div class="hitem slide" id="hitem_16" style="top: 175px; left: 1950px; width: 245px; height: 84px;" data-stellar-ratio="1.50" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-1/Layer-1/cloud_1.png" alt="" title="" />

								</div>

								<div class="hitem" id="hitem_9" style="top: 350px; left: 700px; width: 215px; height: 200;">
									<img width="145" src="<?php bloginfo('template_url'); ?>/slider/Slide-1/Layer-4/routine_text.png" alt="" title="">
									<div class="texticon" style="top: -40px; left: 0px; background-position: 0px 11px;">
										<div style="display: none; background-position: 0px 11px;" class="text_l text_layer" data-value="routine_layer">
											<div class="cb_top">&nbsp;</div>
											<div class="cb_middle">
												<div class="cb_middlePad">
													<h5>ตารางเรียน</h5>
													<p class="bodytext">ครูและเจ้าหน้าที่ธุรการสามารถปรับปรุงตารางเรียนหากจำเป็นพร้อมส่งข้อความแสดงการเปลี่ยนแปลงได้ในทันที.</p>
												</div>
											</div>
											<div class="cb_bottom">&nbsp;</div>
										</div>
									</div>
								</div>

								<!--div style="top: 420px; left: 775px; width: 215px; height: 200;" id="hitem_9" class="hitem">
									<img width="115" title="" alt="" src="<?php //echo base_url(); ?>slider/Slide-1/Layer-3/robote_.png">
									<div class="texticon" style="top: -40px; left: 0px; background-position: 0px 11px;">
										<div style="display: none; background-position: 0px 11px;" class="text_l text_layer" data-value="routine_layer">
											<div class="cb_top">&nbsp;</div>
											<div class="cb_middle">
												<div class="cb_middlePad">
													<h5>Routine</h5>
													<p class="bodytext">Teachers and Management can modify routine when necessary and send instant notification.</p>
												</div>
											</div>
											<div class="cb_bottom">&nbsp;</div>
										</div>
									</div>
								</div-->
								<div class="hitem" id="hitem_9" style="top: 375px; left: 800px; width: 215px; height: 200;">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-1/Layer-3/routine.png" alt="" title="" width="225" />

								</div>
								<div data-stellar-horiz="1" data-stellar-vertical-offset="0" data-stellar-horizontal-offset="83" data-stellar-ratio="0.80" style="top: 285px; left: 450.7px; width: 210px; height: 35px;" id="hitem_3" class="hitem slide">
									<img title="" alt="" src="<?php bloginfo('template_url'); ?>/slider/Slide-1/Layer-4/attendance_text.png" width="210">

								</div>
								<div data-stellar-horiz="1" data-stellar-vertical-offset="0" data-stellar-horizontal-offset="83" data-stellar-ratio="0.80" style="top: 318px; left: 400.7px; width: 280px; height: 250px;" id="hitem_3" class="hitem slide">
									<img title="" alt="" src="<?php bloginfo('template_url'); ?>/slider/Slide-1/Layer-2/attendance.png" width="280">

									<div style="top: 20px; left: 160px;" class="texticon">
										<div class="text_l text_layer" style="display: none;" data-value="attendance_layer">
											<div class="cb_top">&nbsp;</div>
											<div class="cb_middle">
												<div class="cb_middlePad">
													<h5>การเช็คชื่อ</h5>
													<p class="bodytext">เมื่อพูดถึงการเช็คชื่อเข้าเรียน จะง่ายกว่าที่เคย!</p>
													<p class="bodytext">คุณครูสามารถเช็คชื่อนักเรียนทั้งห้องแค่เพียงไม่กี่คลิก ขณะเดียวกันผู้ปกครองจะได้รับการแจ้งเตือนในทันทีที่มีการเช็คชื่อ นอกจากนั้นยังช่วยโรงเรียนในการเก็บบันทึกข้อมูลอีกด้วย.</p>
												</div>
											</div>
											<div class="cb_bottom">&nbsp;</div>
										</div>
									</div>



								</div>
								<div class="hitem slide" id="hitem_5" style="top:440px; left: 685px; width: 100px; height: 178px;" data-stellar-ratio="1.20" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-1/Layer-2/top-tree.png" alt="" title="" width="75"/>
								</div>

								<div class="hitem slide" id="hitem_20" style="top: 200px; left: 3075px; width: 145px; height: 160px;" data-stellar-ratio="1.51" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-3/Layer-1/cloud_2.png" alt="" title="" />

								</div>

								<div class="hitem slide" id="hitem_21" style="top: 145px; left: 2625px; width: 204px; height: 161px;" data-stellar-ratio="1.49" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-1/Layer-1/cloud_2.png" alt="" title="" />



								</div>

								<div class="hitem" id="hitem_17" style="top: 213px; left: 1600px; width: 373px; height: 134px;">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-2/Layer-4/academic_calendar_text.png" alt="" title="" width="220" />					
								</div>


								<div class="hitem slide" id="hitem_24" style="top: 140px; left: 3800px; width: 140px; height: 70px;" data-stellar-ratio="1.50" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-2/Layer-2/pencil.png" alt="" title="" width="100" />					
								</div>

								<div class="hitem" id="hitem_27" style="top: 330px; left: 2460px; width: 180px; height: 135px;" data-stellar-ratio="0.90" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-2/Layer-3/report_card.png" alt="" title="" width="165" />

									<div class="texticon" style="top: 12px; left: 3px;">
										<div class="text_l text_layer" data-value="reportcard_layer">
											<div class="cb_top">&nbsp;</div>
											<div class="cb_middle">
												<div class="cb_middlePad">
													<h5>ผลสอบ</h5>
													<p class="bodytext">ครูสามารถอัพโหลดผลทดสอบในชั้น ผลสอบในเทอม โปรเจ็กงานต่างๆ เข้าไปที่ในระบบ ซึ่งนักเรียนและผู้ปกครองสามารถดูเกรดออนไลน์ และผู้ปกครองสามารถตอบกลับรวมถึงวางแผนร่วมกันในการพัฒนาเด็กได้ทันทีที่ทราบผลผ่านช่องทางออนไลน์.</p>
												</div>
											</div>
											<div class="cb_bottom">&nbsp;</div>
										</div>
									</div>



								</div>

								<div class="hitem slide" id="hitem_22" style="top: 215px; left: 3700px; width: 204px; height: 162px;" data-stellar-ratio="1.49" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-3/Layer-1/cloud.png" alt="" title="" />

								</div>

								<div class="hitem" id="hitem_30" style="top: 419px; left: 3600px; width: 250px; height: 85px;">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-2/Layer-3/book.png" alt="" title="" />
									<div class="texticon" style="top: -35px; left: 140px;">
										<div class="text_l text_layer" data-value="lessonplan_layer">
											<div class="cb_top">&nbsp;</div>
											<div class="cb_middle">
												<div class="cb_middlePad">
													<h5>แผนการเรียน</h5>
													<p class="bodytext">คุณครูสามารถอัพโหลดแผนการเรียนประจำสัปดาห์, ประจำเดือน และประจำปี ซึ่งทำให้ผู้ปกครองและนักเรียนสามารถวางแผนล่วงหน้าเกี่ยวกับการศึกษาได้.</p>
												</div>
											</div>
											<div class="cb_bottom">&nbsp;</div>
										</div>
									</div>
								</div>

								<div class="hitem" id="hitem_32" style="top: 350px; left: 4100px; width: 319px; height: 330px;">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-3/Layer-3/notice.png" alt="" title="" width="250" />

									<div class="texticon" style="top: 15px; left: 250px;">
										<div class="text_l text_layer" data-value="notice_layer">
											<div class="cb_top">&nbsp;</div>
											<div class="cb_middle">
												<div class="cb_middlePad">
													<h5>การประกาศ</h5>
													<p class="bodytext">ทุกการประกาศจะไม่มีการจำกัดความยาวของข้อความ สามารถโต้ตอบกันได้ และส่งการแจ้งเตือนให้ผู้ปกครองในทันที.</p>
												</div>
											</div>
											<div class="cb_bottom">&nbsp;</div>
										</div>
									</div>



								</div>

								<div class="hitem slide" id="hitem_28" style="top: 300px; left: 3180px; width: 150px; height: 70px;" data-stellar-ratio="0.95" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-2/Layer-4/leave_text.png" alt="" title="" width="130" />



								</div>

								<div class="hitem slide" id="hitem_31" style="top: 165px; left: 4800px; width: 197px; height: 140px;" data-stellar-ratio="1.48" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-2/Layer-1/colud_3.png" alt="" title="" />					

								</div>

								<div class="hitem slide" id="hitem_34" style="top: 160px; left: 5660px; width: 150px; height: 80px;" data-stellar-ratio="1.50" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-2/Layer-1/colud_4.png" alt="" title="" width="140" />



								</div>

								<div class="hitem slide" id="hitem_23" style="top: 125px; left: 4900px; width: 80px; height: 85px;" data-stellar-ratio="1.80" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-2/Layer-2/eraser.png" alt="" title="" width="60" />					

								</div>

								<div class="hitem slide" id="hitem_36" style="top: 350px; left: 4900px; width: 170px; height: 80px;" data-stellar-ratio="0.95" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-3/Layer-4/transport_text.png" alt="" title="" width="170" />



								</div>

								<div class="hitem slide" id="hitem_33" style="top: 300px; left: 5950px; width: 251px; height: 238px;" data-stellar-ratio="1.50" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-3/Layer-4/notice_text.png" alt="" title=""  width="150" />


								</div>

								<div class="hitem slide" id="hitem_40" style="top: 162px; left: 5775px; width: 262px; height: 356px;" data-stellar-ratio="1.3" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-3/Layer-1/ballon.png" alt="" title="" width="100" />


								</div>

								<div class="hitem" id="hitem_37" style="top: 330px; left: 4600px; width: 200px; height: 100px;">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-3/Layer-4/meeting_request_text.png" alt="" title="" width="170" />
								</div>

								<div class="hitem slide" id="hitem_39" style="top: 450px; left: 5590px; width: 150px; height: 160px;" data-stellar-ratio="1.30" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-3/Layer-2/clock.png" alt="" title="" width="100"/>

								</div>
								<div class="hitem slide" id="hitem_39" style="top: 150px; left: 6550px; width: 204px; height: 160px;" data-stellar-ratio="1.50" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-3/Layer-1/cloud.png" alt="" title="" />

								</div>
								<div class="hitem" id="hitem_42" style="top: 284px; left: 5600px; width: 170px; height: 55px;">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-3/Layer-4/event_text.png" alt="" title="" width="140" />
								</div>

								<div class="hitem slide" id="hitem_43" style="top: 380px; left: 5875px; width: 300px; height: 258px;" data-stellar-ratio="1.05" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-3/Layer-3/events.png" alt="" title="" width="250" />
									<div class="texticon" style="top: -30px; left: 50px;">
										<div class="text_l text_layer" data-value="event_layer">
											<div class="cb_top">&nbsp;</div>
											<div class="cb_middle">
												<div class="cb_middlePad" style="padding-bottom: 2px;">
													<h5>กิจกรรม</h5>
													<p class="bodytext">หน้ากิจกรรมทำให้ทุกคนสามารถปรับปรุงเกี่ยวกับวันที่ เวลา และสถานที่จัดงาน ที่กำลังจะเกิดขึ้นในโรงเรียน พร้อมมีการแจ้งเตือนกิจกรรมที่สำคัญด้วย. 
													</p>
													<p class="bodytext">&nbsp;</p>
												</div>
											</div>
											<div class="cb_bottom">&nbsp;</div>
										</div>
									</div>


								</div>

								<div class="hitem slide" id="hitem_41" style="top: 140px; left: 7750px; width: 100px; height: 80px;" data-stellar-ratio="1.55" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-3/Layer-3/pencil_cutter.png" alt="" title="" width="90" />					

								</div>

								<div class="hitem" id="hitem_44" style="top: 185px; left: 5425px; width: 440px; height: 150px;" data-stellar-ratio="1.10" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-3/Layer-1/cloud.png" alt="" title="" />



								</div>

								<!--div class="hitem slide" id="hitem_46" style="top: 355px; left: 6600px; width: 407px; height: 352px;" data-stellar-ratio="1.10" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php //echo base_url(); ?>slider/Slide-4/Layer-3/diary_.png" alt="" title="" />
								</div-->

								<div class="hitem" id="hitem_47" style="top: 280px; left: 6050px; width: 300px; height: 100px;">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-4/Layer-4/syllabus_text.png" alt="" title="" width="180" />

								</div>
								<div class="hitem" id="hitem_47" style="top: 280px; left: 6180px; width: 175px; height: 330px;">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-4/Layer-4/syllabus.png" alt="" title="" width="150"  />

								</div>

								<div class="hitem slide" id="hitem_45" style="top: 140px; left: 6800px; width: 150px; height: 100px;" data-stellar-ratio="1.20" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-4/Layer-1/airplane.png" alt="" title="" width="80" />					


								</div>

								<div class="hitem slide" id="hitem_38" style="top: 430px; left: 4800px; width: 250px; height: 180px;" data-stellar-ratio="1.05" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">

									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-3/Layer-3/meeting_request.png" alt="" title="" width="250" />


									<div class="texticon" style="top: -40px; left: 230px;">
										<div class="text_l text_layer" data-value="meetingrequest_layer">
											<div class="cb_top">&nbsp;</div>
											<div class="cb_middle">
												<div class="cb_middlePad">
													<h5>การขอทำนัด</h5>
													<p class="bodytext">"เจ้าหน้าที่บริหารระบบสามารถจัดระเบียบ การประชุมผู้ปกครอง และแจ้งเตือนพวกเค้าได้อย่างรวดเร็ว รวมถึงผู้ปกครองสามารถขอทำนัดเพื่อพบกับคุณครูได้
โดยคุณครูสามารถขอนัดพบกับผู้ปกครองเป็นรายบุคคลได้อีกด้วย".</p>
												</div>
											</div>
											<div class="cb_bottom">&nbsp;</div>
										</div>
									</div>


								</div>

								<div class="hitem slide" id="hitem_52" style="top: 360px; left: 7250px; width: 200px; height: 170px;" data-stellar-ratio="0.95" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-4/Layer-4/end-image.png" alt="" title="" width="350" />
								</div>

								<div class="hitem" id="hitem_49" style="top: 100px; left: 6500px; width: 180px; height: 180px;">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-4/Layer-2/scale_1.png" alt="" title="" width="120" />



								</div>

								<div class="hitem slide" id="hitem_115" style="top: 420px; left: 6670px; width: 100px; height: 110px;" data-stellar-ratio="1.05" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-4/Layer-3/bird.png" alt="" title="" width="60" />

									<div class="texticon" style="top: -39px; left: 0px;">
										<div class="text_l text_layer" data-value="syllabus_layer">
											<div class="cb_top">&nbsp;</div>
											<div class="cb_middle">
												<div class="cb_middlePad">
													<h5>หลักสูตร</h5>
													<p class="bodytext">โรงเรียนอนุญาตให้อัพโหลดหลักสูตรประจำปี ซึ่งนักเรียนและผู้ปกครองสามารถเรียกดูหลักสูตรเพื่ออ้างอิงได้ตลอดปีการศึกษา.
													</p>
													<p class="bodytext">&nbsp;</p>
												</div>
											</div>
											<div class="cb_bottom">&nbsp;</div>
										</div>
									</div>



								</div>


								<!--div class="hitem slide" id="hitem_55" style="top: 520px; left: 6825px; width: 270px; height: 215px;" data-stellar-ratio="0.95" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php //echo base_url(); ?>slider/Slide-4/Layer-1/object_.png" alt="" title="" width="150" />					

								</div-->

								<div class="hitem slide" id="hitem_51" style="top: 180px; left: 7800px; width: 100px; height: 100px;" data-stellar-ratio="1.3" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-4/Layer-2/sessor.png" alt="" title="" width="100" />

								</div>
								<div class="hitem slide" id="hitem_51" style="top: 180px; left: 8200px; width: 100px; height: 100px;" data-stellar-ratio="1.1" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-4/Layer-2/sessor.png" alt="" title="" width="100" />

								</div>

								<div class="hitem slide" id="hitem_51" style="top: 148px; left: 10400px; width: 115; height: 60px;" data-stellar-ratio="1.80" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="<?php bloginfo('template_url'); ?>/slider/Slide-4/Layer-2/hat.png" alt="" title="" width="100"/>

								</div>

								<!--div class="hitem" id="hitem_54" style="top: 271px; left: 7500px; width: 300px; height: 250px;">
									<img src="<?php //echo base_url(); ?>slider/Slide-4/Layer-3/school_.png" alt="" title="" width="250" />
								</div-->
								<div data-stellar-horiz="1" data-stellar-vertical-offset="0" data-stellar-horizontal-offset="0" data-stellar-ratio="1.10" style="top: 185px; left: 7832.45px; width: 440px; height: 150px;" id="hitem_44" class="hitem">
									<img title="" alt="" src="http://www.classtune.dev/slider/Slide-3/Layer-1/cloud.png">
								</div>
								<div class="hitem" id="hitem_44" style="top: 145px; left: 7539.9px; width: 440px; height: 150px;" data-stellar-ratio="1.10" data-stellar-horizontal-offset="0" data-stellar-vertical-offset="0" data-stellar-horiz="1">
									<img src="http://www.classtune.dev/slider/Slide-3/Layer-1/cloud.png" alt="" title="">
								</div>

								<div id="cronnav">
									<ul>

										<li class="cronnavitem act" id="cni_75" data-value="homework">
											<span>การบ้าน</span>
										</li>

										<li class="cronnavitem" id="cni_75" data-value="attendance">
											<span>การเช็คชื่อ</span>
										</li>

										<li class="cronnavitem" id="cni_650" data-value="routine">
											<span>ตารางเรียน</span>
										</li>

										<li class="cronnavitem" id="cni_1100" data-value="troutine">
											<span>ตารางสอนสำหรับครู</span>
										</li>

										<li class="cronnavitem" id="cni_1600" data-value="acalendar">
											<span>ปฏิทินการศึกษา</span>
										</li>

										<li class="cronnavitem" id="cni_2400" data-value="reportcard">
											<span>ผลสอบ</span>
										</li>

										<li class="cronnavitem" id="cni_3000" data-value="leave">
											<span>การลา</span>
										</li>

										<li class="cronnavitem" id="cni_3500" data-value="lessonplan">
											<span>แผนการเรียน</span>
										</li>

										<li class="cronnavitem" id="cni_4000" data-value="notice">
											<span>การประกาศ</span>
										</li>

										<li class="cronnavitem" id="cni_4500" data-value="meetingrequest">
											<span>การขอทำนัด</span>
										</li>

										<li class="cronnavitem" id="cni_5000" data-value="transport">
											<span>รถโรงเรียน</span>
										</li>

										<li class="cronnavitem" id="cni_5500" data-value="event">
											<span>กิจกรรม</span>
										</li>

										<li class="cronnavitem" id="cni_6000" data-value="syllabus">
											<span>หลักสูตร</span>
										</li>

										<li class="cronnavitem" id="cni_6500" data-value="forms">
											<span>แบบฟอร์ม</span>
										</li>

										<li class="cronnavitem" id="cni_7000" data-value="fees">
											<span>ค่าธรรมเนียม</span>
										</li>
										<li class="cronnavitem" id="cni_up">
											<i class="faq fa fa-arrow-up"></i>
										</li>
										<li class="cronnavitem" id="cni_down">
											<i class="faq fa fa-arrow-down"></i>
										</li>

									</ul>
								</div>

							</div>
						</div>
					</div>
		</main><!-- .site-main -->
	</div><!-- .content-area -->

<?php //get_sidebar(); ?>
<?php get_footer(); ?>
