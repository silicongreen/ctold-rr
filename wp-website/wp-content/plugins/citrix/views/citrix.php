<section class="content-header">
    <h1>Citrix Configuration</h1>
</section>

<div class="col-lg-12">

    <div class="box box-info">

        <form id="citrix_base" name="citrix_base" method="POST" class="form-horizontal">

            <?php wp_nonce_field('citrix_base_info', 'citrix_base_nonce'); ?>

            <div class="box-body">

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="citrix_base_url">Base URI</label>
                    <div class="col-sm-8">
                        <input type="text" id="citrix_base_url" name="citrix_base_url" class="form-control" value="<?php echo (isset($citrix_user['citrix_base_url']) && !empty($citrix_user['citrix_base_url']) ? $citrix_user['citrix_base_url'] : '' ); ?>" required="required" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="citrix_base_url">OAuth URI</label>
                    <div class="col-sm-8">
                        <input type="text" id="citrix_oauth_url" name="citrix_oauth_url" class="form-control" value="<?php echo (isset($citrix_user['citrix_oauth_url']) && !empty($citrix_user['citrix_oauth_url']) ? $citrix_user['citrix_oauth_url'] : '' ); ?>" required="required" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="citrix_port">SSL Port</label>
                    <div class="col-sm-8">
                        <input type="text" id="citrix_port" name="citrix_port" class="form-control" value="<?php echo (isset($citrix_user['citrix_port']) && !empty($citrix_user['citrix_port']) ? $citrix_user['citrix_port'] : '' ); ?>" required="required" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="citrix_client_email">Client Email</label>
                    <div class="col-sm-8">
                        <input type="text" id="citrix_client_email" name="citrix_client_email" class="form-control" value="<?php echo (isset($citrix_user['citrix_client_email']) && !empty($citrix_user['citrix_client_email']) ? $citrix_user['citrix_client_email'] : '' ); ?>" required="required" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="citrix_client_fname">Client First Name</label>
                    <div class="col-sm-8">
                        <input type="text" id="citrix_client_fname" name="citrix_client_fname" class="form-control" value="<?php echo (isset($citrix_user['citrix_client_fname']) && !empty($citrix_user['citrix_client_fname']) ? $citrix_user['citrix_client_fname'] : '' ); ?>" required="required" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="citrix_client_lname">Client Last Name</label>
                    <div class="col-sm-8">
                        <input type="text" id="citrix_client_lname" name="citrix_client_lname" class="form-control" value="<?php echo (isset($citrix_user['citrix_client_lname']) && !empty($citrix_user['citrix_client_lname']) ? $citrix_user['citrix_client_lname'] : '' ); ?>" required="required" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="citrix_client_pwd">Client Password</label>
                    <div class="col-sm-8">
                        <input type="text" id="citrix_client_pwd" name="citrix_client_pwd" class="form-control" value="<?php echo (isset($citrix_user['citrix_client_pwd']) && !empty($citrix_user['citrix_client_pwd']) ? $citrix_user['citrix_client_pwd'] : '' ); ?>" required="required" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="citrix_client_tz">Default Timezone</label>
                    <div class="col-sm-8">
                        <select id="citrix_client_tz" name="citrix_client_tz" class="form-control">
                            <?php
                            foreach ($time_zone_list as $tz) {
                                $selected = '';
                                if (isset($citrix_user['citrix_client_tz']) && !empty($citrix_user['citrix_client_tz']) && ($citrix_user['citrix_client_tz'] == $tz)) {
                                    $selected = 'selected="selected"';
                                }
                                ;
                                ?>
                                <option value="<?php echo $tz; ?>" <?php echo $selected; ?>><?php echo $tz; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="g2m_api_uri">Go To Meeting URI</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="g2m_api_uri" name="g2m_api_uri" value="<?php echo (isset($citrix_user['g2m_api_uri']) && !empty($citrix_user['g2m_api_uri']) ? $citrix_user['g2m_api_uri'] : '' ); ?>" required="required" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="g2m_consumer_key">Go To Meeting Consumer Key</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="g2m_consumer_key" name="g2m_consumer_key" value="<?php echo (isset($citrix_user['g2m_consumer_key']) && !empty($citrix_user['g2m_consumer_key']) ? $citrix_user['g2m_consumer_key'] : '' ); ?>" required="required" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="g2m_consumer_secret">Go To Meeting Consumer Secret</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="g2m_consumer_secret" name="g2m_consumer_secret" value="<?php echo (isset($citrix_user['g2m_consumer_secret']) && !empty($citrix_user['g2m_consumer_secret']) ? $citrix_user['g2m_consumer_secret'] : '' ); ?>" required="required" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="g2w_api_uri">Go To Webinar URI</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="g2w_api_uri" name="g2w_api_uri" value="<?php echo (isset($citrix_user['g2w_api_uri']) && !empty($citrix_user['g2w_api_uri']) ? $citrix_user['g2w_api_uri'] : '' ); ?>" required="required" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="g2w_consumer_key">Go To Webinar Consumer Key</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="g2w_consumer_key" name="g2w_consumer_key" value="<?php echo (isset($citrix_user['g2w_consumer_key']) && !empty($citrix_user['g2w_consumer_key']) ? $citrix_user['g2w_consumer_key'] : '' ); ?>" required="required" />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" for="g2w_consumer_secret">Go To Webinar Consumer Secret</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="g2w_consumer_secret" name="g2w_consumer_secret" value="<?php echo (isset($citrix_user['g2w_consumer_secret']) && !empty($citrix_user['g2w_consumer_secret']) ? $citrix_user['g2w_consumer_secret'] : '' ); ?>" required="required" />
                    </div>
                </div>

                <div class="box-footer">
                    <input type="submit" class="btn btn-info" value="Save"/>
                </div>

            </div>

        </form>

    </div>

</div>