<link href="/bootstrap/bootstrap-tour/build/css/bootstrap-tour.min.css" rel="stylesheet">

<div class="item content" id="content_section26">
    <div class="wrapper grey" >

        <div class="container">
            <div class="col-md-12"  style="padding:0px; margin-top: 120px; margin-bottom:30px;">
                <div style="margin:30px 100px; float:left; width:80%;background: white; ">

                    <div class="col-md-12"  style="padding:0px; ">
                        <h2 class="lead text-center editContent"  style="color:#66D56A; font-weight: bold;margin-top: 20px;">
                            Demo Help Guide
                        </h2>
                    </div>


                    <div class="col-lg-12">
                        <div class="col-md-2 center">
                            <button type="button" id="start-tour" name="start-tour" class="btn btn-primary">Start Tour</button>
                        </div>
                    </div>
                    
                    <div class="clearfix"></div>


                    <form class="form-horizontal checkout-frm" method="POST" id="payment-form">


                        <!-- Text input-->
                        <div class="form-group email-wrapper">
                            <label class="col-md-4 control-label" for="email">Email</label>  
                            <div class="col-md-6">
                                <input id="email" name="email" placeholder="Email" class="form-control input-md" type="email"  />
                            </div>
                        </div>

                        <!-- Text input-->
                        <div class="form-group card-wrapper">
                            <label class="col-md-4 control-label" for="card_number">Card Number</label>  
                            <div class="col-md-6">
                                <input id="card_number" placeholder="Card Number" class="form-control input-md" size="16" maxlength="16" type="text" data-stripe="number" />
                            </div>
                        </div>

                        <!-- Text input-->
                        <div class="form-group cvc-wrapper">
                            <label class="col-md-4 control-label" for="cvc">CVC</label>  
                            <div class="col-md-2">
                                <input id="cvc" placeholder="CVC" class="form-control input-md" size="4" maxlength="4" type="text" data-stripe="cvc" />
                            </div>
                        </div>

                        <!-- Text input-->
                        <div class="form-group exp-wrapper">
                            <label class="col-md-4 control-label" for="expire_month">Expiration</label>  
                            <div class="col-md-2">
                                <input id="expire_month" placeholder="Month" class="form-control input-md" size="2" maxlength="2" type="text" data-stripe="exp-month" />
                            </div>
                            <div class="col-md-2">
                                <input id="expire_year" placeholder="Year" class="form-control input-md" size="2" maxlength="4" type="text" data-stripe="exp-year" />
                            </div>
                        </div>

                        <!-- Text input-->
                        <div class="form-group address-wrapper">
                            <label class="col-md-4 control-label" for="address_line1">Address</label>  
                            <div class="col-md-6">
                                <input id="address_line1" placeholder="Address" class="form-control input-md" size="50" maxlength="50" type="text" data-stripe="address_line1" />
                            </div>
                        </div>

                        <!-- Button -->
                        <div class="form-group checkout-wrapper">
                            <label class="col-md-4 control-label" for="checkout"></label>
                            <div class="col-md-4">
                                <button type="button" id="checkout" name="checkout" class="btn btn-primary">Checkout</button>
                            </div>
                        </div>

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
    .popover-title {
        background-color: #64b846;
        color: #fffddd;
    }
</style>

<script src="/bootstrap/bootstrap-tour/build/js/bootstrap-tour.min.js"></script>
<script src="/js/custom/web-tour.js"></script>