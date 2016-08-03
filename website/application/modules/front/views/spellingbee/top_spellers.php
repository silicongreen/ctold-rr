<script type="text/javascript" src="<?php echo base_url('styles/layouts/tdsfront/spelling_bee/jquery.mCustomScrollbar.concat.min.js'); ?>"></script>
<link rel="stylesheet" href="<?php echo base_url('styles/layouts/tdsfront/spelling_bee/jquery.mCustomScrollbar.css'); ?>">
<div class="container" id="tabContainer" style="width: 77%; min-height: 250px; margin-bottom: 250px;">
    <div style="float:left;margin-left:20px;width: 96%;">
        <a href="<?php echo base_url('spellingbee'); ?>">
            <h1 style="color:#93989C;float: left;" class="title noPrint f2">
                Spelling Bee&nbsp;
            </h1>
        </a>                
        <h1 style="color:#93989C;float: left;" class="title noPrint f2">
            > Top Spellers 
        </h1>        
    </div>
    <div style="clear:both;"></div>
    <div class="spellingbee">
        <div style="float: left;width: 100%;padding: 10px 50px;margin-top:20px;">
            <a href="<?php echo base_url('top_spellers'); ?>">
                <h1 style="float: left;color:#000;font-size:40px;margin-top: 20px;" class="title noPrint f2">
                    Top Spellers
                </h1>
            </a>
            <a href="<?php echo base_url('spellingbee'); ?>">
                <img src="<?php echo base_url('styles/layouts/tdsfront/spelling_bee/2015/images/sp-logo.png'); ?>" style="float: right;width:15%;">
            </a>
            
        </div>
        <nav>
            <div class="tabcontainer">

                <ul class="tabheading">
                    <li class="<?php if($active_tab == "season3"){ echo "active";}?>" rel="tab1"><a href="javascript:return false;">Season 3</a> </li>
                    <li class="<?php if($active_tab == "season2"){ echo "active";}?>" rel="tab2"><a href="javascript:return false;">Season 2</a> </li>
                    <li class="<?php if($active_tab == "season1"){ echo "active";}?>" rel="tab3"><a href="javascript:return false;">Season 1</a> </li>
                </ul>

                <div class="tabbody active" id="tab1" style="display: <?php if($active_tab == "season3"){ echo "block;";}else{echo "none;";}?>">
                   <?php $this->load->view('leaderboard/barishal'); ?>
                </div>

                <div class="tabbody" id="tab2" style="display: <?php if($active_tab == "season2"){ echo "block;";}else{echo "none;";}?>">
                  <?php $this->load->view('leaderboard/rangpur'); ?>  
                </div>
                
                <div class="tabbody" id="tab3" style="display: <?php if($active_tab == "season1"){ echo "block;";}else{echo "none;";}?>">
                  <?php $this->load->view('leaderboard/sylhet'); ?>  
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
iframe{
    width: 100%;
    height: 800px;
    overflow: hidden;    
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
        $(this).parents('.tabcontainer').find('.active').removeClass('active');
        $('.tabbody').hide();
        $('#' + tabid).show();
        $(this).addClass('active');

        return false;
    });
</script>

