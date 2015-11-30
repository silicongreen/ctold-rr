
var ci_base_url = $("#ci_base_url").val();
$('.loading_diary21').show();


$(window).bind("load", function () {
    $('.loading_diary21').hide();
    $("#page").show();
});
$('#myCarousel').carousel({
    interval: 5000
});
$('#myCarouselcontent19').carousel({
    interval: 5000
});
$('#myCarousel2').carousel();

function popupwindow(url, title, w, h) {
    var left = (screen.width / 2) - (w / 2);
    var top = (screen.height / 2) - (h / 2);
    return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
}

$(document).ready(function () {
    
    $('#mainlink').click(function () {
        $('html, body').animate({
            scrollTop: $($.attr(this, 'href')).offset().top
        }, 500);
        return false;
    });
    
    $(window).on("hashchange", function () {
        window.scrollTo(window.scrollX, window.scrollY - 80);
    });
    
    $('.animtype1').addClass("hidden_diary21").viewportChecker({
        classToAdd: 'visible_diary21 animated fadeIn', // Class to add to the elements when they are visible
        offset: 100
    });
    $('.animtype2').addClass("hidden_diary21").viewportChecker({
        classToAdd: 'visible_diary21 animated bounceInLeft', // Class to add to the elements when they are visible
        offset: 100
    });
    $('.animtype3').addClass("hidden_diary21").viewportChecker({
        classToAdd: 'visible_diary21 animated flipInX', // Class to add to the elements when they are visible
        offset: 100
    });
    
    
    //email portion
    
    var error = 1;
    $("#email").blur(function ()
    {

        if ($("input#name").val() == '') {
            error = 1;
            $("#namespan").html('<font color="#fff" size="2">Please enter your Name</font>');
        }
        else
        {
            error = 0;
            $("#namespan").html('<font color="#fff" size="2"></font>');
        }
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        var emailaddress = $("#email").val();
        if (!emailReg.test(emailaddress))
        {
            error = 1;
            $("#emailspan").html('<font color="#fff" size="2">Please enter valid Email address</font>');
        }
        else
        {
            error = 0;
            $("#emailspan").html('<font color="#fff" size="2"></font>');
        }
    });


    $('input#name').on('blur', function () {
        if ($(this).val() == '') {
            error = 1;
            $("#namespan").html('<font color="#fff" size="2">Please enter your Name</font>');
        }
        else
        {
            error = 0;
            $("#namespan").html('<font color="#fff" size="2"></font>');
        }
    });


    $('textarea#comment').on('blur', function () {
        if ($(this).val() == '') {
            error = 1;
            $("#commentspan").html('<font color="#fff" size="2">Please enter your Message</font>');
        }
        else
        {
            error = 0;
            $("#commentspan").html('<font color="#fff" size="2"></font>');
        }
    });



    var form = $('#form');
    var submit = $('#submit');
    var alert = $('.alert');

    form.on('submit', function (e) {
        e.preventDefault();

        
        $.ajax({
            url: ci_base_url+'landing/send_mail',
            type: 'POST',
            dataType: 'html',
            data: form.serialize(),
            beforeSend: function () {
                alert.fadeOut();
                submit.html('Sending....');
            },
            success: function (e) {
                alert.html(e).fadeIn();
                form.trigger('reset'); // reset form
                submit.html('Send Email');
            },
            error: function (e) {
                console.log(e)
            }
        });
    });
});


