/*
This page is for user sign up with user_auth controllers
Written By: Likhon
Date:20-10-2015
*/
$(document).ready(function(){


        $('#apply_for_parent_admission_2').validate({
	    rules: {
			s_admission_no: {				
                            regex: true,
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
                                    },
                                dataType:"json",  
                                beforeSend: function(){
                                    var loading = $('#ajaxLoading');
                                    loading.appendTo($('#s_admission_no').parent());       
                                    loading.show();
                                },
                                dataFilter: function(data) {
                                    data = JSON.parse(data);
//                                        console.log(data);
                                    if(data.success !== 1)
                                    {
                                            $('.valid_admission_no').html(data.error_message);
                                            $('.valid_admission_no').css('color','red');
                                            return false;
                                    }
                                    else
                                    {   //console.log(data.id);
                                            $('.valid_admission_no').html('');
                                            return true;
                                    }
                                },
                                complete: function(){
                                    var loading = $('#ajaxLoading');
                                    loading.hide();                     
                                }

                            }
			},
			sn_username:{
					required: true,
					regex: true
			},
			s_first_name: {
				
				required: true
			},
		  
			s_last_name: {
				
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
                                    beforeSend: function(){
                                        var loading = $('#ajaxLoading');
                                        loading.appendTo($('#s_username').parent());       
                                        loading.show();
                                    },
                                    dataFilter: function(data) {
                                        data=JSON.parse(data);
                                        //console.log(data.last_name);
                                        if(data.success !== 1)
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
                                    },
                                    complete: function(){
                                        var loading = $('#ajaxLoading');
                                        loading.hide();                        
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
		
        $.validator.addMethod("regex", function(value, element) {
            return this.optional(element) || /^[a-zA-Z0-9\-]+$/i.test(value);
        }, "Admission no. should include a-z , A-Z , 0-9 , - , _");

}); // end document.ready


