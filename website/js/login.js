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
    }
});
$(document).on('click', '#form_login_classtune button#submit', function (e) {
    e.preventDefault();
    if ($("#form_login_classtune #username").val() == "")
    {
        $("#form_login_classtune span.legend").html("<span class='error'>Username can't be empty</span>");
        $("#form_login_classtune span.legend .error").show("slow");
    }
    else if ($("#form_login_classtune #password").val() == "")
    {
        $("#form_login_classtune span.legend").html("<span class='error'>Password can't be empty</span>");
        $("#form_login_classtune span.legend .error").show("slow");
    }
    else
    {
        $("#form_login_classtune span.legend").html("loading...");
        $.post("/login", {username: $("#form_login_classtune #username").val(), password: $("#form_login_classtune #password").val()})
                .done(function (data) {
                    if (data == "0")
                    {
                        $("#form_login_classtune span.legend").html("<span class='error'>Wrong Username or Password</span>");
                        $("#form_login_classtune span.legend .error").show("slow");
                    }
                    else
                    {
                        location.href = data;
                    }

                }
                );
    }
});


