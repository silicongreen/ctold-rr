<div class="container" id="tabContainer" style="width: 77%; min-height: 250px; margin-bottom: 250px;">
    <div style="float:left;margin-left:20px;width: 96%;">
        <a href="<?php echo base_url('spellingbee'); ?>">
            <h1 style="color:#93989C;float: left;" class="title noPrint f2">
                Spelling Bee&nbsp;
            </h1>
        </a>                
        <h1 style="color:#93989C;float: left;" class="title noPrint f2">
            > Archive 
        </h1>        
    </div>
    <div style="clear:both;"></div>
    <div class="spellingbee">        
        
        <div class="col-lg-12 row-fluid row">
            <div class="tabcontainer">
                <a href="<?php echo base_url('archive'); ?>">
                    <h1 style="float: left;color:#000;font-size:40px;margin-top: 5px;" class="title noPrint f2">
                        Archive
                    </h1>
                </a>
                <ul class="tabheading">
                    <li class="<?php if($active_tab == "season3"){ echo "active";}?>" rel="tab1"><a href="javascript:return false;">Season 3</a> </li>
                    <li class="<?php if($active_tab == "season2"){ echo "active";}?>" rel="tab2"><a href="javascript:return false;">Season 2</a> </li>
                    <li class="<?php if($active_tab == "season1"){ echo "active";}?>" rel="tab3"><a href="javascript:return false;">Season 1</a> </li>
                </ul>
                <div style="clear:both;"></div>   
                <div class="tabbody active" id="tab1" style="display: <?php if($active_tab == "season3"){ echo "block;";}else{echo "none;";}?>">
                  <?php $this->load->view('spell_archive/season_3'); ?>   
                </div>

                <div class="tabbody content mCustomScrollbar" id="tab2" style="display: <?php if($active_tab == "season2"){ echo "block;";}else{echo "none;";}?>">
                  <?php $this->load->view('spell_archive/season_2'); ?>  
                </div>

                <div class="tabbody content mCustomScrollbar" id="tab3" style="display: <?php if($active_tab == "season1"){ echo "block;";}else{echo "none;";}?>;">
                  <?php $this->load->view('spell_archive/season_1'); ?> 
                </div>
            </div>

        </div>
        <div style="margin-top:30px;clear: both;height: 145px;">
            <center><img src="<?php echo base_url('styles/layouts/tdsfront/spelling_bee/2015/images/sponser.png'); ?>" style="width:100%;"></center>
        </div>
    </div>
        
</div>

<style>
.spellingbee {

  margin: 10px 20px;
  
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
    float:right;
    margin-bottom:15px;
    margin-top:15px;
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
    width: 100%;
    padding: 0;
    display: none;
}
.tabbody p
{
    font-family: Arial;
    letter-spacing:0px;
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
<style>
    .row{
        padding: 30px;
        border:1px solid #ccc;       
        background-color: #fff;
    }
</style>