</header>

<div class="clearfix"></div>

<div class="container">

    <div class="wrapper col-lg-8 center">

        <div class="col-lg-12 success-wrapper">

            <div class="col-md-12">
                <h2 class="f2 lead text-center success">Congratulation!!! </h2>
            </div>

            <div class="col-md-12 no-padding">
                <div class="alert alert-success text-center">
                    Your School has been created successfully.
                </div>
            </div>

            <div class="col-md-12 no-padding">

                <img class="pull-left" id="smily" src="/images/logo/smile.png">

                <div class="col-md-9 pull-right success_message_body bottom10">

                    <div class="col-lg-12 bottom10">

                        <div class="row bottom10">
                            <div class="col-xs-6 col-md-4 success_label">Your School Name : </div>
                            <div class="col-xs-12 col-sm-6 col-md-8 success_label"><?php echo $returned_school_info['school']['name']; ?></div>
                        </div>

                        <div class="row bottom10">
                            <div class="col-xs-6 col-md-4 success_label">Your School Code :</div>
                            <div class="col-xs-12 col-sm-6 col-md-8 success_label"> <span class="activation_code"><?php echo $returned_school_info['school']['activation_code']; ?></span></div>
                        </div>

                    </div>

                    <legend class="pull-left bottom10"></legend>
                </div>

                <div class="col-md-9 pull-right">
                    <div class="col-lg-12 bottom10">
                        <div class="row bottom10">

                            <?php
                            $first_name = $ar_free_user_data['first_name'];
                            $middle_name = $ar_free_user_data['middle_name'];

                            if (!empty($middle_name)) {
                                $middle_name = ' ' . $middle_name;
                            }

                            $last_name = $ar_free_user_data['last_name'];

                            $full_name = $first_name . $middle_name . ' ' . $last_name;
                            ?>

                            <p class="user_info">Use this code to create user for your school.</p>
                            <p class="user_info">Your Name is "<?php echo $full_name; ?>". Username is "<?php echo $paid_user_data['paid_username']; ?>"</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-9 pull-right">
                    <div class="col-lg-12 bottom10">
                        <div class="row bottom10">
                            <p class="user_info_check">Please check your e-mail for account activation and login information.</p>
                        </div>
                    </div>
                </div>


                <div class="col-lg-12 bottom10">
                    <div class="row bottom10 text-center">
                        <a class="btn btn-success initial_setup" href="/createschool/initial-setup/<?php echo $returned_school_info['school']['id']; ?>">
                            <h5>Complete initial school setup</h5></a>
                    </div>
                </div>


            </div>

            <div id="school_code_wrp">

                <input type="hidden" id="school_type" name="school_type" value="<?php echo $school_type; ?>" />
                <?php if (isset($returned_school_info['school']['code']) && !empty($returned_school_info['school']['code'])) { ?>
                    <input type="hidden" id="school_code" name="school_code" value="<?php echo $returned_school_info['school']['code']; ?>" />
                    <input type="hidden" id="i_tmp_school_created_data_id" name="i_tmp_school_created_data_id" value="<?php echo $i_tmp_school_created_data_id; ?>" />
                    <input type="hidden" id="i_free_user_id" name="i_free_user_id" value="<?php echo $i_free_user_id; ?>" />
                <?php } ?>
            </div>

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
</style>