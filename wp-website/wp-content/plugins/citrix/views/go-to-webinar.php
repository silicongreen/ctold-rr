<section class="content-header">
    <h1>Citrix Go To Webinar</h1>
</section>

<div class="col-lg-12">

    <div class="box box-info">

        <form id="citrix_webinar_registrant" name="citrix_webinar_registrant" method="POST" class="form-horizontal">
            
            <?php wp_nonce_field('citrix_webinar_reg_info', 'citrix_webinar_reg_nonce'); ?>

            <div class="box-body">
                
                <div class="form-group message-wrapper" style="display: none;">
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="g2w_fname">First Name</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="g2w_fname" name="g2w_fname" value="" required="required" />
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="g2w_lname">Last Name</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="g2w_lname" name="g2w_lname" value="" required="required" />
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="g2w_email">Email</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="g2w_email" name="g2w_email" value="" required="required" />
                    </div>
                </div>
                
                <div class="box-footer">
                    <input type="submit" class="btn btn-info" value="Save" />
                </div>

            </div>
            
        </form>

    </div>

</div>