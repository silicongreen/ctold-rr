<?php
$cover_image = base_url() . "Profiler/images/right/banner.png";

if ($school_details->cover) {

    $cover_image_url = base_url() . $school_details->cover;
   
     $cover_image = $cover_image_url;
   
}

$logo_image = base_url() . "images/backgrounds/bg_content.png";

if ($school_details->logo) {

    $logo_image_url = base_url() . $school_details->logo;
    $logo_image = $logo_image_url;
  
}

$userschool = get_user_school($school_details->id);
$ar_segmens = $this->uri->segment_array();
?>
<div class="container" style="width: 73%;min-height:250px;">
    <div style="display:none;" id="school_didi"><?php echo $school_details->id; ?></div>    
    <div style="margin:20px 0px;height:60px;">
        <div style="float:left">
            <h2 class="f2">School Information</h2>
        </div>
        <!--<div class="header-bg" style="display:block;float:right;  margin-top:5px;">
    <div style="float: left;margin:5px;">                
        <form method="get" class="searchform" action="<?php echo base_url('search'); ?>" role="search">                    
            <input class="field" name="s" id="s" class='search' placeholder="Search this site" type="search" style="border-radius: 6px; -moz-border-radius: 6px; -webkit-border-radius: 6px; width: 220px; margin-top: 3px;">
            <input class="submit search-button" value="" type="submit" />
        </form>                
    </div>
</div>-->
    </div>

    <div class="banner_image">
        <div style="height:300px; width:100%;">
            <img src="<?php echo $cover_image; ?>"  style="height:300px; width:100%;" />
        </div>

        <?php
            $ex_class = ' before-login-user';
            $str_join_btn_text = 'Join In +';
            if (free_user_logged_in()) {
                if (!isset($user_school_status[$school_details->id])) {
                    $ex_class = ' btn_user_join_school';
                } else {
                    if ($user_school_status[$school_details->id] == '1') {
                        $ex_class = ' btn_leave_school';
                        $str_join_btn_text = 'Leave';
                    }
                    if ($user_school_status[$school_details->id] == '0') {
                        $ex_class = ' processing';
                        $str_join_btn_text = 'Processing';
                    }
                }
            }
        ?>

        <div class="join-wrapper">
            <button id="<?php echo $school_details->id; ?>" data="school_join" class="red<?php echo $ex_class; ?>" type="button">
                <span class="clearfix f5">
                    <?php echo $str_join_btn_text; ?>
                </span>
            </button>
        </div>

    </div>

    <div class="school_info_box">
        <!--    <div class="school_logo">
                <img src="<?php echo $logo_image; ?>" width="120" height="120" />
            </div> -->
        <div class="school_details_and_menu">
            <div class="school_details">
                <div class="school_name" id="fitin">
                    <div>
                        <span class="f2" style="/*font-size;30px;*/color:#fff; text-shadow: 2px 4px 3px rgba(0,0,0,0.3);">
                            <?php
                            echo $school_details->name;
                            if ($school_details->district) {
                                echo " , " . $school_details->district;
                            }
                            ?>
                        </span>
              <!--<span><?php //echo $school_details->views;      ?> Visits</span>-->
                    </div>
                </div>
                <div class="school_like">
                    <div class="fb-like" data-href="<?php echo base_url() . "schools/" . sanitize($school_details->name) . "/" ?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="false"></div>
                </div>
            </div> 
            <div class="headerlink f5">
                <div style="width:820px; height:60px; margin:0px 99px; padding:20px 20px 0px 20px; position:absolute; background:linear-gradient(to bottom, #FEFEFE , #D9D9D9);
                     -webkit-box-shadow: 0 10px 25px -2px gray;
                     -moz-box-shadow: 0 10px 25px -2px gray;
                     -ms-box-shadow: 0 10px 25px -2px gray;
                     -o-box-shadow: 0 10px 25px -2px gray;
                     box-shadow: 0 10px 25px -2px gray;
                     border-radius:7px;
                     ">
                    <ul>
                        <?php
                        if ($userschool) {
                            if ($userschool->is_approved == 1 || $userschool->is_approved == 0) {
                                ?>
                                <li><a <?php if (isset($feeds)): ?> class="red_menu"<?php endif; ?> href="<?php echo base_url() . "schools/" . sanitize($school_details->name) . "/feed"; ?>">Feeds</a></li>  
                                <li>|</li>
                                <?php
                            }
                        }
                        ?>
                        <?php
                        $count = count($schools_pages);
                        $ci = 1;
                        foreach ($schools_pages as $value):
                            ?>
                            <li><a <?php if ($menu_details->title == $value->title): ?> class="red_menu"<?php endif; ?> href="<?php echo base_url() . "schools/" . sanitize($school_details->name) . "/" . sanitize($value->title); ?>"><?php echo $value->title ?></a></li>
                            <?php if ($ci < $count): ?>
                                <li>|</li>
                            <?php endif; ?>
                            <?php $ci++;
                        endforeach; ?>
                        <?php
                        if ($userschool) {
                            if ($userschool->is_approved == 1) {
                                ?>
                                <li>|</li>
                                <li href="javascript(0);" data="candle" class="<?php echo ( free_user_logged_in() ) ? 'candlepopup' : 'before-login-user'; ?>">
                                    <a href="">Candle</a>
                                </li>   
                                <?php
                            }
                        }
                        ?>

                    </ul>

                    <div class="all-schools">
                        <a href="<?php echo base_url('schools'); ?>">All Schools</a>
                    </div>

                </div>
            </div>
        </div>    
    </div> 

    <?php if ($school_page_details): ?>
        <div class="school_content_box">

            <?php if (isset($activity_link)): ?>
                <h2 class="f2"><?php echo $school_page_details->title; ?></h2>
            <?php endif; ?>
            <div class="f5"><?php echo $school_page_details->content ?></div>
            <?php if (count($gallery) > 0): ?>
                <div style="clear:both;
                     margin-top: 20px; ">
                    <div style="text-align:center;">


                        <div style="display:none;" class="html5gallery"  data-responsive="true" data-thumbwidth="150" data-thumbheight="75"  data-skin="horizontal" data-thumbshowtitle="false" data-width="900" data-height="470"  data-showsocialmedia="false"  
                             data-resizemode="fit" >

                            <?php foreach ($gallery as $value): ?>
                                <?php
                                $s_thumb_image = str_replace("gallery/", "gallery/weekly/", $value->material_url);

                                list($width, $height, $type, $attr) = @getimagesize(base_url() . $s_thumb_image);
                                if (!isset($width)) {
                                    $s_thumb_image = $value->material_url;
                                }
                                ?>
                                <a href="<?php echo base_url() . $value->material_url ?>"><img src="<?php echo base_url() . $s_thumb_image ?>" ></a>
                            <?php endforeach; ?>
                            <!-- Add images to Gallery -->


                        </div>

                    </div>
                </div>    

            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?php if (isset($activities) && count($activities) > 0): ?>
        <div class="school_activities_box" <?php if (!isset($school_page_details) || !$school_page_details): ?> style="margin-top:20px;" <?php endif; ?>>
            <div class="school_activity_title">
                <span class="f2" style="margin-left:70px;font-size:25px;">School Activity</span>
                <?php if (isset($school_page_details) && $school_page_details): ?> 
                    <a href="<?php echo base_url() . "schools/" . sanitize($school_details->name) . "/activities/" ?>" style="margin-top:10px;text-decoration:none;">See All</a>
                <?php endif; ?>
            </div>
            <div class="school_activity_box" >


                <?php foreach ($activities as $value): ?>

                    <div class="activity">
                        <div class="title"></div>
                        <?php if ($value['image']): ?>
                            <div class="left_img">

                                <img style="float: left; margin-right:15px;" src="<?php echo $value['image']; ?>" width="120" height="120" />

                                <div>
                                    <p style="margin:0px; margin-top:-6px;">
                                        <a class="activity_title" href="<?php echo base_url() . "schools/" . sanitize($school_details->name) . "/activities/" . $value['id']; ?>"><?php echo $value['title']; ?></a>
                                    </p>

                                    <p style="margin:0px; font-size:13px;"><?php echo $value['content']; ?></p>
                                </div> 

                            </div> 
                        <?php else: ?>
                            <div class="leftfull">
                                <p >
                                    <a class="activity_title" href="<?php echo base_url() . "schools/" . sanitize($school_details->name) . "/activities/" . $value['id']; ?>"><?php echo $value['title']; ?></a>
                                </p>
                                <p style="margin:0px;font-size:13px;"><?php echo $value['content']; ?></p> 
                            </div>
                        <?php endif; ?>

                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($feeds)): ?>
        <div class="school_feed_box"> 
            <?php $widget = new Widget;
            $widget->run('postdata', "school", $school_details->id, 'school'); ?>
        </div>
    <?php endif; ?>    

</div>

<?php if (free_user_logged_in()) { ?>
    <div id="school_join_frm_wrapper" style="display: none;">

        <?php echo form_open('', array('class' => 'validate_form', 'id' => 'school_join_frm', 'enctype' => 'multipart/form-data', 'autocomplete' => 'off')); ?>

        <div class="clearfix" style="margin-left: auto; margin-right: auto; width: 90%; margin-top: 0px; ">

            <div>

                <input type="hidden" id="school_id" name="school_id" value="" />

                <fieldset class="reg_logo">
                    <div>
                        <img src="<?php echo base_url('styles/layouts/tdsfront/image/magicmart_red.png'); ?>" width="60px" alt="Chmaps21.com" />
                    </div>
                </fieldset>

                <fieldset class="reg_title f2">
                    <div>
                        <?php echo 'Join To Your School'; ?>
                        <div class="clearfix horizontal-line"></div>
                    </div>
                </fieldset>

                <?php if (empty($model->user_type) || ($model->user_type == 1)) { ?>
                    <fieldset class="hell_box">

                        <div style="text-align: left; padding: 15px 0 0 51px;">
                            <label class="user_type_dialob_label">Joining as... </label>
                        </div>

                        <div class="user_type_div">
                            <ul class="radio-holder">
                                <?php $i = 0;
                                foreach ($join_user_types as $key => $value) { ?>
                                    <li class="user_type_radio" <?php echo ($i > 0) ? 'style="padding-left: 60px !important;"' : '' ?>>
                                        <input class="css-checkbox" id="<?php echo $value; ?>" name="user_type" value="<?php echo $key; ?>" type="radio" 
                                        <?php
                                        if (($model->user_type >= 0 ) && ($key == $model->user_type)) {
                                            echo 'checked="checked"';
                                        } else {
                                            echo ($key == 0) ? 'checked="checked"' : '';
                                        }
                                        ?>
                                               >
                                        <label for="<?php echo $value; ?>" class="css-label"></label>
                                        <label for="<?php echo $value; ?>" class="user_type_label"><?php echo $value; ?></label>
                                    </li>
                                    <?php $i;
                                } ?>
                            </ul>
                        </div>
                    </fieldset>

                    <div class="clearfix" <?php echo ($edit) ? 'style="margin-bottom: 30px"' : ''; ?>></div>
                <?php } ?>

                <fieldset class="grades_ul" <?php echo ($model->user_type == 1) ? 'style="display:none;"' : ''; ?> >
                    <label class="select_grades">Select Your Grade</label>
                    <ul class="radio-holder">
                        <?php $i = 0;
                        foreach ($grades as $value) { ?>
                            <li<?php echo ($i == 0) ? ' style="padding-left: 20px !important;"' : ''; ?>>
                                <input class="custom_checkbox" id="<?php echo $value->id; ?>" name="grade_ids<?php echo (($model->user_type == 3) || ($model->user_type == 4) ) ? '[]' : '[]'; ?>" value="<?php echo $value->id; ?>" type="<?php echo (($model->user_type == 3) || ($model->user_type == 4) ) ? 'checkbox' : 'checkbox'; ?>"
                                <?php
                                $ar_grade_ids = explode(',', $model->grade_ids);

                                if ($edit && in_array($value->id, $ar_grade_ids)) {
                                    echo 'checked="checked"';
                                }
                                ?>
                                       >
                                <label class="checkbox_label_txt" for="<?php echo $value->id; ?>"><?php echo $value->grade_name; ?></label>
                            </li>
                            <?php $i;
                        } ?>
                    </ul>
                </fieldset>

                <fieldset id="addition_row_1">
                    <div class="center">

                        <?php if ($model->user_type == 1) { ?>
                            <input placeholder="Your Batch" class="f5 email_txt large" id="batch" name="batch" value="" type="text" maxlength="15" />
                        <?php } else { ?>
                            <input placeholder="<?php echo ($model->user_type == 4) ? 'Student ' : ''; ?>Section" class="f5 email_txt" id="sections" name="sections" value="" type="text" maxlength="25" />
                            <?php if ($model->user_type == 3) { ?>
                                <input placeholder="Employee ID." class="f5 email_txt" id="employee_id" name="employee_id" value="" type="text" maxlength="15" />
                            <?php } else if ($model->user_type == 2) { ?>
                                <input placeholder="Class Roll NO." class="f5 email_txt" id="roll_no" name="roll_no" value="" type="text" maxlength="15" />
                            <?php } else if ($model->user_type == 4) { ?>
                                <input placeholder="Student ID." class="f5 email_txt" id="roll_no" name="student_id" value="" type="text" maxlength="15" />
                            <?php } ?>
                        <?php } ?>

                    </div>
                </fieldset>

                <fieldset id="addition_row_2">
                    <div class="center">
                        <?php if ($model->user_type == 2) { ?>
                            <input placeholder="Activation Code" class="f5 email_txt large" id="admission_no" name="admission_no" value="" type="text" maxlength="100" />
                        <?php } else { ?>
                            <input placeholder="Contact NO." class="f5 email_txt large" id="contact_no" name="contact_no" value="" type="text" maxlength="15" />
                        <?php } ?>

                    </div>
                </fieldset>

            </div>

            <div class="clearfix" style="margin-left: auto; margin-right: auto; margin-top: 20px; text-align: center; margin-bottom: 20px;">
                <button id="btn_submit_user_join" class="red" type="submit">
                    <span class="clearfix f2">
                        <?php echo 'Join In'; ?>
                    </span>
                </button>
            </div>

        </div>

        <?php echo form_close(); ?>
    </div>
<?php } ?>

<style>
    .action-box
    {
        background: none repeat scroll 0 0 #e7e7e7 !important;
    }
    .post-content
    {
        background: none repeat scroll 0 0 #e7e7e7 !important;
    }

    .school_activities_box
    {
        float:left;
        clear:both;
        margin-bottom:50px;
        border:1px solid gray;
    }
    .school_activity_title
    {
        float:left;
        clear:both;
        width:100%;        
        padding: 5px 0px;
        background: #88BAA1 url(<?php echo base_url('styles/layouts/tdsfront/images/sc_activity.png'); ?>) no-repeat 20px 10px;;
        color:white;     
        border:1px solid #fff	;	
    }

    .school_activity_title span
    {
        float:left;
        margin-left:20px;
    }
    .school_activity_title a
    {
        float:right;
        margin-right:20px;
        color:white;
        text-decoration: underline;
    }
    .school_logo img
    {
        height:120px;
    }
    .activity span
    {

        font-size:11px;
    }
    .activity_title
    {
        font-size:15px;
        color:#74A98D;
    }
    span.activity_title{

        float:left;
        clear: both;
        color:#FC5D51;
        font-size:14px;
    }

    .leftfull
    {
        float:left;
        width: 100%;
    }
    .left_img
    {
        float:left;
        width: 100%; 
    }

    .activity
    {
        float:left;
        width:44%;
        margin-bottom: 20px;
        margin-left:30px;
        height: 143px;

        overflow: hidden;
    }
    .banner_image
    {
        float:left;
        clear:both;
        position: relative;
        width:100%;
    }
    .school_activity_box
    {
        float:left;
        clear:both;    
        width:100%;    
        padding: 41px 20px;
        background: #fff;    
    }
    .school_feed_box
    {
        float:left;
        clear:both;
        width:100%;    
        padding: 40px 30px;
        background: #fff;
        border:1px solid gray;
        margin-bottom:20px;
    }
    .school_content_box
    {
        clear:both;
        width:100%;    
        padding: 40px 30px;
        background: #fff;
        border:1px solid gray;
        margin-bottom:20px;
    }
    .school_info_box
    {
        width:100%;   
    }
    .school_logo
    {
        float:left;
        background: #fff;
        padding: 22px;
        width: 18%;
        margin-top:-10%;

    }
    .school_details_and_menu
    {
        position:absolute;
        top:333px;
        width: 73%;
    }
    .school_details
    {   
        background: none; 
        margin:0px 99px;
    }
    .headerlink
    {    
        height:60px;
        float: left;
        font-family: arial;
        font-size: 14px;
        margin-top: 5px;
    }
    .school_name
    {    

        margin-top:20px;
        text-shadow: 0 1px 1px #4d4d4d;	
        display: inline-block;
    }
    #fitin
    {
        width:790px;
        height:40px;
        font-size: 45px;
    }
    .school_like
    {
        float:right;
        margin:40px 60px;
    }
    .school_details span
    {  
        color:#92979B;
    }
    .school_details span:first-child
    {    

    }
    .school_details_and_menu .headerlink ul li
    {
        margin-bottom:8px;
        display: inline;
        padding: 10px 5px;
    }
    .headerlink ul li a
    {
        font-size:17px;
        padding:16px 10px;

    }
    .headerlink ul li a.red_menu
    {
        color:red;
        border-bottom: 4px solid red;
    }
    
    .all-schools {
        background-color: #60cb97;
        border-radius: 20px;
        -ms-border-radius: 20px;
        -moz-border-radius: 20px;
        -o-border-radius: 20px;
        -webkit-border-radius: 20px;
        border-radius: 20px;
        border-radius: 20px;
        border-radius: 20px;
        color: #ffffff;
        padding: 8px 20px;
        position: absolute;
        right: 30px;
        top: 13px;
    }
    .all-schools a {
        color: #ffffff;
    }
    .join-wrapper{
        position: absolute;
        right: 27px;
        text-align: right;
        top: 27px;
        width: 30%;
    }
    .red{
        background-color: #bbbbbb;
        border: medium none;
        color: #fff;
        height: 35px;
        line-height: 15px;
        padding-left: 12px;
        width: 50%;
        box-shadow: none;
        -o-box-shadow: none;
        -ms-box-shadow: none;
        -moz-box-shadow: none;
        -webkit-box-shadow: none;
    }
    .red:hover{
        background-color: #60cb97;
        -webkit-transition: background-color 0.5s ease;
        -moz-transition: background-color 0.5s ease;
        -o-transition: background-color 0.5s ease;
        -ms-transition: background-color 0.5s ease;
        transition: background-color 0.5s ease;
    }
    .email_txt{
        background-color: #adb2b5;
        border-radius: 0px;
        -moz-border-radius: 0px;
        -webkit-border-radius: 0px;
        -o-border-radius: 0px;
        -ms-border-radius: 0px;
        color: #000000 !important;
        font-size: 13px !important;
        height: 45px !important;
        width: 49.52%;
    }
    .center{
        margin-left: auto;
        margin-right: auto;
        padding-top: 15px;
        text-align: center;
        width: 100%;
    }
    fieldset input.custom_checkbox[type="radio"] {
        clear: both;
        cursor: pointer;
        display: block;
        float: left;
        height: 15px;
        margin-top: 8px !important;
        width: 15px;
    }
    .large{
        width: 100%;
    }
    .processing {
        background-image: url('/styles/layouts/tdsfront/image/processing.png');
        background-position: right 7px center;
        background-repeat: no-repeat;
        background-size: 25px auto;
        text-align: left;
    }
</style>

<script>
    $(function() {    
        while( $('#fitin div').height() > $('#fitin').height() ) {		
            $('#fitin div').css('font-size', (parseInt($('#fitin div').css('font-size')) - 1) + "px" );
        }
		
        if($('.headerlink').width() > $('.headerlink div').width())
        {
            $('.headerlink div').css('margin-left', (parseInt($('.headerlink').width() - ($('.headerlink div').width() + 40))/2) + "px")
            $('.headerlink div').css('margin-right', (parseInt($('.headerlink').width() - ($('.headerlink div').width() + 40))/2) + "px")
        }
	
        $('.school_details_and_menu').css('top', (parseInt( 514 - $('.school_details_and_menu').height() )) + "px");
    });
</script>