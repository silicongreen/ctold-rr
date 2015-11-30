</header>

<link href="<?php echo base_url(); ?>css/flat-ui.css" rel="stylesheet">

<link rel="stylesheet" href="http://css-spinners.com/css/spinner/dots.css" type="text/css">
<link href="/bootstrap/css/prettify.css" rel="stylesheet">

<div class="clearfix"></div>

<div class="container">
    <div class="wrapper">

        <div class="col-lg-12"  style="padding:0px; margin-top: 120px;">
            <div id="page" class="page school-setup-wizard">

                <div class="item content white padding-bottom-60" id="content_section25">

                    <div id="rootwizard">

                        <div class="navbar col-lg-3">
                            <div class="navbar-inner">
                                <ul>
                                    <?php foreach ($setup_forms as $form_key => $form_val) { ?>
                                        <li><a href="#tab<?php echo $form_key; ?>" data-toggle="tab">Setup <?php echo ucwords(str_replace('_', ' ', $form_val)); ?></a></li>
                                    <?php } ?>
                                </ul>

                            </div>
                        </div>

                        <div class="col-lg-9 form-wrapper">

                            <div class="col-lg-12">

                                <div style="float:left">
                                    <!--<input type='button' class='btn btn-primary button-previous' name='previous' value='Previous' />-->
                                </div>

                                <div style="float:right">
                                    <input type='button' class='btn btn-primary button-next' name='Skip' value='Skip' />
                                    <input type='submit' class='btn btn-primary button-save' name='save' value='Save' />
                                </div>

                            </div>

                            <div class="clearfix"></div>

                            <div id="bar" class="progress progress-striped active">
                                <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
                            </div>

                            <div class="tab-content">

                                <input type="hidden" name="school_id" id="school_id" value="<?php echo $school_id; ?>">

                                <div class="col-lg-12 alert-wrapper"></div>

                                <div class="clearfix"></div>

                                <div class="dots-loader" style="display: none;">
                                    Loading…
                                </div>

                                <?php foreach ($setup_forms as $form_key => $form_val) { ?>
                                    <div class="tab-pane" id="tab<?php echo $form_key; ?>"></div>
                                <?php } ?>

                                <div class="clearfix"></div>
                                <button type="button" class="add_txt_field btn btn-primary">Add More <b>+</b></button>

                            </div>

                        </div>


                    </div>

                </div>

            </div>

            <?php
            $this->load->view('modals/myModal');
            $this->load->view('modals/classModal');
            $this->load->view('modals/subjectModal');
            $this->load->view('modals/successModal');
            ?>

        </div>

    </div>
</div>


<style type="text/css">
    .glyphicon {
        top: 0;
    }
    input, textarea {
        margin-top: inherit;
    }
    #subjectModal .modal-body {
        padding: 20px 24px 0;
    }
    #subjectModal .modal-body p, .td_class_names_container p {
        color: #666 !important;
        display: block !important;
        float: left !important;
        font-size: 12px !important;
        line-height: inherit !important;
        padding: 0 15px 10px;
        width: 100% !important;
    }
    #class_names_container .alert, .td_class_names_container .alert {
        background-color: #ddd;
        border: 2px solid #ddd;
        color: #666;
        margin-right: 5px;
        padding: 0;
    }
    #class_names_container .alert:last-child, .td_class_names_container .alert:last-child  {
        margin-right: 0;
    }
    #class_names_container .panel, .td_class_names_container .panel  {
        margin-bottom: 8px;
    }
    #class_names_container .alert .close, .td_class_names_container .alert .close {
        right: 0;
    }
    .assign_to_class {
        margin-left: 10px;
        font-size: 12px;
        padding: 5px 10px;
    }
    .assign_to_class:hover, .assign_to_class:active, .assign_to_class:focus {
        background-color: #16a085;
        color: #ffffff;
        text-decoration: underline;
    }
    #content_section25 label, label {
        cursor: pointer;
    }
    #add_new_class {
        color: #ffffff;
    }
    .additional_shift {
        display: none;
    }
    .additional_shift label {
        padding-left: 30px;
    }
    p, #content_section25 p {
        color: #bbbbbb;
    }
    .form-control[readonly] {
        color: #555555;
    }
    .emp_category_wrapper {
        display: none;
    }
    .alert:not(#successModalLable .alert) {
        background-color: #f2dede;
        color: #a94442;
        border: 2px solid #ebccd1;
        font-size: 13px;
        line-height: 0.5;
    }
    .alert .close {
        line-height: 40px;
    }
    #header20 {
        padding-bottom: 50px;
    }
    #content_section25 {
        float: left;
        width: 100%;
    }
    #bar {
        margin-left: auto;
        margin-right: auto;
        margin-top: 20px;
        width: 97%;
    }
    #content_section25 .wrapper.grey {
        background: transparent none repeat scroll 0 0;
        border-bottom: 1px solid #64c4df;
        border-top: 1px solid #64c4df;
        padding: 50px 0;
    }
    #extra_vaules, #extra_vaules_classes {
        display: none;
        margin-left: 30px;
        padding: 0;
    }
    #extra_vaules table, #extra_vaules_classes table {
        border-left: none; 
        border-right: none; 
    }
    i.fa-trash-o {
        cursor: pointer;
    }
    .dots-loader {
        margin-left: 48%;
    }
    .form-wrapper {
        border: 2px solid #ddd;
        border-radius: 6px;
        margin-bottom: 20px;
        padding: 20px 20px 5px;
        position: relative;
        z-index: 1;
    }
    .tab-content {
        border: none;
    }
    .navbar-inner ul li {
        float: none;
    }
    ul.nav-pills > li > a {
        background-color: transparent;
        color: #818181;
        border-right: 2px solid #cccccc;
        border-top: none;
        border-bottom: none;
        border-left: 2px solid #cccccc;
        font-size: 14px;
        font-weight: normal;
    }
    .nav-pills > li:not(:first-child):not(:last-child).active > a, .nav-pills > li:not(:first-child):not(:last-child).active > a:hover, .nav-pills > li:not(:first-child):not(:last-child).active {
        background-color: #16a085;
    }
    .nav-pills > li.active > a, .nav-pills > li.active > a:hover, .nav-pills > li.active > a:focus {
        background-color: #16a085;
    }
    .nav-pills > li > a:hover, .nav-pills > li > a:focus {
        background-color: transparent;
    }
    .nav-pills > li:last-child > a {
        border-bottom: 2px solid #cccccc;
        border-radius: 0 0 8px 8px;
    }
    .nav-pills > li:first-child > a {
        border-radius: 8px 8px 0 0;
        border-top: 2px solid #cccccc;
    }
    .nav-pills > li:first-child > a {
        border-left: 2px solid #cccccc;
    }
    .right-margin-10 {
        margin-right: 10px !important;
    }
    .no-padding {
        padding: 0;
    }

    /** DEFAULT CSS OVERWRITE **/
    .school-setup-wizard label {
        color:inherit;
        font-weight:normal;
        letter-spacing: inherit;
        margin-top: inherit;
    }
    .school-setup-wizard input {
        height: inherit;
        width: initial;
    }
    /** DEFAULT CSS OVERWRITE **/

    /** MODAL **/
    .modal {
        top: 15%;
    }
    #add_new_item, #add_new_subject_class, #setup_success {
        color: #ffffff;
    }
    .modal-dialog {
        width: 30%;
    }
    .modal-content {
        box-shadow: -9px 0 14px 3px rgba(0, 0, 0, 0.18);
        width: 100%;
    }
    .modal-footer {
        height: 70px;
        margin: 0;
    }
    .modal-footer .btn {
        font-weight: bold;
    }
    .modal-footer .progress {
        display: none;
        height: 32px;
        margin: 0;
    }
    .input-group-addon {
        color: #fff;
        background: #3276B1;
    }
    /** MODAL **/



</style>

<script src="/js/jquery.bootstrap.wizard.min.js"></script>
<script src="/bootstrap/js/google-code-prettify/prettify.js"></script>
<script src="/js/custom/school_setup.js"></script>
