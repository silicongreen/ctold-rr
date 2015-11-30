<div class="clearfix"></div>
<form class="form-horizontal code-frm" method="POST" id="payment-form">

    <?php if (!empty($message)) { ?>
        <?php
        if (isset($success) && $success === true) {
            $str_alert_class = 'alert-success';
        }
        if (isset($error) && $error === true) {
            $str_alert_class = 'alert-danger';
        }
        ?>

        <div class="form-group">
            <div class="col-lg-12">
                <div class="<?php echo $str_alert_class; ?>">
                    <?php echo $message; ?>
                </div>
            </div>
        </div>
    <?php } ?>

    <fieldset>  
        <!-- Text input-->
        <div class="form-group">
            <label class="col-md-3 control-label" for="payment_code">Payment Code</label>  
            <div class="col-md-5">
                <input id="payment_code" name="payment_code" placeholder="Payment Code" class="form-control input-md" required="required" type="text"  required="required" />
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-6 float-right">
                <button type="submit" name="payment_code_submit" class="btn btn-primary">Submit</button>
            </div>
        </div>

    </fieldset>

</form>

<style type="text/css">
    .code-frm {
        margin: 150px auto 50px;
        width: 50%;
    }
    label {
        cursor: pointer;
    }
    .float-right {
        float: right;
    }
</style>