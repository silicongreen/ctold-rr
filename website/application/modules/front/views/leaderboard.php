
<script type="text/javascript" src="<?php echo base_url('styles/layouts/tdsfront/spelling_bee/jquery.mCustomScrollbar.concat.min.js'); ?>"></script>
<link rel="stylesheet" href="<?php echo base_url('styles/layouts/tdsfront/spelling_bee/jquery.mCustomScrollbar.css'); ?>">
<div class="container" id="tabContainer" style="width: 77%; min-height: 250px; margin-bottom: 250px;">
    
    <div class="spellingbee">
        <div style="float: left;width: 100%;padding: 10px 50px;margin-top:20px;">
            <a href="http://www.champs21.com/leaderboard/">
                <h1 style="float: left;color:#000;font-size:40px;margin-top: 20px;" class="title noPrint f2">
                    Leader Board
                </h1>
            </a>
            <a href="http://www.champs21.com/spellingbee/">
                <img src="<?php echo base_url('styles/layouts/tdsfront/spelling_bee/2015/images/sp-logo.png'); ?>" style="float: right;width:15%;">
            </a>
            
        </div>
        <nav>
            <div class="tabcontainer">

                <ul class="tabheading">
                    <li class="active" rel="tab1"><a href="javascript:return false;">Dhaka</a> </li>
                    <li rel="tab2"><a href="javascript:return false;">Chittagong</a> </li>
                    <li rel="tab3"><a href="javascript:return false;">Rajshahi</a> </li>
                    <li rel="tab4"><a href="javascript:return false;">Khulna</a> </li>
                    <li rel="tab5"><a href="javascript:return false;">Sylhet</a> </li>
                    <li rel="tab6"><a href="javascript:return false;">Rangpur</a> </li>
                    <li rel="tab7"><a href="javascript:return false;">Barishal</a> </li>
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
  margin: 70px 20px;
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
    padding: 10px 33px;
    color:#88A5B3;
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