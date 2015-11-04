<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Diary21 - School Management System</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Loading Bootstrap -->
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.css" rel="stylesheet">
	
	
	
	<!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
	<!--[if lt IE 9]>
	  <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
	  <script src="https://cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->
	<style>
	#pluswrap {
		position: fixed;
		width: 100%;
		height:100%;
		display: flex;
		align-items: center;
		background: rgba(255, 255, 255, 0.91);
		top: 0;
	}

	body {
		margin: 0;
	}

	.plus {
		display: flex;
		margin: 0 auto;
	}
	#content_signup
	{
		display:none;
	}
	.error
	{
		font-size:10px;
		color:#C5232A;
	}
	label.valid {
		width: 24px;
		height: 24px;
		background: url(<?php echo base_url('styles/layouts/tdsfront/images/user_auth/valid.png'); ?>) center center no-repeat;
		display: inline-block;
		text-indent: -9999px;
	}
	label.error {
		font-weight: bold;
		color: red;
		padding: 2px 8px;
		margin-top: 2px;
	}
	</style>
</head>
<body class="">
    <input type="hidden" id="ci_base_url" value="<?php echo base_url(); ?>" >
	<div id="pluswrap">
		<div class="plus">
		  <img src="<?php echo base_url('styles/layouts/tdsfront/spelling_bee/loader_home_slider.gif'); ?>" />
		</div>
	</div>
	<div class="item content" id="content_signup">
		<div class="wrapper grey" >

			<div class="container">
			  
				<div class="col-md-12" style="padding:0px; margin-top: 80px;">
					<h2 class="lead text-center editContent">
						Registration For school Creation
					</h2>
				</div>
				<div class="col-md-12">
					<?php //echo form_error('first_name', '<div class="error">', '</div>'); ?>
					<?php //echo form_error('last_name', '<div class="error">', '</div>'); ?>
					<?php //echo form_error('email', '<div class="error">', '</div>'); ?>
					<?php //echo form_error('confirm_email', '<div class="error">', '</div>'); ?> 
					<?php //echo form_error('password', '<div class="error">', '</div>'); ?> 
					<?php //echo form_error('confirm_password', '<div class="error">', '</div>'); ?>
					<?php if(isset($error)) :?>					
						<div class="alert alert-danger">
						  <?php echo $error; ?>
						</div>
					<?php endif; ?>
				</div>

				<div class="col-md-12">
				<div class="row-fluid">
					<form id="registration-form" method="post" action="<?php echo base_url(); ?>front/user_auth/index" class="form-horizontal">
						<input type="hidden" name="back_url" value="<?php echo $back_url; ?>">						
						<div class="form-control-group">
							<label class="control-label" for="name">First Name</label>
							<div class="controls">
								<input type="text" value="<?php echo $this->input->post("first_name")?>" class="form-control input-xlarge" name="first_name" id="first_name" placeholder="First Name *" required>
								<span><font color="#ccc" size="1">More then 3 character</font></span>
								<?php echo form_error('first_name', '<div class="error">', '</div>'); ?>
							</div>
						</div>
						<div class="form-control-group">
							<label class="control-label" for="name">Last Name</label>
							<div class="controls">
							<input type="text" value="<?php echo $this->input->post("last_name")?>" class="form-control input-xlarge" name="last_name" id="last_name" placeholder="Last Name *" required>
							<span><font color="#ccc" size="1">More then 3 character</font></span>
							<?php echo form_error('last_name', '<div class="error">', '</div>'); ?>
							</div>
						</div>
						<div class="form-control-group">
							<label class="control-label" for="name">Email</label>
							<div class="controls">
							<input type="email" value="<?php echo $this->input->post("email")?>" class="form-control input-xlarge" id="email" name="email" id="email" placeholder="Email *" required>
							<span><font color="#ccc" size="1">Valid Email like info@champs21.com</font></span>
							<?php echo form_error('email', '<div class="error">', '</div>'); ?>
							</div>
						</div>
						<div class="form-control-group">
							<label class="control-label" for="name">Confirm Email</label>
							<div class="controls">
							<input type="email" value="<?php echo $this->input->post("confirm_email")?>" class="form-control input-xlarge" name="confirm_email" id="confirm_email" placeholder="Confirm Email *" required>
							<span><font color="#ccc" size="1">Match with Email</font></span>
							<?php echo form_error('confirm_email', '<div class="error">', '</div>'); ?> 
							</div>
						</div>
						<div class="form-control-group">
							<label class="control-label" for="name">Password</label>
							<div class="controls">
							<input type="password" class="form-control input-xlarge"  name="password" id="password" placeholder="Password *" required>
							<span><font color="#ccc" size="1">At least 6 character</font></span>
							<?php echo form_error('password', '<div class="error">', '</div>'); ?> 
							</div>
						</div>
						<div class="form-control-group">
							<label class="control-label" for="name">Confirm Password</label>
							<div class="controls">
							<input type="password" class="form-control input-xlarge"  name="confirm_password" id="confirm_password" placeholder="Confirm Password *" required>
							<span><font color="#ccc" size="1">Match with password</font></span>
							<?php echo form_error('confirm_password', '<div class="error">', '</div>'); ?>
							</div>
						</div>
						<div class="form-control-group">							
							<button name="submit" type="submit" id="submit" class="btn btn-primary btn-success btn-lg">
								<i class="fa fa-thumbs-up"></i> Sign in
							</button>
						</div>
					</form>

				</div><!-- /.row -->
				</div><!-- /.col-md-5 -->

				

			</div><!-- /.container -->

		</div><!-- /.wrapper -->
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.14.0/jquery.validate.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	
	<script src="<?php echo base_url('scripts/user_auth/user_auth.js'); ?>"></script>
	<script>
		$('#pluswrap').show();
		$(window).bind("load", function() {  
			$('#pluswrap').hide();			
			$("#content_signup").show();  					 
		});
	</script>
</body>
</html>

