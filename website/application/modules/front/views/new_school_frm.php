<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

<link rel="stylesheet" href="/scripts/flexslider/flexslider.css" type="text/css" media="screen" />

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="js/libs/jquery-1.7.min.js">\x3C/script>')</script>

<script src="/scripts/flexslider/js/modernizr.js"></script>

<div class="container" style="width: 77%; min-height:250px;">
    <div style="margin:30px 20px; height:60px;">
        <div style="float:left">
            <h2 class="f2">School Information</h2>
        </div>
    </div>
    <div class="clearfix"></div>

    <div class="wrapper">

        <div class="new-school-wrapper">

            <!-- Template information -->
            <div class="template-header">

                <div class="template-header-left col-md-5">

                    <div class="template-image-wrapper">

                        <div class="template-image">
                            <img class="template-image-image" src="<?php echo $ar_templates['cover_url']; ?>" />
                            <div class="template-type-tag">
                                <img src="<?php echo ($ar_templates['price'] > 0) ? $ar_templates['price_tag_url'] : '/styles/layouts/tdsfront/school_templates/free_icon.png'; ?>" />
                            </div>
                        </div>
                        <div class="clearfix"></div>

                        <div class="template-buttons">
                            <button type="button" class="btn btn-danger col-sm-5 f2">Diselect</button>
                            <button type="button" class="btn btn-info col-sm-6 f2" onclick="window.open('<?php echo base_url('demo-school-template?id=' . $ar_templates['name']); ?>','_self')">View Demo</button>
                        </div>

                    </div>

                    <div class="clearfix"></div>

                    <div class="template-hint">Change Your theme anytime by cllicking diselect button.</div>

                </div>

                <div class="template-header-right col-md-7">

                    <div class="top-ribbon f2">
                        Your Template: <?php echo $ar_templates['name']; ?>
                    </div>

                    <div class="feature-list-wrapper">
                        <ul>
                            <li><i class="fa fa-check"></i>One Page Responsive Website</li>
                            <li><i class="fa fa-check"></i>Make Maximum 10 Page</li>
                            <li><i class="fa fa-check"></i>Full Access Admin Panel</li>
                            <li><i class="fa fa-check"></i>100% Free!!!</li>
                        </ul>
                    </div>

                </div>

            </div>
            <!-- Template information -->


            <!-- Template form -->
            <div class="template-form">

                <form action="/submit-new-school?id=<?php echo $ar_templates['name']; ?>" method="post" class="form-inline" enctype='multipart/form-data'>
                    <input type="hidden" name="template_id" value="<?php echo $ar_templates['name']; ?>">
                    <div class="template-form-label f2">Fill up the information</div>
                    <div class="clearfix"></div>

                    <div class="form-group">
                        <input type="text" class="form-control" name="full_name" id="full_name" placeholder="Your Name" required="required" />
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" name="email_addr" id="email_addr" placeholder="Your Email" required="required" />
                    </div>
                    <div class="clearfix"></div>

                    <div class="form-group text-wrapper">
                        <input type="text" class="form-control" name="school_name" id="school_name" placeholder="Your School Name" required="required" />
                    </div>
                    <div class="clearfix"></div>

                    <div class="form-group textarea-wrapper">
                        <textarea class="form-control" rows="3" name="school_addr" id="school_addr" placeholder="Your School Address" required="required" /></textarea>
                    </div>
                    <div class="clearfix"></div>

                    <div class="form-group">
                        <input type="text" class="form-control" name="phone_number" id="phone_number" placeholder="Your Phone Number" required="required" />
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="home_phone" id="home_phone" placeholder="Your Home Phone Number (* If any)">
                    </div>
                    <div class="clearfix"></div>

                    <div class="template-form-label f2">Fill up your school information</div>
                    <div class="clearfix"></div>

                    <div class="form-group textarea-wrapper">
                        <textarea class="form-control" rows="3" name="school_about" id="school_about" placeholder="About your school" required="required" /></textarea>
                    </div>
                    <div class="clearfix"></div>

                    <div class="form-group textarea-wrapper">
                        <textarea class="form-control" rows="3" name="school_admission" id="school_admission" placeholder="Admission details" required="required" /></textarea>
                    </div>
                    <div class="clearfix"></div>

                    <div class="form-group textarea-wrapper">
                        <textarea class="form-control" rows="3" name="school_facilities" id="school_facilities" placeholder="Facilities" required="required" /></textarea>
                    </div>
                    <div class="clearfix"></div>

                    <div class="form-group textarea-wrapper">
                        <textarea class="form-control" rows="3" name="school_achievements" id="school_achievements" placeholder="Achievements" required="required" /></textarea>
                    </div>
                    <div class="clearfix"></div>

                    <div class="form-group textarea-wrapper">
                        <p style="text-align:left;margin:0px;">Allowed file types: gif , jpg , jpeg , png , docx , doc , zip</p>
                    </div>
                    <div class="form-group textarea-wrapper">
                        <label for="school_image" id="school_image1" class="btn btn-success f2 col-sm-2">Upload Image</label>
                        <input type="file" id="school_image" name="school_image" style="display: none;" />
                        
                        <label for="school_file" class="btn btn-info f2 col-sm-2">Upload File</label>
                        <input type="file" id="school_file" name="school_file" style="display: none;" />
                        
                        <button type="submit" class="btn btn-primary col-sm-2 f2">Submit</button>
                        

                    </div>
                    <div class="form-group textarea-wrapper">
                        <p id="school_image_src" style="float:left;background-color: #6DD7AE;padding:5px 10px;color:#fff;display: none;"></p>
                        <p id="school_file_src" style="clear:both;float:left;background-color: #90B5CB;padding:5px 10px;color:#fff;display: none;"></p>
                    </div>
                </form>


            </div>
            <!-- Template form -->


            <!-- Template footer -->
            <div class="template-footer">

                <section class="slider">
                    <div class="flexslider carousel">
                        <ul class="slides">

                            <?php
                            foreach ($all_ar_templates as $template) {

                                if ($template['name'] != $ar_templates['name']) {
                                    ?>

                                    <li>
                                        <div class="template-image-wrapper">

                                            <div class="template-image">
                                                <img class="template-image-image" src="<?php echo $template['cover_url']; ?>" />
                                                <div class="template-type-tag">
                                                    <img src="<?php echo ($template['price'] > 0) ? $template['price_tag_url'] : '/styles/layouts/tdsfront/school_templates/free_icon.png'; ?>" />
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>

                                            <div class="template-buttons">
                                                <button type="button" class="btn btn-danger f2 col-sm-4" onclick="window.location.href = '<?php echo base_url('submit-new-school?id=' . $template['name']); ?>'">Select</button>
                                                <button type="button" class="btn btn-info f2 col-sm-6" onclick="window.open('<?php echo base_url('demo-school-template?id=' . $template['name']); ?>','_self')">View Demo</button>
                                            </div>

                                        </div>
                                    </li>

                                    <?php
                                }
                            }
                            ?>

                        </ul>
                    </div>
                </section>

            </div>
            <!-- Template footer -->

        </div>

    </div>

</div>

<style type="text/css">
    .new-school-wrapper {
        background-color: #efefef;
        box-shadow: 0 0 5px 1px #ccc;
        float: left;
        margin-left: 20px;
        width: 97%;
    }
    .template-header, .template-footer {
        box-shadow: 0 2px 5px 0 #bbbbbb;
        float: left;
        padding-bottom: 40px;
        width: 100%;
    }
    .template-header-left {
        padding: 40px 15px 0 20px;
    }
    .template-header-right {
        padding: 40px 0 0 0;
    }
    .template-image-wrapper {
        background-color: #ffffff;
        float: left;
        width: 86%;
    }
    .template-hint {
        font-size: 10px;
        color: #777777;
        width: 80%;
        padding: 20px 0px 0px;
    }
    .template-image {
        margin-left: auto;
        margin-right: auto;
        position: relative;
    }
    .template-image-image {
        padding: 20px;
        width: 100%;
    }
    .template-type-tag {
        left: 20px;
        position: absolute;
        top: 20px;
    }
    .template-type-tag img {
        width: 60%;
    }
    .template-buttons {
        float: left;
        padding: 0 20px 15px;
        width: 100%;
    }
    .template-buttons .btn.btn-danger {
        background-color: #DC3434;
    }
    .template-buttons .btn.btn-danger:hover {
        background-color: #E35D5D;
    }
    .template-buttons .btn.btn-info {
        background-color: #3F515D;
    }
    .template-buttons .btn.btn-info:hover {
        background-color: #65747D;
    }
    .top-ribbon {
        background-color: #37a67c;
        color: #ffffff;
        font-size: 30px;
        padding: 10px 20px;
    }
    .feature-list-wrapper {
        margin-top: 40px;
    }
    .feature-list-wrapper ul {
        margin: 0;
    }
    .feature-list-wrapper ul li {
        color: #010101;
        line-height: 30px;
    }
    .feature-list-wrapper ul li i {
        font-size: 25px;
        margin-right: 25px;
    }

    .template-form {
        background-color: #ffffff;
        box-shadow: -4px 7px 10px -7px #bbbbbb;
        float: left;
        margin-top: 3px;
        width: 100%;
    }
    .template-form-label {
        color: #3f515d;
        font-size: 45px;
        padding: 40px 0 20px;
        text-align: center;
        width: 100%;
    }
    .form-inline {
        margin-left: auto;
        margin-right: auto;
        padding: 0 10px;
        text-align: center;
        width: 90%;
    }
    .form-inline .form-group {
        margin-bottom: 15px;
        width: 49%;
    }
    .form-inline .form-control {
        height: 45px;
        width: 98%;
    }
    .form-inline .text-wrapper, .form-inline .textarea-wrapper {
        width: 98%;
    }
    .form-inline .text-wrapper .form-control, .form-inline .textarea-wrapper .form-control {
        width: 99.5%;
    }
    .form-inline .textarea-wrapper .form-control {
        height: 90px;
    }
    .btn.btn-success {
        background-color: #52D0A0;
    }
    .btn.btn-success:hover {
        background-color: #75D9B3;
    }
    .btn.btn-info {
        background-color: #6598B7;
    }
    .btn.btn-info:hover {
        background-color: #90B5CB;
    }
    .btn.btn-primary {
        background-color: #813186;
    }
    .btn.btn-primary:hover {
        background-color: #B383B6;
    }

    .template-footer {
        margin-top: 3px;
        padding-bottom: 0;
    }

    .flexslider {
        margin-bottom: 30px;
        margin-left: auto;
        margin-right: auto;
        margin-top: 40px;
        width: 95%;
    }
    .flexslider .slides .template-type-tag img {
        width: 60%;
    }
    .flexslider .slides .template-image-wrapper {
        width: 94%;
    }
    .flexslider .slides .template-buttons {
        padding: 0 5px 0 15px;
    }
    .flexslider .slides .template-buttons .btn.btn-danger, .flexslider .slides .template-buttons .btn.btn-info {
        font-size: 12px;
        padding: 10px 0;
    }
    .flex-direction-nav a {
        overflow: visible !important;
    }
</style>

<!-- FlexSlider -->
<script defer src="/scripts/flexslider/jquery.flexslider.js"></script>

<script type="text/javascript">
        $(function () {
            SyntaxHighlighter.all();
        });
        $(window).load(function () {
            $('.flexslider').flexslider({
                animation: "slide",
                slideShow: false,
                animationLoop: false,
                itemWidth: 210,
                itemMargin: 0,
                minItems: 2,
                maxItems: 4,
                controlNav: false,
                nextText: "",
                prevText: "",
                start: function (slider) {
                    $('body').removeClass('loading');
                }
        });
    });
   $(document).ready(function() { 
       
    $(document).on('change',"#school_image",function(){
        var image_name = $("#school_image").val(); 
        $("#school_image_src").html(image_name);
        $("#school_image_src").show();
    });
    $(document).on('change',"#school_file",function(){
        var file_name = $("#school_file").val(); 
        $("#school_file_src").html(file_name);
        $("#school_file_src").show();
    });
        
       
    });

</script>

<!-- Optional FlexSlider Additions -->
<!--<script src="/scripts/flexslider/js/jquery.easing.js"></script>
<script src="/scripts/flexslider/js/jquery.mousewheel.js"></script>
<script defer src="/scripts/flexslider/js/demo.js"></script>-->