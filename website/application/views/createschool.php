<div class="item content" id="content_section26">
    <div class="wrapper grey" >

        <div class="container">
            <div class="col-md-12"  style="padding:0px; margin-top: 120px; margin-bottom:30px;">
                <div style="margin:30px 100px; float:left; width:80%;background: white; ">

                    <div class="col-md-12"  style="padding:0px; ">
                        <h2 class="lead text-center editContent"  style="color:#66D56A; font-weight: bold;margin-top: 20px;">
                            Create your School
                        </h2>
                    </div>


                    <div class="col-md-12">
                        <?php echo form_error('name', '<div class="error">', '</div>'); ?>
                        <?php echo form_error('institution_address', '<div class="error">', '</div>'); ?>
                        <?php echo form_error('institution_phone_no', '<div class="error">', '</div>'); ?> 
                        <?php if ($school_type == "paid"): ?>
                            <?php echo form_error('number_of_student', '<div class="error">', '</div>'); ?> 
                        <?php endif; ?>
                        <?php echo form_error('code', '<div class="error">', '</div>'); ?>

                        <?php if (isset($ar_error)) { ?>
                            <div class="alert error" style="margin-top: 15px; margin-bottom: 15px; ">
                                <?php echo $ar_error['message']; ?>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="col-md-12">
                        <form id="form_newschool"   class="form-horizontal"  method="post" action="<?php echo base_url(); ?>createschool/newschool">
                            <legend></legend>

                            <input type="hidden" name="i_tmp_free_user_data_id" value="<?php echo $i_tmp_free_user_data_id; ?>" />
                            <input type="hidden" name="school_type" value="<?php echo $school_type; ?>" />

                            <div class="form-group">
                                <label class="col-md-2 control-label" for="school_name"></label> 
                                <div class="col-md-8">
                                    <input type="text" value="<?php echo $this->input->post("name") ?>" class="form-control input-md" id="school_name" name="name" placeholder="Enter School Name *" required="required" />
                                    <!--<span class="help-block"><font color="#ccc" size="1">More then 5 character</font></span>-->
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label" for="institution_address"></label> 
                                <div class="col-md-8">
                                    <input type="text" value="<?php echo $this->input->post("institution_address") ?>" class="form-control input-md" id="institution_address" name="institution_address" placeholder="School Address *" required="required" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label" for="institution_phone_no"></label> 

                                <div class="col-md-4">
                                    <div class="dropdown">
                                        <button data-coutntry-id="14" class="btn btn-default dropdown-toggle col-md-12" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                            Bangladesh (বাংলাদেশ) (880)
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu country-list" aria-labelledby="dropdownMenu1">

                                            <?php foreach ($countries as $country) { ?>
                                                <li id="<?php echo $country['id']; ?>" data-call-code="<?php echo $country['phonecode']; ?>"><a href="javascript:void(0);"><?php echo $country['name']; ?> <?php echo (!empty($country['phonecode'])) ? '(' . $country['phonecode'] . ')' : '' ?></a></li>
                                            <?php } ?>

                                        </ul>
                                    </div>
                                </div>

                                <!--<div class="col-md-2">-->
                                    <input readonly="readonly" type="hidden" value="<?php //echo $country_call_code; ?>" class="form-control" id="country_call_code" name="country_call_code" placeholder="Country Code *" required="required" />
                                <!--</div>-->
                                
                                <div class="col-md-4">
                                    <input type="text" value="<?php echo $this->input->post("institution_phone_no") ?>" class="form-control" id="institution_phone_no" name="institution_phone_no" placeholder="Contact Number *" required="required" />
                                </div>
                            </div>

                            <?php if ($school_type == "paid") { ?>
                                <div class="form-group">
                                    <label class="col-md-2 control-label" for="number_of_student"></label> 
                                    <div class="col-md-8">
                                        <input type="text" value="<?php echo $this->input->post("number_of_student") ?>" class="form-control input-md" id="number_of_student" name="number_of_student" placeholder="Number of Student *" required />
                                        <span class="help-block"><font color="#ccc" size="1">Number of student you want to register</font></span>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="form-group">
                                <label class="col-md-2 control-label" for="code"></label> 
                                <div class="col-md-8">
                                    <input type="text" value="<?php echo $this->input->post("code") ?>" class="form-control input-md" id="code" name="code" placeholder="Enter the Subdomain *" required="required" />
                                    <span class="help-block"><font color="#ccc" size="1">Short code (3 to 8 character no space no special character ex: mos => Monipur school)</font></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label" for="singlebutton"></label>
                                <div class="col-md-8">
                                    <button name="submit" type="submit" id="submit" class="btn btn-primary btn-success btn-lg">
                                        <i class="fa fa-thumbs-up"></i> SEND
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div><!-- /.col-md-5 -->

                </div> 
            </div>      

        </div><!-- /.container -->

    </div><!-- /.wrapper -->
</div>

<style type="text/css">
    .form-horizontal input
    {
        margin-top:0px;
    }
    .error
    {
        color:red;
    }
    .dropdown-menu {
        height: 272px;
        overflow-y: scroll;
    }
    .btn {
        overflow-x: hidden;
    }
</style>

<script type="text/javascript" src="/js/custom/school.js"></script>