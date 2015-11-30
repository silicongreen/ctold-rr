<html lang="en">
    <head>
        <?php require_once('./stripeConf.php'); ?>

        <link type="text/css" rel="stylesheet" href="/jqgrid/css/bootstrap.css" media="all" />

        <script type="text/javascript" src="/jqgrid/js/jquery.min.js"></script>
        <script type="text/javascript" src="https://js.stripe.com/v2/"></script>

        <script type="text/javascript">
            // This identifies your website in the createToken call below
            Stripe.setPublishableKey('<?php echo $stripe['publishable_key']; ?>');

            function stripeResponseHandler(status, response) {
                if (response.error) {
                    // re-enable the submit button
                    $('.submit-button').removeAttr("disabled");
                    // show the errors on the form
                    $(".payment-errors").html(response.error.message);
                } else {
                    var form$ = $("#payment-form");
                    // token contains id, last4, and card type
                    var token = response['id'];
                    // insert the token into the form so it gets submitted to the server
                    form$.append("<input type='hidden' name='stripeToken' value='" + token + "' />");
                    // and submit
                    form$.get(0).submit();
                }
            }
        </script>

        <script type="text/javascript">
            jQuery(function ($) {
                $('#payment-form').submit(function (event) {
                    var $form = $(this);

                    // Disable the submit button to prevent repeated clicks
                    $form.find('buttont').prop('disabled', true);

                    Stripe.card.createToken($form, stripeResponseHandler);

                    // Prevent the form from submitting with the default action
                    return false;
                });
            });
        </script>

    </head>
    <body>
        
        <form action="charge.php" method="POST" id="payment-form">
            <span class="payment-errors"></span>

            <div class="form-row">
                <label>
                    <span>Card Number</span>
                    <input type="text" size="20" data-stripe="number"/>
                </label>
            </div>

            <div class="form-row">
                <label>
                    <span>CVC</span>
                    <input type="text" size="4" data-stripe="cvc"/>
                </label>
            </div>

            <div class="form-row">
                <label>
                    <span>Expiration (MM/YYYY)</span>
                    <input type="text" size="2" data-stripe="exp-month"/>
                </label>
                <span> / </span>
                <input type="text" size="4" data-stripe="exp-year"/>
            </div>

            <button type="submit">Submit Payment</button>
        </form>

    </body>
</html>