<section class="content-header">
    <h1>Create Webinar</h1>
</section>

<div class="col-lg-12">

    <div class="box box-info">

        <form id="citrix_webinar" name="citrix_webinar" method="POST" class="form-horizontal">

            <?php wp_nonce_field('citrix_webinar_info', 'citrix_webinar_nonce'); ?>

            <div class="box-body">
                
                <div class="form-group message-wrapper" style="display: none;">
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="g2w_subject">Subject</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="g2w_subject" name="g2w_subject" value="" required="required" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="g2w_description">Description</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="g2w_description" name="g2w_description" value="" required="required" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="g2w_startDateTime">Start At</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control datetime" id="g2w_startDateTime" name="g2w_startDateTime" value="" required="required" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="g2w_endDateTime">End At</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control datetime" id="g2w_endDateTime" name="g2w_endDateTime" value="" required="required" />
                    </div>
                </div>

                <div class="box-footer">
                    <input type="submit" class="btn btn-info" value="Save" />
                </div>

            </div>

        </form>

    </div>

</div>