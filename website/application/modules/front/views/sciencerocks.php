<!--[if lte IE 8]><script src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/assets/js/ie/html5shiv.js'); ?>"></script><![endif]-->
    <link rel="stylesheet" href="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/assets/css/main.css?v=1.4'); ?>">

<!--[if lte IE 8]><link rel="stylesheet" href="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/assets/css/ie8.css'); ?>" /><![endif]-->
<div class="container landing" id="tabContainer" style="width: 100%; min-height: 250px; margin-bottom: -25px;">
   <div id="page-wrapper">
        <!-- Banner -->
                <section id="banner">
                        <div style="min-height: 50px;">
                            <!-- Jssor Slider Begin -->
                            <!-- To move inline styles to css file/block, please specify a class name for each element. --> 
                            <!-- ================================================== -->
                            <div id="slider1_container" style="visibility: hidden; position: relative; margin: 0 auto;
                            top: 0px; left: 0px; width: 1300px; height: 500px; overflow: hidden;">
                                <!-- Loading Screen -->
                                <div u="loading" style="position: absolute; top: 0px; left: 0px;">
                                    <div style="filter: alpha(opacity=70); opacity: 0.7; position: absolute; display: block;
                                    top: 0px; left: 0px; width: 100%; height: 100%;">
                                    </div>
                                    <div style="position: absolute; display: block; background: url(../img/loading.gif) no-repeat center center;
                                    top: 0px; left: 0px; width: 100%; height: 100%;">
                                    </div>
                                </div>
                                <!-- Slides Container -->
                                <div u="slides" style="cursor: move; position: absolute; left: 0px; top: 0px; width: 1300px; height: 500px; overflow: hidden;">
                                    <!--div>
                                        <img u="image" src2="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/home/p1.png'); ?>" />
                                    </div-->
                                    <div>
                                        <img u="image" src2="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/home/p2.png'); ?>" />
                                    </div>
                                    <!--div>
                                        <img u="image" src2="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/home/p3.png'); ?>" />
                                    </div-->
                                </div>

                                <!--#region Bullet Navigator Skin Begin -->
                                <!-- Help: http://www.jssor.com/tutorial/set-bullet-navigator.html -->
                                <style>
                                    /* jssor slider bullet navigator skin 21 css */
                                    /*
                                    .jssorb21 div           (normal)
                                    .jssorb21 div:hover     (normal mouseover)
                                    .jssorb21 .av           (active)
                                    .jssorb21 .av:hover     (active mouseover)
                                    .jssorb21 .dn           (mousedown)
                                    */
                                    .jssorb21 {
                                        position: absolute;
                                    }
                                    .jssorb21 div, .jssorb21 div:hover, .jssorb21 .av {
                                        position: absolute;
                                        /* size of bullet elment */
                                        width: 19px;
                                        height: 19px;
                                        text-align: center;
                                        line-height: 19px;
                                        color: white;
                                        font-size: 12px;
                                        background: url(<?php echo base_url('styles/layouts/tdsfront/sceincerocks/img/b21.png'); ?>) no-repeat;
                                        overflow: hidden;
                                        cursor: pointer;
                                    }
                                    .jssorb21 div { background-position: -5px -5px; }
                                    .jssorb21 div:hover, .jssorb21 .av:hover { background-position: -35px -5px; }
                                    .jssorb21 .av { background-position: -65px -5px; }
                                    .jssorb21 .dn, .jssorb21 .dn:hover { background-position: -95px -5px; }
                                </style>
                                <!-- bullet navigator container -->
                                <div u="navigator" class="jssorb21" style="bottom: 26px; right: 6px;">
                                    <!-- bullet navigator item prototype -->
                                    <div u="prototype"></div>
                                </div>
                                <!--#endregion Bullet Navigator Skin End -->

                                <!--#region Arrow Navigator Skin Begin -->
                                <!-- Help: http://www.jssor.com/tutorial/set-arrow-navigator.html -->
                                <style>
                                    /* jssor slider arrow navigator skin 21 css */
                                    /*
                                    .jssora21l                  (normal)
                                    .jssora21r                  (normal)
                                    .jssora21l:hover            (normal mouseover)
                                    .jssora21r:hover            (normal mouseover)
                                    .jssora21l.jssora21ldn      (mousedown)
                                    .jssora21r.jssora21rdn      (mousedown)
                                    */
                                    .jssora21l, .jssora21r {
                                        display: block;
                                        position: absolute;
                                        /* size of arrow element */
                                        width: 55px;
                                        height: 55px;
                                        cursor: pointer;
                                        background: url(<?php echo base_url('styles/layouts/tdsfront/sceincerocks/img/a21.png'); ?>) center center no-repeat;
                                        overflow: hidden;
                                    }
                                    .jssora21l { background-position: -3px -33px; }
                                    .jssora21r { background-position: -63px -33px; }
                                    .jssora21l:hover { background-position: -123px -33px; }
                                    .jssora21r:hover { background-position: -183px -33px; }
                                    .jssora21l.jssora21ldn { background-position: -243px -33px; }
                                    .jssora21r.jssora21rdn { background-position: -303px -33px; }
                                </style>
                                <!-- Arrow Left -->
                                <span u="arrowleft" class="jssora21l" style="top: 123px; left: 8px;">
                                </span>
                                <!-- Arrow Right -->
                                <span u="arrowright" class="jssora21r" style="top: 123px; right: 8px;">
                                </span>
                                <!--#endregion Arrow Navigator Skin End -->
                                <a style="display: none" href="http://www.jssor.com">Bootstrap Carousel</a>
                            </div>
                            <!-- Jssor Slider End -->
                        </div>
                        
                </section>

        <!-- Main -->
                <section id="main" class="container">

                        <section class="box special section1">
                                <header class="major">
                                        <h2>ABOUT SCIENCE ROCKS</h2>
                                        <p>বিজ্ঞান খুব মজার একটি বিষয়। আমাদের চারপাশে ঘটে যাওয়া দৈনন্দিন নানা ঘটনা বিজ্ঞান দিয়ে খুব সহজেই ব্যাখ্যা করা যায়। যেমন ধরো, রংধনু কিভাবে সৃষ্টি হয়, আকাশ কেন নীল দেখায়, লোহা পানিতে ডুবে গেলেও লোহার তৈরি জাহাজ কেন পানিতে ভাসে ইত্যাদি। এসব জিনিস জানতে পারার পর বিজ্ঞানকে আরও মজাদার বলে মনে হয় এবং নতুন নতুন বিজ্ঞানভিত্তিক জিনিস জানার ইচ্ছা জাগে। 
অথচ বিজ্ঞান নিয়ে অনেকের মনেই ভয়ভীতি থাকে। জটিল সব সূত্র, গাণিতিক ব্যাখ্যা আর কঠিন সব শব্দ শুনলেই কেমন যেন ভয় ভয় লাগে।  একটু বোঝার চেষ্টা করলেই বিজ্ঞান তোমাদের কাছে খুব সহজেই ধরা দেবে। আর তোমাদের জন্য সে কাজটিই করছে ‘Science Rocks’। 
<br /><br />
                                        <span class="more-content" style="display:none;">
                                        জটিল সব ঘটনাকে বৈজ্ঞানিক ব্যাখ্যা দিয়ে সহজভাবে তোমাদের কাছে তুলে ধরাই এ অনুষ্ঠানের উদ্দেশ্য। প্রতি সপ্তাহে ২টি করে ৫২ সপ্তাহে মোট ১০৪টি মজার মজার বিজ্ঞানের পরীক্ষা দেখানো হবে তোমাদের আর বলে দেয়া হবে সেটা কেন হলো, কিভাবে হলো। ‘সায়েন্স রকস’ দেখতে চোখ রাখো চ্যানেল আই-এর পর্দায় প্রতি শুক্রবার সকাল ১১টায়। তবে কোন পর্ব দেখতে না পারলে সেটা পরে দেখে নিতে পারো চ্যাম্পস টোয়েন্টিওয়ান ডট কম ওয়েবসাইটে।  <br /><br />
                                        সায়েন্স রকস নিয়ে তৈরি করা হয়েছে একটি অ্যান্ড্রয়েড অ্যাপসও। বিজ্ঞান সংক্রান্ত অনেক মজার বিষয় জানা যাবে ও এমনকি প্রতিটি পর্বের ভিডিও দেখা যাবে এই অ্যাপ থেকে। বিজ্ঞানের মজার মজার সব ব্যাপার নিয়ে প্রতিদিন থাকছে একটি করে ‘Daily Doze’।<br /><br />
                                        এছাড়া, প্রতি পর্বে তোমাদের জন্য থাকছে কুইজ যাতে অংশগ্রহণ করে জিতে নিতে পারো দারুণ সব পুরষ্কার। 
                                        </span></p>
                                </header>
                                <ul class="actions">
                                        <li><a href="javascript:void(0);" class="button special read-more waves-effect">READ MORE</a></li>							
                                </ul>
                                <!--span class="image featured"><img src="images/pic01.jpg" alt="" /></span-->
                        </section>
                        <section class="special features">
                                <div class="features-row">
                                        
                                        <div class="box alt">
                                                <div class="row no-collapse 50% uniform">
                                                    <div class="4u 12u(narrower)">
                                                        <section>
                                                                <span class="image fit" style="margin-bottom:20px;"><a href="https://play.google.com/store/apps/details?id=com.champs21.sciencerocks&hl=en"><img src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/goodnews_app.png'); ?>" alt="" /></a></span>
                                                        </section>
                                                        <section>
                                                                <span class="image fit" style="margin-bottom:0px;"><a href="<?php echo base_url('/videos/science-rocks'); ?>"><img src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/episode.png'); ?>" alt="" /></a></span>
                                                        </section>
                                                    </div>
                                                    <div class="8u 12u(narrower) daily-doz" style="height:auto">
                                                        <div style="width:100%;background:#fff;height:87%;">
                                                            <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">                                                               

                                                                <!-- Wrapper for slides -->
                                                                <div class="carousel-inner" style="height:100%;">
                                                                  <?php //echo "<pre>";print_r($daily_doz);?>
                                                                  <?php $i = 0; foreach ($daily_doz as $doz){ ?>
                                                                    <div class="item <?php if($i==0){echo "active";}?>">
                                                                        <div class="row no-collapse 50% uniform">
                                                                            
                                                                            <div class="6u">
                                                                                <div style="height:90%;">
                                                                                    <?php echo $doz->content;?>
                                                                                    <?php //echo $doz->id;?>
                                                                                </div>
                                                                            </div>
                                                                            <div class="6u">
                                                                                
                                                                                <div style="height:60%;margin:15%;">
                                                                                    <span class="image fit" style="margin-bottom:20px;"><img style="width:100%;" src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/DailyDOze.png'); ?>" alt="" /></span>
                                                                                </div>
                                                                                    
                                                                            </div>                                                            
                                                                                                                                  
                                                                        </div> 
                                                                    </div>
                                                                  <?php $i++; } ?>
                                                                </div>

                                                                
                                                                <div style="width:100%;height:70px;border-top:1px solid gray;position: absolute;bottom: 0%;">
                                                                    <!-- Controls -->
                                                                    <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                                                                        < Previous
                                                                    </a>
                                                                    <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                                                                      Next >
                                                                    </a>
                                                                </div>
                                                              </div> <!-- Carousel -->                                                           
                                                        </div>
                                                        
                                                    </div>
                                                        
                                                </div>
                                        </div>
                                </div>
                                

                                
                        </section>
                        <!--section class="special features">
                                <div class="features-row">
                                        
                                        <div class="box alt">
                                                <div class="row no-collapse 50% uniform">
                                                        <div class="4u"><span class="image fit"><a href="#"><img src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/goodnews_app.png'); ?>" alt="" /></a></span></div>
                                                        <div class="4u"><span class="image fit"><a href="#"><img src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/episode.png'); ?>" alt="" /></a></span></div>
                                                        <div class="4u"><span class="image fit"><a href="javascript:void(0);"  data="candle" class="daily_doz"><img src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/dailydoz.png'); ?>" alt="" /></a></span></div>
                                                </div>
                                        </div>
                                
                                
                               
                                    
                                
                            </div>
                        </section-->



                </section>
                <section class="box special features"  style='overflow:hidden; padding-top: 0;'>
                    <header class="major">
                        <h2>ROCKING EXPERIMENTS</h2>
                    </header>
                        
                    <div class="wrapper_carousel wrapper_carousel--demo">
                        <div class="carousel">
                          <div class="carousel__content">
                            <?php foreach ($experiment as $exp){ ?>
                              <div class="item"><div style="width:90%;text-align:center;margin:0px auto;">
                              <a href="<?php echo $exp['ci_key']; ?>"><p class="title"><?php echo $exp['headline']; ?></p>
                              <img src="<?php echo $exp['lead_material']; ?>" style="width:600px;height:400px;" alt=""></a> </div></div>
                            <?php } ?>
                          </div>
                          <div class="carousel__nav"> 
                              <a href="javascript:void(0)" class="nav nav--left">Previous</a> 
                              <a href="javascript:void(0)" class="nav nav--right">Next</a>
                               <a href="javascript:void(0)" class="nav">View All Experiment</a>
                          </div>
                        </div>
                      </div>
                </section>

                <!--section id="cta" class="weekly">

                        <h2>Sign up for beta access</h2>
                        <p>Blandit varius ut praesent nascetur eu penatibus nisi risus faucibus nunc.</p>

                        <div class="clearfix"></div>

                        <div class="suggest-header-div">
                            <div class="f2">suggested for you</div>
                        </div>

                        <div id="demo2"> 
                            <?php echo "<pre>";print_r($experiment);?>
                        </div>
                </section-->
                <section class="container" style='margin-top: 0;'>
                    <header class="major">
                                    <h2>MEET THE ANCHORS</h2>
                    </header>
                    <div class="row ">
                        <div class="6u 12u(narrower)">
                            <section class="box special anchor" style="overflow: hidden;    position: relative;padding:0px;">
                                <span class="image featured" style="margin:0px;width: 100%;"><img src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/man.png'); ?>" alt="" /></span>
                                <div class="caption" style="border: 0 none;bottom:-75%;padding: 20px;position: absolute;color:#fff !important;background-color:#29A49F;">
                                        <h3 style="color:#fff !important;margin-bottom:40px;">আফনান শাহ কুরেশী</h3>
                                        <p>বিজ্ঞানের প্রতি আফনানের আকর্ষণ ছোটবেলা থেকেই। ক্লাস সিক্সে পড়ার সময় আফনানের শিক্ষকের কাছ থেকে সে বিজ্ঞান নিয়ে জানার অনুপ্রেরণা প্রায়। আর বিজ্ঞানের এই যাত্রাটা শুরু হয় রসায়ন দিয়ে। রসায়নের জটিল সব বিক্রিয়া আর অণু-পরমাণুর গঠন তার কাছে হয়ে উঠে মজার বিষয়। ধীরে ধীরে পদার্থেও তার আকর্ষণ বাড়ে। আর গণিতও ছিল তার খুব পছন্দের বিষয়।</p>
                                        <p>আফনানের মতে বিজ্ঞান আমাদের দৈনন্দিন জীবনেরই একটি অংশ। আর বিজ্ঞানকে বুঝতে পারলে আমরা আমাদের দৈনন্দিন জীবন আর চারপাশের অনেক ঘটনাবলীকেই খুব সহজে বুঝতে এবং ব্যাখ্যা করতে পারবো। এ কারণেই বিজ্ঞানের প্রতি আফনানের আগ্রহটা একটু বেশি।</p>
                                        <p>আফনানের পুরো নাম আফনান শাহ কুরেশী। আগা খান স্কুল থেকে ও’লেভেল আর এ’লেভেল শেষ করে বি.এস.সি করেছেন বুয়েটের কম্পিউটার সায়েন্স এন্ড ইঞ্জিনিয়ারিং ডিপার্টমেন্ট থেকে। নটরডেম কলেজ আয়োজিত পদার্থবিজ্ঞান অলিম্পিয়াডে হয়েছিলেন দ্বিতীয়। </p>
                                        <p>তবে বিজ্ঞান নিয়ে নাড়াচাড়া করলেও আফনান কিন্তু মোটেও নিরস বা কাঠখোট্টা নয়। সে বেশ সংস্কৃতিমনা। আফনান খুব ভালো পিয়ানো বাজায় এবং গজল গায়। আফনান বর্তমানে একটি স্কুলে শিক্ষকতা করছে। </p>

                                </div>
                            </section>					

                        </div>
                        <div class="6u 12u(narrower)">

                                <section class="box special anchor" style="overflow: hidden;    position: relative;padding:0px;">
                                        <span class="image featured" style="margin:0px;width: 100%;"><img src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/woman.png'); ?>" alt="" /></span>
                                        <div class="caption" style="border: 0 none;bottom:-75%;padding: 20px;position: absolute;color:#fff !important;background-color:#29A49F;">
                                                <h3 style="color:#fff !important;margin-bottom:40px;">সানজিদা চৌধুরী স্বর্ণা 
                                                    </h3>
                                                <p>আফনানের ঠিক বিপরীত হচ্ছে স্বর্ণা। ছোটবেলা থেকেই বিজ্ঞানকে তার দারুণ ভয়। এমনকি বিজ্ঞানের নাম শুনলেই তার হাত-পা কাঁপা শুরু হয়ে যেতো। বিজ্ঞানের কথা শুনলেই স্বর্ণার যেন দাঁত ভাঙার যোগার। পারতপক্ষে তাই বিজ্ঞানের ধারে কাছে ঘেঁষত না স্বর্ণা। কিন্তু ক্লাস নাইনে সায়েন্স গ্রুপে ভর্তি হওয়ার পর থেকে আসল বিপদ শুরু হলো। বিজ্ঞানের জটিল সব ব্যাখ্যা আর কাঠখোট্টা সব শব্দ শুনেই তার মাথা ঘুরে যেতো। পদার্থবিজ্ঞান, রসায়নবিজ্ঞান, গণিত সবকিছুই ছিল তার কাছে দুরূহ। বিজ্ঞানের কোন পড়াই তার কাছে ভালো লাগতো না। </p>
                                                <p>স্বর্ণাকে এ বিপদ থেকে উদ্ধার করলো তার আফনান ভাইয়া। স্বর্ণাকে বোঝাল যে একটু চেষ্টা করলেই বিজ্ঞানকে খুব সহজেই বুঝতে পারা যায়। আর এজন্যই তারা বিজ্ঞান নিয়ে শুরু করলো নানা ধরণের এক্সপেরিমেন্ট। আফনানের উৎসাহে ধীরে ধীরে বিজ্ঞানের প্রতি আগ্রহ বাড়তে থাকে স্বর্ণার।  </p>
                                                <p>সানজিদা চৌধুরী স্বর্ণা পড়াশোনা করছে অর্থনীতি নিয়ে ব্র্যাক ইউনিভার্সিটিতে। এর আগে মোহাম্মাদপুর প্রিপারেটরী হায়ার সেকেন্ডারি স্কুল থেকে থেকে করেছে এসএসসি ও এইচএসসি। সেই সাথে ছায়ানটে পড়াশোনা করেছে গান নিয়ে। স্বর্ণা খুব ভালো রবীন্দ্র সংগীত গায়। জাতীয় পর্যায়ে গান গেয়ে অনেক পুরষ্কারও অর্জন করেছে সে।  </p>
                                                <p></p>

                                        </div>
                                </section>

                        </div>
                    </div>
                </section>
                <section class="container" style="height:auto;margin-top: 0;">
                        <header class="major">
                                        <h2>PHOTO GALLERY</h2>
                        </header>
                        <div class="row ">
                        <div class="col-md-12">
                        <div class="tab-content">
                        <div class="tab-pane fade in active" id="screens">
                                <div class="scroller app-gallery light-color">
                                        <div class="item">
                                          <a href="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/gallery/m/18.JPG'); ?>" class="waves-effect">
                                                <img src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/gallery/s/18.JPG'); ?>" alt="Thumbnail">
                                          </a>
                                          <a href="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/gallery/m/1.JPG'); ?>" class="waves-effect">
                                                <img src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/gallery/s/1.JPG'); ?>" alt="Thumbnail">
                                          </a>
                                            
                                        </div>
                                    
                                        <div class="item">
                                          <a href="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/gallery/m/7.JPG'); ?>" class="waves-effect">
                                                <img src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/gallery/s/7.JPG'); ?>" alt="Thumbnail">
                                          </a>
                                          <a href="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/gallery/m/12.JPG'); ?>" class="waves-effect">
                                                <img src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/gallery/s/12.JPG'); ?>" alt="Thumbnail">
                                          </a>
                                          <a href="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/gallery/m/14.JPG'); ?>" class="waves-effect">
                                                <img src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/gallery/s/14.JPG'); ?>" alt="Thumbnail">
                                          </a>
                                        </div>
                                        <div class="item">
                                          <a href="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/gallery/m/15.JPG'); ?>" class="waves-effect">
                                                <img src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/gallery/s/15.JPG'); ?>" alt="Thumbnail">
                                          </a>
                                          <a href="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/gallery/m/16.JPG'); ?>" class="waves-effect">
                                                <img src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/gallery/s/16.JPG'); ?>" alt="Thumbnail">
                                          </a>
                                        </div>
                                        <div class="item">
                                          <a href="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/gallery/m/17.JPG'); ?>" class="waves-effect">
                                                <img src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/gallery/s/17.JPG'); ?>" alt="Thumbnail">
                                          </a>
                                          <a href="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/gallery/m/19.JPG'); ?>" class="waves-effect">
                                                <img src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/gallery/s/19.JPG'); ?>" alt="Thumbnail">
                                          </a>
                                          <a href="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/gallery/m/20.JPG'); ?>" class="waves-effect">
                                                <img src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/gallery/s/20.JPG'); ?>" alt="Thumbnail">
                                          </a>
                                        </div>
                                </div>
                        </div>
                        </div>
                        </div>
                        </div>
                </section>
        <!-- CTA -->
                <!--section class="container" style='margin-top: 0;'>
                    <div class="row ">
                        <div class="col-md-12">
                            <div class="winners">
                                <h2 style="color:#fff;">WINNERS</h2>
                                 <div class="row no-collapse 50% uniform">
                                     <div class="4u">
                                         <div style="margin-top:100px;font-weight:bold;margin-left:160px;">
                                            <p style="font-size:30px;">Md Sujon Bhuiyan</p>
                                            <p style="font-size:20px;">Student</p>
                                            <p style="font-size:20px;">Dhaka</p>
                                        </div>
                                     </div>
                                     <div class="4u">
                                         <div style="margin-top:100px;font-weight:bold;">
                                        <p style="font-size:30px;">Omur Faruk Jayed</p>
                                        <p style="font-size:20px;">Student</p>
                                        <p style="font-size:20px;">Noakhali</p>
                                        </div>
                                     </div>
                                     <div class="4u">
                                         <div style="margin-top:100px;font-weight:bold;">
                                        <p style="font-size:30px;">Saraban Tohura Lima</p>
                                        <p style="font-size:20px;">Student</p>
                                        <p style="font-size:20px;">Jessore</p>
                                        </div>
                                     </div>
                                        
                                    							
                                
                            </div>
                        </div>
                    </div>    
                </section-->
                <section id="cta" class="partners">

                        <h2>PARTNERS</h2>
                        <div class="row ">
                                <div class="col-md-12">
                                        <ul class="icons">
                                                <li><img src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/Partner_doze.png'); ?>" width="70%" alt="" /></li>

                                                <li><img src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/Partner_channel_i.png'); ?>" width="85%" alt="" /></li>	
                                                
                                                <li><img style=" margin-left: 45px;" src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/images/Partner_CatsEye.jpg'); ?>" width="100" alt="" /></li>							
                                        </ul>
                                </div>
                        </div>

                </section>
</div>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
</div><!--END OF CLASS container-->
<!--script src="<?php //echo base_url('styles/layouts/tdsfront/sceincerocks/assets/js/jquery-2.1.3.min.js'); ?>assets/js/jquery-2.1.3.min.js"></script>
<script src="<?php //echo base_url('styles/layouts/tdsfront/sceincerocks/assets/js/bootstrap.min.js'); ?>"></script-->
<script src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/assets/js/jquery.dropotron.min.js'); ?>"></script>
<script src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/assets/js/jquery.scrollgress.min.js'); ?>"></script>
<script src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/assets/js/skel.min.js'); ?>"></script>
<script src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/assets/js/util.js'); ?>"></script>
<!--[if lte IE 8]><script src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/assets/js/ie/respond.min.js'); ?>"></script><![endif]-->
<script src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/assets/js/main.js'); ?>"></script>

<script src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/assets/js/jquery.magnific-popup.min.js'); ?>"></script>
<script src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/assets/js/jquery.mCustomScrollbar.min.js'); ?>"></script>
<script src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/assets/js/waves.min.js'); ?>"></script>
<script src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/assets/js/jquery.transit.min.js'); ?>"></script>
<script src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/assets/js/jQuery.Infinite.Carousel.js?v=1'); ?>"></script>
<script src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/assets/js/jquery.tinycarousel.min.js'); ?>"></script>
<script src="<?php echo base_url('styles/layouts/tdsfront/sceincerocks/assets/js/jssor.slider.mini.js'); ?>"></script>

<script>
    (function($) {
            $( ".section1 .read-more" ).click(function() {
                    $( ".section1 span.more-content" ).show();
                    $( ".section1 .read-more" ).hide();
            });
            /** App Gallery
            *********************************************************/
            if($('.app-gallery a').length > 0) {
                    $('.app-gallery a').magnificPopup({ 
                      type: 'image',
                      gallery:{
                            enabled:true
                      },
                            removalDelay: 300,
                      mainClass: 'mfp-fade'
                    });
            }
            /** Custom Horizontal Scrollbar for Gallery/Blog (Home Page)
            **************************************************************/
            $(window).load(function(){
                    $('.scroller').mCustomScrollbar({
                                    axis:"x",
                                    theme:"dark",
                                    scrollInertia: 300,
                                    advanced:{autoExpandHorizontalScroll:true}
                    });
            });
        /** Waves Effect (on Buttons)
            *******************************************/
            Waves.displayEffect({duration: 600});


            //Caption Sliding (Partially Hidden to Visible)
            $('.anchor').hover(function(){
                    $(".caption", this).stop().animate({bottom:'0%'},{queue:false,duration:160});
            }, function() {
                    $(".caption", this).stop().animate({bottom:'-75%'},{queue:false,duration:160});
            });
            
            //$('#slider1').tinycarousel({ interval: true });
    })(jQuery);
</script>

<script>
        jQuery(document).ready(function ($) {

            var options = {
                $FillMode: 2,                                       //[Optional] The way to fill image in slide, 0 stretch, 1 contain (keep aspect ratio and put all inside slide), 2 cover (keep aspect ratio and cover whole slide), 4 actual size, 5 contain for large image, actual size for small image, default value is 0
                $AutoPlay: true,                                    //[Optional] Whether to auto play, to enable slideshow, this option must be set to true, default value is false
                $Idle: 4000,                            //[Optional] Interval (in milliseconds) to go for next slide since the previous stopped if the slider is auto playing, default value is 3000
                $PauseOnHover: 1,                                   //[Optional] Whether to pause when mouse over if a slider is auto playing, 0 no pause, 1 pause for desktop, 2 pause for touch device, 3 pause for desktop and touch device, 4 freeze for desktop, 8 freeze for touch device, 12 freeze for desktop and touch device, default value is 1

                $ArrowKeyNavigation: true,   			            //[Optional] Allows keyboard (arrow key) navigation or not, default value is false
                $SlideEasing: $JssorEasing$.$EaseOutQuint,          //[Optional] Specifies easing for right to left animation, default value is $JssorEasing$.$EaseOutQuad
                $SlideDuration: 800,                               //[Optional] Specifies default duration (swipe) for slide in milliseconds, default value is 500
                $MinDragOffsetToSlide: 20,                          //[Optional] Minimum drag offset to trigger slide , default value is 20
                //$SlideWidth: 600,                                 //[Optional] Width of every slide in pixels, default value is width of 'slides' container
                //$SlideHeight: 300,                                //[Optional] Height of every slide in pixels, default value is height of 'slides' container
                $SlideSpacing: 0, 					                //[Optional] Space between each slide in pixels, default value is 0
                $Cols: 1,                                  //[Optional] Number of pieces to display (the slideshow would be disabled if the value is set to greater than 1), the default value is 1
                $ParkingPosition: 0,                                //[Optional] The offset position to park slide (this options applys only when slideshow disabled), default value is 0.
                $UISearchMode: 1,                                   //[Optional] The way (0 parellel, 1 recursive, default value is 1) to search UI components (slides container, loading screen, navigator container, arrow navigator container, thumbnail navigator container etc).
                $PlayOrientation: 1,                                //[Optional] Orientation to play slide (for auto play, navigation), 1 horizental, 2 vertical, 5 horizental reverse, 6 vertical reverse, default value is 1
                $DragOrientation: 1,                                //[Optional] Orientation to drag slide, 0 no drag, 1 horizental, 2 vertical, 3 either, default value is 1 (Note that the $DragOrientation should be the same as $PlayOrientation when $Cols is greater than 1, or parking position is not 0)
              
                $BulletNavigatorOptions: {                          //[Optional] Options to specify and enable navigator or not
                    $Class: $JssorBulletNavigator$,                 //[Required] Class to create navigator instance
                    $ChanceToShow: 2,                               //[Required] 0 Never, 1 Mouse Over, 2 Always
                    $AutoCenter: 1,                                 //[Optional] Auto center navigator in parent container, 0 None, 1 Horizontal, 2 Vertical, 3 Both, default value is 0
                    $Steps: 1,                                      //[Optional] Steps to go for each navigation request, default value is 1
                    $Rows: 1,                                      //[Optional] Specify lanes to arrange items, default value is 1
                    $SpacingX: 8,                                   //[Optional] Horizontal space between each item in pixel, default value is 0
                    $SpacingY: 8,                                   //[Optional] Vertical space between each item in pixel, default value is 0
                    $Orientation: 1,                                //[Optional] The orientation of the navigator, 1 horizontal, 2 vertical, default value is 1
                    $Scale: false                                   //Scales bullets navigator or not while slider scale
                },

                $ArrowNavigatorOptions: {                           //[Optional] Options to specify and enable arrow navigator or not
                    $Class: $JssorArrowNavigator$,                  //[Requried] Class to create arrow navigator instance
                    $ChanceToShow: 1,                               //[Required] 0 Never, 1 Mouse Over, 2 Always
                    $AutoCenter: 2,                                 //[Optional] Auto center arrows in parent container, 0 No, 1 Horizontal, 2 Vertical, 3 Both, default value is 0
                    $Steps: 1                                       //[Optional] Steps to go for each navigation request, default value is 1
                }
            };

            var jssor_slider1 = new $JssorSlider$("slider1_container", options);

            //responsive code begin
            //you can remove responsive code if you don't want the slider scales while window resizing
            function ScaleSlider() {
                var bodyWidth = document.body.clientWidth;
                if (bodyWidth)
                    jssor_slider1.$ScaleWidth(Math.min(bodyWidth, 1920));
                else
                    window.setTimeout(ScaleSlider, 30);
            }
            ScaleSlider();

            $(window).bind("load", ScaleSlider);
            $(window).bind("resize", ScaleSlider);
            $(window).bind("orientationchange", ScaleSlider);
            //responsive code end
        });
    </script>
