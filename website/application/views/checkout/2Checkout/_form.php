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
    <input type="hidden" id="token_request" name="token_request" value="0" />
    
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
            <label class="col-md-4 control-label" for="billing_name">Name</label>  
            <div class="col-md-6">
                <input id="billing_name" name="billing_name" placeholder="Enter your Name" class="form-control input-md" size="50" maxlength="50" type="text" required="required" />
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
            <label class="col-md-4 control-label" for="street_address">Billing Address</label>  
            <div class="col-md-6">
                <input id="street_address" name="street_address" placeholder="Street address" class="form-control input-md" size="50" maxlength="50" type="text" required="required" />
            </div>
        </div>
    
        <div class="form-group">
            <label class="col-md-4 control-label" for="city">City</label>  
            <div class="col-md-6">
                <input id="city" name="city" placeholder="City" class="form-control input-md" size="50" maxlength="50" type="text" required="required" />
            </div>
        </div>
    
        <div class="form-group">
            <label class="col-md-4 control-label" for="state">State</label>  
            <div class="col-md-6">
                <input id="state" name="state" placeholder="State" class="form-control input-md" size="50" maxlength="50" type="text" required="required" />
            </div>
        </div>
    
        <div class="form-group">
            <label class="col-md-4 control-label" for="zip_code">Zip Code</label>  
            <div class="col-md-6">
                <input id="zip_code" name="zip_code" placeholder="Zip Code" class="form-control input-md" size="50" maxlength="50" type="text" required="required" />
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-md-4 control-label" for="zip_code">Country</label>  
            <div class="col-md-6">
                <select id="country" name="country" class="form-control select">
                    <option value="AFG">Afghanistan</option>
                    <option value="ALA">Åland Islands</option>
                    <option value="ALB">Albania</option>
                    <option value="DZA">Algeria</option>
                    <option value="ASM">American Samoa</option>
                    <option value="AND">Andorra</option>
                    <option value="AGO">Angola</option>
                    <option value="AIA">Anguilla</option>
                    <option value="ATA">Antarctica</option>
                    <option value="ATG">Antigua and Barbuda</option>
                    <option value="ARG">Argentina</option>
                    <option value="ARM">Armenia</option>
                    <option value="ABW">Aruba</option>
                    <option value="AUS">Australia</option>
                    <option value="AUT">Austria</option>
                    <option value="AZE">Azerbaijan</option>
                    <option value="BHS">Bahamas</option>
                    <option value="BHR">Bahrain</option>
                    <option value="BGD">Bangladesh</option>
                    <option value="BRB">Barbados</option>
                    <option value="BLR">Belarus</option>
                    <option value="BEL">Belgium</option>
                    <option value="BLZ">Belize</option>
                    <option value="BEN">Benin</option>
                    <option value="BMU">Bermuda</option>
                    <option value="BTN">Bhutan</option>
                    <option value="BOL">Bolivia, Plurinational State of</option>
                    <option value="BES">Bonaire, Sint Eustatius and Saba</option>
                    <option value="BIH">Bosnia and Herzegovina</option>
                    <option value="BWA">Botswana</option>
                    <option value="BVT">Bouvet Island</option>
                    <option value="BRA">Brazil</option>
                    <option value="IOT">British Indian Ocean Territory</option>
                    <option value="BRN">Brunei Darussalam</option>
                    <option value="BGR">Bulgaria</option>
                    <option value="BFA">Burkina Faso</option>
                    <option value="BDI">Burundi</option>
                    <option value="KHM">Cambodia</option>
                    <option value="CMR">Cameroon</option>
                    <option value="CAN">Canada</option>
                    <option value="CPV">Cape Verde</option>
                    <option value="CYM">Cayman Islands</option>
                    <option value="CAF">Central African Republic</option>
                    <option value="TCD">Chad</option>
                    <option value="CHL">Chile</option>
                    <option value="CHN">China</option>
                    <option value="CXR">Christmas Island</option>
                    <option value="CCK">Cocos (Keeling) Islands</option>
                    <option value="COL">Colombia</option>
                    <option value="COM">Comoros</option>
                    <option value="COG">Congo</option>
                    <option value="COD">Congo, the Democratic Republic of the</option>
                    <option value="COK">Cook Islands</option>
                    <option value="CRI">Costa Rica</option>
                    <option value="CIV">Côte d'Ivoire</option>
                    <option value="HRV">Croatia</option>
                    <option value="CUB">Cuba</option>
                    <option value="CUW">Curaçao</option>
                    <option value="CYP">Cyprus</option>
                    <option value="CZE">Czech Republic</option>
                    <option value="DNK">Denmark</option>
                    <option value="DJI">Djibouti</option>
                    <option value="DMA">Dominica</option>
                    <option value="DOM">Dominican Republic</option>
                    <option value="ECU">Ecuador</option>
                    <option value="EGY">Egypt</option>
                    <option value="SLV">El Salvador</option>
                    <option value="GNQ">Equatorial Guinea</option>
                    <option value="ERI">Eritrea</option>
                    <option value="EST">Estonia</option>
                    <option value="ETH">Ethiopia</option>
                    <option value="FLK">Falkland Islands (Malvinas)</option>
                    <option value="FRO">Faroe Islands</option>
                    <option value="FJI">Fiji</option>
                    <option value="FIN">Finland</option>
                    <option value="FRA">France</option>
                    <option value="GUF">French Guiana</option>
                    <option value="PYF">French Polynesia</option>
                    <option value="ATF">French Southern Territories</option>
                    <option value="GAB">Gabon</option>
                    <option value="GMB">Gambia</option>
                    <option value="GEO">Georgia</option>
                    <option value="DEU">Germany</option>
                    <option value="GHA">Ghana</option>
                    <option value="GIB">Gibraltar</option>
                    <option value="GRC">Greece</option>
                    <option value="GRL">Greenland</option>
                    <option value="GRD">Grenada</option>
                    <option value="GLP">Guadeloupe</option>
                    <option value="GUM">Guam</option>
                    <option value="GTM">Guatemala</option>
                    <option value="GGY">Guernsey</option>
                    <option value="GIN">Guinea</option>
                    <option value="GNB">Guinea-Bissau</option>
                    <option value="GUY">Guyana</option>
                    <option value="HTI">Haiti</option>
                    <option value="HMD">Heard Island and McDonald Islands</option>
                    <option value="VAT">Holy See (Vatican City State)</option>
                    <option value="HND">Honduras</option>
                    <option value="HKG">Hong Kong</option>
                    <option value="HUN">Hungary</option>
                    <option value="ISL">Iceland</option>
                    <option value="IND">India</option>
                    <option value="IDN">Indonesia</option>
                    <option value="IRN">Iran, Islamic Republic of</option>
                    <option value="IRQ">Iraq</option>
                    <option value="IRL">Ireland</option>
                    <option value="IMN">Isle of Man</option>
                    <option value="ISR">Israel</option>
                    <option value="ITA">Italy</option>
                    <option value="JAM">Jamaica</option>
                    <option value="JPN">Japan</option>
                    <option value="JEY">Jersey</option>
                    <option value="JOR">Jordan</option>
                    <option value="KAZ">Kazakhstan</option>
                    <option value="KEN">Kenya</option>
                    <option value="KIR">Kiribati</option>
                    <option value="PRK">Korea, Democratic People's Republic of</option>
                    <option value="KOR">Korea, Republic of</option>
                    <option value="KWT">Kuwait</option>
                    <option value="KGZ">Kyrgyzstan</option>
                    <option value="LAO">Lao People's Democratic Republic</option>
                    <option value="LVA">Latvia</option>
                    <option value="LBN">Lebanon</option>
                    <option value="LSO">Lesotho</option>
                    <option value="LBR">Liberia</option>
                    <option value="LBY">Libya</option>
                    <option value="LIE">Liechtenstein</option>
                    <option value="LTU">Lithuania</option>
                    <option value="LUX">Luxembourg</option>
                    <option value="MAC">Macao</option>
                    <option value="MKD">Macedonia, the former Yugoslav Republic of</option>
                    <option value="MDG">Madagascar</option>
                    <option value="MWI">Malawi</option>
                    <option value="MYS">Malaysia</option>
                    <option value="MDV">Maldives</option>
                    <option value="MLI">Mali</option>
                    <option value="MLT">Malta</option>
                    <option value="MHL">Marshall Islands</option>
                    <option value="MTQ">Martinique</option>
                    <option value="MRT">Mauritania</option>
                    <option value="MUS">Mauritius</option>
                    <option value="MYT">Mayotte</option>
                    <option value="MEX">Mexico</option>
                    <option value="FSM">Micronesia, Federated States of</option>
                    <option value="MDA">Moldova, Republic of</option>
                    <option value="MCO">Monaco</option>
                    <option value="MNG">Mongolia</option>
                    <option value="MNE">Montenegro</option>
                    <option value="MSR">Montserrat</option>
                    <option value="MAR">Morocco</option>
                    <option value="MOZ">Mozambique</option>
                    <option value="MMR">Myanmar</option>
                    <option value="NAM">Namibia</option>
                    <option value="NRU">Nauru</option>
                    <option value="NPL">Nepal</option>
                    <option value="NLD">Netherlands</option>
                    <option value="NCL">New Caledonia</option>
                    <option value="NZL">New Zealand</option>
                    <option value="NIC">Nicaragua</option>
                    <option value="NER">Niger</option>
                    <option value="NGA">Nigeria</option>
                    <option value="NIU">Niue</option>
                    <option value="NFK">Norfolk Island</option>
                    <option value="MNP">Northern Mariana Islands</option>
                    <option value="NOR">Norway</option>
                    <option value="OMN">Oman</option>
                    <option value="PAK">Pakistan</option>
                    <option value="PLW">Palau</option>
                    <option value="PSE">Palestinian Territory, Occupied</option>
                    <option value="PAN">Panama</option>
                    <option value="PNG">Papua New Guinea</option>
                    <option value="PRY">Paraguay</option>
                    <option value="PER">Peru</option>
                    <option value="PHL">Philippines</option>
                    <option value="PCN">Pitcairn</option>
                    <option value="POL">Poland</option>
                    <option value="PRT">Portugal</option>
                    <option value="PRI">Puerto Rico</option>
                    <option value="QAT">Qatar</option>
                    <option value="REU">Réunion</option>
                    <option value="ROU">Romania</option>
                    <option value="RUS">Russian Federation</option>
                    <option value="RWA">Rwanda</option>
                    <option value="BLM">Saint Barthélemy</option>
                    <option value="SHN">Saint Helena, Ascension and Tristan da Cunha</option>
                    <option value="KNA">Saint Kitts and Nevis</option>
                    <option value="LCA">Saint Lucia</option>
                    <option value="MAF">Saint Martin (French part)</option>
                    <option value="SPM">Saint Pierre and Miquelon</option>
                    <option value="VCT">Saint Vincent and the Grenadines</option>
                    <option value="WSM">Samoa</option>
                    <option value="SMR">San Marino</option>
                    <option value="STP">Sao Tome and Principe</option>
                    <option value="SAU">Saudi Arabia</option>
                    <option value="SEN">Senegal</option>
                    <option value="SRB">Serbia</option>
                    <option value="SYC">Seychelles</option>
                    <option value="SLE">Sierra Leone</option>
                    <option value="SGP">Singapore</option>
                    <option value="SXM">Sint Maarten (Dutch part)</option>
                    <option value="SVK">Slovakia</option>
                    <option value="SVN">Slovenia</option>
                    <option value="SLB">Solomon Islands</option>
                    <option value="SOM">Somalia</option>
                    <option value="ZAF">South Africa</option>
                    <option value="SGS">South Georgia and the South Sandwich Islands</option>
                    <option value="SSD">South Sudan</option>
                    <option value="ESP">Spain</option>
                    <option value="LKA">Sri Lanka</option>
                    <option value="SDN">Sudan</option>
                    <option value="SUR">Suriname</option>
                    <option value="SJM">Svalbard and Jan Mayen</option>
                    <option value="SWZ">Swaziland</option>
                    <option value="SWE">Sweden</option>
                    <option value="CHE">Switzerland</option>
                    <option value="SYR">Syrian Arab Republic</option>
                    <option value="TWN">Taiwan, Province of China</option>
                    <option value="TJK">Tajikistan</option>
                    <option value="TZA">Tanzania, United Republic of</option>
                    <option value="THA">Thailand</option>
                    <option value="TLS">Timor-Leste</option>
                    <option value="TGO">Togo</option>
                    <option value="TKL">Tokelau</option>
                    <option value="TON">Tonga</option>
                    <option value="TTO">Trinidad and Tobago</option>
                    <option value="TUN">Tunisia</option>
                    <option value="TUR">Turkey</option>
                    <option value="TKM">Turkmenistan</option>
                    <option value="TCA">Turks and Caicos Islands</option>
                    <option value="TUV">Tuvalu</option>
                    <option value="UGA">Uganda</option>
                    <option value="UKR">Ukraine</option>
                    <option value="ARE">United Arab Emirates</option>
                    <option value="GBR">United Kingdom</option>
                    <option value="USA">United States</option>
                    <option value="UMI">United States Minor Outlying Islands</option>
                    <option value="URY">Uruguay</option>
                    <option value="UZB">Uzbekistan</option>
                    <option value="VUT">Vanuatu</option>
                    <option value="VEN">Venezuela, Bolivarian Republic of</option>
                    <option value="VNM">Viet Nam</option>
                    <option value="VGB">Virgin Islands, British</option>
                    <option value="VIR">Virgin Islands, U.S.</option>
                    <option value="WLF">Wallis and Futuna</option>
                    <option value="ESH">Western Sahara</option>
                    <option value="YEM">Yemen</option>
                    <option value="ZMB">Zambia</option>
                    <option value="ZWE">Zimbabwe</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-md-4 control-label" for="email">Email</label>  
            <div class="col-md-6">
                <input id="email" name="email" placeholder="Email" class="form-control input-md" required="required" type="email"  required="required" />
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
        console.log(data);
        // Set the token as the value for the token input
        $("#token_request").val( data.response.token.token );

        // IMPORTANT: Here we call `submit()` on the form element directly instead of using jQuery to prevent and infinite token request loop.
        $("#payment-form").submit();
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
