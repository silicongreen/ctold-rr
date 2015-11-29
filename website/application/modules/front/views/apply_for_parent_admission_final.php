<html>
    <head>
        <title>Select School</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" type="text/css" media="all" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker.css" type="text/css" media="all" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.css" type="text/css" media="all" />
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
		margin: 0px !important;
                float:left;
                width: 100%;
	}

	.plus {
		display: flex;
		margin: 0 auto;
	}
	#content_signup
	{
		display:none;
	}
	
	</style>
    </head>
    <body>
        <input type="hidden" id="ci_base_url" value="<?php echo base_url(); ?>" >
        <div id="pluswrap">
                <div class="plus">
                  <img src="<?php echo base_url('styles/layouts/tdsfront/spelling_bee/loader_home_slider.gif'); ?>" />
                </div>
        </div>
        <div class="item content" id="content_signup">
            <div class="wrapper grey" >

                <div class="container">			  
                    <div class="col-md-12" style="padding:0px; margin-top: 40px;">
                        <h2 class="lead text-center editContent"  style="color:#66D56A; font-weight: bold; font-size:25px;">
                                <?php if($error) :?>Sign Up Error For Guardian<?php else: ?>Congratulation!!! <?php endif; ?>
                        </h2>
                    </div>
                    <div class="col-md-12" style="padding-left:0px; padding-right: 0px;">
                        <?php if($error) :?>					
                                <div class="alert alert-danger">
                                  There is some Error.Try Again or Contact to the Admin.
                                </div>
                        <?php else: ?>
                                <div class="alert alert-success">
                                  Your Sign Up process is complete.Please Sign In.
                                </div>
                                <image src="/images/smile.png" id="smily" style="position: absolute;" />
                                <div class="col-md-8 widthp_60">
                                    <div class="row-fluid">
                                        <?php if($guardian['fulname'] != ""):?>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">Your Name :</label>  
                                                <div class="col-md-4">
                                                    <?php echo $guardian['fulname'];?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if($guardian['username'] != ""):?>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label">UserName :</label>  
                                                <div class="col-md-4">
                                                    <?php echo $guardian['username'];?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <legend></legend>
                                        <?php foreach($student as $row):?>
                                            <?php if($row['fulname'] != ""):?>
                                                <div class="form-group">
                                                    <label class="col-md-4 control-label">Student Name :</label>  
                                                    <div class="col-md-4">
                                                        <?php echo $row['fulname'];?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            <?php if($row['username'] != ""):?>
                                                <div class="form-group">
                                                    <label class="col-md-4 control-label">Student UserName :</label>  
                                                    <div class="col-md-4">
                                                        <?php echo $row['username'];?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if($row['admission_no'] != ""):?>
                                                <div class="form-group">
                                                    <label class="col-md-4 control-label">Admission No :</label>  
                                                    <div class="col-md-4">
                                                        <?php echo $row['admission_no'];?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            <legend></legend>
                                        <?php endforeach; ?>
                                            
                                        <div class="form-group"  style="width: 60%;margin-left: 288px;">                                            
                                            <div class="col-md-12">
                                                <p><span>Please <a style="color: #2CABE1;" href="<?php echo base_url('login'); ?>">Click here</a> to login into your account or go to the following url:</span></p>
                                                <a style="color: #2CABE1;" href="<?php echo base_url('login'); ?>"><?php echo base_url('login'); ?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        <?php endif; ?>
                    </div>
                    
                </div><!-- /.container -->

            </div><!-- /.wrapper -->
        </div>
    </body>
</html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="<?php echo base_url('scripts/iframe-resizer/js/iframeResizer.contentWindow.js?v=1'); ?>"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script>
 //document.domain = "champs21.com";
    $('#pluswrap').show();
        $(window).bind("load", function() {  
                $('#pluswrap').hide();			
                $("#content_signup").show(); 
        });
</script>
<style>
.error_validation p {
    color: red;
    padding: 5px 0px;
    font-weight: bold;
}
.alert-success
{
    color: #ffffff;
    background-color: #61D766;
    border-color: #d6e9c6;
    text-align: center;
    padding:8px;
}
#smily
{
    position: absolute;
    margin-top: -30px;
    
}
.widthp_60
{
    width: 75%;
    margin-left: 143px;
    margin-top: 45px;
}
.form-group {
    margin-bottom: 15px;
    width: 58%;
    margin: 15px 130px;
}
.control-label
{
    width:50%;
    float:left;
}
</style>