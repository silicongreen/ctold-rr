/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function validateEmail(email) {
    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    return re.test(email);
}
jQuery(document).on('click', '#contact_classtune input#sub', function (e) {
    e.preventDefault();
	
	//var isSubjectTextDisabled = jQuery("#subject_text").is(':disabled');
	var isSchoolNameDisabled = jQuery("#school_name").is(':disabled');

    if (jQuery("#contact_classtune #name").val() == "")
    {

        jQuery("#contact_classtune span.legend").html("<div class='alert alert-danger'><b>CONATCT USER PLUGIN </b><strong>Name</strong> can't be empty</div>");
        jQuery("#contact_classtune span.legend .error").show("slow");
    }
    else if (jQuery("#contact_classtune #email").val() == "")
    {
        jQuery("#contact_classtune span.legend").html("<div class='alert alert-danger'><strong>Email</strong> can't be empty</div>");
        jQuery("#contact_classtune span.legend .error").show("slow");
    }
    else if(!validateEmail(jQuery("#contact_classtune #email").val()))
    {
        jQuery("#contact_classtune span.legend").html("<div class='alert alert-danger'>Invalid <strong>Email</strong> Address</div>");
        jQuery("#contact_classtune span.legend .error").show("slow");
    }
    else if (jQuery("#contact_classtune #phone").val() == "")
    {
        jQuery("#contact_classtune span.legend").html("<div class='alert alert-danger'><strong>Phone Number</strong> can't be empty</div>");
        jQuery("#contact_classtune span.legend .error").show("slow");
    }
    else if(jQuery('#contact_classtune #subject_type option:selected').val() == "")
    {
            jQuery("#contact_classtune span.legend").html("<div class='alert alert-danger'>Select a <strong>Subject</strong></div>");
            jQuery("#contact_classtune span.legend .error").show("slow");
    }
    else if ( jQuery("#contact_classtune #subject_text").val() == "")
    {
		jQuery("#contact_classtune span.legend").html("<div class='alert alert-danger'><strong>Subject</strong> can't be empty</div>");
		jQuery("#contact_classtune span.legend .error").show("slow");
    }
    else if(isSchoolNameDisabled == false && jQuery("#contact_classtune #school_name").val() == "")
    {
		jQuery("#contact_classtune span.legend").html("<div class='alert alert-danger'><strong>School Name</strong> can't be empty</div>");
		jQuery("#contact_classtune span.legend .error").show("slow");
	
    }
    else if (jQuery("#contact_classtune #massage").val() == "")
    {
        jQuery("#contact_classtune span.legend").html("<div class='alert alert-danger'><strong>Massage</strong> can't be empty</div>");
        jQuery("#contact_classtune span.legend .error").show("slow");
    }
    else
    {
        var subject_type = jQuery('#contact_classtune #subject_type option:selected').val();
		var subject = "";
		if(subject_type == "A")
			subject = "New Account : "+jQuery("#contact_classtune #subject_text").val();
		else if(subject_type == "B")
			subject = "Inquiry : "+jQuery("#contact_classtune #subject_text").val();
		else if(subject_type == "C")
			subject = "Complaint : "+jQuery("#contact_classtune #subject_text").val();
		else if(subject_type == "D")
			subject = "Suggestion : "+jQuery("#contact_classtune #subject_text").val();
		else if(subject_type == "E")
			subject = "Others : "+jQuery("#contact_classtune #subject_text").val();
			
		
		jQuery("#contact_classtune span.legend").html("<div class='alert alert-info'><strong>Sending......</strong></div>");
		
		jQuery.ajax({
			url : "/wp-admin/admin-ajax.php",
			type : 'post',
			data : {
				action :     'send_mail_classtune',
				name:        jQuery("#contact_classtune #name").val(), 
				email:       jQuery("#contact_classtune #email").val(),
				phone:       jQuery("#contact_classtune #phone").val(),			
				subject:     subject, 
				user_type:   jQuery('#contact_classtune #user_type option:selected').val(),
				school_name: jQuery("#contact_classtune #school_name").val(),			
				massage:     jQuery("#contact_classtune #massage").val()
			},
			success : function( data ) {
				if(data =="0")
				{
					jQuery("#contact_classtune span.legend").html("<div class='alert alert-danger'><strong>Massage</strong> can't sent at the moment</div>");
				}
				else if(data =="1")
				{
					jQuery("#contact_classtune span.legend").html("<div class='alert alert-danger'><strong>All</strong> the information is required</div>");
				} 
				else
				{
					jQuery("#contact_classtune span.legend").html("<div class='alert alert-success'><strong>"+data+"</strong></div>");
					jQuery("#contact_classtune").find("input[type=text], select, textarea").val("");
				}
			}
		});	
    }
	
	return false;
});