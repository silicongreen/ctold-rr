<script type="text/javascript" src="<?php echo base_url('styles/layouts/tdsfront/spelling_bee/jquery.mCustomScrollbar.concat.min.js'); ?>"></script>
<link rel="stylesheet" href="<?php echo base_url('styles/layouts/tdsfront/spelling_bee/jquery.mCustomScrollbar.css'); ?>">
<div class="container" id="tabContainer" style="width: 77%; min-height: 250px; margin-bottom: 250px;">
    <div style="float:left;margin-left:20px;width: 96%;">
        <a href="http://www.champs21.dev/spellingbee/">
            <h1 style="color:#93989C;float: left;" class="title noPrint f2">
                Spelling Bee
            </h1>
        </a>
        
    </div>
    
    <div style="clear:both;"></div>
    <div class="spellingbee">
        <div class="tabcontainer">
            <ul class="tabheading">
                <li class="active" rel="tab1"><a href="javascript:return false;">Home</a> </li>
                <li rel="tab2"><a href="javascript:return false;">Competition Format</a> </li>
                <li rel="tab3"><a href="javascript:return false;">Award</a> </li>
                <li rel="tab4"><a href="javascript:return false;">How to Participate</a> </li>
                <li rel="tab5"><a href="javascript:return false;">Study Tips</a> </li>
<!--                <li rel="tab6"><a href="javascript:return false;">Rangpur</a> </li>
                <li rel="tab7"><a href="javascript:return false;">Barishal</a> </li>-->
            </ul>

            <div class="tabbody active" id="tab1" style="display: block;">
              <?php $this->load->view('spellingbee/home'); ?>   
            </div>

            <div class="tabbody content mCustomScrollbar" id="tab2" style="display: none;padding: 20px;">
              <?php $this->load->view('spellingbee/about'); ?>  
            </div>

            <div class="tabbody content mCustomScrollbar" id="tab3" style="display: none;padding: 20px;">
              <?php $this->load->view('spellingbee/award'); ?> 
            </div>

            <div class="tabbody content mCustomScrollbar" id="tab4" style="display: none;padding: 20px;">
              <?php $this->load->view('spellingbee/how_to_perticipate'); ?>  
            </div>

            <div class="tabbody content mCustomScrollbar" id="tab5" style="display: none;padding: 20px;">
              <?php $this->load->view('spellingbee/study_tips'); ?>  
            </div>
            
        </div>
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
<!--        <div class="submenu">
            <div class='ribbon'>
                <a id="tabHeader_1" href='#'><span>Home</span></a>
                <a id="tabHeader_2" href='#'><span>Competition Format</span></a>
                <a id="tabHeader_8" href='#'><span>Award</span></a>
                <a id="tabHeader_3" href='#'><span>How to Participate</span></a>
                <a id="tabHeader_9" href='#'><span>Registration</span></a>
                <a id="tabHeader_10" href='#'><span>Study Tips</span></a>
            </div>
        </div>-->
        
        
        <div style="margin-top:30px;clear: both;height: 250px;">
            <div class="spelling_bee_play swing google_bg" style="">
                <h2 class="f2" style="margin-left:30px;font-size: 16px;">Good News!!</h2>
                <p style="font-size: 12px !important;line-height: 16px;padding: 0 30px;width: 225px;">
                    Now you can play online round from your android mobile.Go to Play Store and download the App <a href="https://play.google.com/store/apps/details?id=com.champs21.schoolapp&hl=en" target="_blank">Champs21</a>
                </p>
                <a style="margin-left:30px;" href="https://play.google.com/store/apps/details?id=com.champs21.schoolapp&hl=en" target="_blank"><img src="<?php echo base_url('styles/layouts/tdsfront/spelling_bee/2015/images/google_play.png'); ?>" style="width:55%;margin-top:15px;" class="scalup"></a>
            </div>
            <div class="swing" style="float: left;width: 31%;background: #fff;height: 250px;">
                <h2 class="f2" style="margin-left:30px;font-size: 16px;">Archive</h2>
                <a  href="#" class="f2 button-filter">Season 3</a>
                <a  href="#" class="f2 button-filter">Season 2</a>
                <a  href="#" class="f2 button-filter">Season 1</a>
            </div>
            <div class="swing" style="float: right;width: 31%;background-color: #fff;height: 250px;">
                <h2 class="f2" style="margin-left:30px;font-size: 16px;">Download PDF</h2>
                <center><img data="spellato" src="<?php echo base_url('styles/layouts/tdsfront/spelling_bee/2015/images/spellato.png'); ?>" style="width:55%;margin-top:15px;cursor: pointer;" class="scalup spellato_dl_link"></center>                
            </div>
        </div>
        
        <div style="margin-top:30px;clear: both;height: 145px;">
            <center><img src="<?php echo base_url('styles/layouts/tdsfront/spelling_bee/2015/images/sponser.png'); ?>" style="width:100%;"></center>
        </div>
    </div>
</div>
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
    .spellingbee
    {
        
        margin: 1px 20px;
        
    }
    .submenu {
    display: block; 
    margin: 57px;
    margin-bottom: 10px;
}
/*Forked ends*/

.ribbon:after, .ribbon:before {
    margin-top:0.5em;
    content: "";
    float:left;
    border:1.5em solid #fff;
}

.ribbon:after {
    border-right-color:transparent;
}

.ribbon:before {
    border-left-color:transparent;
}

/*Links*/
 .ribbon a:link { 
    color:#093651;
    text-decoration:none;
    float:left;
    height:3.5em;
    overflow:hidden;
}
/*Animated Folds*/
.ribbon span {
    background:#fff;
    display:inline-block;
    line-height:3em;
    padding:0 1em;
    margin-top:0.5em;
    position:relative;

    -webkit-transition: background-color 0.2s, margin-top 0.2s;  /* Saf3.2+, Chrome */
    -moz-transition: background-color 0.2s, margin-top 0.2s;  /* FF4+ */
    -ms-transition: background-color 0.2s, margin-top 0.2s;  /* IE10 */
    -o-transition: background-color 0.2s, margin-top 0.2s;  /* Opera 10.5+ */
    transition: background-color 0.2s, margin-top 0.2s;
}

.ribbon a:hover span,.ribbon a:focus span {
    background:#FFD204;
    margin-top:0;
}

.ribbon span:before {
    content: "";
    position:absolute;
    top:3em;
    left:0;
    border-right:0.5em solid #9B8651;
    border-bottom:0.5em solid #fff;
}

.ribbon span:after {
    content: "";
    position:absolute;
    top:3em;
    right:0;
    border-left:0.5em solid #9B8651;
    border-bottom:0.5em solid #fff;
}
/*TAb*/
.tabcontainer {
    float: left;
    width: 100%;    
    margin-bottom:30px;
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
	-moz-border-radius-topleft: 0px;
	-moz-border-radius-topright: 4px;
	-moz-border-radius-bottomright: 4px;
	-moz-border-radius-bottomleft: 4px;
	border-top-left-radius: 0px;
        font-family:Arial !important;
	border-top-right-radius: 4px;
	border-bottom-right-radius: 4px;
	border-bottom-left-radius: 4px; 
	height: 410px;
        background-color: #fff;
        overflow: hidden;
        clear: both;
	/*background: #FFFFFF; /* old browsers */
	/*background: -moz-linear-gradient(top, #FFFFFF 0%, #FFFFFF 90%, #e4e9ed 100%); /* firefox */
	/*background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#FFFFFF), color-stop(90%,#FFFFFF), color-stop(100%,#e4e9ed));  webkit */
	margin:0;
	color:#333;
}
.tabbody p
{
    font-family:Arial !important;
}
.tabbody table td
{
    font-family:Arial !important;
}
.tabbody ol
{
    margin: 0 0 10px 30px
}
.spelling_bee_play{
  float: left;
  height: 250px;
  width: 31%;
  margin-right:36px;
  background-color: #fff;  

}
#tab2 ul li, #tab3 ul li, #tab5 ul li {
    list-style: disc !important;
}
#tab3 ol li {
    margin-left: 10px;
    padding-left: 10px;
}

.google_bg
{
    background-image: url("<?php echo base_url('styles/layouts/tdsfront/spelling_bee/2015/images/phone.png'); ?>");
    background-position: right center;
    background-repeat: no-repeat;
    background-size: 25% auto;    
    left: 0px;
    position: relative;
    top: 2px;    
}


.scalup
{
    transition: all 300ms linear;
}
.scalup:hover
{
    
    -webkit-transform: scale(1.1);
    -moz-transform: scale(1.1);
    -o-transform: scale(1.1);
    -ms-transform: scale(1.1);
    transform: scale(1.1);
}
.swing:hover
{
       border:1px solid #ccc;       
       -webkit-animation: swing 1s ease;
        animation: swing 1s ease;
        -webkit-animation-iteration-count: 1;
        animation-iteration-count: 1;
}

.button-filter {
  background-color: #f2fafd;
  border: 1px solid #D6F2FC;
  color: #93989c;
  cursor: pointer;
  display: block;
  font-size: 17px;
  font-weight: normal;
  margin:8px 56px;
  padding: 10px 17px;
  text-decoration: none;
  transition: all 0.25s ease-in 0s;
  width: 200px;
  text-align:center;
}

.button-filter:hover, .button-filter1:active {
  background-color: #FDB218;
  color: #ffffff;
  transition: all 0.25s linear 0s;
}

@-webkit-keyframes swing
{
    15%
    {
        -webkit-transform: translateX(5px);
        transform: translateX(5px);
    }
    30%
    {
        -webkit-transform: translateX(-5px);
       transform: translateX(-5px);
    } 
    50%
    {
        -webkit-transform: translateX(3px);
        transform: translateX(3px);
    }
    65%
    {
        -webkit-transform: translateX(-3px);
        transform: translateX(-3px);
    }
    80%
    {
        -webkit-transform: translateX(2px);
        transform: translateX(2px);
    }
    100%
    {
        -webkit-transform: translateX(0);
        transform: translateX(0);
    }
}
@keyframes swing
{
    15%
    {
        -webkit-transform: translateX(5px);
        transform: translateX(5px);
    }
    30%
    {
        -webkit-transform: translateX(-5px);
        transform: translateX(-5px);
    }
    50%
    {
        -webkit-transform: translateX(3px);
        transform: translateX(3px);
    }
    65%
    {
        -webkit-transform: translateX(-3px);
        transform: translateX(-3px);
    }
    80%
    {
        -webkit-transform: translateX(2px);
        transform: translateX(2px);
    }
    100%
    {
        -webkit-transform: translateX(0);
        transform: translateX(0);
    }
}
</style>
