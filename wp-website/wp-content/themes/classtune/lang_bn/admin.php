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
Template Name: admin-bn
*/

get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<link rel="stylesheet" href="<?php echo get_site_url(); ?>/bootstrap/css/bootstrap-lightbox.min.css" type="text/css" media="all" />
<div class="container"><div class="container">
	<div class="wrapper">
		<div id="startWrap">
			
		</div>

		<div id="beforeWrap" style="background: transparent url(<?php bloginfo('template_url'); ?>/images/cover/admin-page.png) no-repeat top left;    background-size: cover;">
			<!--img src="http://www.classtune.dev/images/test/CLASSTUNE-COVER.png" alt="" title="" width="100%" /<li style="color:#64B846;">|</li>-->
			<h2 class="f2" style="margin-top:100px;"><i>হয়ে উঠুন স্কুলের নিয়ন্ত্রক!</i></h2>
			<div class="postlist-tab2">
				<div style=" background-color: #fff;height: 220px;margin-left: 100px;position: relative;top: -120px;width: 500px;z-index: 1;box-shadow: 2px 4px 2px -3px gray;border:1px solid #ccc;">
					<div class="col-sm-12" style="margin-top:40px;">
						<div class="col-sm-3" style="border:1px solid;margin-top:12px;"><span style=""></span></div>
						<div class="col-sm-6" style="font-size:18px;"><b>বেছে নিন আপনার প্যাকেজ</b>	</div>					
						<div class="col-sm-3" style="border:1px solid;margin-top:12px;"><span style=""></span></div>
					</div>
					<div class="col-sm-12" style="">
					<div class="col-sm-6"><a href="<?php echo get_site_url(); ?>/package-type?local=basic" class="btn-basic-pack">
					বেসিক</a><!--span class="btn-basic-pack-text">Free</span--></div> 
					<div class="col-sm-6"><a href="<?php echo get_site_url(); ?>/package-type?local=premium" class="btn-primium-pack">
					প্রিমিয়াম</a><!--span class="btn-primium-pack-text">$1.99 Per Month/ Student</span--></div> 
					
					
					</div>
				</div>
			</div>
		</div>

		<div id="cronWrap" style="background-color:#F4FAFA;background-image:none;top:500px;height:800px;">
			<div style="border: 0 solid #ccc;left: 50%;margin: 0 0 0 -499px;position: absolute;width: 1000px;padding:55px 10px 0;">
				<h2 class="f2" style="text-align:center;"><i>এখন মনে রাখা সহজ আর কাজ হবে ঝামেলাবিহীন।</i></h2><br>
				<p style="font-size:16px;"><b>ClassTune</b> স্কুল বিষয়ক প্রাতিষ্ঠানিক পর্যবেক্ষন এবং ব্যবস্থাপনাকে সহজ ও কার্যকর করে তোলে। শিক্ষক, শিক্ষার্থী এবং অভিভাবকদের একই প্লাটফর্মে এনে আপনার স্কুল সংশ্লিষ্ট সকল কার্যক্রমকে উন্নত, সারিবদ্ধ ও সুশৃংঙ্খল করে তোলে। ক্লাসটিউন অভিভাবকদের পরামর্শ এবং অংশগ্রহণ বৃদ্ধির মাধ্যমে শিক্ষা ব্যবস্থার উন্নতি নিশ্চিত করে। </p><br>
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
										<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>উপস্থিতি</i></p></div>
									</div>
									
									<div class="col-sm-12"  style="margin-top:30px;">
									<p>
										পুরো স্কুলের সব ক্লাস এবং শাখার শিক্ষার্থীদের উপস্থিতির রেকর্ড দেখুন এক জায়গাতেই। শিক্ষার্থীদের উপস্থিতির খোঁজ এখন এক ক্লিকেই! একজন শিক্ষার্থী কতগুলো ক্লাস মিস করেছে তাও জানা যাবে এক নিমিষেই। 
									</p>
									<h3 class="f2" style="font-size:20px;padding: 16px 5px;">পুরো স্কুলের সব শিক্ষার্থীদের উপস্থিতির রেকর্ড দেখুন এক স্থানে।</h3>
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
										<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>একাডেমিক ক্যালেন্ডার</i></p></div>
									</div>
									
									<div class="col-sm-12"  style="margin-top:30px;">
									<p>
										আপনার একাডেমিক ক্যালেন্ডার আপলোড করুন এবং সবাইকে সেই অনুযায়ী পরবর্তী পরিকল্পনা তৈরি করতে সাহায্য করুন। সারা বছরের ক্যালেন্ডার একসাথেই পাওয়া যাবে ক্লাসটিউনে। 
									</p>
                                    <h3 class="f2" style="font-size:20px;padding: 16px 5px;">বছরের সব গুরুত্বপূর্ণ তারিখগুলো এখন আঙুলের ছোঁয়ায় </h3>
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
										<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>পাঠ্ পরিকল্পনা</i></p></div>
									</div>
									
									<div class="col-sm-12"  style="margin-top:30px;">
									<p>
										পাঠ পরিকল্পনা এখন অনলাইনেই পাওয়া যাবে। এমনকি প্রয়োজনমত সংশোধনও করা যাবে। ক্যালেন্ডার অনুযায়ী পড়াশোনার পরিকল্পনা করা এখন আরও সহজ।
									</p>
									<h3 class="f2" style="font-size:20px;padding: 16px 5px;">পাঠ্ পরিকল্পনা থাকবে আপডেটেট </h3>
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
										<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>শিক্ষকদের রুটিন</i></p></div>
									</div>
									
									<div class="col-sm-12"  style="margin-top:30px;">
									<p>
										শিক্ষকদের সুবিধার জন্য রয়েছে রুটিন আপলোডের ব্যবস্থা। প্রত্যেক শিক্ষকের আলাদা আলাদা রুটিন জানতে চোখ রাখুন ক্লাসটিউনে। 
									</p>
                                    <h3 class="f2" style="font-size:20px;padding: 16px 5px;">শিক্ষকদের সুবিধামতো রুটিন আপলোড করুন </h3>
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
										<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>হোমওয়ার্ক</i></p></div>
									</div>
									
									<div class="col-sm-12"  style="margin-top:30px;">
									<p>
										একজন শিক্ষার্থী বা একটি ক্লাসের বা পুরো স্কুলের হোমওয়ার্ক খোঁজ জানা যাবে এক জায়গাতেই।
									</p>
									<h3 class="f2" style="font-size:20px;padding: 16px 5px;">সবার হোমওয়ার্ক জানার সুবিধা</h3>
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
										<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>বেতন</i></p></div>
									</div>
									
									<div class="col-sm-12"  style="margin-top:30px;">
									<p>
										নির্ধারিত সময়ের মধ্যে ফি জমা দেয়া এবং বকেয়া বেতন প্রদানের জন্য নিয়মিত মনে করিয়ে দিবে ক্লাসটিউন। 
									</p>
                                    <h3 class="f2" style="font-size:20px;padding: 16px 5px;">ঝামেলাবিহীন বেতন প্রদান</h3>
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
										<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>স্কুলের যাবতীয় ফর্ম</i></p></div>
									</div>
									
									<div class="col-sm-12"  style="margin-top:30px;">
									<p>
										স্কুলের সব ফর্ম আপলোড করুন যেন সবাই প্রয়োজনমত ব্যবহার করতে পারে। অফিসের কাজে বাড়বে কর্মদক্ষতা। 
									</p>
                                    <h3 class="f2" style="font-size:20px;padding: 16px 5px;">সব প্রয়োজনীয় ফর্ম পাওয়া যাচ্ছে অনলাইনেই</h3>
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
										<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>সভা অনুরোধ</i></p></div>
									</div>
									
									<div class="col-sm-12"  style="margin-top:30px;">
									<p>
										শিক্ষক এবং অভিভাবকদের সাথে দেখা করা হবে অনেক সহজে এবং প্রয়োজনে তা পরিবর্তনও করা যাবে। অভিভাবকদের সাক্ষাতের জন্য অনুরোধ করা যাবে কম সময়ে এবং কোন ঝামেলা ছাড়াই। 
									</p>
                                    <h3 class="f2" style="font-size:20px;padding: 16px 5px;">সাক্ষাৎ-এর আয়োজন করা এখন সহজ। </h3>
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
										<div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>রিপোর্ট কার্ড</i></p></div>
									</div>
									
									<div class="col-sm-12"  style="margin-top:30px;">
									<p>
										ক্লাস টেস্ট, পরীক্ষার নম্বর আর প্রোজেক্ট রিপোর্ট এখন অনলাইনেই জমা থাকবে। 
									</p>
                                    <h3 class="f2" style="font-size:20px;padding: 16px 5px;">রিপোর্ট কার্ডের অনলাইন আর্কাইভ </h3>
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
                                                                            <div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>ইভেন্ট</i></p></div>
                                                                    </div>

                                                                    <div class="col-sm-12"  style="margin-top:30px;">
                                                                    <p>
                                                                            শিক্ষার্থী, শিক্ষক আর অভিভাবকদের স্কুলের ইভেন্ট সম্পর্কে অবহিত করুন। যেকোন প্রয়োজনে ইভেন্ট তৈরি ও পরিবর্তন  করুন। দেখুন কতজন শিক্ষক, শিক্ষার্থী এবং অভিভাবক আপনার ইভেন্টে অংশগ্রহণ করছে। 
                                                                    </p>
                                                                    <h3 class="f2" style="font-size:20px;padding: 16px 5px;">সব ইভেন্টের খবরাখবর পাওয়া যাবে এক জায়গাতেই</h3>
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
                                                                            <div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>পরিবহন</i></p></div>
                                                                    </div>

                                                                    <div class="col-sm-12"  style="margin-top:30px;">
                                                                    <p>
																		আপনার যাতায়াত ও পরিবহনের পরিকল্পনা আপলোড করুন। কোন কারণে এতে পরিবর্তন ঘটলে সেটা সাথে সাথেই জানিয়ে দেন অভিভাবকদের।
                                                                    </p>
                                                                    <h3 class="f2" style="font-size:20px;padding: 16px 5px;">পরিবহনের সময়সূচী দেখুন</h3>
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
                                                                            <div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>রুটিন</i></p></div>
                                                                    </div>

                                                                    <div class="col-sm-12"  style="margin-top:30px;">
                                                                    <p>
																		যেকোনো ক্লাস বা সেকশনের রুটিন তৈরি করুন এখানে এবং সবার দেখার সুবিধার্থে ক্লাস টিউনে প্রকাশ করুন। রুটিনে কোন পরিবর্তন আসলে সাথে সাথে নোটিফিকেশনের মাধ্যমে জানিয়ে দেন সবাইকে।
                                                                    </p>
                                                                    <h3 class="f2" style="font-size:20px;padding: 16px 5px;">রুটিন আপলোড করুন আর প্রয়োজনে সেটা পরিবর্তন করুন</h3>
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
                                                                            <div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>পাঠক্রম</i></p></div>
                                                                    </div>

                                                                    <div class="col-sm-12"  style="margin-top:30px;">
                                                                    <p>
																		বার্ষিক পাঠক্রম পাওয়া যাবে অনলাইনেই। ক্লাস অনুযায়ী এবং পরীক্ষা অনুযায়ী সিলেবাস তৈরি করুন এবং খোঁজ রাখুন প্রতিটি ক্লাসের সিলেবাসের খবরাখবর।
                                                                    </p>
                                                                    <h3 class="f2" style="font-size:20px;padding: 16px 5px;">স্কুলের বার্ষিক সিলেবাস আপলোড করুন</h3>
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
                                                                            <div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>বিজ্ঞপ্তি</i></p></div>
                                                                    </div>

                                                                    <div class="col-sm-12"  style="margin-top:30px;">
                                                                    <p>
																		কোন জরুরী বিজ্ঞপ্তি প্রকাশ করতে হবে? চিন্তার কিছু নেই। একজন শিক্ষার্থী বা একটি ক্লাস বা পুরো স্কুলকে কোন বিজ্ঞপ্তি পাঠানো যাবে এখান থেকে খুব সহজেই।
                                                                    </p>
                                                                    <h3 class="f2" style="font-size:20px;padding: 16px 5px;">আপনার বিজ্ঞপ্তি প্রকাশ করুন। </h3>
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
                                                                            <div class="col-sm-8"><p class="f2" style="font-size:20px;padding: 16px 5px;"><i>ছুটি</i></p></div>
                                                                    </div>

                                                                    <div class="col-sm-12"  style="margin-top:30px;">
                                                                    <p>
																		এখন ছুটির আবেদন করা যাবে অনলাইনেই আর সেটা মঞ্জুর হয়েছে কিনা তা জানা যাবে নোটিফিকেশনের মাধ্যমে। উপস্থিতির সাথে ছুটির দিনগুলোকে সহজেই সাজিয়ে নেন আজকেই। 
                                                                    </p>
                                                                    <h3 class="f2" style="font-size:20px;padding: 16px 5px;">ছুটির আবেদন তৈরি করুন </h3>
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
