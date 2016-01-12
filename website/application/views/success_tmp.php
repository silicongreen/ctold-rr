</header>

<div class="clearfix"></div>

<div class="container">

    <div class="wrapper col-lg-8 center">

        <div class="col-lg-12 success-wrapper" style="padding-bottom:30px;">

            <?php if (isset($error)) { ?>

                <div class="col-md-12">
                    <h2 class="f2 lead text-center error">Sorry!!! </h2>
                </div>

                <div class="col-md-12 no-padding">
                    <div class="alert alert-danger text-center">
                        <?php echo $error; ?>
                    </div>
                </div>

            <?php } else { ?>

                <div class="col-md-12">
                    <h2 class="f2 lead text-center success">Congratulation!!! </h2>
                </div>

                <div class="col-md-12 no-padding">
                    <div class="alert alert-success text-center">
                        Please check your Email. Your School has been entered in Queue. Very shortly we will contact with about the Premium Package.Thanks you.
                    </div>
                </div>

            <?php } ?>
           
        </div>
        

    </div>

</div>

<script type="text/javascript" src="/js/custom/school.js"></script>

<style type="text/css">
    .initial_setup h5 {
        font-size: initial;
        color: #ffffff;
    }
    .activation_code {
        color: #018fff;
    }
    .user_info {
        color: #bbb;
        line-height: 0.45;
    }
    .user_info_check {
        padding: 15px 0 30px;
    }
    .success_message_body {
        padding-top: 40px;
    }
    .success_label {
        font-size: 16px;
    }
    #smily {
        margin-top: -10px;
        width: 140px;
    }
    .success-wrapper {
        background-color: #ffffff;
        margin-top: 120px;
        margin-bottom: 50px;
        padding:0px;
    }
    .lead {
        color: #5cb85c;
        font-size: 25px;
        padding: 25px;
        margin-bottom: 0;
    }
    .no-padding {
        padding: 0;
    }
    .alert {
        border-radius: 0;
        padding: 10px;
        margin-bottom: 0;
    }
    .alert-success {
        background-color: #7fd268;
        border-color: #7fd268;
        color: #ffffff;
    }
    .error {
        color: #C9302C;
    }
</style>