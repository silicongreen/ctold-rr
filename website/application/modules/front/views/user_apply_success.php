<?php $s_ci_key = (isset($ci_key)) ? $ci_key : NULL; ?>

<div class="container" style="width: 77%;min-height:250px;">	 
	<div style="margin:30px 20px;height:60px;">
			<div style="float:left">
				<h2 class="f2">Apply for Guardian Admission</h2>
			</div>
			
	</div>
	<div id="toPopup"> 

    
    
    <div class="createpage">
        <?= form_open('', array('id' => 'validate_form_school', 'class' => 'validate_form', 'enctype' => "multipart/form-data")); ?>
        <div class="error_validation"><?php echo validation_errors(); ?></div>
        <div id="section_form_school">
            <div class="createpage_left">
                <label class="candle-input" style="padding:10px 0px;">
                    <span><font style="color:red; font-weight:bold; float: left; font-size: 16px;">*</font>Admission NO Of Your Children</span>
                    <input type="text" name="admission_no" id="admission_no" value="<?php echo $post_data['admission_no']; ?>" class="cd-input f5" >
                    
                </label>
                <label class="candle-input" style="padding:10px 0px;">
                    <span><font style="color:red; font-weight:bold;float: left; font-size: 16px;">*</font>Your Login Password</span>
                    <input type="password" name="password" id="password" value="" class="cd-input f5"> 
                </label>
                <label class="candle-input" style="padding:10px 0px;">
                    <span><font style="color:red; font-weight:bold;float: left; font-size: 16px;">*</font>First Name</span>
                    <input type="text" name="first_name" value="<?php echo $post_data['first_name']; ?>" id="first_name" class="cd-input f5" >
                    
                </label>
                <label class="candle-input" style="padding:10px 0px;">
                    <span><font style="color:red; font-weight:bold;float: left; font-size: 16px;">*</font>Last Name</span>
                    <input type="text" name="last_name" value="<?php echo $post_data['last_name']; ?>" id="last_name" class="cd-input f5" >
                    
                </label>
                <label class="candle-input" style="padding:10px 0px;">
                    <span><font style="color:red; font-weight:bold;float: left; font-size: 16px;">*</font>Relation</span>
                    <input type="text" name="relation" value="<?php echo $post_data['relation']; ?>" id="relation" class="cd-input f5">
                    
                </label>
                <label class="candle-input" style="padding:10px 0px;">
                    <span><font style="color:red; font-weight:bold;float: left; font-size: 16px;">*</font>Select Birth Date</span>
                    <input type="text" name="dob" value="<?php echo $post_data['dob']; ?>" id="dob" class="cd-input f5 datepicker" >
                    
                </label>
                <label class="candle-input" style="padding:10px 0px;">
                    <span><font style="color:red; font-weight:bold;float: left; font-size: 16px;">*</font>Your City</span>
                    <input type="text" name="city" value="<?php echo $post_data['city']; ?>" id="city" class="cd-input f5" >
                
                </label>
                <label class="candle-input" style="padding:10px 0px;">
                    <span>Mobile</span>
                    <input type="text" name="mobile_phone" value="<?php echo $post_data['mobile_phone']; ?>" id="mobile_phone" class="cd-input f5" >
                
                </label>
                <label class="candle-textarea" style="padding:10px 0px;">
                    <span>Occupation</span>
                    <input type="text" name="occupation" value="<?php echo $post_data['occupation']; ?>" id="occupation" class="cd-input f5" >
                    
                </label>
            </div>
            <div class="createpage_right">
                <img src="<?php echo base_url('Profiler/images/right/have_a_smiley_face.png');?>" style="width:100%;" />
                
                <p>All your 
                <span class="a">Information</span> need to be
                <span class="b">Parfect</span>.
                </p>
            </div>
            <div class="createpage_full">
                <label>
                    <input type="submit" id="submit_form_school"  value="Submit" />
                </label>
                
            </div>

        </div>
        <?= form_close(); ?>
    </div>



</div> <!--toPopup end-->


<style>
.createpage .error_validation p {
    color: red;
    float: left;
    clear: both;
    padding: 5px 0px;
    font-weight: bold;
}
 .datepicker {
  padding: 4px;
  margin-top: 1px;
  -webkit-border-radius: 4px;
  -moz-border-radius: 4px;
  border-radius: 4px;
  direction: ltr;
  /*.dow {
		border-top: 1px solid #ddd !important;
	}*/

}
.datepicker-inline {
  width: 220px;
}
.datepicker.datepicker-rtl {
  direction: rtl;
}
.datepicker.datepicker-rtl table tr td span {
  float: right;
}
.datepicker-dropdown {
  top: 0;
  left: 0;
}
.datepicker-dropdown:before {
  content: '';
  display: inline-block;
  border-left: 7px solid transparent;
  border-right: 7px solid transparent;
  border-bottom: 7px solid #ccc;
  border-bottom-color: rgba(0, 0, 0, 0.2);
  position: absolute;
  top: -7px;
  left: 6px;
}
.datepicker-dropdown:after {
  content: '';
  display: inline-block;
  border-left: 6px solid transparent;
  border-right: 6px solid transparent;
  border-bottom: 6px solid #ffffff;
  position: absolute;
  top: -6px;
  left: 7px;
}
.datepicker > div {
  display: none;
}
.datepicker.days div.datepicker-days {
  display: block;
}
.datepicker.months div.datepicker-months {
  display: block;
}
.datepicker.years div.datepicker-years {
  display: block;
}
.datepicker table {
  margin: 0;
}
.datepicker td,
.datepicker th {
  text-align: center;
  width: 20px;
  height: 20px;
  -webkit-border-radius: 4px;
  -moz-border-radius: 4px;
  border-radius: 4px;
  border: none;
}
.table-striped .datepicker table tr td,
.table-striped .datepicker table tr th {
  background-color: transparent;
}
.datepicker table tr td.day:hover {
  background: #eeeeee;
  cursor: pointer;
}
.datepicker table tr td.old,
.datepicker table tr td.new {
  color: #999999;
}
.datepicker table tr td.disabled,
.datepicker table tr td.disabled:hover {
  background: none;
  color: #999999;
  cursor: default;
}
.datepicker table tr td.today,
.datepicker table tr td.today:hover,
.datepicker table tr td.today.disabled,
.datepicker table tr td.today.disabled:hover {
  background-color: #fde19a;
  background-image: -moz-linear-gradient(top, #fdd49a, #fdf59a);
  background-image: -ms-linear-gradient(top, #fdd49a, #fdf59a);
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#fdd49a), to(#fdf59a));
  background-image: -webkit-linear-gradient(top, #fdd49a, #fdf59a);
  background-image: -o-linear-gradient(top, #fdd49a, #fdf59a);
  background-image: linear-gradient(top, #fdd49a, #fdf59a);
  background-repeat: repeat-x;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#fdd49a', endColorstr='#fdf59a', GradientType=0);
  border-color: #fdf59a #fdf59a #fbed50;
  border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
  filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
}
.datepicker table tr td.today:hover,
.datepicker table tr td.today:hover:hover,
.datepicker table tr td.today.disabled:hover,
.datepicker table tr td.today.disabled:hover:hover,
.datepicker table tr td.today:active,
.datepicker table tr td.today:hover:active,
.datepicker table tr td.today.disabled:active,
.datepicker table tr td.today.disabled:hover:active,
.datepicker table tr td.today.active,
.datepicker table tr td.today:hover.active,
.datepicker table tr td.today.disabled.active,
.datepicker table tr td.today.disabled:hover.active,
.datepicker table tr td.today.disabled,
.datepicker table tr td.today:hover.disabled,
.datepicker table tr td.today.disabled.disabled,
.datepicker table tr td.today.disabled:hover.disabled,
.datepicker table tr td.today[disabled],
.datepicker table tr td.today:hover[disabled],
.datepicker table tr td.today.disabled[disabled],
.datepicker table tr td.today.disabled:hover[disabled] {
  background-color: #fdf59a;
}
.datepicker table tr td.today:active,
.datepicker table tr td.today:hover:active,
.datepicker table tr td.today.disabled:active,
.datepicker table tr td.today.disabled:hover:active,
.datepicker table tr td.today.active,
.datepicker table tr td.today:hover.active,
.datepicker table tr td.today.disabled.active,
.datepicker table tr td.today.disabled:hover.active {
  background-color: #fbf069 \9;
}
.datepicker table tr td.active,
.datepicker table tr td.active:hover,
.datepicker table tr td.active.disabled,
.datepicker table tr td.active.disabled:hover {
  background-color: #006dcc;
  background-image: -moz-linear-gradient(top, #0088cc, #0044cc);
  background-image: -ms-linear-gradient(top, #0088cc, #0044cc);
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#0088cc), to(#0044cc));
  background-image: -webkit-linear-gradient(top, #0088cc, #0044cc);
  background-image: -o-linear-gradient(top, #0088cc, #0044cc);
  background-image: linear-gradient(top, #0088cc, #0044cc);
  background-repeat: repeat-x;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#0088cc', endColorstr='#0044cc', GradientType=0);
  border-color: #0044cc #0044cc #002a80;
  border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
  filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
  color: #fff;
  text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
}
.datepicker table tr td.active:hover,
.datepicker table tr td.active:hover:hover,
.datepicker table tr td.active.disabled:hover,
.datepicker table tr td.active.disabled:hover:hover,
.datepicker table tr td.active:active,
.datepicker table tr td.active:hover:active,
.datepicker table tr td.active.disabled:active,
.datepicker table tr td.active.disabled:hover:active,
.datepicker table tr td.active.active,
.datepicker table tr td.active:hover.active,
.datepicker table tr td.active.disabled.active,
.datepicker table tr td.active.disabled:hover.active,
.datepicker table tr td.active.disabled,
.datepicker table tr td.active:hover.disabled,
.datepicker table tr td.active.disabled.disabled,
.datepicker table tr td.active.disabled:hover.disabled,
.datepicker table tr td.active[disabled],
.datepicker table tr td.active:hover[disabled],
.datepicker table tr td.active.disabled[disabled],
.datepicker table tr td.active.disabled:hover[disabled] {
  background-color: #0044cc;
}
.datepicker table tr td.active:active,
.datepicker table tr td.active:hover:active,
.datepicker table tr td.active.disabled:active,
.datepicker table tr td.active.disabled:hover:active,
.datepicker table tr td.active.active,
.datepicker table tr td.active:hover.active,
.datepicker table tr td.active.disabled.active,
.datepicker table tr td.active.disabled:hover.active {
  background-color: #003399 \9;
}
.datepicker table tr td span {
  display: block;
  width: 23%;
  height: 54px;
  line-height: 54px;
  float: left;
  margin: 1%;
  cursor: pointer;
  -webkit-border-radius: 4px;
  -moz-border-radius: 4px;
  border-radius: 4px;
}
.datepicker table tr td span:hover {
  background: #eeeeee;
}
.datepicker table tr td span.disabled,
.datepicker table tr td span.disabled:hover {
  background: none;
  color: #999999;
  cursor: default;
}
.datepicker table tr td span.active,
.datepicker table tr td span.active:hover,
.datepicker table tr td span.active.disabled,
.datepicker table tr td span.active.disabled:hover {
  background-color: #006dcc;
  background-image: -moz-linear-gradient(top, #0088cc, #0044cc);
  background-image: -ms-linear-gradient(top, #0088cc, #0044cc);
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#0088cc), to(#0044cc));
  background-image: -webkit-linear-gradient(top, #0088cc, #0044cc);
  background-image: -o-linear-gradient(top, #0088cc, #0044cc);
  background-image: linear-gradient(top, #0088cc, #0044cc);
  background-repeat: repeat-x;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#0088cc', endColorstr='#0044cc', GradientType=0);
  border-color: #0044cc #0044cc #002a80;
  border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
  filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
  color: #fff;
  text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
}
.datepicker table tr td span.active:hover,
.datepicker table tr td span.active:hover:hover,
.datepicker table tr td span.active.disabled:hover,
.datepicker table tr td span.active.disabled:hover:hover,
.datepicker table tr td span.active:active,
.datepicker table tr td span.active:hover:active,
.datepicker table tr td span.active.disabled:active,
.datepicker table tr td span.active.disabled:hover:active,
.datepicker table tr td span.active.active,
.datepicker table tr td span.active:hover.active,
.datepicker table tr td span.active.disabled.active,
.datepicker table tr td span.active.disabled:hover.active,
.datepicker table tr td span.active.disabled,
.datepicker table tr td span.active:hover.disabled,
.datepicker table tr td span.active.disabled.disabled,
.datepicker table tr td span.active.disabled:hover.disabled,
.datepicker table tr td span.active[disabled],
.datepicker table tr td span.active:hover[disabled],
.datepicker table tr td span.active.disabled[disabled],
.datepicker table tr td span.active.disabled:hover[disabled] {
  background-color: #0044cc;
}
.datepicker table tr td span.active:active,
.datepicker table tr td span.active:hover:active,
.datepicker table tr td span.active.disabled:active,
.datepicker table tr td span.active.disabled:hover:active,
.datepicker table tr td span.active.active,
.datepicker table tr td span.active:hover.active,
.datepicker table tr td span.active.disabled.active,
.datepicker table tr td span.active.disabled:hover.active {
  background-color: #003399 \9;
}
.datepicker table tr td span.old {
  color: #999999;
}
.datepicker th.switch {
  width: 145px;
}
.datepicker thead tr:first-child th,
.datepicker tfoot tr:first-child th {
  cursor: pointer;
}
.datepicker thead tr:first-child th:hover,
.datepicker tfoot tr:first-child th:hover {
  background: #eeeeee;
}
.input-append.date .add-on i,
.input-prepend.date .add-on i {
  display: block;
  cursor: pointer;
  width: 16px;
  height: 16px;
}   
.datepicker.dropdown-menu {
    opacity: 1;
    visibility: visible;
    width:auto;
}
    #button-top { width: 100px; position: absolute; left: 75%; top: 40px; padding-left: 100px;overflow: hidden;}
    #button-top:hover, #button-bottom:hover {cursor: pointer;}
    .createpage { margin: 0 auto; width: 90%;  text-align: center; }  
    .createpage label{width:100%;}
    .createpage label span{color: black;    float: left;    font-size: 13px;}
    .createpage p { padding:8px 16px; color: #fff; margin: 0; }
    #button-bottom { width: 100px; position: absolute; left: 75%; top: 240px; padding-left: 100px;overflow: hidden;}
    .createpage_left{width:60%;float:left; clear:both;}
    .createpage_right{width:30%;float:right;padding-left:20px;}
    .createpage_right span{text-align:left;font-size: 16px;}
    .createpage_full{width:100%;}
    .createpage_right p{color:gray;font-size:27px;}
    .createpage_right span.a{color:#71B0DF;font-size:35px;font-weight: bold;line-height: 40px;}
   .createpage_right span.b{color:#DB3434;font-size:37px;font-weight: bold;line-height: 40px;}


    input[type=checkbox].css-checkbox {
        position: absolute; 
        overflow: hidden; 
        clip: rect(0 0 0 0); 
        height:1px; 
        width:1px; 
        margin:-1px; 
        padding:0;
        border:0;
    }

    input[type=checkbox].css-checkbox + label.css-label {
        padding-left:31px;
        height:20px; 
        display:inline-block;
        line-height:20px;
        background-repeat:no-repeat;
        background-position: 0 0;
        font-size:15px;
        color:#A6AEAF;
        vertical-align:middle;
        cursor:pointer;
        margin-right:26px;
    }

    input[type=checkbox].css-checkbox:checked + label.css-label {
        background-position: 0 -20px;
    }

    .css-label{ background-image:url("Profiler/images/right/checkbox.png"); }



    .select-style {
        border: 1px solid #ccc;
        width: 440px;
        height:35px;
        border-radius: 2px;
        overflow: hidden;
        background: #E7EBEE url("Profiler/images/right/downarrow.png") no-repeat 100% 50%;
    }

    .select-style select {
        padding: 5px 8px;
        color:#3D3D3B;
        opacity: 0.5;
        font-size: 13px;
        width: 105%;
        border: none;
        box-shadow: none;
        background: transparent;
        background-image: none;
        -webkit-appearance: none;
    }

    .select-style select:focus {
        outline: none;
    }					










</style>

	
</div>

<style>
#backgroundPopup { 
	z-index:5000;
	position: fixed;
	display:none;
	height:100%;
	width:100%;
	background:#000000;	
	top:0px;
	left:0px;
}
#toPopup {
	font-family: "lucida grande",tahoma,verdana,arial,sans-serif;
    background: none repeat scroll 0 0 #FFFFFF;
    padding: 40px 20px !important;	
    border-radius: 3px 3px 3px 3px;
    color: #333333;
    display: block !important;
    font-size: 14px;
    position: relative !important;
    left: 0px !important;
    top: 0px !important;
    width: 96% !important;
    z-index: 6000 !important;
	margin:30px 20px !important;
}
div.loader {
    background: url("../merapi/img/bx_loader.gif") no-repeat scroll 0 0 transparent;
    height: 32px;
    width: 32px;
	display: none;
	z-index: 9999;
	top: 40%;
	left: 50%;
	position: absolute;
	margin-left: -10px;
}
div.close {
    background: url("../merapi/img/close.png") no-repeat scroll 0 0 transparent;
    bottom: 30px;
    cursor: pointer;
    float: right;
    height: 30px;
    left: 10px;
    position: relative;
    width: 31px;
	display:none !important;
}
</style>