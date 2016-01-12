<section class="content-header">
    <h1>Citrix Go To Meeting</h1>
</section>

<div class="col-lg-12">

    <div class="box box-info">

        <form id="citrix_meeting" name="citrix_meeting" method="POST" class="form-horizontal">
            
            <?php wp_nonce_field('citrix_meeting_info', 'citrix_meeting_nonce'); ?>

            <div class="box-body">

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="g2m_consumer_key">Go To Meeting Consumer Key</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="g2m_consumer_key" name="g2m_consumer_key" value="<?php echo (isset($consumer_key_and_secret['g2m_consumer_key']) && !empty($consumer_key_and_secret['g2m_consumer_key']) ? $consumer_key_and_secret['g2m_consumer_key'] : '' ); ?>" required="required" />
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="g2m_consumer_secret">Go To Meeting Consumer Secret</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="g2m_consumer_secret" name="g2m_consumer_secret" value="<?php echo (isset($consumer_key_and_secret['g2m_consumer_secret']) && !empty($consumer_key_and_secret['g2m_consumer_secret']) ? $consumer_key_and_secret['g2m_consumer_secret'] : '' ); ?>" required="required" />
                    </div>
                </div>

                <div class="box-footer">
                    <input type="submit" class="btn btn-info" value="Save" />
                </div>

            </div>
            
        </form>

    </div>

</div>