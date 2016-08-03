<div class="container" style="width: 77%; min-height:250px;">
    <div style="padding: 0px 22px 0 35px;" class="sports-inner-news yesPrint">    
        <div style="float:left;">
            <h1 style="color:#93989C;" class="title noPrint f2">
                &nbsp;
            </h1>
        </div>
        <div style="clear:both;"></div>
    </div>
    
    <div class="contact-us-wrapper">
        
        <div class="contact-content-wrapper">
            
            <div class="title-wrapper">
                <h1 class="f2">CONTACT US</h1>
            </div>
            
            <?php echo form_open(base_url('contact-us'), array('class' => 'validate_form', 'id' => 'frm_contact_us', 'autocomplete' => TRUE)); ?>
            
            <div class="text-fields-wrapper">

                <div class="col-lg-6">
                    <input id="full_name" class="name-and-email" type="text" name="full_name" value="" placeholder="Full Name" />
                </div>

                <div class="col-lg-6">
                    <input id="email" class="name-and-email" type="text" name="email" value="" placeholder="E-mail" />
                </div>

            </div>

            <div class="body-wrapper">

                <div class="col-lg-7 left-panel">

                    <div class="title-wrapper">
                        <h4 class="f2">What you want to contact us about:</h4>
                    </div>

                    <div class="clearfix"></div>

                    <div class="ques-list-wrapper">

                        <ul>
                            <li>
                                <div>
                                    <input type="radio" name="contact_type" id="ask_ques" value="question" checked="checked" />
                                    <label for="ask_ques">I want to ask you a question</label>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <input type="radio" name="contact_type" id="ask_put_ad" value="advertisement" />
                                    <label for="ask_put_ad">I want to put an ad on your site</label>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <input type="radio" name="contact_type" id="ask_buis_oppor" value="business_opportunity" />
                                    <label for="ask_buis_oppor">I want to find business opportunity with you</label>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <input type="radio" name="contact_type" id="ask_others" value="other" />
                                    <label for="ask_others">Other</label>
                                </div>
                            </li>
                        </ul>

                    </div>

                    <div class="contact-message-wrapper">
                        <textarea name="ques_description" placeholder="Describe your thoughts..."></textarea>
                    </div>

                    <div class="contact-message-btn-wrapper">
                        <button class="contact-submit" type="submit">
                            <span class="clearfix f2">Submit</span>
                        </button>
                    </div>

            <?php echo form_close(); ?>
                    
                    <div class="clearfix"></div>
                    
                    <div class="contact-candle-wrapper">
                        
                        <div class="candle-icon-wrapper">
                            <img src="/styles/layouts/tdsfront/image/candle_red_icon.png" />
                        </div>
                        <div class="clearfix"></div>
                        
                        <div class="candle-text-wrapper">
                            <div>You can also Candle us to publish your articles.</div>
                            <div><a data="candle" class="<?php echo ( free_user_logged_in() ) ? 'candlepopup' : 'before-login-user'; ?>">Click here to Candle</a></div>
                        </div>
                        
                        
                    </div>
                    
                </div>
                
                <div class="col-lg-5 right-panel">
                    
                    <div class="title-wrapper">
                        <h4 class="f2">Address &amp; Direction:</h4>
                    </div>
                    
                    <div class="clearfix"></div>
                    
                    <div class="company-name-wrapper">
                        <h6><a href="<?php echo base_url(); ?>">Champs21.com</a></h6>
                    </div>
                    
                    <div class="company-address-wrapper">
                        <div>House 54 (5th Floor)</div>
                        <div>Road 10, Block E</div>
                        <div>Banani, Dhaka - 1213</div>
                    </div>
                    
                    <div class="clearfix"></div>
                    <div>&nbsp;</div>
                    
                    <div class="company-address-wrapper">
                        <div>+880-2-9891367-8</div>
                        <div>+880-9612212121-2</div>
                    </div>
                    
                    <div class="clearfix"></div>
                    
                    <div class="contact-map-wrapper">
                        <div id="map_canvas"></div>
                    </div>
                    
                    <div class="clearfix"></div>
                    
                    <div class="title-wrapper">
                        <h5 class="f2">Find Us on</h5>
                    </div>
                    <div>&nbsp;</div>
                    
                    <div class="clearfix"></div>
                    
                    <div class="contact-sns-bar">
                        <ul>
                            <li class="cnt-fb-li">
                                <a href="#"><img src="/styles/layouts/tdsfront/image/cnt_facebook.png" /></a>
                            </li>
                            <li class="cnt-gl-li">
                                <a href="#"><img src="/styles/layouts/tdsfront/image/cnt_g_plus.png" /></a>
                            </li>
                            <li class="cnt-tw-li">
                                <a href="#"><img src="/styles/layouts/tdsfront/image/cnt_twitter.png" /></a>
                            </li>
                            <li class="cnt-ln-li">
                                <a href="#"><img src="/styles/layouts/tdsfront/image/cnt_linked_in.png" /></a>
                            </li>
                            <li class="cnt-yt-li">
                                <a href="#"><img src="/styles/layouts/tdsfront/image/cnt_you_tube.png" /></a>
                            </li>
                            <li class="cnt-pn-li">
                                <a href="#"><img src="/styles/layouts/tdsfront/image/cnt_pinterst.png" /></a>
                            </li>
                            <li class="cnt-rs-li">
                                <a href="#"><img src="/styles/layouts/tdsfront/image/cnt_rss.png" /></a>
                            </li>
                        </ul>
                    </div>
                    
                </div>
                
            </div>
            
        </div>
        
        
    </div>
    
</div>

<style type="text/css">
    .contact-us-wrapper
    {
        background-color: #ffffff;
        margin: 0 20px 80px;
        padding:20px;
        min-height: 1050px;
    }
    .contact-content-wrapper{
        padding: 50px;
    }
    .contact-content-wrapper .title-wrapper h1, h5, h4{
        color: #000000;
    }
    .contact-content-wrapper .text-fields-wrapper {
        padding-top: 35px;
    }
    .name-and-email{
        color: #000000 !important;
        font-size: 20px !important;
        height: 45px !important;
        padding-left: 30px !important;
        width: 98%;
    }
    .contact-content-wrapper .body-wrapper{
        padding-top: 100px;
    }
    .left-panel{
        border-right: 1px solid #ddd;
        padding: 15px 40px 60px 0px;
    }
    .right-panel{
        padding: 15px 0px 15px 40px;
    }
    .ques-list-wrapper ul {
        position: relative;
        width: 100%;
        display: block;
    }
    .ques-list-wrapper ul li {
        cursor: pointer;
        line-height: 0px;
    }
    .ques-list-wrapper ul li div input[type="radio"] {
        width: 15px;
    }
    .ques-list-wrapper ul li div input[type="radio"]:hover {
        cursor: pointer;
    }
    .ques-list-wrapper ul li div label{
        padding-left: 15px;
    }
    .ques-list-wrapper ul li div label:hover{
        cursor: pointer;
    }
    .contact-message-wrapper{
        padding-top: 20px;
    }
    .contact-message-wrapper textarea{
        color: #000000;
        font-size: 18px;
        width: 95%;
        height: auto;
        min-height: 200px;
    }
    .contact-message-btn-wrapper{
        float: right;
        padding: 20px 22px 0 0;
        text-align: right;
        width: 150px;
    }
    .contact-submit{
        background-color: #d9d9d9;
        border: medium none;
        box-shadow: 0 5px 0 0 #a3a3a3;
        -moz-box-shadow: 0 5px 0 0 #a3a3a3;
        -webkit-box-shadow: 0 5px 0 0 #a3a3a3;
        -ms-box-shadow: 0 5px 0 0 #a3a3a3;
        -o-box-shadow: 0 5px 0 0 #a3a3a3;
        color: #000000;
        font-size: 25px;
        height: 50px;
        width: 95%;
        
        -webkit-transition: background-color 0.5s ease;
        -moz-transition: background-color 0.5s ease;
        -o-transition: background-color 0.5s ease;
        -ms-transition: background-color 0.5s ease;
        transition: background-color 0.5s ease;
    }
    .contact-submit:hover {
        background-color: #bbb;
    }
    .contact-candle-wrapper{
        padding: 20px 0px;
    }
    .contact-candle-wrapper img{
        height: 60px;
    }
    .candle-text-wrapper{
        color: #727272;
        font-size: 14px;
        padding: 10px 0px 0px 0px;
    }
    .company-name-wrapper{
        padding-top: 20px;
    }
    .company-name-wrapper h6 a{
        font-size: 20px;
        font-weight: bold;
        color: #D83435;
    }
    .company-address-wrapper {
        padding-top: 5px;
    }
    .company-address-wrapper div {
        color: #727272;
        font-size: 14px;
        line-height: 18px;
    }
    .contact-map-wrapper{
        padding: 50px 0px;
    }
    #map_canvas {
        width: 320px;
        height: 200px;
    }
    .contact-sns-bar ul{
        padding: 0px;
        margin: 0px;
    }
    .contact-sns-bar ul li {
        border-radius: 48px;
        -moz-border-radius: 48px;
        -webkit-border-radius: 48px;
        -ms-border-radius: 48px;
        -o-border-radius: 48px;
        display: inline-block;
        padding: 5px;
        text-align: center;
        width: 35px;
        cursor: pointer;
    }
    .contact-sns-bar ul li:hover {
        background-color: #B1B8BA;
        -webkit-transition: background-color 0.5s ease;
        -moz-transition: background-color 0.5s ease;
        -o-transition: background-color 0.5s ease;
        -ms-transition: background-color 0.5s ease;
        transition: background-color 0.5s ease;
    }
    .contact-sns-bar ul li img{
        height: 25px;
        padding: 2px;
    }
    .cnt-fb-li{
        background-color: #4C70A4;
    }
    .cnt-tw-li{
        background-color: #4ED8FF;
    }
    .cnt-gl-li{
        background-color: #E72E35;
    }
    .cnt-ln-li{
        background-color: #0278A8;
    }
    .cnt-rs-li{
        background-color: #FF6300;
    }
    .cnt-pn-li{
        background-color: #D20000;
    }
    .cnt-yt-li{
        background-color: #FE102A;
    }
    
    @media all and (min-width: 319px) and (max-width: 480px){
        .contact-content-wrapper {
            padding: 20px !important;
        }
        .contact-content-wrapper .text-fields-wrapper {
            padding-top: 10px;
        }
        .contact-us-wrapper{
            padding: 0 20px;
        }
        .name-and-email {
            color: #000000 !important;
            font-size: 15px !important;
            height: 45px !important;
            padding-left: 25px !important;
            margin: 9px 0;
        }
        .ques-list-wrapper{
            width: 100%;
        }
        .ques-list-wrapper ul{
            padding: 0;
            margin: 0;
        }
        .ques-list-wrapper ul li{
            line-height: 1.5;
        }
        .ques-list-wrapper ul li div {
            text-align: left top;
        }
        .ques-list-wrapper ul li div input[type="radio"] {
            float: left;
            width: 10px;
        }
        .ques-list-wrapper ul li div label{
            font-size: 13px;
            padding: 0 0 0 7px;
            width: 85%;
        }
        .contact-message-wrapper {
            padding-top: 0;
        }
        .contact-message-wrapper textarea {
            color: #000000;
            font-size: 14px;
            height: auto;
            min-height: 200px;
            width: 100%;
        }
        .contact-message-btn-wrapper {
            float: right;
            padding: 20px 0 0;
            text-align: right;
            width: 120px;
        }
        .contact-submit {
            background-color: #d9d9d9;
            border: medium none;
            box-shadow: 0 5px 0 0 #a3a3a3;
            color: #000000;
            font-size: 18px;
            height: 36px;
            transition: background-color 0.5s ease 0s;
            width: 80%;
        }
        .contact-candle-wrapper {
            padding: 50px 0 0;
        }
        .left-panel {
            border-right: none;
            padding: 15px 0 30px;
        }
        .title-wrapper h1{
            font-size: 30px;
        }
        .contact-content-wrapper .body-wrapper {
            padding-top: 0px;
        }
        .contact-content-wrapper .title-wrapper h4 {
            font-size: 18px;
        }
        .contact-content-wrapper {
            padding: 30px 50px;
        }
        .right-panel {
            padding: 15px 0;
        }
        .company-name-wrapper {
            padding-top: 0;
        }
        .contact-map-wrapper {
            padding: 30px 0;
        }
        #map_canvas {
            height: 170px;
            width: 100%;
        }
        .contact-sns-bar ul li {
            border-radius: 30px;
            cursor: pointer;
            display: inline-block;
            padding: 3px;
            text-align: center;
            width: 27px;
        }
        .contact-sns-bar ul li img {
            height: 20px;
            padding: 3px;
        }
    }
    
    @media all and (min-width: 481px) and (max-width: 800px){
        .contact-content-wrapper {
            padding: 20px !important;
        }
        .contact-content-wrapper .text-fields-wrapper {
            padding-top: 10px;
        }
        .contact-us-wrapper{
            padding: 0 20px;
        }
        .name-and-email {
            color: #000000 !important;
            font-size: 15px !important;
            height: 45px !important;
            padding-left: 25px !important;
            margin: 9px 0;
        }
        .ques-list-wrapper{
            width: 100%;
        }
        .ques-list-wrapper ul{
            padding: 0;
            margin: 0;
        }
        .ques-list-wrapper ul li{
            line-height: 1.5;
        }
        .ques-list-wrapper ul li div {
            text-align: left top;
        }
        .ques-list-wrapper ul li div input[type="radio"] {
            float: left;
            width: 10px;
        }
        .ques-list-wrapper ul li div label{
            font-size: 13px;
            padding: 0 0 0 7px;
            width: 85%;
        }
        .contact-message-wrapper {
            padding-top: 0;
        }
        .contact-message-wrapper textarea {
            color: #000000;
            font-size: 14px;
            height: auto;
            min-height: 200px;
            width: 100%;
        }
        .contact-message-btn-wrapper {
            float: right;
            padding: 20px 0 0;
            text-align: right;
            width: 120px;
        }
        .contact-submit {
            background-color: #d9d9d9;
            border: medium none;
            box-shadow: 0 5px 0 0 #a3a3a3;
            color: #000000;
            font-size: 18px;
            height: 36px;
            transition: background-color 0.5s ease 0s;
            width: 80%;
        }
        .contact-candle-wrapper {
            padding: 50px 0 0;
        }
        .left-panel {
            border-right: none;
            padding: 15px 0 30px;
        }
        .title-wrapper h1{
            font-size: 30px;
        }
        .contact-content-wrapper .body-wrapper {
            padding-top: 0px;
        }
        .contact-content-wrapper .title-wrapper h4 {
            font-size: 18px;
        }
        .contact-content-wrapper {
            padding: 30px 50px;
        }
        .right-panel {
            padding: 15px 0;
        }
        .company-name-wrapper {
            padding-top: 0;
        }
        .contact-map-wrapper {
            padding: 30px 0;
        }
        #map_canvas {
            height: 170px;
            width: 100%;
        }
        .contact-sns-bar ul li {
            border-radius: 30px;
            cursor: pointer;
            display: inline-block;
            padding: 3px;
            text-align: center;
            width: 27px;
        }
        .contact-sns-bar ul li img {
            height: 20px;
            padding: 3px;
        }
    }
    
    @media all and (min-width: 801px) and (max-width: 1279px){
        .contact-content-wrapper {
            padding: 20px !important;
        }
        .contact-content-wrapper .text-fields-wrapper {
            padding-top: 10px;
        }
        .contact-us-wrapper{
            padding: 0 20px;
        }
        .name-and-email {
            color: #000000 !important;
            font-size: 15px !important;
            height: 45px !important;
            padding-left: 25px !important;
            margin: 9px 0;
        }
        .ques-list-wrapper{
            width: 100%;
        }
        .ques-list-wrapper ul{
            padding: 0;
            margin: 0;
        }
        .ques-list-wrapper ul li{
            line-height: 1.5;
        }
        .ques-list-wrapper ul li div {
            text-align: left top;
        }
        .ques-list-wrapper ul li div input[type="radio"] {
            float: left;
            width: 10px;
        }
        .ques-list-wrapper ul li div label{
            font-size: 13px;
            padding: 0 0 0 7px;
            width: 85%;
        }
        .contact-message-wrapper {
            padding-top: 0;
        }
        .contact-message-wrapper textarea {
            color: #000000;
            font-size: 14px;
            height: auto;
            min-height: 200px;
            width: 100%;
        }
        .contact-message-btn-wrapper {
            float: right;
            padding: 20px 0 0;
            text-align: right;
            width: 120px;
        }
        .contact-submit {
            background-color: #d9d9d9;
            border: medium none;
            box-shadow: 0 5px 0 0 #a3a3a3;
            color: #000000;
            font-size: 18px;
            height: 36px;
            transition: background-color 0.5s ease 0s;
            width: 80%;
        }
        .contact-candle-wrapper {
            padding: 50px 0 0;
        }
        .left-panel {
            border-right: none;
            padding: 15px 0 30px;
        }
        .title-wrapper h1{
            font-size: 30px;
        }
        .contact-content-wrapper .body-wrapper {
            padding-top: 0px;
        }
        .contact-content-wrapper .title-wrapper h4 {
            font-size: 18px;
        }
        .contact-content-wrapper {
            padding: 30px 50px;
        }
        .right-panel {
            padding: 15px 0;
        }
        .company-name-wrapper {
            padding-top: 0;
        }
        .contact-map-wrapper {
            padding: 30px 0;
        }
        #map_canvas {
            height: 170px;
            width: 100%;
        }
        .contact-sns-bar ul li {
            border-radius: 30px;
            cursor: pointer;
            display: inline-block;
            padding: 3px;
            text-align: center;
            width: 27px;
        }
        .contact-sns-bar ul li img {
            height: 20px;
            padding: 3px;
        }
    }
</style>

