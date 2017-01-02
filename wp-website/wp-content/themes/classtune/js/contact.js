/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function validateEmail(email) {
    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    return re.test(email);
}
$(document).on('click', '#contact_classtune input#sub', function (e) {
    e.preventDefault();
    if ($("#contact_classtune #name").val() == "")
    {

        $("#contact_classtune span.legend").html("<div class='alert alert-danger'><strong>Name</strong> can't be empty</div>");
        $("#contact_classtune span.legend .error").show("slow");
    }
    else if ($("#contact_classtune #email").val() == "")
    {
        $("#contact_classtune span.legend").html("<div class='alert alert-danger'><strong>Email</strong> can't be empty</div>");
        $("#contact_classtune span.legend .error").show("slow");
    }
    else if(!validateEmail($("#contact_classtune #email").val()))
    {
        $("#contact_classtune span.legend").html("<div class='alert alert-danger'>Invalid <strong>Email</strong> Address</div>");
        $("#contact_classtune span.legend .error").show("slow");
    }
    else if ($("#contact_classtune #subject").val() == "")
    {
        $("#contact_classtune span.legend").html("<div class='alert alert-danger'><strong>Subject</strong> can't be empty</div>");
        $("#contact_classtune span.legend .error").show("slow");
    }
    else if ($("#contact_classtune #massage").val() == "")
    {
        $("#contact_classtune span.legend").html("<div class='alert alert-danger'><strong>Massage</strong> can't be empty</div>");
        $("#contact_classtune span.legend .error").show("slow");
    }
    else
    {
        $("#contact_classtune span.legend").html("<div class='alert alert-info'><strong>Sending......</strong></div>");
        $.post("/wp-admin/admin-ajax.php", {action:"send_mail_classtune",login_security_field:$("#login_security_field").val(),name: $("#contact_classtune #name").val(), email: $("#contact_classtune #email").val(),
        subject: $("#contact_classtune #subject").val(), massage: $("#contact_classtune #massage").val()})
                .done(function (data) 
                    {
                        if(data == "-1")
                        {
                           $("#contact_classtune span.legend").html("<div class='alert alert-danger'>Invalid Request. Please reload the page and try again</div>");
                           
                        }
                        else if(data =="0")
                        {
                            $("#contact_classtune span.legend").html("<div class='alert alert-danger'><strong>Massage</strong> can't sent at the moment</div>");
                        }
                        else if(data =="1")
                        {
                            $("#contact_classtune span.legend").html("<div class='alert alert-danger'><strong>All</strong> the information is required</div>");
                        } 
                        else
                        {
                            $("#contact_classtune span.legend").html("<div class='alert alert-success'><strong>"+data+"</strong></div>");
                        }
                    }
                );
    }
});


