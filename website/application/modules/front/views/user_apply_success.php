<?php $s_ci_key = (isset($ci_key)) ? $ci_key : NULL; ?>

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
                <h1 class="f2">Success Fully Applied for Admission</h1>
            </div>
            
            <div class="text-fields-wrapper">
                <div>
                    <img src="/styles/layouts/tdsfront/image/privacy_policy.png" />
                </div>
            </div>

            <div class="body-wrapper">

                <div class="privacy-policy-intro">
                    <h4 class="f2">Your Login and approval informations</h4>
                </div>

                <div class="col-lg-12 left-panel">
                    
                    <div class="clearfix"></div>
                    
                    <div class="col-lg-12 about-us-ul-wrapper">
                        <ul class="about-us-ul">
                            <li>
                                <div class="title-wrapper">
                                    <h4 class="f2">Your new username</h4>
                                </div>
                                <p><b>"<?php echo $user_data->email; ?>"</b> use this username for login instead of your email address</p>
                            </li>
                            <?php if(isset($parents) && count($parents)>0): ?>
                            <li>
                                <div class="title-wrapper">
                                    <h4 class="f2">Guardian username</h4>
                                </div>
                                
                                <?php foreach($parents as $key=>$value): ?>
                                    <p><b><?php echo $key+1 ?>. <?php echo $value->username; ?></b></p>
                                <?php endforeach; ?>
                            </li>
                            <?php endif; ?>
                            <li>
                                <div class="title-wrapper">
                                    <h4 class="f2">Approval informations</h4>
                                </div>
                                <p>
                                    You can login to our premium school after getting approval from school. 
                                    School administration will automatically approve you if you are a valid user of that school.
                                    Please contact with your school for Approval if it takes long. thanks.
                                </p>
                            </li>
                            
                            
                            
                        </ul>
                    </div>
                    
                    <div class="clearfix"></div>
                    
                </div>
                
                
            </div>
            
        </div>
        
    </div>
    
    <div class="clearfix"></div>
    
    <div class="col-lg-12 terms-footer">
        <div class="col-lg-2 agreement-text">
            <img src="/styles/layouts/tdsfront/image/privacy_policy_lock.png" />
        </div>
    </div>
        
    
</div>
<style type="text/css">
    .contact-us-wrapper
    {
        background-color: #ffffff;
        margin: 0 20px;
        padding:20px;
        min-height: 1050px;
    }
    .contact-content-wrapper{
        padding: 50px 50px 0;
    }
    .contact-content-wrapper .title-wrapper h1, h5, h4{
        color: #000000;
    }
    .contact-content-wrapper .text-fields-wrapper {
        padding-top: 35px;
    }
    .contact-content-wrapper .text-fields-wrapper div {
        text-align: center;
        width: 100%;
    }
    .contact-content-wrapper .text-fields-wrapper img {
        width: 85%;
    }
    .contact-content-wrapper .body-wrapper{
        padding-top: 60px;
    }
    .left-panel{
        padding: 20px 40px 0px 0px;
    }
    .about-us-ul-wrapper{
        padding: 20px 0 0 0px;
    }
    .about-us-ul li {
        background: url("/styles/layouts/tdsfront/image/tickmark.png") no-repeat scroll 0 7px / 30px auto rgba(0, 0, 0, 0);
        line-height: 20px;
        list-style: outside none none;
        margin: 60px 0;
        padding-left: 60px;
    }
    .about-us-ul li:first-child {
        margin: 0;
    }
    .about-us-ul li div {
        padding-bottom: 20px;
    }
    .about-us-ul li p {
        font-size: 15px;
        margin-top: 25px;
    }
    .about-us-ul li p:first-of-type {
        margin-top: 0;
    }
    .terms-footer {
        background-color: #df092d;
        margin: 0 20px 80px;
        padding: 23px;
        position: relative;
        width: initial;
    }
    .agreement-text{
        position: absolute;
        right: 0;
        top: -105px;
    }
    .agreement-text img {
        width: 70%;
    }
    
    @media all and (min-width: 319px) and (max-width: 479px){
        .contact-us-wrapper{
            padding: 0 20px;
        }
        .title-wrapper h1{
            font-size: 25px;
        }
        .contact-content-wrapper {
            padding: 20px !important;
        }
        .contact-content-wrapper .text-fields-wrapper {
            padding-top: 10px;
        }
        .contact-content-wrapper .text-fields-wrapper img {
            width: 95%;
        }
        .contact-content-wrapper .body-wrapper {
            padding-top: 25px;
        }
        .left-panel{
            padding: 0;
        }
        .about-us-ul{
            margin: 0;
            padding: 0;
        }
        .about-us-ul li {
            padding-left: 45px;
            margin: 20px 0;
        }
        .about-us-ul li:first-child {
            margin: 0;
        }
        .about-us-ul li div {
            padding-bottom: 5px;
        }
        .about-us-ul li p {
            font-size: 14px;
        }
        .about-us-ul li div h4 {
            font-size: 18px;
        }
        .agreement-text {
            padding-right: 10px;
            position: absolute;
            right: 0;
            text-align: right;
            top: -60px;
        }
        .agreement-text img {
            width: 30%;
        }
    }
    
    @media all and (min-width: 480px) and (max-width: 799px){
        .contact-us-wrapper{
            padding: 0 20px;
        }
        .title-wrapper h1{
            font-size: 30px;
        }
        .contact-content-wrapper {
            padding: 20px !important;
        }
        .contact-content-wrapper .text-fields-wrapper {
            padding-top: 10px;
        }
        .contact-content-wrapper .text-fields-wrapper img {
            width: 95%;
        }
        .contact-content-wrapper .body-wrapper {
            padding-top: 25px;
        }
        .left-panel{
            padding: 0;
        }
        .about-us-ul{
            margin: 0;
            padding: 0;
        }
        .about-us-ul li {
            padding-left: 45px;
            margin: 20px 0;
        }
        .about-us-ul li:first-child {
            margin: 0;
        }
        .about-us-ul li div {
            padding-bottom: 5px;
        }
        .about-us-ul li p {
            font-size: 14px;
        }
        .about-us-ul li div h4 {
            font-size: 18px;
        }
        .agreement-text {
            padding-right: 10px;
            position: absolute;
            right: 0;
            text-align: right;
            top: -80px;
        }
        .agreement-text img {
            width: 40%;
        }
    }
    
    @media all and (min-width: 800px) and (max-width: 1279px){
        .contact-content-wrapper {
            padding: 20px !important;
        }
        .contact-content-wrapper .text-fields-wrapper {
            padding-top: 10px;
        }
        .contact-us-wrapper{
            padding: 0 20px;
        }
        .agreement-text {
            padding-right: 10px;
            position: absolute;
            right: 0;
            text-align: right;
            top: -80px;
        }
        .agreement-text img {
            width: 40%;
        }
    }
</style>