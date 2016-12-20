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
	
	//var isSubjectTextDisabled = $("#subject_text").is(':disabled');
	var isSchoolNameDisabled = $("#school_name").is(':disabled');

    if ($("#contact_classtune #name").val() == "")
    {

        $("#contact_classtune span.legend").html("<div class='alert alert-danger'><strong>Name LOLO</strong> can't be empty</div>");
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
    else if ($("#contact_classtune #phone").val() == "")
    {
        $("#contact_classtune span.legend").html("<div class='alert alert-danger'><strong>Phone Number</strong> can't be empty</div>");
        $("#contact_classtune span.legend .error").show("slow");
    }
    else if($('#contact_classtune #subject_type option:selected').val() == "")
    {
            $("#contact_classtune span.legend").html("<div class='alert alert-danger'>Select a <strong>Subject</strong></div>");
            $("#contact_classtune span.legend .error").show("slow");
    }
    else if ( $("#contact_classtune #subject_text").val() == "")
    {
		$("#contact_classtune span.legend").html("<div class='alert alert-danger'><strong>Subject</strong> can't be empty</div>");
		$("#contact_classtune span.legend .error").show("slow");
    }
    else if(isSchoolNameDisabled == false && $("#contact_classtune #school_name").val() == "")
    {
		$("#contact_classtune span.legend").html("<div class='alert alert-danger'><strong>School Name</strong> can't be empty</div>");
		$("#contact_classtune span.legend .error").show("slow");
	
    }
    else if ($("#contact_classtune #massage").val() == "")
    {
        $("#contact_classtune span.legend").html("<div class='alert alert-danger'><strong>Massage</strong> can't be empty</div>");
        $("#contact_classtune span.legend .error").show("slow");
    }
    else
    {
        var subject_type = $('#contact_classtune #subject_type option:selected').val();
		var subject = "";
		if(subject_type == "A")
			subject = "New Account : "+$("#contact_classtune #subject_text").val();
		else if(subject_type == "B")
			subject = "Inquiry : "+$("#contact_classtune #subject_text").val();
		else if(subject_type == "C")
			subject = "Complaint : "+$("#contact_classtune #subject_text").val();
		else if(subject_type == "D")
			subject = "Suggestion : "+$("#contact_classtune #subject_text").val();
		else if(subject_type == "E")
			subject = "Others : "+$("#contact_classtune #subject_text").val();
			
		
		$("#contact_classtune span.legend").html("<div class='alert alert-info'><strong>Sending......</strong></div>");
		
		$.ajax({
			url : postlove.ajax_url,
			type : 'post',
			data : {
				action :     'wp_ajax_send_mail_classtune',
				name:        $("#contact_classtune #name").val(), 
				email:       $("#contact_classtune #email").val(),
				phone:       $("#contact_classtune #phone").val(),			
				subject:     subject, 
				user_type:   $('#contact_classtune #user_type option:selected').val(),
				school_name: $("#contact_classtune #school_name").val(),			
				massage:     $("#contact_classtune #massage").val()
			},
			success : function( data ) {
				if(data =="0")
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
					$("#contact_classtune").find("input[type=text], select, textarea").val("");
				}
			}
		});	
    }
	
	return false;
});