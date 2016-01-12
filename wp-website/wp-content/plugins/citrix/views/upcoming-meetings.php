<?php add_thickbox(); ?>
<section class="content-header">
    <h1>Upcoming Meetings</h1>
</section>

<div class="col-lg-12 pull-ajax" data="citrix_meeting_list">
    <?php wp_nonce_field('citrix_meeting_list', 'citrix_meeting_list_nonce'); ?>
</div>

<div id="thickbox_content" style="display:none;">

    <div class="col-lg-12">

        <div class="box box-info">

            <form id="citrix_start_meeting" name="citrix_start_meeting" method="POST" class="form-horizontal">

                <?php wp_nonce_field('citrix_meeting_info', 'citrix_meeting_nonce'); ?>

                <div class="box-body">
                    
                    <input type="hidden" id="meeting_id" name="meeting_id" value="" />

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="g2m_fname">First Name</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="g2m_fname" name="g2m_fname" value="" required="required" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="g2m_lname">Last Name</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="g2m_lname" name="g2m_lname" value="" required="required" />
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="g2m_email">Email</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="g2m_email" name="g2m_email" value="" required="required" />
                        </div>
                    </div>

                    <div class="box-footer">
                        <input type="submit" class="btn btn-info" value="Add" />
                    </div>

                </div>

            </form>

        </div>

    </div>

</div>