
<div style="border:0px solid;height:550px;padding-top:30px;">
    <h2 class="f2" style="margin-bottom:30px;"><i><?php echo "আমাদের কাছে লিখুন ...";?></i></h2>
    
    <form id="contact_classtune">
        <div class="row" style="width:500px;margin:0px auto;">
         <span class="legend"></span>
        </div>
        <div class="row" style="width:500px;margin:0px auto;">
            <input type="text" name="name" id="name" placeholder="<?php echo "নাম";?>">
        </div>
		<div class="row" style="width:500px;margin:0px auto;">
            <div style="float:left;width:47%;">
                <input type="text" name="email" id="email" placeholder="<?php echo "ই-মেইল";?>">
            </div>
            <div style="float:right;width:47%;">
                <input type="text" name="phone" id="phone" placeholder="<?php echo "ফোন";?>">
            </div>
        </div>
		<div class="row" style="width:500px;margin:0px auto;">
            <div style="float:left;width:47%;">
                <select name="subject_type" id="subject_type">
					<option value=""><?php echo "সিলেক্ট সাবজেক্ট";?></option>
					<option value="A"><?php echo "নুতন অ্যাকাউন্ট";?></option>
					<option value="B"><?php echo "অনুসন্ধান";?></option>
					<option value="C"><?php echo "অভিযোগ";?></option>
					<option value="D"><?php echo "পরামর্শ";?></option>
					<option value="E"><?php echo "অন্যান্য";?></option>
                </select>
            </div>
            <div style="float:right;width:47%;">
                <input type="text" name="subject_text" id="subject_text" placeholder="<?php echo "সাবজেক্ট";?>">
            </div>
        </div>
        <!--div class="row" style="width:500px;margin:0px auto;">
            <input type="text" name="subject" id="subject" placeholder="Subject">
        </div-->
		<div class="row" style="width:500px;margin:0px auto;">
            <div style="float:left;width:47%;">
                <select name="user_type" id="user_type">
					<option value="Admin">আমি স্কুল অ্যাডমিন</option>
					<option value="Teacher">আমি শিক্ষক</option>
					<option value="Student">আমি শিক্ষার্থী</option>
					<option value="Parent">আমি অভিভাবক</option>
					<option value="Visitor">আমি দর্শনার্থী</option>
                </select>
            </div>
            <div style="float:right;width:47%;">
                <input type="text" name="school_name" id="school_name" placeholder="<?php echo "স্কুল নাম";?>">
            </div>
        </div>
        <div class="row" style="width:500px;margin:0px auto;">
            <textarea row="5" name="massage" id="massage"></textarea>
        </div>
        <div class="row" style="width:500px;margin:0px auto;">
            <div style="float:left;width:30%;">
                <input class="button_c" id="sub" name="submit" type="submit" value="<?php echo "দাখিল করা";?>">
            </div>
            <div style="float:left;width:30%;">
                <button type="submit" class="button_c" id="reset"><?php echo "রিসেট";?></button>
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
