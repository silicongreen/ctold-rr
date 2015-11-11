/*
This page is for user sign up with user_auth controllers
Written By: Likhon
Date:20-10-2015
*/
$(document).ready(function(){


        $('#select_school_for_sign_up_form').validate({
	    rules: {
			school_code: {
				required: true,                                
				remote: {
                                    url: $('#ci_base_url').val() + "front/paid/school_code_check",
                                    type: "post",
                                    dataType:"json",                                    
                                    dataFilter: function(data) {
                                        data=JSON.parse(data);
                                        //console.log(data.last_name);
                                        if(data == false)
                                        {
                                            $('.school_name').html("School is not found");
                                            $('.school_name').css('color','red');
                                            return false;
                                        }
                                        else
                                        {   //console.log(data.id);
                                            
                                            $('#paid_school_id').val(data.id);
                                            $('.school_name').html("Is <b>"+data.name + "</b> your school?");
                                            $('.school_name').css('color','green');
                                            return true;
                                        }
                                    }
                                }
			},
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
				email: true
				//remote: {
                                //    url: $('#ci_base_url').val() + "front/paid/email_unique",
                                //    type: "post"
                                // }
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