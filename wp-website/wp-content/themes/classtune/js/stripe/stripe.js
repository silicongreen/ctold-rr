var address_line_1 = '';

function stripeResponseHandler(status, response) {
    if (response.error) {
        // re-enable the submit button
        $('.submit-button').removeAttr("disabled");
        // show the errors on the form
        $(".payment-errors").html(response.error.message);
        $('.pe-wrapper').show();
    } else {
        if (address_line_1 != response.card.address_line1) {
            $(".payment-errors").html("Your card's address is invalid.");
            return false;
        }

        var form$ = $("#payment-form");
        // token contains id, last4, and card type
        var token = response['id'];
        // insert the token into the form so it gets submitted to the server
        form$.append("<input type='hidden' name='stripeToken' value='" + token + "' />");
        // and submit
        form$.get(0).submit();
    }
}

jQuery(function ($) {

    $(document).ready(function () {
        $('.pe-wrapper').hide();
        var publishKey = $('#stripe-publish-key').attr('data');
        Stripe.setPublishableKey(publishKey);
    });

    $('#payment-form').submit(function (event) {
        var $form = $(this);
        address_line_1 = $('#address_line1').val();

        if (address_line_1 == '') {
            $(".payment-errors").html("Your card's address is invalid.");
            return false;
        }

        // Disable the submit button to prevent repeated clicks
        $form.find('buttont').prop('disabled', true);

        Stripe.card.createToken($form, stripeResponseHandler);

        // Prevent the form from submitting with the default action
        return false;
    });
});