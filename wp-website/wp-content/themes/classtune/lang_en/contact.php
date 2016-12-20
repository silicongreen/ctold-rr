<div style="border:0px solid;height:550px;padding-top:30px;">
    <h2 class="f2" style="margin-bottom:30px;"><i>Write to us...</i></h2>
    
    <form id="contact_classtune">
        <div class="row" style="width:500px;margin:0px auto;">
         <span class="legend"></span>
        </div>
        <div class="row" style="width:500px;margin:0px auto;">
            <input type="text" name="name" id="name" placeholder="Name">
        </div>
		<div class="row" style="width:500px;margin:0px auto;">
            <div style="float:left;width:47%;">
                <input type="text" name="email" id="email" placeholder="Email">
            </div>
            <div style="float:right;width:47%;">
                <input type="text" name="phone" id="phone" placeholder="Phone">
            </div>
        </div>
		<div class="row" style="width:500px;margin:0px auto;">
            <div style="float:left;width:47%;">
                <select name="subject_type" id="subject_type">
					<option value="">Select Subject</option>
					<option value="A">New Account</option>
					<option value="B">Inquiry</option>
					<option value="C">Complaint</option>
					<option value="D">Suggestion</option>
					<option value="E">Other</option>
                </select>
            </div>
            <div style="float:right;width:47%;">
                <input type="text" name="subject_text" id="subject_text" placeholder="Subject">
            </div>
        </div>
        <!--div class="row" style="width:500px;margin:0px auto;">
            <input type="text" name="subject" id="subject" placeholder="Subject">
        </div-->
		<div class="row" style="width:500px;margin:0px auto;">
            <div style="float:left;width:47%;">
                <select name="user_type" id="user_type">
					<option value="Admin">I am Admin</option>
					<option value="Teacher">I am Teacher</option>
					<option value="Student">I am Student</option>
					<option value="Parent">I am Parent</option>
					<option value="Visitor">I am Visitor</option>
                </select>
            </div>
            <div style="float:right;width:47%;">
                <input type="text" name="school_name" id="school_name" placeholder="School Name">
            </div>
        </div>
        <div class="row" style="width:500px;margin:0px auto;">
            <textarea row="5" name="massage" id="massage"></textarea>
        </div>
        <div class="row" style="width:500px;margin:0px auto;">
            <div style="float:left;width:30%;">
                <input class="button_c" id="sub" name="submit" type="submit" value="Submit">
            </div>
            <div style="float:left;width:30%;">
                <button type="submit" class="button_c" id="reset">Reset</button>
            </div>
        </div>
    </form>

</div>

<script>
$(document).ready(function () {
	$('#subject_type').on('change', function() {
	  //alert( this.value ); // or $(this).val()
	  if($(this).val() == "E")
	  {
		  $("#subject_text").prop('disabled', false);		  
	  }
	  else
	  {
		  $("#subject_text").prop('disabled', true);
		  $('#subject_text').val('')
	  }
	});
	
	$('#user_type').on('change', function() {
	  //alert( this.value ); // or $(this).val()
	  if($(this).val() == "Visitor")
	  {
		  $("#school_name").prop('disabled', true);	
		  $('#school_name').val('')		  
	  }
	  else
	  {
		  $("#school_name").prop('disabled', false);
	  }
	});
        
        
        /* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



$(document).on('click', '#contact_classtune input#sub', function (e) {
    e.preventDefault();
	
	//var isSubjectTextDisabled = $("#subject_text").is(':disabled');
	var isSchoolNameDisabled = $("#school_name").is(':disabled');

    if ($("#contact_classtune #name").val() == "")
    {

        $("#contact_classtune span.legend").html("<div class='alert alert-danger'><strong>Name LOL</strong> can't be empty</div>");
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
			url : ajaxurl,
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
	
});
function validateEmail(email) {
    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    return re.test(email);
}
</script>