<div id="page" class="page">

    <div class="item content white padding-bottom-60" id="content_section25">
        <div class="wrapper grey">    		
            <div class="container">

                <div class="row">

                    <div class="col-md-12 text-left box_bg_1" style="margin-top:80px;">

                        <div class="row text-left">
                            <div class="number-bg-15">
                                <span class="icon-stack">
                                    <i class="icon-sign-blank icon-stack-base"></i>
                                    <span class="icon-fixed-width icon-light char-overlay">
                                        Your school has been successfully created.
                                    </span>
                                </span>
                            </div>
                        </div>

                        <div class="editContent">
                            <h4>Your User name is "<?php echo $paid_user_data['paid_username']; ?>"</h4>
                            <h4>Your School Code is "<?php echo $returned_school_info['school']['activation_code']; ?>" (Use this code to create user for your school)</h4>
                            <h4>Please check the your email for account activation and login information.</h4>
                        </div><!-- /.editContent -->

                        <?php // if (isset($returned_school_info['school']['id']) && !empty($returned_school_info['school']['id'])) { ?>
<!--                            <div class="col-lg-5">
                                <a class="btn btn-primary btn-full-width" href="/createschool/initial-setup/<?php echo $returned_school_info['school']['id']; ?>">
                                    <h5>I'll complete the initial school setup</h5></a>
                            </div>-->
                        <?php // } ?>

                    </div><!-- /.col-md-6 col -->


                </div><!-- /.row -->

            </div><!-- /.container -->
        </div><!-- /.wrapper -->

    </div><!-- /.item -->
    <div id="school_code_wrp">

        <input type="hidden" id="school_type" name="school_type" value="<?php echo $school_type; ?>" />
        <?php if (isset($returned_school_info['school']['code']) && !empty($returned_school_info['school']['code'])) { ?>
            <input type="hidden" id="school_code" name="school_code" value="<?php echo $returned_school_info['school']['code']; ?>" />
            <input type="hidden" id="i_tmp_school_created_data_id" name="i_tmp_school_created_data_id" value="<?php echo $i_tmp_school_created_data_id; ?>" />
            <input type="hidden" id="i_free_user_id" name="i_free_user_id" value="<?php echo $i_free_user_id; ?>" />
        <?php } ?>
    </div>

</div><!-- /#page -->

<script type="text/javascript" src="/js/custom/school.js"></script>

<style type="text/css">
    #content_section25 .number-bg-15 {
        background-color: transparent !important;
    }
</style>