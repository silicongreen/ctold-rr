<script type="text/javascript" src="<?php echo base_url('styles/layouts/tdsfront/spelling_bee/jquery.mCustomScrollbar.concat.min.js'); ?>"></script>
<link rel="stylesheet" href="<?php echo base_url('styles/layouts/tdsfront/spelling_bee/jquery.mCustomScrollbar.css'); ?>">
<script type="text/javascript" src="<?php echo base_url('styles/layouts/tdsfront/spelling_bee/jquery.popupWindow.js'); ?>"></script>
<div class="container" id="tabContainer" style="width: 77%; min-height: 250px; margin-bottom: 250px;">
    <div style="float:left;margin-left:20px;width: 96%;">
        <a href="<?php echo base_url('spellingbee'); ?>">
            <h1 style="color:#93989C;float: left;" class="title noPrint f2">
                Spelling Bee&nbsp;
            </h1>
        </a>                
        <h1 style="color:#93989C;float: left;" class="title noPrint f2">
            > Divisional Participants
        </h1>        
    </div>
    <div style="clear:both;"></div>
    <div class="spellingbee">
        <div style="float: left;width: 100%;padding: 10px 50px;margin-top:20px;">
            <a href="<?php echo base_url('leaderboard'); ?>">
                <h1 style="float: left;color:#000;font-size:40px;margin-top: 20px;" class="title noPrint f2">
                    Divisional Participants
                </h1>                
            </a>
            <p style="clear: both; float: left; width: 70%;">
                Congratulation Spellers! 
Here is the list of Top Spellers who will be competing in the upcoming Divisional Round.
Look up your name & find <a href="http://www.champs21.com/spelling-bee-season-4-divisional-schedule-1687">when and where</a> to come for the next level of competition. 
            </p>
            <a href="<?php echo base_url('spellingbee'); ?>">
                <img src="<?php echo base_url('styles/layouts/tdsfront/spelling_bee/2015/images/sp-logo.png'); ?>" style="float: right;width:15%; margin-top: -55px;">
            </a>
            
        </div>
        <?php if( free_user_logged_in() ) { ?>
            <?php 
                    $login_user_data = get_free_user_session();
                    $userfullname = ucfirst($login_user_data['first_name'])." ".ucfirst($login_user_data['middle_name'])." ".ucfirst($login_user_data['last_name']);
                    $userschoolname = ucfirst($login_user_data['school_name']);
                    $userdivision = ucfirst($login_user_data['division']);
                    
                if($login_user_data['is_joined_spellbee'] == 1 && $login_user_data['type'] == 2){
            ?> 
        <div class="score_box">
            <h2 class="f2" style="background-color: #ffd109;font-size: 40px;padding: 15px;text-align: center;margin: 0px;">Your Score</h2>
            <div style="background-color: #575757;padding: 15px;width: 100%;height: 150px;">
                <div style="float:left;width:20%;height:120px; ">
                    <?php
           
                $has_profile_img = FALSE;
                //$profile_image_url = base_url('upload/free_user_profile_images/2.jpg');
                $profile_image_url = base_url('Profiler/images/right/nopic.png');
                
                if( free_user_logged_in() ){
                    $user_data = get_free_user_session();



                    if( !empty($user_data['profile_image'] )){
                        //$profile_image_url = base_url() . 'upload/free_user_profile_images/' . $user_data->profile_image;
                        $profile_image_url = $user_data['profile_image'];
                        $has_profile_img = TRUE;
                        
                    }else{
                        $_user_data = get_user_data();
                        
                        $user_data = get_free_user_session();
                            
                        if( !empty($_user_data->profile_image) ){
                            $profile_image_url = $_user_data->profile_image;
                            $has_profile_img = TRUE;
                        }
                        
                    }
                }
                
                        ?>
                    <img src="<?php echo $profile_image_url; ?>" style="width:90%;" >
                </div>
                <div style="float:left;width:60%;height:120px; ">
                    <p class="name"><?php echo $userfullname;?></p>
                    <p class="school_division"><?php echo $userschoolname;?></p>
                    <p class="school_division"><?php echo $userdivision;?></p>
                    <?php if($spellbee_user_score != -1){?>
                    <p class="best_score"> Best Score :<?php echo $spellbee_user_score[0]->score;?></p>
                    <?php }?>
                    <?php if($spellbee_user_rank != -1){?>
                    <p class="user_rank"><span></span>Rank&nbsp;:&nbsp;<?php echo $spellbee_user_rank[0]->rank;?></p>
                    <?php }?>
                </div>
                <!--div style="float:right;width:20%;height:120px; ">
                    <?php // if( free_user_logged_in() ) { ?>
                        <?php // $is_joined_spellbee = get_free_user_session('is_joined_spellbee');
//                        if($is_joined_spellbee == 1 || get_free_user_session('type') != 2){
                        ?>                                        
                        <a href="http://www.champs21.com/swf/spellingbee_2015/index.html" title="Spelling Bee | Season 4" class="example2demo" style="float: left;width:110px;" name="Spelling Bee">
                            <img src="<?php // echo base_url('styles/layouts/tdsfront/spelling_bee/2015/images/play.png'); ?>" style="width:100%;" onMouseOver="MouseRollover(this)" onMouseOut="MouseOut(this)">
                        </a>
                        <?php // } else { ?>
                            <a  id="join_spellbee_reg" style="float: left;width:110px;" href="javascript:void(0);">
                                <img src="<?php // echo base_url('styles/layouts/tdsfront/spelling_bee/2015/images/play_again.png'); ?>" style="width:100%;" onMouseOver="MouseRollover(this)" onMouseOut="MouseOut(this)">
                            </a>
                        <?php // }
//                        } else { ?>
                        <a  class="f2 login-user" style="float: left;width:110px;" href="javascript:void(0);">
                            <img src="<?php // echo base_url('styles/layouts/tdsfront/spelling_bee/2015/images/play_again.png'); ?>" style="width:100%;" onMouseOver="MouseRollover(this)" onMouseOut="MouseOut(this)">
                        </a>
                    <?php // } ?>
                </div-->
            </div>
        </div>
            <?php } ?>
        <?php } ?>
        <nav>
            <div class="tabcontainer">

                <ul class="tabheading">
                    <li class="active" rel="tab1"><a href="javascript:return false;">Dhaka A</a> </li>
                    <li rel="tab8"><a href="javascript:return false;">Dhaka B</a> </li>
                    <li rel="tab2"><a href="javascript:return false;">Chittagong</a> </li>
                    <li rel="tab3"><a href="javascript:return false;">Rajshahi</a> </li>
                    <li rel="tab4"><a href="javascript:return false;">Khulna</a> </li>
                    <li rel="tab5"><a href="javascript:return false;">Sylhet</a> </li>
                    <li rel="tab6"><a href="javascript:return false;">Rangpur</a> </li>
                    <li rel="tab7"><a href="javascript:return false;">Barisal</a> </li>
                </ul>

                <div class="tabbody active" id="tab1" style="display: block;">
                    <table cellspacing='0'>                       
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Name & School</th>
                                <th>Score</th>
                            </tr>
                        </thead>
                        <tbody><?php echo $spellbee_data;?></tbody>
                    </table>    
                  <?php //$this->load->view('leaderboard/dhaka'); ?> 
                </div>

                <div class="tabbody" id="tab8" style="display: none;">
                  <?php //$this->load->view('leaderboard/chittagong'); ?>  
                </div>
                
                <div class="tabbody" id="tab2" style="display: none;">
                  <?php //$this->load->view('leaderboard/chittagong'); ?>  
                </div>
                
                <div class="tabbody" id="tab3" style="display: none;">
                  <?php //$this->load->view('leaderboard/rajshahi'); ?>  
                </div>
                
                <div class="tabbody" id="tab4" style="display: none;">
                  <?php //$this->load->view('leaderboard/khulna'); ?>  
                </div>
                
                <div class="tabbody" id="tab5" style="display: none;">
                  <?php //$this->load->view('leaderboard/sylhet'); ?>  
                </div>
                
                <div class="tabbody" id="tab6" style="display: none;">
                  <?php //$this->load->view('leaderboard/rangpur'); ?>  
                </div>
                
                <div class="tabbody" id="tab7" style="display: none;">
                  <?php //$this->load->view('leaderboard/barishal'); ?>  
                </div>
            </div>
        </nav>
        
        <div style="clear:both;"></div>        
        <div style="width: 100%;margin-top:40px;height:130px;">    
<!--        <a  href="#" class="f2 button-viewmore">View Top 30 Scorers</a>    -->
</div>
        
    </div>
</div>
<style>
    .button-viewmore {
  background-color: #F4A91C;
  border: 1px solid #b3b3b3;
  border-radius: 6px;
  color: #fff;
  cursor: pointer;
  display: block;
  width: 230px;
  font-size: 17px;
  font-weight: normal;
  padding: 10px 17px;
  margin: 0px auto;
  text-decoration: none;
  transition: all 0.25s ease-in 0s;
}

.button-viewmore:hover, .button-viewmore:active {
  background-color: #FF8F35;
  color: #ffffff;
  transition: all 0.25s linear 0s;
}
</style>
<style>
.spellingbee {
  background: none repeat scroll 0 0 #ffffff;
  margin: 10px 20px;
  min-height: 600px;
  background-image: url("<?php echo base_url('styles/layouts/tdsfront/spelling_bee/2015/images/bee.png'); ?>");
  background-position: right bottom;
  background-repeat: no-repeat;
  background-size: 13% auto;
}
nav {
    display: block;
}
.tabcontainer {
    float: left;
    width: 100%;
    text-align:center;
    
}
.tabcontainer ul
{
    margin:0px;
}
.tabheading li.active {
    background-color: #FDF8CE;
    border-bottom: 0;
    margin-bottom: -1px;
}
.tabheading li {
    display: inline-block;
    border: 1px solid #ddd;
    background-color: #F2FAFD;
    margin: 0;
    padding: 10px 0px;
}
.tabheading li a { 
    color: #88a5b3;
    font-size: 14px;
    padding: 10px 20px;
}
.tabbody.active {
    display: block;
}
.tabbody {    
    margin: 0px auto;
    min-height: 10px;
    width: 94%;
    padding: 0;
    display: none;
}
.score_box
{
    float: left;
    width: 100%;
    padding: 10px 50px;
    margin-top:20px;
    margin-bottom: 20px;
}
.score_box .name
{
    color: #ffd109;
    font-size: 20px;
    font-weight: bold;
    font-family: Verdana;
    letter-spacing: 0px;
}
.score_box .school_division
{
    color: #b4b4b4;
    font-family: Verdana;
    font-size: 12px;
    letter-spacing: 0;
    line-height: 18px;
    margin-bottom: 0;
}
.score_box .best_score
{
    color: #fff;
    font-size: 12px;    
    font-family: Verdana;
    letter-spacing: 0px;
    line-height: 18px;
    margin-bottom: 5px;
}
.score_box .user_rank
{
    color: #fff;
    font-size: 18px;    
    font-family: arial;
    letter-spacing: 0px;
    background-image: url("<?php echo base_url('styles/layouts/tdsfront/spelling_bee/2015/images/rank_icon.png'); ?>");
    background-position: left top;
    background-repeat: no-repeat;
    background-size: 5% auto;
    height:28px;
    padding-left:35px;
}
</style>
<script>
 $('.tabheading li').click(function () {
        var tabid = $(this).attr("rel");
        var tabval = $(this).find("a").html();
        var base_url = $('#base_url').val();
        $(this).parents('.tabcontainer').find('.active').removeClass('active');
        
        $.ajax({
            type: "POST",
            url: base_url + 'front/ajax/getleaderboarddata',
            data:'stdivision='+tabval,
            beforeSend: function(){
                    $(this).css("background"," url("+base_url+"styles/layouts/tdsfront/spelling_bee/LoaderIcon.gif) no-repeat 350px");
            },
            success: function(data){                       
                if(data)
                {   
                    $('#' + tabid).html(data);                    
                }                    
            },
            error: function (event) {

            }
            });
        
        $('.tabbody').hide();
        $('#' + tabid).show();
        $(this).addClass('active');

        return false;
    });
</script>
<script language="javascript">
        function MouseRollover(MyImage) {
        MyImage.src = "styles/layouts/tdsfront/spelling_bee/2015/images/play_again_hover.png";
    }
        function MouseOut(MyImage) {
        MyImage.src = "styles/layouts/tdsfront/spelling_bee/2015/images/play_again.png";
    }
</script>
<script type="text/javascript"> 
$('.example2demo').popupWindow({ 
centerBrowser:1 ,
height:600,
width:800,
resizable:1
}); 
</script>
<style>
    table a:link {
	color: #666;
	font-weight: bold;
	text-decoration:none;
        border: 0px solid #e0e0e0;
}
table a:visited {
	color: #999999;
	font-weight:bold;
	text-decoration:none;
}
table a:active,
table a:hover {
	color: #bd5a35;
	text-decoration:underline;
}
table {
	font-family:Arial, Helvetica, sans-serif;
	color:#666;
	font-size:15px;	
	background:#F5F5F5;
	margin:0px 20px;
	border:#ccc 1px solid;	
        width: 95.5%;
}
table th {
	padding:10px;
	border-top:0px solid #fafafa;
	border-bottom:0px solid #e0e0e0;
        border-right: 0px solid #e0e0e0;
        border-left: 0px solid #e0e0e0;
        color:#fff;
	background: #FCB316;
}
table th:first-child {
	text-align: left;
	padding-left:2px;
}
table tr:first-child th:first-child {
	
}
table tr:first-child th:last-child {
	
}
table tr {
	text-align: center;
	padding-left:10px;
}
table td:first-child {
	text-align: left;
	padding-left:15px;
	border-left: 0;
}
table td:last-child {
	color:red;
        font-weight: bold;
	
}
table td {
	padding:5px;
	border-top: 0px solid #ffffff;
	border-bottom:0px solid #e0e0e0;
	border-left: 0px solid #e0e0e0;
	border-right: 0px solid #e0e0e0;
	background: #F5F5F5;
        font-family: Arial !important;
        letter-spacing:0px;
}
table tr.even td {
	background: #F5F5F5;
}
table tr:last-child td {
	border-bottom:0;
}
table tr:last-child td:first-child {
}
table tr:last-child td:last-child {
	
}
table tr:hover td {
	background: #F5F5CF;
        cursor: pointer;
}
</style>