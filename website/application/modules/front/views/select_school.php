<?php
$paid_schools = get_paid_school_droupdown();
?>
<html>
    <head>
        <title>Select School</title>
        <link rel="stylesheet" id="bootstrap-css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" type="text/css" media="all" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.css" rel="stylesheet">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
            <!--[if lt IE 9]>
             <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
             <script src="https://cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <form class="form-horizontal col-md-10" method="post" action="">
             <div class="error_validation" class="col-md-12" ><?php echo validation_errors(); ?></div>
            <fieldset>

                <!-- Form Name -->
                <legend></legend>

                <!-- Select Basic -->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="selectbasic">School Name</label>
                    
                    <div class="col-md-4">
                        <?php echo $paid_schools; ?>
                    </div>
                </div>

                <!-- Text input-->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="school_code">School Code</label>  
                    <div class="col-md-4">
                        <input id="school_code" name="school_code" type="text" placeholder="school code" class="form-control input-md" >
                        <span class="help-block">code supply from your school for registration</span>  
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-md-4 control-label" for="singlebutton"></label>
                    <div class="col-md-4">
                        <button name="submit" type="submit" id="submit" class="btn btn-primary btn-success btn-lg">
                            <i class="fa fa-thumbs-up"></i> Continue
                        </button>
                    </div>
                </div>

            </fieldset>
        </form>
    </body>
</html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<style>
.error_validation p {
    color: red;
    padding: 5px 0px;
    font-weight: bold;
}
</style>

