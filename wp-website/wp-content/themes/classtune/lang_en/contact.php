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
	
});
</script>