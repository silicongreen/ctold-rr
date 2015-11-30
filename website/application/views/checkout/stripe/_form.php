<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript" src="/js/stripe/stripe.js"></script>

<div id="stripe-publish-key" data="<?php echo $publishable_key; ?>" style="display: none;"></div>

<div class="item content" id="content_section26">
    <div class="wrapper grey" >

        <div class="container">
            <div class="col-md-12"  style="padding:0px; margin-top: 120px; margin-bottom:30px;">
            <div style="margin:30px 100px; float:left; width:80%;background: white; ">
          
            <div class="col-md-12"  style="padding:0px; ">
                <h2 class="lead text-center editContent"  style="color:#66D56A; font-weight: bold;margin-top: 20px;">
                    Checkout
                </h2>
            </div>

<form class="form-horizontal checkout-frm" method="POST" id="payment-form">

    <input type="hidden" name="i_tmp_free_user_data_id" value="<?php echo $i_tmp_free_user_data_id; ?>" />
    <input type="hidden" name="i_tmp_school_creation_data_id" value="<?php echo $i_tmp_school_creation_data_id; ?>" />
    <input type="hidden" name="school_type" value="<?php echo $school_type; ?>" />

    <div class="form-group pe-wrapper">
        <div class="col-lg-12">
            <div class="alert-danger payment-errors"></div>
        </div>
    </div>

    <?php if (!empty($message)) { ?>
        <?php
        if (isset($success) && $success === true) {
            $str_alert_class = 'alert alert-success';
        }
        if (isset($error) && $error === true) {
            $str_alert_class = 'alert alert-danger';
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
            <label class="col-md-4 control-label" for="email">Email</label>  
            <div class="col-md-6">
                <input id="email" name="email" placeholder="Email" class="form-control input-md" required="required" type="email"  required="required" />
            </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
            <label class="col-md-4 control-label" for="card_number">Card Number</label>  
            <div class="col-md-6">
                <input id="card_number" placeholder="Card Number" class="form-control input-md" size="16" maxlength="16" type="text" data-stripe="number" required="required" />
            </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
            <label class="col-md-4 control-label" for="cvc">CVC</label>  
            <div class="col-md-2">
                <input id="cvc" placeholder="CVC" class="form-control input-md" size="4" maxlength="4" type="text" data-stripe="cvc" required="required" />
            </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
            <label class="col-md-4 control-label" for="expire_month">Expiration</label>  
            <div class="col-md-2">
                <input id="expire_month" placeholder="Month" class="form-control input-md" size="2" maxlength="2" type="text" data-stripe="exp-month" required="required" />
            </div>
            <div class="col-md-2">
                <input id="expire_year" placeholder="Year" class="form-control input-md" size="2" maxlength="4" type="text" data-stripe="exp-year" required="required" />
            </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
            <label class="col-md-4 control-label" for="address_line1">Address</label>  
            <div class="col-md-6">
                <input id="address_line1" placeholder="Address" class="form-control input-md" size="50" maxlength="50" type="text" data-stripe="address_line1" required="required" />
            </div>
        </div>

        <!-- Button -->
        <div class="form-group">
            <label class="col-md-4 control-label" for="checkout"></label>
            <div class="col-md-4">
                <button id="checkout" name="checkout" class="btn btn-primary">Checkout</button>
            </div>
        </div>

    </fieldset>
</form>
                </div><!-- /.col-md-5 -->

            </div> 
          </div>      

        </div><!-- /.container -->

    </div><!-- /.wrapper -->
</div>

<style type="text/css">
    .checkout-frm {
        margin: 80px auto 10px;
        width: 50%;
    }
</style>