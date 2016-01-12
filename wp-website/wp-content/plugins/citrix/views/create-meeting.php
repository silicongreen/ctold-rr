<section class="content-header">
    <h1>Citrix Go To Webinar</h1>
</section>

<div class="col-lg-12">

    <div class="box box-info">

        <form id="citrix_create_meeting" name="citrix_create_meeting" method="POST" class="form-horizontal">

            <?php wp_nonce_field('citrix_create_meeting_info', 'citrix_create_meeting_nonce'); ?>

            <div class="box-body">
                
                <div class="form-group message-wrapper" style="display: none;">
                </div>
                
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="g2m_subject">Subject</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="g2m_subject" name="g2m_subject" value="" required="required" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="g2m_startDateTime">Start At</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control datetime" id="g2m_startDateTime" name="g2m_startDateTime" value="" required="required" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="g2m_endDateTime">End At</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control datetime" id="g2m_endDateTime" name="g2m_endDateTime" value="" required="required" />
                    </div>
                </div>

                <div class="box-footer">
                    <input type="submit" class="btn btn-info" value="Save" />
                </div>

            </div>

        </form>

    </div>

</div>