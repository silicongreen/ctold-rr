/*
This page is for user sign up with user_auth controllers
Written By: Likhon
Date:20-10-2015
*/
$(document).ready(function(){


		$('#registration-form').validate({
	    rules: {
			first_name: {
				minlength: 3,
				required: true
			},
		  
			last_name: {
				minlength: 3,
				required: true
			},

			password: {
				required: true,
				minlength: 6
			},
			confirm_password: {
				required: true,
				minlength: 6,
				equalTo: "#password"
			},

			email: {
				required: true,
				email: true,
				remote: {
                        url: $('#ci_base_url').val() + "front/user_auth/email_unique",
                        type: "post"
                     }
			},
			
			confirm_email: {
				required: true,
				email: true,
				equalTo: "#email"
			}			
		  
	    },
		messages: {
			email: {				
				remote: "Email already in use!"
			}
		},
	
		highlight: function(element) {
			$(element).closest('.control-group').removeClass('success').addClass('error');
		},
		success: function(element) {
			element
			.text('OK!').addClass('valid')
			.closest('.control-group').removeClass('error').addClass('success');
		}
	  });

}); // end document.ready