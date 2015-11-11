/*
This page is for user sign up with user_auth controllers
Written By: Likhon
Date:20-10-2015
*/
$(document).ready(function(){


        $('#apply_for_student_admission').validate({
	    rules: {
			admission_no: {				
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

                        g_username: {
                                minlength: 6,
                                required: function() {                                  
                                  return $('.choose_guardian').is(':checked');
                                },
                                remote: {
                                    url: $('#ci_base_url').val() + "front/paid/is_guardian_exist",
                                    type: "post",
                                    dataType:"json",                                    
                                    dataFilter: function(data) {
                                        data=JSON.parse(data);
                                        //console.log(data.last_name);
                                        if(data == false)
                                        {
                                            $('.g_fullname').html("Guardian Username not found");
                                            $('.g_fullname').css('color','red');
                                            return false;
                                        }
                                        else
                                        {   //console.log(data.id);
                                            
                                            $('#g_id').val(data.id);
                                            $('.g_fullname').html("Is "+data.first_name+" "+data.last_name+ " your Guardian?");
                                            $('.g_fullname').css('color','green');
                                            return true;
                                        }
                                    }
                                },
                                guser_name_not_same: function() {                                  
                                    return $('.choose_guardian2').is(':checked');
                                }
                                
                        },
                        g_username2: {
                                minlength: 6,
                                required: function() {                                  
                                    return $('.choose_guardian2').is(':checked');
                                },
                                remote: {
                                    url: $('#ci_base_url').val() + "front/paid/is_guardian_exist",
                                    type: "post",
                                    data: {
                                        g_username: function() {
                                          return $( "#g_username2" ).val();
                                        }
                                    },
                                    dataType:"json",                                    
                                    dataFilter: function(data) {
                                        data=JSON.parse(data);
                                        //console.log(data.last_name);
                                        if(data == false)
                                        {
                                            $('.g_fullname2').html("Guardian Username not found");
                                            $('.g_fullname2').css('color','red');
                                            return false;
                                        }
                                        else
                                        {
                                            $('#g_id2').val(data.id);
                                            $('.g_fullname2').html("Is "+data.first_name+" "+data.last_name+ " your Guardian?");
                                            $('.g_fullname2').css('color','green');
                                            return true;
                                        }
                                    }
                                },
                                guser_name_not_same: function() {                                  
                                    return $('.choose_guardian').is(':checked');
                                }
                                
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
                	
                        if ($(element).hasClass('g_username2') && $('.choose_guardian').is(':checked')) {                            
                            $('.g_fullname2').html("Guardian Username should not match");
                            $('.g_fullname2').css('color','red');
                        }
                        if ($(element).hasClass('g_username') && $('.choose_guardian2').is(':checked')) {                            
                            $('.g_fullname').html("Guardian Username should not match");
                            $('.g_fullname').css('color','red');
                        }
                       
                        $(element).closest('.control-group').removeClass('success').addClass('error');	
                        
            },                
            success: function(element) {
			element
			.text('OK!').addClass('valid')
			.closest('.control-group').removeClass('error').addClass('success');
            }
	  });
          
          
          
            $.validator.addMethod("guser_name_not_same", function(value, element) {
                return $('#g_username').val() != $('#g_username2').val()
            }, "* Guardian Username should not match");

}); // end document.ready


