<?php if ( $type == 'plugin' ) :?>
<div class="updated" style="padding: 0; margin: 0; border: none; background: none;">
	<style type="text/css">
.akismet_activate{min-width:825px;border:1px solid #4F800D;padding:5px;margin:15px 0;background:#83AF24;background-image:-webkit-gradient(linear,0% 0,80% 100%,from(#83AF24),to(#4F800D));background-image:-moz-linear-gradient(80% 100% 120deg,#4F800D,#83AF24);-moz-border-radius:3px;border-radius:3px;-webkit-border-radius:3px;position:relative;overflow:hidden}.akismet_activate .aa_a{position:absolute;top:-5px;right:10px;font-size:140px;color:#769F33;font-family:Georgia, "Times New Roman", Times, serif;z-index:1}.akismet_activate .aa_button{font-weight:bold;border:1px solid #029DD6;border-top:1px solid #06B9FD;font-size:15px;text-align:center;padding:9px 0 8px 0;color:#FFF;background:#029DD6;background-image:-webkit-gradient(linear,0% 0,0% 100%,from(#029DD6),to(#0079B1));background-image:-moz-linear-gradient(0% 100% 90deg,#0079B1,#029DD6);-moz-border-radius:2px;border-radius:2px;-webkit-border-radius:2px}.akismet_activate .aa_button:hover{text-decoration:none !important;border:1px solid #029DD6;border-bottom:1px solid #00A8EF;font-size:15px;text-align:center;padding:9px 0 8px 0;color:#F0F8FB;background:#0079B1;background-image:-webkit-gradient(linear,0% 0,0% 100%,from(#0079B1),to(#0092BF));background-image:-moz-linear-gradient(0% 100% 90deg,#0092BF,#0079B1);-moz-border-radius:2px;border-radius:2px;-webkit-border-radius:2px}.akismet_activate .aa_button_border{border:1px solid #006699;-moz-border-radius:2px;border-radius:2px;-webkit-border-radius:2px;background:#029DD6;background-image:-webkit-gradient(linear,0% 0,0% 100%,from(#029DD6),to(#0079B1));background-image:-moz-linear-gradient(0% 100% 90deg,#0079B1,#029DD6)}.akismet_activate .aa_button_container{cursor:pointer;display:inline-block;background:#DEF1B8;padding:5px;-moz-border-radius:2px;border-radius:2px;-webkit-border-radius:2px;width:266px}.akismet_activate .aa_description{position:absolute;top:22px;left:285px;margin-left:25px;color:#E5F2B1;font-size:15px;z-index:1000}.akismet_activate .aa_description strong{color:#FFF;font-weight:normal}
	</style>
	<form name="akismet_activate" action="<?php echo esc_url( Akismet_Admin::get_page_url() ); ?>" method="POST">
		<div class="akismet_activate">
			<div class="aa_a">A</div>
			<div class="aa_button_container" onclick="document.akismet_activate.submit();">
				<div class="aa_button_border">
					<div class="aa_button"><?php esc_html_e('Activate your Akismet account', 'akismet');?></div>
				</div>
			</div>
			<div class="aa_description"><?php _e('<strong>Almost done</strong> - activate Akismet and say goodbye to spam', 'akismet');?></div>
		</div>
	</form>
</div>
<?php elseif ( $type == 'spam-check' ) :?>
<div id="akismet-warning" class="updated fade">
	<p><strong><?php esc_html_e( 'Akismet has detected a problem.', 'akismet' );?></strong></p>
	<p><?php printf( __( 'Some comments have not yet been checked for spam by Akismet. They have been temporarily held for moderation and will automatically be rechecked later.', 'akismet' ) ); ?></p>
	<?php if ( $link_text ) { ?>
		<p><?php echo $link_text; ?></p>
	<?php } ?>
</div>
<?php elseif ( $type == 'version' ) :?>
<div id="akismet-warning" class="updated fade"><p><strong><?php printf( esc_html__('Akismet %s requires WordPress 3.0 or higher.', 'akismet'), AKISMET_VERSION);?></strong> <?php printf(__('Please <a href="%1$s">upgrade WordPress</a> to a current version, or <a href="%2$s">downgrade to version 2.4 of the Akismet plugin</a>.', 'akismet'), 'https://codex.wordpress.org/Upgrading_WordPress', 'https://wordpress.org/extend/plugins/akismet/download/');?></p></div>
<?php elseif ( $type == 'alert' ) :?>
<div class='error'>
	<p><strong><?php printf( esc_html__( 'Akismet Error Code: %s', 'akismet' ), $code ); ?></strong></p>
	<p><?php echo esc_html( $msg ); ?></p>
	<p><?php

	/* translators: the placeholder is a clickable URL that leads to more information regarding an error code. */
	printf( esc_html__( 'For more information: %s' , 'akismet'), '<a href="https://akismet.com/errors/' . $code . '">https://akismet.com/errors/' . $code . '</a>' );

	?>
	</p>
</div>
<?php elseif ( $type == 'missing-functions' ) :?>
<div class="wrap alert critical">
	<h3 class="key-status failed"><?php esc_html_e('Network functions are disabled.', 'akismet'); ?></h3>
	<p class="description"><?php printf( __('Your web host or server administrator has disabled PHP&#8217;s <code>gethostbynamel</code> function.  <strong>Akismet cannot work correctly until this is fixed.</strong>  Please contact your web host or firewall administrator and give them <a href="%s" target="_blank">this information about Akismet&#8217;s system requirements</a>.', 'akismet'), 'http://blog.akismet.com/akismet-hosting-faq/'); ?></p>
</div>
<?php elseif ( $type == 'servers-be-down' ) :?>
<div class="wrap alert critical">
	<h3 class="key-status failed"><?php esc_html_e("Akismet can&#8217;t connect to your site.", 'akismet'); ?></h3>
	<p class="description"><?php printf( __('Your firewall may be blocking Akismet. Please contact your host and refer to <a href="%s" target="_blank">our guide about firewalls</a>.', 'akismet'), 'http://blog.akismet.com/akismet-hosting-faq/'); ?></p>
</div>
<?php elseif ( $type == 'active-dunning' ) :?>
<div class="wrap alert critical">
	<h3 class="key-status"><?php esc_html_e("Please update your payment information.", 'akismet'); ?></h3>
	<p class="description"><?php printf( __('We cannot process your payment. Please <a href="%s" target="_blank">update your payment details</a>.', 'akismet'), 'https://akismet.com/account/'); ?></p>
</div>
<?php elseif ( $type == 'cancelled' ) :?>
<div class="wrap alert critical">
	<h3 class="key-status"><?php esc_html_e("Your Akismet plan has been cancelled.", 'akismet'); ?></h3>
	<p class="description"><?php printf( __('Please visit your <a href="%s" target="_blank">Akismet account page</a> to reactivate your subscription.', 'akismet'), 'https://akismet.com/account/'); ?></p>
</div>
<?php elseif ( $type == 'suspended' ) :?>
<div class="wrap alert critical">
	<h3 class="key-status failed"><?php esc_html_e("Your Akismet subscription is suspended.", 'akismet'); ?></h3>
	<p class="description"><?php printf( __('Please contact <a href="%s" target="_blank">Akismet support</a> for assistance.', 'akismet'), 'https://akismet.com/contact/'); ?></p>
</div>
<?php elseif ( $type == 'active-notice' && $time_saved ) :?>
<div class="wrap alert active">
	<h3 class="key-status"><?php echo esc_html( $time_saved ); ?></h3>
	<p class="description"><?php printf( __('You can help us fight spam and upgrade your account by <a href="%s" target="_blank">contributing a token amount</a>.', 'akismet'), 'https://akismet.com/account/upgrade/'); ?></p>
</div>
<?php elseif ( $type == 'missing' ) :?>
<div class="wrap alert critical">
	<h3 class="key-status failed"><?php esc_html_e( 'There is a problem with your API key.', 'akismet'); ?></h3>
	<p class="description"><?php printf( __('Please contact <a href="%s" target="_blank">Akismet support</a> for assistance.', 'akismet'), 'https://akismet.com/contact/'); ?></p>
</div>
<?php elseif ( $type == 'no-sub' ) :?>
<div class="wrap alert critical">
	<h3 class="key-status failed"><?php esc_html_e( 'You don&#8217;t have an Akismet plan.', 'akismet'); ?></h3>
	<p class="description">
		<?php printf( __( 'In 2012, Akismet began using subscription plans for all accounts (even free ones). A plan has not been assigned to your account, and we&#8217;d appreciate it if you&#8217;d <a href="%s" target="_blank">sign into your account</a> and choose one.', 'akismet'), 'https://akismet.com/account/upgrade/' ); ?>
		<br /><br />
		<?php printf( __( 'Please <a href="%s" target="_blank">contact our support team</a> with any questions.', 'akismet' ), 'https://akismet.com/contact/' ); ?>
	</p>
</div>
<?php elseif ( $type == 'new-key-valid' ) :?>
<div class="wrap alert active">
	<h3 class="key-status"><?php esc_html_e('Akismet is now activated. Happy blogging!', 'akismet'); ?></h3>
</div>
<?php elseif ( $type == 'new-key-invalid' ) :?>
<div class="wrap alert critical">
	<h3 class="key-status"><?php esc_html_e( 'The key you entered is invalid. Please double-check it.' , 'akismet'); ?></h3>
</div>
<?php elseif ( $type == 'existing-key-invalid' ) :?>
<div class="wrap alert critical">
	<h3 class="key-status"><?php esc_html_e( 'Your API key is no longer valid. Please enter a new key or contact support@akismet.com.' , 'akismet'); ?></h3>
</div>
<?php elseif ( $type == 'new-key-failed' ) :?>
<div class="wrap alert critical">
	<h3 class="key-status"><?php esc_html_e( 'The API key you entered could not be verified.' , 'akismet'); ?></h3>
	<p class="description"><?php printf( __('The connection to akismet.com could not be established. Please refer to <a href="%s" target="_blank">our guide about firewalls</a> and check your server configuration.', 'akismet'), 'http://blog.akismet.com/akismet-hosting-faq/'); ?></p>
</div>
<?php elseif ( $type == 'limit-reached' && in_array( $level, array( 'yellow', 'red' ) ) ) :?>
<div class="wrap alert critical">
	<?php if ( $level == 'yellow' ): ?>
	<h3 class="key-status failed"><?php esc_html_e( 'You&#8217;re using your Akismet key on more sites than your Pro subscription allows.', 'akismet' ); ?></h3>
	<p class="description">
		<?php printf( __( 'Your Pro subscription allows the use of Akismet on only one site. Please <a href="%s" target="_blank">purchase additional Pro subscriptions</a> or upgrade to an Enterprise subscription that allows the use of Akismet on unlimited sites.', 'akismet' ), 'http://docs.akismet.com/billing/add-more-sites/' ); ?>
		<br /><br />
		<?php printf( __( 'Please <a href="%s" target="_blank">contact our support team</a> with any questions.', 'akismet' ), 'https://akismet.com/contact/'); ?>
	</p>
	<?php elseif ( $level == 'red' ): ?>
	<h3 class="key-status failed"><?php esc_html_e( 'You&#8217;re using Akismet on far too many sites for your Pro subscription.', 'akismet' ); ?></h3>
	<p class="description">
		<?php printf( __( 'To continue your service, <a href="%s" target="_blank">upgrade to an Enterprise subscription</a>, which covers an unlimited number of sites.', 'akismet'), 'https://akismet.com/account/upgrade/' ); ?></p>
		<br /><br />
		<?php printf( __( 'Please <a href="%s" target="_blank">contact our support team</a> with any questions.', 'akismet' ), 'https://akismet.com/contact/'); ?></p>
	</p>
	<?php endif; ?>
</div>
<?php endif;?>