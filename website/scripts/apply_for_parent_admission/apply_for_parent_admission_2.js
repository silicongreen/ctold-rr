/*
This page is for user sign up with user_auth controllers
Written By: Likhon
Date:20-10-2015
*/
$(document).ready(function(){


        $('#apply_for_parent_admission_2').validate({
	    rules: {
			s_admission_no: {				
				required: true,
                                remote: {
                                    url: $('#ci_base_url').val() + "front/paid/is_student_username_exist",
                                    type: "post",
                                    data: {
                                        admission_no: function() {
                                          return $( "#sn_username" ).val();
                                        },
                                        paid_school_id: function() {
                                          return $( "#paid_school_id" ).val();
                                        }
                                    }
                                }
			},
			s_first_name: {
				minlength: 3,
				required: true
			},
		  
			s_last_name: {
				minlength: 3,
				required: true
			},

			s_password: {
				required: true,
				minlength: 6
			},
                        s_username: {
                                minlength: 6,
                                required: function() {                                  
                                  return $('.choose_guardian').is(':checked');
                                },
                                remote: {
                                    url: $('#ci_base_url').val() + "front/paid/is_student_exist_username",
                                    type: "post",
                                    dataType:"json",                                    
                                    dataFilter: function(data) {
                                        data=JSON.parse(data);
                                        //console.log(data.last_name);
                                        if(data == false)
                                        {
                                            $('.s_fullname').html("Student Username not found");
                                            $('.s_fullname').css('color','red');
                                            return false;
                                        }
                                        else
                                        {   //console.log(data.id);
                                            
                                            $('#s_id').val(data.id);
                                            $('.s_fullname').html("Is "+data.first_name+" "+data.last_name+ " your Student?");
                                            $('.s_fullname').css('color','green');
                                            return true;
                                        }
                                    }
                                }
                                
                        }
		  
	    },
            messages: {
                    s_admission_no: {				
                            remote: "admission no or username already in use!"
                    },
                    s_username: {				
                            remote: "s_username already in use!"
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
        $('#skip_to_confirmation').on('click', function(){        
            
            var url = $('#ci_base_url').val() + "front/paid/apply_for_parent_admission_final";
            
            $('#apply_for_parent_admission_2').submit(function() {
                $(this).attr('action', url); 
                return true;
            });
            
            
            
        });

}); // end document.ready


