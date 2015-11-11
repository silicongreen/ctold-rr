/*
This page is for user sign up with user_auth controllers
Written By: Likhon
Date:20-10-2015
*/
$(document).ready(function(){


        $('#apply_for_parent_admission').validate({
	    rules: {			                        
                        date_of_birth: {
				required: true				
			},
                        
                        phone_ext: {
				required:true,
                                minlength:2,
                                maxlength:2,
                                number: true		
			},
                        
                        phone_number: {
				required:true,
                                minlength:6,
                                maxlength:6,
                                number: true		
			},
                         city: {
				required: true				
			},
                         address: {
				required: true				
			}
	    },
            messages: {
                    admission_no: {				
                            remote: "admission no or username already in use!"
                    },
                    g_username: {				
                            remote: "g_username already in use!"
                    },
                    g_username2: {				
                            remote: "g_username already in use!"
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


