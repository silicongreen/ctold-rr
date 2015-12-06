<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"></script>
<script type="text/javascript" src="https://www.2checkout.com/checkout/api/2co.min.js"></script>

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
    <input type="hidden" name="token_request" value="67657575" />
    <input type="hidden" id="street_number" name="street_number" />
    <input type="hidden" id="street_address" name="street_address"  />
    <input type="hidden" id="city" name="city"  />
    <input type="hidden" id="state" name="state"  />
    <input type="hidden" id="zip_code" name="zip_code"  />
    <input type="hidden" id="country" name="country"  />

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
                <input id="card_number" placeholder="Card Number" class="form-control input-md" size="16" maxlength="16" type="text" required="required" />
            </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
            <label class="col-md-4 control-label" for="cvv">CVV</label>  
            <div class="col-md-3">
                <input id="cvv" placeholder="CVV" class="form-control input-md" size="10" maxlength="4" type="text" required="required" />
            </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
            <label class="col-md-4 control-label" for="expire_month">Expiration</label>  
            <div class="col-md-3">
                <input id="expire_month" placeholder="Month" class="form-control input-md" size="4" maxlength="2" type="text" required="required" />
            </div>
            <div class="col-md-3">
                <input id="expire_year" placeholder="Year" class="form-control input-md" size="2" maxlength="4" type="text" required="required" />
            </div>
        </div>
        
        <div class="col-md-12"  style="padding:0px; ">
            <h2 class="lead text-center editContent"  style="color:#66D56A; font-weight: bold;margin-top: 20px;">
                Billing Address
            </h2>
        </div>

        <!-- Text input-->
        <div class="form-group">
            <label class="col-md-4 control-label" for="billing_name">Name</label>  
            <div class="col-md-6">
                <input id="billing_name" name="billing_name" placeholder="Enter your Name" class="form-control input-md" size="50" maxlength="50" type="text" required="required" />
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-md-4 control-label" for="billing_address">Billing Address</label>  
            <div class="col-md-6">
                <input id="billing_address" name="billing_address" placeholder="Enter your Billing address" class="form-control input-md" size="50" maxlength="50" type="text" required="required" />
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
<script>
    // Called when token created successfully.
    var successCallback = function(data) {
        var myForm = document.getElementById('payment-form');

        // Set the token as the value for the token input
        myForm.token_request.value = data.response.token.token;

        // IMPORTANT: Here we call `submit()` on the form element directly instead of using jQuery to prevent and infinite token request loop.
        myForm.submit();
    };
    
    // Called when token creation fails.
    var errorCallback = function(data) {
        if (data.errorCode === 200) {
          // This error code indicates that the ajax call failed. We recommend that you retry the token request.
        } else {
          alert(data.errorMsg);
        }
    };
  
    var tokenRequest = function() {
        // Setup token request arguments
        var args = {
          sellerId: "<?php echo $seller_id; ?>",
          publishableKey: "<?php echo $public_key; ?>",
          ccNo: $("#card_number").val(),
          cvv: $("#cvv").val(),
          expMonth: $("#expire_month").val(),
          expYear: $("#expire_year").val()
        };

        // Make the token request
        TCO.requestToken(successCallback, errorCallback, args);
    };
    
    
    $(function() {
        // Pull in the public encryption key for our environment
        TCO.loadPubKey('<?php echo $payment_type; ?>');

        $("#checkout").click(function(){
            // Call our token request function
            tokenRequest();

            // Prevent form from submitting
            return false;
        });
    });
</script>
<script>
    var countryRestrict = { 'country': ($("#countries").val() == undefined) ? "US" : $("#countries").val() };
    var autocomplete;
    var input;
    
    var componentForm = {
          street_number: 'short_name',
          route: 'long_name',
          locality: 'long_name',
          administrative_area_level_1: 'short_name',
          postal_code: 'short_name',
          country: 'short_name'
    };
    
    var registrationForm = {
          street_number: 'street_number',
          route: 'street_address',
          locality: 'city',
          administrative_area_level_1: 'state',
          postal_code: 'zip_code',
          country: 'country'
    };
    
    var add_src = "";
    var cnt = 0;
    
    function initialize() 
    {
          input = /** @type {HTMLInputElement} */(
                        document.getElementById('billing_address')
          );

          autocomplete = new google.maps.places.Autocomplete(input); //, { componentRestrictions: countryRestrict }

          
          google.maps.event.addListener(autocomplete, 'place_changed', function() {
                var place = autocomplete.getPlace();
                
                if (!place.geometry) 
                {
                    return;
                }

                var address = '';
                console.log(place.address_components);
                if (place.address_components) 
                {
                    for (var i = 0; i < place.address_components.length; i++) 
                    {
                        var addressType = place.address_components[i].types[0];
                        if (registrationForm[addressType])
                        {
                            var val = place.address_components[i][componentForm[addressType]];
                            console.log(addressType);
                            if ( registrationForm[addressType] == "txt_address" && addressType == "street_number" )
                            {
                                add_src += val + ", ";
                                cnt++;
                                if ( cnt == 2 )
                                {
                                    $("#" + registrationForm[addressType]).val(add_src.substr(0, add_src.length - 2));
                                }
                            }
                            else
                            {
                                $("#" + registrationForm[addressType]).val(val);
                            }
                        }
//                        if (componentForm[addressType]) {
//                          var val = place.address_components[i][componentForm[addressType]];
//                          document.getElementById(addressType).value = val;
//                        }
                    }

                    address = [
                                (place.address_components[0] && place.address_components[0].short_name || ''),
                                (place.address_components[1] && place.address_components[1].short_name || ''),
                                (place.address_components[2] && place.address_components[2].short_name || '')
                              ].join('+');
                }
          });

    }

    google.maps.event.addDomListener(window, 'load', initialize);

</script>
