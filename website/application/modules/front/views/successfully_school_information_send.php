<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

<link rel="stylesheet" href="/scripts/flexslider/flexslider.css" type="text/css" media="screen" />

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="js/libs/jquery-1.7.min.js">\x3C/script>')</script>

<script src="/scripts/flexslider/js/modernizr.js"></script>

<div class="container" style="width: 77%; min-height:250px;">
   
    <div class="clearfix"></div>

    <div class="wrapper">

        <div class="new-school-wrapper">

            <!-- Template information -->
            <div class="template-header">

                <div class="template-header-left col-md-5" style="text-align:center;">

                    <div class="template-image-wrapper">

                        <div class="template-image" >
                            <img class="template-image-image" src="<?php echo $ar_templates['cover_url']; ?>" />
                            <div class="template-type-tag">
                                <img src="<?php echo ($ar_templates['price'] > 0) ? $ar_templates['price_tag_url'] : '/styles/layouts/tdsfront/school_templates/free_icon.png'; ?>" />
                            </div>
                        </div>
                        <div class="clearfix"></div>


                    </div>

                    <div class="clearfix"></div>

                    <div class="template-hint"><b style="text-align:center; font-size:18px;">Your selected School Theme</b></div>

                </div>

                <div class="template-header-right col-md-7">

                    <div style="text-align:center;" class="top-ribbon f2">
                        <img   src="scripts/flexslider/images/hand.png" />
                    </div>

                    <div class="feature-list-wrapper">
                        <h2><p style="text-align:center; font-size:26px;"><b>Congratulation!!!</b></b></h2>
                       <p style="text-align:center;">
                           Your submission posted successfully
                       </p>
                       <p style="text-align:center;">
                           You will be notified soon
                       </p>
                    </div>

                </div>

            </div>
       
            


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
                                                <button type="button" class="btn btn-info f2 col-sm-6" onclick="window.open('<?php echo $template['demo_url']; ?>', '_blank')">View Demo</button>
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
</script>

<!-- Optional FlexSlider Additions -->
<!--<script src="/scripts/flexslider/js/jquery.easing.js"></script>
<script src="/scripts/flexslider/js/jquery.mousewheel.js"></script>
<script defer src="/scripts/flexslider/js/demo.js"></script>-->