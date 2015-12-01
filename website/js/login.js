/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).on('click', '#login_button_classune a#classtune_a', function () {
    if ($("#login_form_classune").is(":hidden")) {
        $("#login_form_classune").slideDown("slow");
        $("#login_button_classune").addClass('act_login');
    }
    else {
        $("#login_form_classune").slideUp("slow");
        $("#login_button_classune").removeClass('act_login');
        $("#form_login_classtune span.legend").html("");
    }
});
$(document).click(function(e) {
        if (!$(e.target).is('#login_button_classune, #login_button_classune *')) {
            $("#login_form_classune").slideUp("slow");
            $("#login_button_classune").removeClass('act_login');
            $("#form_login_classtune span.legend").html("");
        }
 });
$(document).on('click', '#form_login_classtune button#submit', function (e) {
    e.preventDefault();
    if ($("#form_login_classtune #username").val() == "")
    {
        $("#form_login_classtune span.legend").html("<div class='alert alert-danger'><strong>Username</strong> can't be empty</div>");
        $("#form_login_classtune span.legend .error").show("slow");
    }
    else if ($("#form_login_classtune #password").val() == "")
    {
        $("#form_login_classtune span.legend").html("<div class='alert alert-danger'><strong>Password</strong> can't be empty</div>");
        $("#form_login_classtune span.legend .error").show("slow");
    }
    else
    {
        $("#form_login_classtune span.legend").html("<div class='alert alert-info'><strong>Loading......</strong></div>");
        $.post("/login", {username: $("#form_login_classtune #username").val(), password: $("#form_login_classtune #password").val()})
                .done(function (data) {
                    if (data == "0")
                    {
                        $("#form_login_classtune span.legend").html("<div class='alert alert-danger'>Wrong <strong>Username</strong> or <strong>Password</strong></div>");
                        $("#form_login_classtune span.legend .error").show("slow");
                    }
                    else
                    {
                        $("#form_login_classtune span.legend").html("<div class='alert alert-info'><strong>Redirecting to your school...</strong></div>");
                        location.href = data;
                    }

                }
                );
    }
});


