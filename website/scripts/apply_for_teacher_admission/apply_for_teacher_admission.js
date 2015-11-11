/*
This page is for user sign up with user_auth controllers
Written By: Likhon
Date:20-10-2015
*/
$(document).ready(function(){


        $('#apply_for_teacher_admission').validate({
	    rules: {
                    admission_no: {				
                            required: true,
                            remote: {
                            url: $('#ci_base_url').val() + "front/paid/is_teacher_username_exist",
                            type: "post",
                            data: {
                                admission_no: function() {
                                  return $( "#tn_username" ).val();
                                },
                                paid_school_id: function() {
                                  return $( "#paid_school_id" ).val();
                                }
                            }
                         }
                    }
		  
	    },
            messages: {
                    admission_no: {				
                            remote: "admission no or username already in use!"
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


